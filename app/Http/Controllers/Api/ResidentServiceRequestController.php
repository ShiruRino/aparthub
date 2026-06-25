<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestAttachment;
use App\Models\ServiceRequestCategory;
use App\Models\ServiceRequestSubcategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ResidentServiceRequestController extends Controller
{
    /**
     * Return the active service request catalog for the authenticated resident.
     */
    public function catalog(Request $request): JsonResponse
    {
        /** @var Resident $resident */
        $resident = $request->user();

        $catalog = ServiceRequestCategory::query()
            ->where('is_active', true)
            ->with(['subcategories' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')->orderBy('name')])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (ServiceRequestCategory $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'subcategories' => $category->subcategories->map(fn (ServiceRequestSubcategory $subcategory) => [
                    'id' => $subcategory->id,
                    'name' => $subcategory->name,
                    'sla' => [
                        'Low' => $subcategory->low_sla_minutes,
                        'Medium' => $subcategory->medium_sla_minutes,
                        'High' => $subcategory->high_sla_minutes,
                        'Emergency' => $subcategory->emergency_sla_minutes,
                    ],
                ])->values(),
            ])->values();

        return response()->json([
            'data' => [
                'resident_id' => $resident->id,
                'catalog' => $catalog,
            ],
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        /** @var Resident $resident */
        $resident = $request->user();

        $validated = $request->validate([
            'status' => ['nullable', Rule::in(ServiceRequest::canonicalStatusOptions())],
        ]);

        $requests = ServiceRequest::query()
            ->with(['categoryMaster', 'subcategory', 'attachments', 'resident.unit'])
            ->where('resident_id', $resident->id)
            ->when(isset($validated['status']), fn (Builder $query) => $query->canonicalStatus($validated['status']))
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => $requests->getCollection()->map(fn (ServiceRequest $ticket) => $this->ticketPayload($ticket))->values(),
            'meta' => [
                'current_page' => $requests->currentPage(),
                'last_page' => $requests->lastPage(),
                'per_page' => $requests->perPage(),
                'total' => $requests->total(),
            ],
        ]);
    }

    /**
     * Create a resident service request from multipart form-data.
     *
     * Expected payload:
     * - subcategory_id
     * - title
     * - description
     * - priority
     * - attachments[] (optional, max 3 image files)
     */
    public function store(Request $request): JsonResponse
    {
        /** @var Resident $resident */
        $resident = $request->user();

        $validated = $request->validate([
            'subcategory_id' => ['required', 'exists:service_request_subcategories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['required', Rule::in(ServiceRequest::priorityOptions())],
            'attachments' => ['nullable', 'array', 'max:3'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'images' => ['prohibited'],
            'resident_id' => ['prohibited'],
            'category_id' => ['prohibited'],
            'ticket_number' => ['prohibited'],
            'source' => ['prohibited'],
            'status' => ['prohibited'],
            'assigned_to' => ['prohibited'],
            'sla_target_minutes' => ['prohibited'],
            'sla_due_at' => ['prohibited'],
            'requested_date' => ['prohibited'],
            'requested_time' => ['prohibited'],
            'preferred_schedule' => ['prohibited'],
        ]);

        $subcategory = ServiceRequestSubcategory::query()
            ->with('category')
            ->where('is_active', true)
            ->findOrFail($validated['subcategory_id']);

        if (! $subcategory->category || ! $subcategory->category->is_active) {
            throw ValidationException::withMessages([
                'subcategory_id' => 'Subkategori atau kategori service request tidak aktif.',
            ]);
        }

        $files = $request->file('attachments', []);

        $ticket = DB::transaction(function () use ($files, $resident, $subcategory, $validated) {
            $slaTargetMinutes = $subcategory->slaMinutesFor($validated['priority']);
            $createdAt = now();

            $ticket = ServiceRequest::query()->create([
                'ticket_number' => $this->nextTicketNumber(),
                'resident_id' => $resident->id,
                'service_request_category_id' => $subcategory->service_request_category_id,
                'service_request_subcategory_id' => $subcategory->id,
                'category' => $subcategory->category?->name,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'priority' => $validated['priority'],
                'status' => ServiceRequest::STATUS_SUBMITTED,
                'source' => 'Resident App',
                'sla_target_minutes' => $slaTargetMinutes,
                'sla_due_at' => $createdAt->copy()->addMinutes($slaTargetMinutes),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            foreach ($files as $file) {
                $path = $file->store('service-requests/attachments', 'public');

                ServiceRequestAttachment::query()->create([
                    'service_request_id' => $ticket->id,
                    'disk' => 'public',
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                    'file_size' => $file->getSize(),
                    'attachment_type' => ServiceRequestAttachment::TYPE_RESIDENT_SUPPORTING,
                ]);
            }

            return $ticket->load(['categoryMaster', 'subcategory', 'attachments', 'resident.unit']);
        });

        return response()->json([
            'message' => 'Service request berhasil dibuat.',
            'data' => $this->ticketPayload($ticket),
        ], 201);
    }

    public function show(Request $request, ServiceRequest $ticket): JsonResponse
    {
        /** @var Resident $resident */
        $resident = $request->user();

        if ($ticket->resident_id !== $resident->id) {
            return response()->json([
                'message' => 'Service request tidak ditemukan.',
            ], 404);
        }

        $ticket->loadMissing(['categoryMaster', 'subcategory', 'attachments', 'resident.unit']);

        return response()->json([
            'data' => $this->ticketPayload($ticket, true),
        ]);
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
            'sla_target_minutes' => $ticket->sla_target_minutes,
            'sla_due_at' => $ticket->sla_due_at?->toIso8601String(),
            'sla_state' => $ticket->slaState(),
            'assigned_to' => $ticket->assigned_to,
            'operational_timestamp' => $ticket->operationalTimestamp()?->toIso8601String(),
            'created_at' => $ticket->created_at?->toIso8601String(),
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
            'unit' => $ticket->resident?->unit ? [
                'id' => $ticket->resident->unit->id,
                'code' => $ticket->resident->unit->code,
                'tower' => $ticket->resident->unit->tower,
                'floor' => $ticket->resident->unit->floor,
            ] : null,
            'attachments' => $ticket->attachments->map(fn (ServiceRequestAttachment $attachment) => [
                'id' => $attachment->id,
                'file_name' => $attachment->original_name,
                'mime_type' => $attachment->mime_type,
                'file_size' => $attachment->file_size,
                'attachment_type' => $attachment->attachment_type,
                'url' => $attachment->url,
            ])->values(),
        ];

        if ($includeTimeline) {
            $payload['timeline'] = $ticket->timeline();
            $payload['completed_at'] = $ticket->completed_at?->toIso8601String();
        }

        return $payload;
    }

    private function nextTicketNumber(): string
    {
        $year = now()->format('Y');
        $latest = ServiceRequest::query()
            ->where('ticket_number', 'like', 'SR-'.$year.'-%')
            ->latest('id')
            ->value('ticket_number');

        $sequence = $latest
            ? ((int) substr($latest, strrpos($latest, '-') + 1)) + 1
            : 1;

        return 'SR-'.$year.'-'.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
    }
}
