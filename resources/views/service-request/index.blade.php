@extends('layouts.app')

@php
    use App\Models\ServiceRequest;
    use App\Models\ServiceRequestCategory;
    use App\Models\ServiceRequestSubcategory;

    $pageKey = $pageKey ?? 'ticket-queue';

    $navTabs = [
        ['label' => 'Ticket Queue', 'route' => 'service-request.ticket-queue', 'active' => ['service-request.index', 'service-request.ticket-queue']],
        ['label' => 'New Request', 'route' => 'service-request.new-request', 'active' => ['service-request.new-request']],
        ['label' => 'Work Orders', 'route' => 'service-request.work-orders', 'active' => ['service-request.work-orders']],
        ['label' => 'Technician Schedule', 'route' => 'service-request.technician-schedule', 'active' => ['service-request.technician-schedule']],
        ['label' => 'Work In Progress', 'route' => 'service-request.work-in-progress', 'active' => ['service-request.work-in-progress']],
        ['label' => 'Completed Requests', 'route' => 'service-request.completed-requests', 'active' => ['service-request.completed-requests']],
        ['label' => 'Service History', 'route' => 'service-request.service-history', 'active' => ['service-request.service-history']],
        ['label' => 'Settings', 'route' => 'service-request.settings', 'active' => ['service-request.settings']],
    ];

    $pages = [
        'ticket-queue' => ['label' => 'Ticket Queue', 'title' => 'Ticket Queue', 'subtitle' => 'Monitor seluruh ticket resident dan operasional assignment dari satu sumber data.'],
        'new-request' => ['label' => 'New Request', 'title' => 'Create New Service Request', 'subtitle' => 'Buat ticket baru dengan category, subcategory, priority, dan SLA snapshot yang dihitung server.'],
        'work-orders' => ['label' => 'Work Orders', 'title' => 'Work Orders', 'subtitle' => 'Lihat ticket yang sudah assigned atau sedang dikerjakan sebagai work order aktif.'],
        'technician-schedule' => ['label' => 'Technician Schedule', 'title' => 'Technician Schedule', 'subtitle' => 'Pantau distribusi ticket yang sudah terjadwal untuk teknisi atau vendor.'],
        'work-in-progress' => ['label' => 'Work In Progress', 'title' => 'Work In Progress', 'subtitle' => 'Fokus pada ticket yang sedang berjalan dan ticket yang sudah melewati SLA.'],
        'completed-requests' => ['label' => 'Completed Requests', 'title' => 'Completed Requests', 'subtitle' => 'Riwayat penyelesaian service request yang sudah selesai dan terdokumentasi.'],
        'service-history' => ['label' => 'Service History', 'title' => 'Service Request History', 'subtitle' => 'Audit trail service request, SLA, assignment, dan penyelesaian ticket.'],
        'settings' => ['label' => 'Settings', 'title' => 'Service Request Settings', 'subtitle' => 'Kelola Service Catalog & SLA untuk category dan subcategory yang dipakai mobile maupun admin.'],
    ];

    $page = $pages[$pageKey] ?? $pages['ticket-queue'];

    $metrics = [
        ['label' => 'Submitted', 'value' => $summary['submitted'], 'tone' => 'status-pending'],
        ['label' => 'Assigned', 'value' => $summary['assigned'], 'tone' => 'status-approved'],
        ['label' => 'On The Way', 'value' => $summary['on_the_way'], 'tone' => 'status-approved'],
        ['label' => 'In Progress', 'value' => $summary['in_progress'], 'tone' => 'status-approved'],
        ['label' => 'Completed Today', 'value' => $summary['completed_today'], 'tone' => 'status-approved'],
        ['label' => 'Emergency', 'value' => $summary['emergency'], 'tone' => 'status-rejected'],
    ];

    $statusClass = function (string $status): string {
        return match ($status) {
            ServiceRequest::STATUS_COMPLETED, ServiceRequest::STATUS_ASSIGNED, ServiceRequest::STATUS_ON_THE_WAY, ServiceRequest::STATUS_IN_PROGRESS => 'status-approved',
            ServiceRequest::STATUS_SUBMITTED => 'status-pending',
            ServiceRequest::STATUS_CANCELLED => 'status-expired',
            default => 'status-expired',
        };
    };

    $priorityClass = function (string $priority): string {
        return match ($priority) {
            ServiceRequest::PRIORITY_EMERGENCY => 'status-rejected',
            ServiceRequest::PRIORITY_HIGH => 'status-pending',
            ServiceRequest::PRIORITY_MEDIUM => 'status-expired',
            default => 'status-approved',
        };
    };

    $requestsById = $requests->getCollection()->keyBy('id');
@endphp

@section('title', $page['label'])
@section('topbar_context')
    Service Request > {{ $page['label'] }}
@endsection
@section('topbar_subtitle', $page['subtitle'])

@section('content')
    <div class="service-page">
        <section class="visitor-toolbar">
            <div class="visitor-heading">
                <span class="visitor-step">OPS</span>
                <div>
                    <h2>{{ $page['title'] }}</h2>
                    <p>{{ $page['subtitle'] }}</p>
                </div>
            </div>

            <div class="visitor-toolbar-actions">
                <button class="btn secondary" type="button" data-modal-open="service-export-modal">Export Service Data</button>
                @if ($pageKey === 'new-request')
                    <button class="btn" type="submit" form="serviceRequestForm">Submit Request</button>
                @elseif ($pageKey === 'settings')
                    <button class="btn" type="button" data-modal-open="create-category-modal">Create Category</button>
                @else
                    <a class="btn" href="{{ route('service-request.new-request') }}">Create New Request</a>
                @endif
            </div>
        </section>

        <nav class="visitor-tabs" aria-label="Service request navigation">
            @foreach ($navTabs as $tab)
                <a href="{{ route($tab['route']) }}" @class(['visitor-tab', 'active' => request()->routeIs(...$tab['active'])])>
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </nav>

        @if ($pageKey !== 'new-request')
            <section class="service-metrics">
                @foreach ($metrics as $metric)
                    <article class="visitor-chip" style="display:grid;gap:6px;padding:16px 18px;border:1px solid #dce4ef;border-radius:16px;background:#fff;min-width:160px;">
                        <span style="color:#67758a;font-weight:600;">{{ $metric['label'] }}</span>
                        <strong style="font-size:24px;color:#0b2149;">{{ $metric['value'] }}</strong>
                        <span class="resident-status {{ $metric['tone'] }}">{{ $metric['label'] === 'Emergency' ? 'Priority' : 'Live' }}</span>
                    </article>
                @endforeach
            </section>
        @endif

        @if ($pageKey === 'new-request')
            <section class="visitor-panel">
                <form class="visitor-modal-body" id="serviceRequestForm" method="POST" action="{{ route('service-request.store') }}">
                    @csrf
                    <div class="visitor-form-grid">
                        <label class="resident-filter-field">
                            <span>Resident</span>
                            <select name="resident_id" required>
                                <option value="">Pilih resident</option>
                                @foreach ($residentOptions as $resident)
                                    <option value="{{ $resident->id }}" @selected(old('resident_id') == $resident->id)>
                                        {{ $resident->name }}{{ $resident->unit?->code ? ' - Unit '.$resident->unit->code : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                        <label class="resident-filter-field">
                            <span>Category</span>
                            <select name="category_id" id="service-category-select" required data-current="{{ old('category_id') }}">
                                <option value="">Pilih category</option>
                                @foreach ($categoryOptions as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="resident-filter-field">
                            <span>Subcategory</span>
                            <select name="subcategory_id" id="service-subcategory-select" required data-current="{{ old('subcategory_id') }}">
                                <option value="">Pilih subcategory</option>
                            </select>
                        </label>
                        <label class="resident-filter-field">
                            <span>Priority</span>
                            <select name="priority" id="service-priority-select" required>
                                @foreach ($priorityOptions as $priority)
                                    <option value="{{ $priority }}" @selected(old('priority', ServiceRequest::PRIORITY_MEDIUM) === $priority)>{{ $priority }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="resident-filter-field">
                            <span>Source</span>
                            <select name="source">
                                @foreach (['Front Office', 'Phone Call', 'Security Desk', 'Management', 'Resident App'] as $source)
                                    <option value="{{ $source }}" @selected(old('source', 'Front Office') === $source)>{{ $source }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="resident-filter-field">
                            <span>Technician Team</span>
                            <select name="technician_team_id">
                                <option value="">Belum ditetapkan</option>
                                @foreach ($teamOptions as $team)
                                    <option value="{{ $team->id }}" @selected((string) old('technician_team_id') === (string) $team->id)>{{ $team->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="resident-filter-field">
                            <span>Scheduled At</span>
                            <input name="scheduled_at" type="datetime-local" value="{{ old('scheduled_at') ? \Illuminate\Support\Carbon::parse(old('scheduled_at'))->format('Y-m-d\\TH:i') : '' }}">
                        </label>
                        <label class="resident-filter-field">
                            <span>Request Title</span>
                            <input name="title" type="text" value="{{ old('title') }}" required>
                        </label>
                        <label class="resident-filter-field">
                            <span>Initial Status</span>
                            <select name="status">
                                @foreach ($statusOptions as $status)
                                    <option value="{{ $status }}" @selected(old('status', ServiceRequest::STATUS_SUBMITTED) === $status)>{{ $status }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                    <label class="resident-filter-field" style="margin-top:14px;">
                        <span>Description</span>
                        <textarea name="description" rows="6" placeholder="Jelaskan kebutuhan resident secara ringkas dan operasional." required>{{ old('description') }}</textarea>
                    </label>

                    <div class="visitor-panel" style="margin-top:16px;border-style:dashed;">
                        <div class="visitor-panel-head">
                            <h3 class="visitor-panel-title">Calculated SLA Preview</h3>
                            <span class="visitor-chip" id="service-sla-target">Pilih subcategory & priority</span>
                        </div>
                        <div class="visitor-panel-body">
                            <p class="muted" id="service-sla-due-copy" style="margin:0;">SLA target dan due time akan dihitung server-side saat ticket dibuat.</p>
                        </div>
                    </div>

                    <div class="visitor-form-actions">
                        <span class="visitor-chip">Operational timestamp will use created_at automatically.</span>
                        <button class="btn secondary" type="button" data-modal-open="service-export-modal">Save Draft Preview</button>
                        <button class="btn" type="submit">Submit Request</button>
                    </div>
                </form>
            </section>
        @elseif ($pageKey === 'settings')
            <section class="visitor-grid">
                <section class="visitor-panel visitor-span-9">
                    <div class="visitor-panel-head">
                        <h2 class="visitor-panel-title">Service Categories</h2>
                        <button class="btn" type="button" data-modal-open="create-category-modal">Create Category</button>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Sort</th>
                                    <th>Subcategories</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($categoryOptions as $category)
                                    <tr>
                                        <td>{{ $category->name }}</td>
                                        <td><span class="resident-status {{ $category->is_active ? 'status-approved' : 'status-expired' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span></td>
                                        <td>{{ $category->sort_order }}</td>
                                        <td>{{ $category->subcategories->count() }}</td>
                                        <td>
                                            <div class="resident-action-row">
                                                @include('partials.icon-action-button', ['icon' => 'edit', 'label' => 'Edit Category', 'modal' => 'edit-category-modal-'.$category->id])
                                                @include('partials.icon-action-button', ['icon' => 'trash', 'label' => 'Delete Category', 'modal' => 'delete-category-modal-'.$category->id, 'variant' => 'danger'])
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5">Belum ada category service request.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="visitor-panel visitor-span-9">
                    <div class="visitor-panel-head">
                        <h2 class="visitor-panel-title">Service Catalog & SLA</h2>
                        <button class="btn" type="button" data-modal-open="create-subcategory-modal">Create Subcategory</button>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Subcategory</th>
                                    <th>Status</th>
                                    <th>Low</th>
                                    <th>Medium</th>
                                    <th>High</th>
                                    <th>Emergency</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($subcategoryOptions as $subcategory)
                                    <tr>
                                        <td>{{ $subcategory->category?->name }}</td>
                                        <td>{{ $subcategory->name }}</td>
                                        <td><span class="resident-status {{ $subcategory->is_active ? 'status-approved' : 'status-expired' }}">{{ $subcategory->is_active ? 'Active' : 'Inactive' }}</span></td>
                                        <td>{{ $subcategory->low_sla_minutes }} min</td>
                                        <td>{{ $subcategory->medium_sla_minutes }} min</td>
                                        <td>{{ $subcategory->high_sla_minutes }} min</td>
                                        <td>{{ $subcategory->emergency_sla_minutes }} min</td>
                                        <td>
                                            <div class="resident-action-row">
                                                @include('partials.icon-action-button', ['icon' => 'edit', 'label' => 'Edit Subcategory', 'modal' => 'edit-subcategory-modal-'.$subcategory->id])
                                                @include('partials.icon-action-button', ['icon' => 'trash', 'label' => 'Delete Subcategory', 'modal' => 'delete-subcategory-modal-'.$subcategory->id, 'variant' => 'danger'])
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8">Belum ada subcategory service request.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </section>
        @else
            <section class="visitor-panel">
                <div class="visitor-panel-head">
                    <h2 class="visitor-panel-title">{{ $page['title'] }}</h2>
                    <form method="GET" action="{{ url()->current() }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
                        <label class="resident-filter-field"><span>Search</span><input type="search" name="search" value="{{ request('search') }}" placeholder="Search ticket, resident, subcategory"></label>
                        <label class="resident-filter-field">
                            <span>Status</span>
                            <select name="status">
                                <option value="">All Status</option>
                                @foreach ($statusOptions as $status)
                                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="resident-filter-field">
                            <span>Priority</span>
                            <select name="priority">
                                <option value="">All Priority</option>
                                @foreach ($priorityOptions as $priority)
                                    <option value="{{ $priority }}" @selected(request('priority') === $priority)>{{ $priority }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="resident-filter-field">
                            <span>Category</span>
                            <select name="category_id">
                                <option value="">All Category</option>
                                @foreach ($categoryOptions as $category)
                                    <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="resident-filter-field">
                            <span>Subcategory</span>
                            <select name="subcategory_id">
                                <option value="">All Subcategory</option>
                                @foreach ($subcategoryOptions as $subcategory)
                                    <option value="{{ $subcategory->id }}" @selected((string) request('subcategory_id') === (string) $subcategory->id)>{{ $subcategory->category?->name }} - {{ $subcategory->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="resident-filter-field">
                            <span>Technician Team</span>
                            <select name="technician_team_id">
                                <option value="">All Teams</option>
                                @foreach ($teamOptions as $team)
                                    <option value="{{ $team->id }}" @selected((string) request('technician_team_id') === (string) $team->id)>{{ $team->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="resident-filter-field">
                            <span>SLA State</span>
                            <select name="sla_state">
                                <option value="">All SLA</option>
                                <option value="over" @selected(request('sla_state') === 'over')>Over SLA</option>
                            </select>
                        </label>
                        <button class="btn secondary" type="submit">Apply</button>
                    </form>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Ticket</th>
                                <th>Resident / Unit</th>
                                <th>Category / Subcategory</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Technician Team</th>
                                <th>Assigned Snapshot</th>
                                <th>Scheduled At</th>
                                <th>Operational Timestamp</th>
                                <th>SLA Due At</th>
                                <th>SLA State</th>
                                @if ($pageKey === 'completed-requests' || $pageKey === 'service-history')
                                    <th>Completed At</th>
                                @endif
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($requests as $ticket)
                                <tr>
                                    <td>
                                        <strong>{{ $ticket->ticket_number }}</strong>
                                        <div style="color:#67758a;">{{ $ticket->title }}</div>
                                    </td>
                                    <td>{{ $ticket->resident?->name ?? 'Resident tidak tersedia' }}<br><span style="color:#67758a;">{{ $ticket->resident?->unit?->code ? 'Unit '.$ticket->resident->unit->code : '-' }}</span></td>
                                    <td>
                                        {{ $ticket->categoryMaster?->name ?? $ticket->category }}
                                        <br>
                                        <span style="color:#67758a;">{{ $ticket->subcategory?->name ?? 'Legacy ticket' }}</span>
                                    </td>
                                    <td><span class="resident-status {{ $priorityClass($ticket->priority) }}">{{ $ticket->priority }}</span></td>
                                    <td><span class="resident-status {{ $statusClass($ticket->canonicalStatus()) }}">{{ $ticket->canonicalStatus() }}</span></td>
                                    <td>{{ $ticket->technicianTeam?->name ?: 'Belum ada team' }}</td>
                                    <td>{{ $ticket->assigned_to ?: 'Belum ditugaskan' }}</td>
                                    <td>{{ $ticket->scheduled_at?->format('d M Y ? H:i') ?? '-' }}</td>
                                    <td>{{ $ticket->operationalTimestamp()?->format('d M Y ? H:i') }}</td>
                                    <td>{{ $ticket->sla_due_at?->format('d M Y ? H:i') ?? '-' }}</td>
                                    <td><span class="resident-status {{ $ticket->isOverSla() ? 'status-rejected' : 'status-approved' }}">{{ $ticket->slaState() }}</span></td>
                                    @if ($pageKey === 'completed-requests' || $pageKey === 'service-history')
                                        <td>{{ $ticket->completed_at?->format('d M Y ? H:i') ?? '-' }}</td>
                                    @endif
                                    <td>
                                        <div class="resident-action-row">
                                            @include('partials.icon-action-button', ['icon' => 'eye', 'label' => 'View Request Detail', 'modal' => 'service-detail-modal-'.$ticket->id])
                                            @include('partials.icon-action-button', ['icon' => 'edit', 'label' => 'Edit Service Request', 'modal' => 'service-edit-modal-'.$ticket->id])
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $pageKey === 'completed-requests' || $pageKey === 'service-history' ? 13 : 12 }}">Belum ada data service request.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="display:flex;justify-content:space-between;gap:14px;align-items:center;flex-wrap:wrap;margin-top:18px;">
                    <span style="color:#67758a;">Menampilkan {{ $requests->firstItem() ?? 0 }} - {{ $requests->lastItem() ?? 0 }} dari {{ $requests->total() }} request</span>
                    {{ $requests->links() }}
                </div>
            </section>

            <section class="service-dashboard-strip" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;margin-top:18px;">
                <article class="visitor-panel">
                    <div class="visitor-panel-head"><h3 class="visitor-panel-title">Assignment Visibility</h3></div>
                    <div class="visitor-panel-body">
                        <div class="visitor-info-row"><span>Submitted</span><strong>{{ $summary['submitted'] }}</strong></div>
                        <div class="visitor-info-row"><span>Assigned</span><strong>{{ $summary['assigned'] }}</strong></div>
                        <div class="visitor-info-row"><span>On The Way</span><strong>{{ $summary['on_the_way'] }}</strong></div>
                        <div class="visitor-info-row"><span>In Progress</span><strong>{{ $summary['in_progress'] }}</strong></div>
                    </div>
                </article>
                <article class="visitor-panel">
                    <div class="visitor-panel-head"><h3 class="visitor-panel-title">SLA Snapshot</h3></div>
                    <div class="visitor-panel-body">
                        <div class="visitor-info-row"><span>Over SLA</span><strong>{{ $summary['over_sla'] }}</strong></div>
                        <div class="visitor-info-row"><span>Completed Today</span><strong>{{ $summary['completed_today'] }}</strong></div>
                        <div class="visitor-info-row"><span>Priority Matrix</span><strong>Low / Medium / High / Emergency</strong></div>
                    </div>
                </article>
                <article class="visitor-panel">
                    <div class="visitor-panel-head"><h3 class="visitor-panel-title">Service Notes</h3></div>
                    <div class="visitor-panel-body">
                        <p style="margin:0;color:#67758a;">Dashboard admin dan workspace Service Request sekarang membaca SLA, assignment, dan emergency priority dari query database yang sama dengan mobile app.</p>
                    </div>
                </article>
            </section>
        @endif

        @foreach ($requestsById as $ticket)
            @php
                $residentLabel = $ticket->resident?->name ?? 'Resident tidak tersedia';
                $subcategoryLabel = $ticket->subcategory?->name ?? 'Legacy ticket';
                $attachmentSummary = $ticket->attachments->isNotEmpty()
                    ? $ticket->attachments->pluck('original_name')->implode(', ')
                    : 'Tidak ada lampiran';
            @endphp

            <div class="visitor-modal" id="service-detail-modal-{{ $ticket->id }}" aria-hidden="true">
                <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
                <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="service-detail-title-{{ $ticket->id }}">
                    <div class="visitor-modal-head">
                        <h2 class="visitor-modal-title" id="service-detail-title-{{ $ticket->id }}">Service Request Detail</h2>
                        <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
                    </div>
                    <div class="visitor-modal-body">
                        <div class="visitor-detail-top">
                            <div class="visitor-detail-avatar">SR</div>
                            <div>
                                <strong class="visitor-detail-name">{{ $ticket->ticket_number }}</strong>
                                <small class="muted">{{ $ticket->title }}</small>
                            </div>
                        </div>

                        <div class="visitor-modal-grid">
                            <div class="visitor-info-row"><span>Resident</span><strong>{{ $residentLabel }}</strong></div>
                            <div class="visitor-info-row"><span>Unit</span><strong>{{ $ticket->resident?->unit?->code ? 'Unit '.$ticket->resident->unit->code : '-' }}</strong></div>
                            <div class="visitor-info-row"><span>Category</span><strong>{{ $ticket->categoryMaster?->name ?? $ticket->category }}</strong></div>
                            <div class="visitor-info-row"><span>Subcategory</span><strong>{{ $subcategoryLabel }}</strong></div>
                            <div class="visitor-info-row"><span>Priority</span><strong>{{ $ticket->priority }}</strong></div>
                            <div class="visitor-info-row"><span>Status</span><strong>{{ $ticket->canonicalStatus() }}</strong></div>
                            <div class="visitor-info-row"><span>Technician Team</span><strong>{{ $ticket->technicianTeam?->name ?: 'Belum ada team' }}</strong></div>
                            <div class="visitor-info-row"><span>Assigned Snapshot</span><strong>{{ $ticket->assigned_to ?: 'Belum ditugaskan' }}</strong></div>
                            <div class="visitor-info-row"><span>Scheduled At</span><strong>{{ $ticket->scheduled_at?->format('d M Y - H:i') ?? '-' }}</strong></div>
                            <div class="visitor-info-row"><span>SLA Due At</span><strong>{{ $ticket->sla_due_at?->format('d M Y - H:i') ?? '-' }}</strong></div>
                            <div class="visitor-info-row"><span>SLA State</span><strong>{{ $ticket->slaState() }}</strong></div>
                            <div class="visitor-info-row"><span>Created At</span><strong>{{ $ticket->created_at?->format('d M Y - H:i') ?? '-' }}</strong></div>
                            <div class="visitor-info-row"><span>Completed At</span><strong>{{ $ticket->completed_at?->format('d M Y - H:i') ?? '-' }}</strong></div>
                            <div class="visitor-info-row"><span>Resolution Notes</span><strong>{{ $ticket->completion_notes ?: 'Belum ada catatan penyelesaian.' }}</strong></div>
                        </div>

                        <div style="display:grid;gap:18px;margin-top:18px;">
                            @foreach ([
                                'Resident Supporting' => $ticket->attachmentsByType(\App\Models\ServiceRequestAttachment::TYPE_RESIDENT_SUPPORTING),
                                'Technician Before' => $ticket->attachmentsByType(\App\Models\ServiceRequestAttachment::TYPE_TECHNICIAN_BEFORE),
                                'Technician After' => $ticket->attachmentsByType(\App\Models\ServiceRequestAttachment::TYPE_TECHNICIAN_AFTER),
                            ] as $attachmentLabel => $attachmentSet)
                                <div>
                                    <strong style="display:block;margin-bottom:10px;color:#0b2149;">{{ $attachmentLabel }}</strong>
                                    @if ($attachmentSet->isNotEmpty())
                                        <div class="service-attachment-row">
                                            @foreach ($attachmentSet as $attachment)
                                                <a href="{{ $attachment->url }}" target="_blank" rel="noreferrer" style="display:block;border:1px solid #dce4ef;border-radius:14px;overflow:hidden;background:#f8fbff;">
                                                    <img src="{{ $attachment->url }}" alt="{{ $attachment->original_name }}" style="width:100%;height:180px;object-fit:cover;">
                                                    <span style="display:block;padding:10px 12px;color:#0b2149;font-weight:600;">{{ $attachment->original_name }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="muted" style="margin:0;">Belum ada lampiran {{ strtolower($attachmentLabel) }}.</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @if ($ticket->events->isNotEmpty())
                            <div style="display:grid;gap:10px;margin-top:18px;">
                                <strong style="color:#0b2149;">Execution Timeline</strong>
                                @foreach ($ticket->events as $event)
                                    <div class="visitor-info-row">
                                        <span>{{ $event->event_type }}</span>
                                        <strong>{{ ($event->actor?->name ? $event->actor->name.' - ' : '').($event->created_at?->format('d M Y - H:i') ?? '-') }}</strong>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="muted" style="margin:18px 0 0;">Timeline event belum tersedia untuk ticket ini.</p>
                        @endif

                        <div class="visitor-form-actions">
                            <button class="btn secondary" type="button" data-modal-close>Close</button>
                            <button class="btn" type="button" data-modal-open="service-edit-modal-{{ $ticket->id }}">Edit Ticket</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="visitor-modal" id="service-edit-modal-{{ $ticket->id }}" aria-hidden="true">
                <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
                <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="service-edit-title-{{ $ticket->id }}">
                    <div class="visitor-modal-head">
                        <h2 class="visitor-modal-title" id="service-edit-title-{{ $ticket->id }}">Update Service Request</h2>
                        <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
                    </div>
                    <form class="visitor-modal-body service-edit-form" method="POST" action="{{ route('service-request.update', $ticket) }}" data-service-form>
                        @csrf
                        @method('PUT')
                        <div class="visitor-form-grid">
                            <label class="resident-filter-field">
                                <span>Resident</span>
                                <select name="resident_id" required>
                                    @foreach ($residentOptions as $resident)
                                        <option value="{{ $resident->id }}" @selected($ticket->resident_id === $resident->id)>{{ $resident->name }}{{ $resident->unit?->code ? ' - Unit '.$resident->unit->code : '' }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="resident-filter-field">
                                <span>Category</span>
                                <select name="category_id" required data-service-category>
                                    @foreach ($categoryOptions as $category)
                                        <option value="{{ $category->id }}" @selected($ticket->service_request_category_id === $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="resident-filter-field">
                                <span>Subcategory</span>
                                <select name="subcategory_id" required data-service-subcategory data-current="{{ $ticket->service_request_subcategory_id }}"></select>
                            </label>
                            <label class="resident-filter-field">
                                <span>Priority</span>
                                <select name="priority" required data-service-priority>
                                    @foreach ($priorityOptions as $priority)
                                        <option value="{{ $priority }}" @selected($ticket->priority === $priority)>{{ $priority }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="resident-filter-field">
                                <span>Status</span>
                                <select name="status" required>
                                    @foreach ($statusOptions as $status)
                                        <option value="{{ $status }}" @selected($ticket->canonicalStatus() === $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="resident-filter-field">
                                <span>Source</span>
                                <input type="text" name="source" value="{{ $ticket->source }}">
                            </label>
                            <label class="resident-filter-field">
                                <span>Technician Team</span>
                                <select name="technician_team_id">
                                    <option value="">Belum ditetapkan</option>
                                    @foreach ($teamOptions as $team)
                                        <option value="{{ $team->id }}" @selected($ticket->technician_team_id === $team->id)>{{ $team->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="resident-filter-field">
                                <span>Assigned Snapshot</span>
                                <input type="text" name="assigned_to" value="{{ $ticket->assigned_to }}">
                            </label>
                            <label class="resident-filter-field">
                                <span>Scheduled At</span>
                                <input type="datetime-local" name="scheduled_at" value="{{ $ticket->scheduled_at?->format('Y-m-d\TH:i') }}">
                            </label>
                            <label class="resident-filter-field">
                                <span>SLA Preview</span>
                                <input type="text" value="{{ $ticket->sla_target_minutes ? $ticket->sla_target_minutes.' minutes / due '.$ticket->sla_due_at?->format('d M Y H:i') : 'Belum dihitung' }}" readonly data-service-sla-readout>
                            </label>
                        </div>
                        <label class="resident-filter-field" style="margin-top:14px;"><span>Title</span><input type="text" name="title" value="{{ $ticket->title }}" required></label>
                        <label class="resident-filter-field" style="margin-top:14px;"><span>Description</span><textarea name="description" rows="4" required>{{ $ticket->description }}</textarea></label>
                        <label class="resident-filter-field" style="margin-top:14px;"><span>Completion Notes</span><textarea name="completion_notes" rows="3">{{ $ticket->completion_notes }}</textarea></label>

                        @if ($ticket->attachments->isNotEmpty())
                            <div class="service-attachment-row" style="margin-top:14px;">
                                @foreach ($ticket->attachments as $attachment)
                                    <a href="{{ $attachment->url }}" target="_blank" rel="noreferrer" style="display:block;border:1px solid #dce4ef;border-radius:14px;overflow:hidden;background:#f8fbff;">
                                        <img src="{{ $attachment->url }}" alt="{{ $attachment->original_name }}" style="width:100%;height:160px;object-fit:cover;">
                                        <span style="display:block;padding:10px 12px;color:#0b2149;font-weight:600;">{{ $attachment->original_name }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        <div class="visitor-form-actions">
                            <button class="btn secondary" type="button" data-modal-close>Batal</button>
                            <button class="btn" type="submit">Update Request</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach

        @include('partials.action-preview-modal', [
            'id' => 'service-export-modal',
            'title' => 'Export Service Data',
            'summary' => 'Dummy Export',
            'subtitle' => 'Preview ekspor untuk service request. File nyata belum dihasilkan pada tahap ini.',
            'avatar' => 'EX',
            'rows' => [
                ['Format', 'Excel / CSV preview only'],
                ['Scope', 'Filtered current workspace'],
                ['Included Fields', 'Ticket, resident, category, subcategory, SLA, timestamps'],
            ],
            'confirmLabel' => 'Close',
        ])

        <div class="visitor-modal" id="create-category-modal" aria-hidden="true">
            <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
            <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="create-category-title">
                <div class="visitor-modal-head">
                    <h2 class="visitor-modal-title" id="create-category-title">Create Service Category</h2>
                    <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
                </div>
                <form class="visitor-modal-body" method="POST" action="{{ route('service-request.settings.categories.store') }}">
                    @csrf
                    <div class="visitor-form-grid">
                        <label class="resident-filter-field"><span>Name</span><input type="text" name="name" required></label>
                        <label class="resident-filter-field"><span>Sort Order</span><input type="number" name="sort_order" min="0" value="10"></label>
                        <label class="resident-filter-field"><span>Status</span><select name="is_active"><option value="1">Active</option><option value="0">Inactive</option></select></label>
                    </div>
                    <div class="visitor-form-actions">
                        <button class="btn secondary" type="button" data-modal-close>Batal</button>
                        <button class="btn" type="submit">Save Category</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="visitor-modal" id="create-subcategory-modal" aria-hidden="true">
            <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
            <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="create-subcategory-title">
                <div class="visitor-modal-head">
                    <h2 class="visitor-modal-title" id="create-subcategory-title">Create Service Subcategory & SLA</h2>
                    <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
                </div>
                <form class="visitor-modal-body" method="POST" action="{{ route('service-request.settings.subcategories.store') }}">
                    @csrf
                    <div class="visitor-form-grid">
                        <label class="resident-filter-field"><span>Category</span><select name="service_request_category_id" required>@foreach ($categoryOptions as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select></label>
                        <label class="resident-filter-field"><span>Subcategory</span><input type="text" name="name" required></label>
                        <label class="resident-filter-field"><span>Sort Order</span><input type="number" name="sort_order" min="0" value="10"></label>
                        <label class="resident-filter-field"><span>Status</span><select name="is_active"><option value="1">Active</option><option value="0">Inactive</option></select></label>
                        <label class="resident-filter-field"><span>Low SLA (min)</span><input type="number" name="low_sla_minutes" min="1" value="360" required></label>
                        <label class="resident-filter-field"><span>Medium SLA (min)</span><input type="number" name="medium_sla_minutes" min="1" value="240" required></label>
                        <label class="resident-filter-field"><span>High SLA (min)</span><input type="number" name="high_sla_minutes" min="1" value="120" required></label>
                        <label class="resident-filter-field"><span>Emergency SLA (min)</span><input type="number" name="emergency_sla_minutes" min="1" value="60" required></label>
                    </div>
                    <div class="visitor-form-actions">
                        <button class="btn secondary" type="button" data-modal-close>Batal</button>
                        <button class="btn" type="submit">Save Subcategory</button>
                    </div>
                </form>
            </div>
        </div>

        @foreach ($categoryOptions as $category)
            <div class="visitor-modal" id="edit-category-modal-{{ $category->id }}" aria-hidden="true">
                <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
                <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="edit-category-title-{{ $category->id }}">
                    <div class="visitor-modal-head">
                        <h2 class="visitor-modal-title" id="edit-category-title-{{ $category->id }}">Edit Category</h2>
                        <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
                    </div>
                    <form class="visitor-modal-body" method="POST" action="{{ route('service-request.settings.categories.update', $category) }}">
                        @csrf
                        @method('PUT')
                        <div class="visitor-form-grid">
                            <label class="resident-filter-field"><span>Name</span><input type="text" name="name" value="{{ $category->name }}" required></label>
                            <label class="resident-filter-field"><span>Sort Order</span><input type="number" name="sort_order" min="0" value="{{ $category->sort_order }}"></label>
                            <label class="resident-filter-field"><span>Status</span><select name="is_active"><option value="1" @selected($category->is_active)>Active</option><option value="0" @selected(! $category->is_active)>Inactive</option></select></label>
                        </div>
                        <div class="visitor-form-actions">
                            <button class="btn secondary" type="button" data-modal-close>Batal</button>
                            <button class="btn" type="submit">Update Category</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="visitor-modal" id="delete-category-modal-{{ $category->id }}" aria-hidden="true">
                <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
                <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="delete-category-title-{{ $category->id }}">
                    <div class="visitor-modal-head">
                        <h2 class="visitor-modal-title" id="delete-category-title-{{ $category->id }}">Delete Category</h2>
                        <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
                    </div>
                    <form class="visitor-modal-body" method="POST" action="{{ route('service-request.settings.categories.destroy', $category) }}">
                        @csrf
                        @method('DELETE')
                        <p class="muted" style="margin:0;">Kategori <strong>{{ $category->name }}</strong> akan dihapus bila belum dipakai oleh subcategory atau ticket.</p>
                        <div class="visitor-form-actions">
                            <button class="btn secondary" type="button" data-modal-close>Batal</button>
                            <button class="btn danger" type="submit">Delete Category</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach

        @foreach ($subcategoryOptions as $subcategory)
            <div class="visitor-modal" id="edit-subcategory-modal-{{ $subcategory->id }}" aria-hidden="true">
                <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
                <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="edit-subcategory-title-{{ $subcategory->id }}">
                    <div class="visitor-modal-head">
                        <h2 class="visitor-modal-title" id="edit-subcategory-title-{{ $subcategory->id }}">Edit Subcategory & SLA</h2>
                        <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
                    </div>
                    <form class="visitor-modal-body" method="POST" action="{{ route('service-request.settings.subcategories.update', $subcategory) }}">
                        @csrf
                        @method('PUT')
                        <div class="visitor-form-grid">
                            <label class="resident-filter-field"><span>Category</span><select name="service_request_category_id" required>@foreach ($categoryOptions as $category)<option value="{{ $category->id }}" @selected($subcategory->service_request_category_id === $category->id)>{{ $category->name }}</option>@endforeach</select></label>
                            <label class="resident-filter-field"><span>Subcategory</span><input type="text" name="name" value="{{ $subcategory->name }}" required></label>
                            <label class="resident-filter-field"><span>Sort Order</span><input type="number" name="sort_order" min="0" value="{{ $subcategory->sort_order }}"></label>
                            <label class="resident-filter-field"><span>Status</span><select name="is_active"><option value="1" @selected($subcategory->is_active)>Active</option><option value="0" @selected(! $subcategory->is_active)>Inactive</option></select></label>
                            <label class="resident-filter-field"><span>Low SLA (min)</span><input type="number" name="low_sla_minutes" min="1" value="{{ $subcategory->low_sla_minutes }}" required></label>
                            <label class="resident-filter-field"><span>Medium SLA (min)</span><input type="number" name="medium_sla_minutes" min="1" value="{{ $subcategory->medium_sla_minutes }}" required></label>
                            <label class="resident-filter-field"><span>High SLA (min)</span><input type="number" name="high_sla_minutes" min="1" value="{{ $subcategory->high_sla_minutes }}" required></label>
                            <label class="resident-filter-field"><span>Emergency SLA (min)</span><input type="number" name="emergency_sla_minutes" min="1" value="{{ $subcategory->emergency_sla_minutes }}" required></label>
                        </div>
                        <div class="visitor-form-actions">
                            <button class="btn secondary" type="button" data-modal-close>Batal</button>
                            <button class="btn" type="submit">Update Subcategory</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="visitor-modal" id="delete-subcategory-modal-{{ $subcategory->id }}" aria-hidden="true">
                <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
                <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="delete-subcategory-title-{{ $subcategory->id }}">
                    <div class="visitor-modal-head">
                        <h2 class="visitor-modal-title" id="delete-subcategory-title-{{ $subcategory->id }}">Delete Subcategory</h2>
                        <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
                    </div>
                    <form class="visitor-modal-body" method="POST" action="{{ route('service-request.settings.subcategories.destroy', $subcategory) }}">
                        @csrf
                        @method('DELETE')
                        <p class="muted" style="margin:0;">Subkategori <strong>{{ $subcategory->name }}</strong> akan dihapus bila belum dipakai oleh ticket.</p>
                        <div class="visitor-form-actions">
                            <button class="btn secondary" type="button" data-modal-close>Batal</button>
                            <button class="btn danger" type="submit">Delete Subcategory</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const catalog = @json(json_decode($catalogJson, true));

            const priorityMinutes = (subcategory, priority) => {
                if (!subcategory) return null;

                return {
                    Low: subcategory.low_sla_minutes,
                    Medium: subcategory.medium_sla_minutes,
                    High: subcategory.high_sla_minutes,
                    Emergency: subcategory.emergency_sla_minutes,
                }[priority] ?? null;
            };

            const formatDuePreview = (minutes) => {
                if (!minutes) return 'SLA target dan due time akan dihitung server-side saat ticket dibuat.';

                const due = new Date(Date.now() + minutes * 60 * 1000);
                return `Target ${minutes} menit · estimasi due ${due.toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}`;
            };

            const syncForm = (form) => {
                const categorySelect = form.querySelector('[data-service-category]') || form.querySelector('#service-category-select');
                const subcategorySelect = form.querySelector('[data-service-subcategory]') || form.querySelector('#service-subcategory-select');
                const prioritySelect = form.querySelector('[data-service-priority]') || form.querySelector('#service-priority-select');
                const targetChip = form.querySelector('[data-service-sla-readout]') || document.getElementById('service-sla-target');
                const dueCopy = form.querySelector('[data-service-sla-copy]') || document.getElementById('service-sla-due-copy');

                if (!categorySelect || !subcategorySelect || !prioritySelect) {
                    return;
                }

                const populateSubcategories = () => {
                    const selectedCategory = catalog.find((item) => String(item.id) === String(categorySelect.value));
                    const currentValue = subcategorySelect.dataset.current || subcategorySelect.value;

                    subcategorySelect.innerHTML = '<option value="">Pilih subcategory</option>';

                    (selectedCategory?.subcategories ?? []).forEach((subcategory) => {
                        const option = document.createElement('option');
                        option.value = subcategory.id;
                        option.textContent = subcategory.name;
                        if (String(currentValue) === String(subcategory.id)) {
                            option.selected = true;
                        }
                        subcategorySelect.appendChild(option);
                    });

                    updateSlaPreview();
                };

                const updateSlaPreview = () => {
                    const selectedCategory = catalog.find((item) => String(item.id) === String(categorySelect.value));
                    const selectedSubcategory = (selectedCategory?.subcategories ?? []).find((item) => String(item.id) === String(subcategorySelect.value));
                    const minutes = priorityMinutes(selectedSubcategory, prioritySelect.value);
                    const copy = formatDuePreview(minutes);

                    if (targetChip) {
                        if ('value' in targetChip) {
                            targetChip.value = minutes ? `${minutes} minutes / ${prioritySelect.value}` : 'Belum dihitung';
                        } else {
                            targetChip.textContent = minutes ? `${prioritySelect.value}: ${minutes} min` : 'Pilih subcategory & priority';
                        }
                    }

                    if (dueCopy) {
                        dueCopy.textContent = copy;
                    }
                };

                categorySelect.addEventListener('change', () => {
                    subcategorySelect.dataset.current = '';
                    populateSubcategories();
                });
                subcategorySelect.addEventListener('change', updateSlaPreview);
                prioritySelect.addEventListener('change', updateSlaPreview);

                populateSubcategories();
            };

            const mainForm = document.getElementById('serviceRequestForm');
            if (mainForm) {
                syncForm(mainForm);
            }

            document.querySelectorAll('[data-service-form]').forEach(syncForm);
        });
    </script>
@endsection


