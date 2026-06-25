<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestAttachment;
use App\Models\User;
use App\Services\ServiceRequestWorkflowService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TechnicianServiceRequestController extends Controller
{
    public function __construct(private readonly ServiceRequestWorkflowService $workflow) {}

    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->loadMissing('technicianTeams');

        $validated = $request->validate([
            'status' => ['nullable', Rule::in(ServiceRequest::canonicalStatusOptions())],
        ]);

        $tickets = ServiceRequest::query()
            ->with(['resident.unit', 'categoryMaster', 'subcategory', 'technicianTeam', 'attachments', 'events.actor'])
            ->whereIn('technician_team_id', $user->technicianTeams->pluck('id'))
            ->when(isset($validated['status']), fn (Builder $query) => $query->canonicalStatus($validated['status']))
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => $tickets->getCollection()->map(fn (ServiceRequest $ticket) => $this->ticketPayload($ticket))->values(),
            'meta' => [
                'current_page' => $tickets->currentPage(),
                'last_page' => $tickets->lastPage(),
                'per_page' => $tickets->perPage(),
                'total' => $tickets->total(),
            ],
        ]);
    }

    public function show(Request $request, ServiceRequest $ticket): JsonResponse
    {
        $ticket = $this->resolveAccessibleTicket($request->user(), $ticket);
        $ticket->loadMissing(['resident.unit', 'categoryMaster', 'subcategory', 'technicianTeam', 'attachments', 'events.actor']);

        return response()->json([
            'data' => $this->ticketPayload($ticket, true),
        ]);
    }

    public function onTheWay(Request $request, ServiceRequest $ticket): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $ticket = $this->resolveAccessibleTicket($user, $ticket);

        $validated = $request->validate([
            'estimated_arrival_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
        ]);

        $ticket = $this->workflow->markOnTheWay($ticket, $user, $validated['estimated_arrival_minutes']);

        return response()->json([
            'message' => 'Ticket ditandai On The Way.',
            'data' => $this->ticketPayload($ticket, true),
        ]);
    }

    public function start(Request $request, ServiceRequest $ticket): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $ticket = $this->resolveAccessibleTicket($user, $ticket);

        $validated = $request->validate([
            'before_photos' => ['required', 'array', 'min:1', 'max:3'],
            'before_photos.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $ticket = $this->workflow->start($ticket, $user, $request->file('before_photos', []));

        return response()->json([
            'message' => 'Ticket masuk ke In Progress.',
            'data' => $this->ticketPayload($ticket, true),
        ]);
    }

    public function complete(Request $request, ServiceRequest $ticket): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $ticket = $this->resolveAccessibleTicket($user, $ticket);

        $validated = $request->validate([
            'completion_notes' => ['required', 'string'],
            'after_photos' => ['required', 'array', 'min:1', 'max:3'],
            'after_photos.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $ticket = $this->workflow->complete($ticket, $user, $request->file('after_photos', []), $validated['completion_notes']);

        return response()->json([
            'message' => 'Ticket berhasil diselesaikan.',
            'data' => $this->ticketPayload($ticket, true),
        ]);
    }

    public function hotline(): JsonResponse
    {
        return response()->json([
            'data' => [
                'name' => AppSetting::query()->where('key', 'technician_hotline_name')->value('value') ?: 'Service Dispatch Hotline',
                'phone' => AppSetting::query()->where('key', 'technician_hotline_phone')->value('value') ?: '021-1500-112',
                'note' => AppSetting::query()->where('key', 'technician_hotline_note')->value('value') ?: 'Hubungi hotline bila ada eskalasi onsite atau kendala akses unit.',
            ],
        ]);
    }

    private function resolveAccessibleTicket(User $user, ServiceRequest $ticket): ServiceRequest
    {
        $user->loadMissing('technicianTeams');

        abort_unless(
            $ticket->technician_team_id && $user->technicianTeams->contains('id', $ticket->technician_team_id),
            404
        );

        return $ticket;
    }

    private function ticketPayload(ServiceRequest $ticket, bool $includeTimeline = false): array
    {
        $payload = [
            'id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'title' => $ticket->title,
            'description' => $ticket->description,
            'priority' => $ticket->priority,
            'status' => $ticket->canonicalStatus(),
            'raw_status' => $ticket->status,
            'source' => $ticket->source,
            'assigned_to' => $ticket->assigned_to,
            'scheduled_at' => $ticket->scheduled_at?->toIso8601String(),
            'estimated_arrival_minutes' => $ticket->estimated_arrival_minutes,
            'sla_due_at' => $ticket->sla_due_at?->toIso8601String(),
            'sla_state' => $ticket->slaState(),
            'resident' => $ticket->resident ? [
                'id' => $ticket->resident->id,
                'name' => $ticket->resident->name,
                'unit' => $ticket->resident->unit?->code,
            ] : null,
            'team' => $ticket->technicianTeam ? [
                'id' => $ticket->technicianTeam->id,
                'name' => $ticket->technicianTeam->name,
            ] : null,
            'category' => $ticket->categoryMaster ? [
                'id' => $ticket->categoryMaster->id,
                'name' => $ticket->categoryMaster->name,
            ] : [
                'id' => $ticket->service_request_category_id,
                'name' => $ticket->category,
            ],
            'subcategory' => $ticket->subcategory ? [
                'id' => $ticket->subcategory->id,
                'name' => $ticket->subcategory->name,
            ] : null,
            'attachments' => $ticket->attachments->map(fn (ServiceRequestAttachment $attachment) => [
                'id' => $attachment->id,
                'file_name' => $attachment->original_name,
                'mime_type' => $attachment->mime_type,
                'file_size' => $attachment->file_size,
                'attachment_type' => $attachment->attachment_type,
                'url' => $attachment->url,
            ])->values(),
            'before_attachments' => $ticket->attachmentsByType(ServiceRequestAttachment::TYPE_TECHNICIAN_BEFORE)->map(fn (ServiceRequestAttachment $attachment) => [
                'id' => $attachment->id,
                'file_name' => $attachment->original_name,
                'url' => $attachment->url,
            ])->values(),
            'after_attachments' => $ticket->attachmentsByType(ServiceRequestAttachment::TYPE_TECHNICIAN_AFTER)->map(fn (ServiceRequestAttachment $attachment) => [
                'id' => $attachment->id,
                'file_name' => $attachment->original_name,
                'url' => $attachment->url,
            ])->values(),
        ];

        if ($includeTimeline) {
            $payload['timeline'] = $ticket->events->map(fn ($event) => [
                'event_type' => $event->event_type,
                'from_status' => $event->from_status,
                'to_status' => $event->to_status,
                'notes' => $event->notes,
                'actor' => $event->actor?->name,
                'created_at' => $event->created_at?->toIso8601String(),
                'meta' => $event->meta,
            ])->values();
        }

        return $payload;
    }
}
