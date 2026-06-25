<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestCategory;
use App\Models\ServiceRequestSubcategory;
use App\Models\TechnicianTeam;
use App\Models\User;
use App\Services\ServiceRequestWorkflowService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ServiceRequestController extends Controller
{
    public function __construct(private readonly ServiceRequestWorkflowService $workflow) {}

    public function index(Request $request): View
    {
        return $this->page($request, 'ticket-queue');
    }

    public function ticketQueue(Request $request): View
    {
        return $this->page($request, 'ticket-queue');
    }

    public function newRequest(Request $request): View
    {
        return $this->page($request, 'new-request');
    }

    public function workOrders(Request $request): View
    {
        return $this->page($request, 'work-orders');
    }

    public function technicianSchedule(Request $request): View
    {
        return $this->page($request, 'technician-schedule');
    }

    public function workInProgress(Request $request): View
    {
        return $this->page($request, 'work-in-progress');
    }

    public function completedRequests(Request $request): View
    {
        return $this->page($request, 'completed-requests');
    }

    public function serviceHistory(Request $request): View
    {
        return $this->page($request, 'service-history');
    }

    public function settings(Request $request): View
    {
        return $this->page($request, 'settings');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedPayload($request);
        /** @var User|null $actor */
        $actor = $request->user();

        DB::transaction(function () use ($data, $actor) {
            $ticket = ServiceRequest::query()->create(
                $this->buildServiceRequestAttributes($data, null) + [
                    'ticket_number' => $this->nextTicketNumber(),
                ]
            );

            $this->workflow->logEvent(
                $ticket,
                'created',
                null,
                $ticket->status,
                $actor,
                null,
                [
                    'source' => $ticket->source,
                    'technician_team_id' => $ticket->technician_team_id,
                ]
            );
        });

        return redirect()
            ->route('service-request.ticket-queue')
            ->with('status', 'Service request berhasil dibuat.');
    }

    public function update(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $data = $this->validatedPayload($request, $serviceRequest);
        /** @var User|null $actor */
        $actor = $request->user();

        DB::transaction(function () use ($data, $serviceRequest, $actor) {
            $fromStatus = $serviceRequest->status;
            $attributes = $this->buildServiceRequestAttributes($data, $serviceRequest);
            $serviceRequest->update($attributes);

            $this->workflow->logEvent(
                $serviceRequest->fresh(),
                'admin_update',
                $fromStatus,
                $attributes['status'],
                $actor,
                $attributes['completion_notes'] ?? null,
                [
                    'technician_team_id' => $attributes['technician_team_id'] ?? null,
                    'scheduled_at' => $attributes['scheduled_at'] ?? null,
                    'assigned_to' => $attributes['assigned_to'] ?? null,
                ]
            );
        });

        return redirect()
            ->back()
            ->with('status', 'Service request berhasil diperbarui.');
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        ServiceRequestCategory::query()->create($this->validatedCategory($request));

        return redirect()
            ->route('service-request.settings')
            ->with('status', 'Kategori service request berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, ServiceRequestCategory $category): RedirectResponse
    {
        $category->update($this->validatedCategory($request, $category));

        return redirect()
            ->route('service-request.settings')
            ->with('status', 'Kategori service request berhasil diperbarui.');
    }

    public function destroyCategory(ServiceRequestCategory $category): RedirectResponse
    {
        if ($category->serviceRequests()->exists() || $category->subcategories()->exists()) {
            return redirect()
                ->route('service-request.settings')
                ->withErrors(['service_catalog' => 'Kategori masih dipakai oleh subkategori atau ticket.']);
        }

        $category->delete();

        return redirect()
            ->route('service-request.settings')
            ->with('status', 'Kategori service request berhasil dihapus.');
    }

    public function storeSubcategory(Request $request): RedirectResponse
    {
        ServiceRequestSubcategory::query()->create($this->validatedSubcategory($request));

        return redirect()
            ->route('service-request.settings')
            ->with('status', 'Subkategori dan SLA berhasil ditambahkan.');
    }

    public function updateSubcategory(Request $request, ServiceRequestSubcategory $subcategory): RedirectResponse
    {
        $subcategory->update($this->validatedSubcategory($request, $subcategory));

        return redirect()
            ->route('service-request.settings')
            ->with('status', 'Subkategori dan SLA berhasil diperbarui.');
    }

    public function destroySubcategory(ServiceRequestSubcategory $subcategory): RedirectResponse
    {
        if ($subcategory->serviceRequests()->exists()) {
            return redirect()
                ->route('service-request.settings')
                ->withErrors(['service_catalog' => 'Subkategori masih dipakai oleh ticket.']);
        }

        $subcategory->delete();

        return redirect()
            ->route('service-request.settings')
            ->with('status', 'Subkategori berhasil dihapus.');
    }

    private function page(Request $request, string $page): View
    {
        $categories = ServiceRequestCategory::query()
            ->with(['subcategories' => fn ($query) => $query->orderBy('sort_order')->orderBy('name')])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $summary = $this->summary();
        $requests = $this->requestsForPage($request, $page);
        $residents = Resident::query()->with('unit')->orderBy('name')->get();
        $teams = TechnicianTeam::query()->withCount('users')->orderBy('name')->get();

        return view('service-request.index', [
            'pageKey' => $page,
            'summary' => $summary,
            'requests' => $requests,
            'residentOptions' => $residents,
            'priorityOptions' => ServiceRequest::priorityOptions(),
            'statusOptions' => ServiceRequest::canonicalStatusOptions(),
            'categoryOptions' => $categories,
            'subcategoryOptions' => $categories->flatMap->subcategories,
            'teamOptions' => $teams,
            'catalogJson' => $categories->map(fn (ServiceRequestCategory $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'subcategories' => $category->subcategories->map(fn (ServiceRequestSubcategory $subcategory) => [
                    'id' => $subcategory->id,
                    'name' => $subcategory->name,
                    'is_active' => $subcategory->is_active,
                    'low_sla_minutes' => $subcategory->low_sla_minutes,
                    'medium_sla_minutes' => $subcategory->medium_sla_minutes,
                    'high_sla_minutes' => $subcategory->high_sla_minutes,
                    'emergency_sla_minutes' => $subcategory->emergency_sla_minutes,
                ])->values(),
            ])->values()->toJson(),
        ]);
    }

    private function requestsForPage(Request $request, string $page): LengthAwarePaginator
    {
        $query = ServiceRequest::query()
            ->with(['resident.unit', 'categoryMaster', 'subcategory', 'technicianTeam', 'attachments', 'events.actor'])
            ->when($request->string('search')->toString(), function (Builder $builder, string $search) {
                $builder->where(function (Builder $searchQuery) use ($search) {
                    $searchQuery->where('ticket_number', 'like', '%'.$search.'%')
                        ->orWhere('title', 'like', '%'.$search.'%')
                        ->orWhere('category', 'like', '%'.$search.'%')
                        ->orWhereHas('subcategory', fn (Builder $subcategoryQuery) => $subcategoryQuery->where('name', 'like', '%'.$search.'%'))
                        ->orWhereHas('resident', fn (Builder $residentQuery) => $residentQuery->where('name', 'like', '%'.$search.'%'));
                });
            })
            ->when($request->filled('priority'), fn (Builder $builder) => $builder->where('priority', $request->string('priority')->toString()))
            ->when($request->filled('status'), fn (Builder $builder) => $builder->canonicalStatus($request->string('status')->toString()))
            ->when($request->filled('category_id'), fn (Builder $builder) => $builder->where('service_request_category_id', $request->integer('category_id')))
            ->when($request->filled('subcategory_id'), fn (Builder $builder) => $builder->where('service_request_subcategory_id', $request->integer('subcategory_id')))
            ->when($request->filled('technician_team_id'), fn (Builder $builder) => $builder->where('technician_team_id', $request->integer('technician_team_id')))
            ->when($request->filled('sla_state'), function (Builder $builder) use ($request) {
                if ($request->string('sla_state')->toString() === 'over') {
                    $builder->overSla();
                }
            });

        match ($page) {
            'work-orders' => $query->whereIn('status', [ServiceRequest::STATUS_ASSIGNED, ServiceRequest::STATUS_ON_THE_WAY, ServiceRequest::STATUS_IN_PROGRESS]),
            'technician-schedule' => $query->whereIn('status', [ServiceRequest::STATUS_ASSIGNED, ServiceRequest::STATUS_ON_THE_WAY, ServiceRequest::STATUS_IN_PROGRESS]),
            'work-in-progress' => $query->canonicalStatus(ServiceRequest::STATUS_IN_PROGRESS),
            'completed-requests', 'service-history' => $query->canonicalStatus(ServiceRequest::STATUS_COMPLETED),
            default => null,
        };

        return $query
            ->latest()
            ->paginate(8)
            ->withQueryString();
    }

    private function summary(): array
    {
        return [
            'submitted' => ServiceRequest::query()->canonicalStatus(ServiceRequest::STATUS_SUBMITTED)->count(),
            'assigned' => ServiceRequest::query()->canonicalStatus(ServiceRequest::STATUS_ASSIGNED)->count(),
            'on_the_way' => ServiceRequest::query()->canonicalStatus(ServiceRequest::STATUS_ON_THE_WAY)->count(),
            'in_progress' => ServiceRequest::query()->canonicalStatus(ServiceRequest::STATUS_IN_PROGRESS)->count(),
            'completed_today' => ServiceRequest::query()->whereDate('completed_at', today())->count(),
            'over_sla' => ServiceRequest::query()->overSla()->count(),
            'emergency' => ServiceRequest::query()->where('priority', ServiceRequest::PRIORITY_EMERGENCY)->count(),
        ];
    }

    private function validatedPayload(Request $request, ?ServiceRequest $serviceRequest = null): array
    {
        $data = $request->validate([
            'resident_id' => ['required', 'exists:residents,id'],
            'category_id' => ['required', 'exists:service_request_categories,id'],
            'subcategory_id' => ['required', 'exists:service_request_subcategories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['required', Rule::in(ServiceRequest::priorityOptions())],
            'status' => ['nullable', Rule::in(ServiceRequest::canonicalStatusOptions())],
            'source' => ['nullable', 'string', 'max:255'],
            'assigned_to' => ['nullable', 'string', 'max:255'],
            'technician_team_id' => ['nullable', 'exists:technician_teams,id'],
            'scheduled_at' => ['nullable', 'date'],
            'completion_notes' => ['nullable', 'string'],
        ]);

        $subcategory = ServiceRequestSubcategory::query()
            ->with('category')
            ->findOrFail($data['subcategory_id']);

        if ((int) $subcategory->service_request_category_id !== (int) $data['category_id']) {
            throw ValidationException::withMessages([
                'subcategory_id' => 'Subkategori tidak sesuai dengan kategori terpilih.',
            ]);
        }

        $data['category'] = $subcategory->category?->name ?? '';
        $data['service_request_category_id'] = $subcategory->service_request_category_id;
        $data['service_request_subcategory_id'] = $subcategory->id;
        $data['status'] = $data['status'] ?? ($serviceRequest?->status ?? ServiceRequest::STATUS_SUBMITTED);
        $data['source'] = $data['source'] ?? ($serviceRequest?->source ?? 'Front Office');
        $data['subcategory'] = $subcategory;

        return $data;
    }

    private function buildServiceRequestAttributes(array $data, ?ServiceRequest $serviceRequest): array
    {
        /** @var ServiceRequestSubcategory $subcategory */
        $subcategory = $data['subcategory'];
        $status = $data['status'];
        $slaTargetMinutes = $subcategory->slaMinutesFor($data['priority']);
        $createdAt = $serviceRequest?->created_at ?? now();
        $technicianTeam = ! empty($data['technician_team_id'])
            ? TechnicianTeam::query()->find($data['technician_team_id'])
            : null;
        $assignedTo = $technicianTeam?->name ?: (($data['assigned_to'] ?? null) ?: null);

        return [
            'resident_id' => $data['resident_id'],
            'service_request_category_id' => $data['service_request_category_id'],
            'service_request_subcategory_id' => $data['service_request_subcategory_id'],
            'technician_team_id' => $technicianTeam?->id,
            'category' => $data['category'],
            'title' => $data['title'],
            'description' => $data['description'],
            'priority' => $data['priority'],
            'status' => $status,
            'source' => $data['source'],
            'sla_target_minutes' => $slaTargetMinutes,
            'sla_due_at' => $createdAt?->copy()->addMinutes($slaTargetMinutes),
            'assigned_to' => $assignedTo,
            'assigned_at' => in_array($status, [ServiceRequest::STATUS_ASSIGNED, ServiceRequest::STATUS_ON_THE_WAY, ServiceRequest::STATUS_IN_PROGRESS, ServiceRequest::STATUS_COMPLETED], true)
                ? ($serviceRequest?->assigned_at ?? now())
                : ($serviceRequest?->assigned_at),
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'on_the_way_at' => $status === ServiceRequest::STATUS_ON_THE_WAY
                ? ($serviceRequest?->on_the_way_at ?? now())
                : ($serviceRequest?->on_the_way_at),
            'estimated_arrival_minutes' => $serviceRequest?->estimated_arrival_minutes,
            'in_progress_at' => $status === ServiceRequest::STATUS_IN_PROGRESS
                ? ($serviceRequest?->in_progress_at ?? now())
                : ($status === ServiceRequest::STATUS_COMPLETED ? ($serviceRequest?->in_progress_at ?? now()) : $serviceRequest?->in_progress_at),
            'completion_notes' => $data['completion_notes'] ?? null,
            'completed_at' => $status === ServiceRequest::STATUS_COMPLETED ? ($serviceRequest?->completed_at ?? now()) : null,
        ];
    }

    private function validatedCategory(Request $request, ?ServiceRequestCategory $category = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('service_request_categories', 'name')->ignore($category),
            ],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function validatedSubcategory(Request $request, ?ServiceRequestSubcategory $subcategory = null): array
    {
        return $request->validate([
            'service_request_category_id' => ['required', 'exists:service_request_categories,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('service_request_subcategories', 'name')
                    ->where(fn ($builder) => $builder->where('service_request_category_id', $request->integer('service_request_category_id')))
                    ->ignore($subcategory),
            ],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'low_sla_minutes' => ['required', 'integer', 'min:1'],
            'medium_sla_minutes' => ['required', 'integer', 'min:1'],
            'high_sla_minutes' => ['required', 'integer', 'min:1'],
            'emergency_sla_minutes' => ['required', 'integer', 'min:1'],
        ]);
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
