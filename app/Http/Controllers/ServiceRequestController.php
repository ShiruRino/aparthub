<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use App\Models\ServiceRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ServiceRequestController extends Controller
{
    /**
     * Show the default service request workspace.
     */
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

    /**
     * Store a newly created service request.
     */
    public function store(Request $request): RedirectResponse
    {
        ServiceRequest::query()->create($this->validatedPayload($request) + [
            'ticket_number' => $this->nextTicketNumber(),
        ]);

        return redirect()
            ->route('service-request.ticket-queue')
            ->with('status', 'Service request berhasil dibuat.');
    }

    /**
     * Update the given service request.
     */
    public function update(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $serviceRequest->update($this->validatedPayload($request, $serviceRequest));

        return redirect()
            ->back()
            ->with('status', 'Service request berhasil diperbarui.');
    }

    /**
     * Render a service request workspace.
     */
    private function page(Request $request, string $page): View
    {
        $summary = $this->summary();
        $requests = $this->requestsForPage($request, $page);
        $residents = Resident::query()->with('unit')->orderBy('name')->get();

        return view('service-request.index', [
            'pageKey' => $page,
            'summary' => $summary,
            'requests' => $requests,
            'residentOptions' => $residents,
            'priorityOptions' => $this->priorityOptions(),
            'statusOptions' => $this->statusOptions(),
            'categoryOptions' => ['Plumbing', 'AC', 'Electrical', 'Housekeeping', 'Internet', 'General'],
        ]);
    }

    /**
     * Get a filtered paginator for the requested page.
     */
    private function requestsForPage(Request $request, string $page): LengthAwarePaginator
    {
        $query = ServiceRequest::query()
            ->with(['resident.unit'])
            ->when($request->string('search')->toString(), function ($builder, string $search) {
                $builder->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('ticket_number', 'like', '%'.$search.'%')
                        ->orWhere('title', 'like', '%'.$search.'%')
                        ->orWhere('category', 'like', '%'.$search.'%')
                        ->orWhereHas('resident', fn ($residentQuery) => $residentQuery->where('name', 'like', '%'.$search.'%'));
                });
            })
            ->when($request->filled('priority'), fn ($builder) => $builder->where('priority', $request->string('priority')))
            ->when($request->filled('status'), fn ($builder) => $builder->where('status', $request->string('status')))
            ->when($request->filled('category'), fn ($builder) => $builder->where('category', $request->string('category')));

        match ($page) {
            'work-orders' => $query->whereIn('status', ['Assigned', 'In Progress', 'Over SLA']),
            'technician-schedule' => $query->whereIn('status', ['Assigned', 'In Progress']),
            'work-in-progress' => $query->whereIn('status', ['In Progress', 'Over SLA']),
            'completed-requests' => $query->where('status', 'Completed'),
            default => null,
        };

        return $query
            ->latest()
            ->paginate(8)
            ->withQueryString();
    }

    /**
     * Build dashboard-like service summary metrics.
     *
     * @return array<string, int>
     */
    private function summary(): array
    {
        return [
            'new' => ServiceRequest::query()->where('status', 'New')->count(),
            'assigned' => ServiceRequest::query()->where('status', 'Assigned')->count(),
            'in_progress' => ServiceRequest::query()->where('status', 'In Progress')->count(),
            'completed_today' => ServiceRequest::query()->whereDate('completed_at', today())->count(),
            'over_sla' => ServiceRequest::query()->where('status', 'Over SLA')->count(),
            'emergency' => ServiceRequest::query()->where('priority', 'Emergency')->count(),
        ];
    }

    /**
     * Validate a service request payload.
     *
     * @return array<string, mixed>
     */
    private function validatedPayload(Request $request, ?ServiceRequest $serviceRequest = null): array
    {
        $data = $request->validate([
            'resident_id' => ['required', 'exists:residents,id'],
            'category' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', Rule::in($this->priorityOptions())],
            'status' => ['nullable', Rule::in($this->statusOptions())],
            'source' => ['nullable', 'string', 'max:255'],
            'assigned_to' => ['nullable', 'string', 'max:255'],
            'completion_notes' => ['nullable', 'string'],
        ]);

        $data['status'] = $data['status'] ?? ($serviceRequest?->status ?? 'New');
        $data['source'] = $data['source'] ?? ($serviceRequest?->source ?? 'Front Office');
        $data['completed_at'] = $data['status'] === 'Completed'
            ? ($serviceRequest?->completed_at ?? now())
            : null;

        return $data;
    }

    /**
     * Get the allowed service priorities.
     *
     * @return list<string>
     */
    private function priorityOptions(): array
    {
        return ['Low', 'Medium', 'High', 'Emergency'];
    }

    /**
     * Get the allowed service statuses.
     *
     * @return list<string>
     */
    private function statusOptions(): array
    {
        return ['New', 'Assigned', 'In Progress', 'Pending', 'Completed', 'Over SLA'];
    }

    /**
     * Generate the next ticket number.
     */
    private function nextTicketNumber(): string
    {
        $sequence = ServiceRequest::query()->count() + 1;

        return 'SR-'.now()->format('Y').'-'.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
    }
}
