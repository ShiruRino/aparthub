@extends('layouts.app')

@php
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
        'ticket-queue' => ['label' => 'Ticket Queue', 'title' => 'Ticket Queue', 'subtitle' => 'Manage incoming resident requests, assignment status, and SLA priority in one workspace.'],
        'new-request' => ['label' => 'New Request', 'title' => 'Create New Service Request', 'subtitle' => 'Input permintaan layanan baru tanpa requested date atau timeslot manual. Timestamp operasional memakai waktu dibuat.'],
        'work-orders' => ['label' => 'Work Orders', 'title' => 'Work Orders', 'subtitle' => 'Assigned dan active work orders untuk teknisi dan vendor operasional.'],
        'technician-schedule' => ['label' => 'Technician Schedule', 'title' => 'Technician Schedule', 'subtitle' => 'Lihat distribusi assignment teknisi yang sedang aktif dan terjadwal.'],
        'work-in-progress' => ['label' => 'Work In Progress', 'title' => 'Work In Progress', 'subtitle' => 'Pantau pekerjaan aktif, emergency case, dan request yang mendekati breach.'],
        'completed-requests' => ['label' => 'Completed Requests', 'title' => 'Completed Requests', 'subtitle' => 'Daftar pekerjaan yang sudah selesai dan ringkasan penyelesaian teknis.'],
        'service-history' => ['label' => 'Service History', 'title' => 'Service Request History', 'subtitle' => 'Riwayat layanan dan tren penyelesaian request penghuni.'],
        'settings' => ['label' => 'Settings', 'title' => 'Service Request Settings', 'subtitle' => 'Konfigurasi kategori, prioritas, notifikasi, dan pengaturan SLA level operasional.'],
    ];

    $page = $pages[$pageKey] ?? $pages['ticket-queue'];

    $metrics = [
        ['label' => 'New Tickets', 'value' => $summary['new'], 'tone' => 'status-pending'],
        ['label' => 'Assigned', 'value' => $summary['assigned'], 'tone' => 'status-approved'],
        ['label' => 'In Progress', 'value' => $summary['in_progress'], 'tone' => 'status-approved'],
        ['label' => 'Completed Today', 'value' => $summary['completed_today'], 'tone' => 'status-approved'],
        ['label' => 'Over SLA', 'value' => $summary['over_sla'], 'tone' => 'status-rejected'],
        ['label' => 'Emergency', 'value' => $summary['emergency'], 'tone' => 'status-rejected'],
    ];

    $statusClass = function (string $status): string {
        return match ($status) {
            'Completed', 'Assigned', 'In Progress' => 'status-approved',
            'Pending', 'New' => 'status-pending',
            'Over SLA' => 'status-rejected',
            default => 'status-expired',
        };
    };

    $priorityClass = function (string $priority): string {
        return match ($priority) {
            'Emergency' => 'status-rejected',
            'High' => 'status-pending',
            'Medium' => 'status-expired',
            default => 'status-approved',
        };
    };

    $requestRows = $requests->map(function ($request) use ($statusClass, $priorityClass) {
        return [
            'id' => $request->id,
            'resident_id' => $request->resident_id,
            'ticket_number' => $request->ticket_number,
            'resident' => $request->resident?->name ?? 'Resident tidak tersedia',
            'unit' => $request->resident?->unit?->code ? 'Unit '.$request->resident->unit->code : '-',
            'category' => $request->category,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'priority_class' => $priorityClass($request->priority),
            'status' => $request->status,
            'status_class' => $statusClass($request->status),
            'source' => $request->source,
            'assigned_to' => $request->assigned_to ?: 'Belum ditugaskan',
            'created' => $request->created_at?->format('d M Y · H:i'),
            'completed' => $request->completed_at?->format('d M Y · H:i') ?? '-',
            'completion_notes' => $request->completion_notes ?: 'Belum ada catatan penyelesaian.',
        ];
    });
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
                            <select name="category" required>
                                @foreach ($categoryOptions as $category)
                                    <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="resident-filter-field">
                            <span>Request Title</span>
                            <input name="title" type="text" value="{{ old('title') }}" required>
                        </label>
                        <label class="resident-filter-field">
                            <span>Priority</span>
                            <select name="priority" required>
                                @foreach ($priorityOptions as $priority)
                                    <option value="{{ $priority }}" @selected(old('priority', 'Medium') === $priority)>{{ $priority }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="resident-filter-field">
                            <span>Source</span>
                            <select name="source">
                                @foreach (['Front Office', 'Mobile App', 'Phone Call', 'Security Desk', 'Management'] as $source)
                                    <option value="{{ $source }}" @selected(old('source', 'Front Office') === $source)>{{ $source }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="resident-filter-field">
                            <span>Assign To</span>
                            <input name="assigned_to" type="text" value="{{ old('assigned_to') }}" placeholder="Opsional">
                        </label>
                    </div>
                    <label class="resident-filter-field" style="margin-top:14px;">
                        <span>Description</span>
                        <textarea name="description" rows="6" placeholder="Jelaskan kebutuhan resident secara ringkas dan operasional.">{{ old('description') }}</textarea>
                    </label>

                    <div class="visitor-form-actions">
                        <span class="visitor-chip">Operational timestamp will use created_at automatically.</span>
                        <button class="btn secondary" type="button" data-modal-open="service-export-modal">Save Draft Preview</button>
                        <button class="btn" type="submit">Submit Request</button>
                    </div>
                </form>
            </section>
        @else
            <section class="visitor-panel">
                <div class="visitor-panel-head">
                    <h2 class="visitor-panel-title">{{ $page['title'] }}</h2>
                    <form method="GET" action="{{ url()->current() }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
                        <label class="resident-filter-field"><span>Search</span><input type="search" name="search" value="{{ request('search') }}" placeholder="Search ticket, resident, category"></label>
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
                            <select name="category">
                                <option value="">All Category</option>
                                @foreach ($categoryOptions as $category)
                                    <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                                @endforeach
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
                                <th>Category</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Operational Timestamp</th>
                                @if ($pageKey === 'completed-requests')
                                    <th>Completed At</th>
                                @endif
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($requestRows as $row)
                                <tr>
                                    <td>
                                        <strong>{{ $row['ticket_number'] }}</strong>
                                        <div style="color:#67758a;">{{ $row['title'] }}</div>
                                    </td>
                                    <td>{{ $row['resident'] }}<br><span style="color:#67758a;">{{ $row['unit'] }}</span></td>
                                    <td>{{ $row['category'] }}</td>
                                    <td><span class="resident-status {{ $row['priority_class'] }}">{{ $row['priority'] }}</span></td>
                                    <td><span class="resident-status {{ $row['status_class'] }}">{{ $row['status'] }}</span></td>
                                    <td>{{ $row['assigned_to'] }}</td>
                                    <td>{{ $row['created'] }}</td>
                                    @if ($pageKey === 'completed-requests')
                                        <td>{{ $row['completed'] }}</td>
                                    @endif
                                    <td>
                                        <div class="resident-action-row">
                                            @include('partials.icon-action-button', ['icon' => 'eye', 'label' => 'View Request Detail', 'modal' => 'service-detail-modal-'.$row['id']])
                                            @include('partials.icon-action-button', ['icon' => 'edit', 'label' => 'Edit Service Request', 'modal' => 'service-edit-modal-'.$row['id']])
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $pageKey === 'completed-requests' ? 8 : 7 }}">Belum ada data service request.</td>
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
                        <div class="visitor-info-row"><span>Assigned Tickets</span><strong>{{ $summary['assigned'] }}</strong></div>
                        <div class="visitor-info-row"><span>In Progress</span><strong>{{ $summary['in_progress'] }}</strong></div>
                        <div class="visitor-info-row"><span>Emergency Open</span><strong>{{ $summary['emergency'] }}</strong></div>
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
                        <p style="margin:0;color:#67758a;">Assignment board dan SLA monitoring sekarang dikonsolidasikan ke dashboard admin dan workspace service utama, jadi operator tetap dapat satu alur kerja yang lebih ringkas.</p>
                    </div>
                </article>
            </section>
        @endif

        @foreach ($requestRows as $row)
            @include('partials.action-preview-modal', [
                'id' => 'service-detail-modal-'.$row['id'],
                'title' => 'Service Request Detail',
                'summary' => $row['ticket_number'],
                'subtitle' => $row['title'],
                'avatar' => 'SR',
                'rows' => [
                    ['Resident', $row['resident']],
                    ['Unit', $row['unit']],
                    ['Category', $row['category']],
                    ['Priority', $row['priority']],
                    ['Status', $row['status']],
                    ['Assigned To', $row['assigned_to']],
                    ['Created At', $row['created']],
                    ['Completed At', $row['completed']],
                    ['Resolution Notes', $row['completion_notes']],
                ],
                'confirmLabel' => 'Close',
            ])

            <div class="visitor-modal" id="service-edit-modal-{{ $row['id'] }}" aria-hidden="true">
                <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
                <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="service-edit-title-{{ $row['id'] }}">
                    <div class="visitor-modal-head">
                        <h2 class="visitor-modal-title" id="service-edit-title-{{ $row['id'] }}">Update Service Request</h2>
                        <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
                    </div>
                    <form class="visitor-modal-body" method="POST" action="{{ route('service-request.update', $row['id']) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="resident_id" value="{{ $row['resident_id'] }}">
                        <div class="visitor-form-grid">
                            <label class="resident-filter-field"><span>Category</span><input type="text" name="category" value="{{ $row['category'] }}" required></label>
                            <label class="resident-filter-field"><span>Title</span><input type="text" name="title" value="{{ $row['title'] }}" required></label>
                            <label class="resident-filter-field">
                                <span>Priority</span>
                                <select name="priority" required>
                                    @foreach ($priorityOptions as $priority)
                                        <option value="{{ $priority }}" @selected($row['priority'] === $priority)>{{ $priority }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="resident-filter-field">
                                <span>Status</span>
                                <select name="status" required>
                                    @foreach ($statusOptions as $status)
                                        <option value="{{ $status }}" @selected($row['status'] === $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="resident-filter-field"><span>Source</span><input type="text" name="source" value="{{ $row['source'] }}"></label>
                            <label class="resident-filter-field"><span>Assigned To</span><input type="text" name="assigned_to" value="{{ $row['assigned_to'] === 'Belum ditugaskan' ? '' : $row['assigned_to'] }}"></label>
                        </div>
                        <label class="resident-filter-field" style="margin-top:14px;"><span>Description</span><textarea name="description" rows="4">{{ $row['description'] }}</textarea></label>
                        <label class="resident-filter-field" style="margin-top:14px;"><span>Completion Notes</span><textarea name="completion_notes" rows="3">{{ $row['completion_notes'] === 'Belum ada catatan penyelesaian.' ? '' : $row['completion_notes'] }}</textarea></label>
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
                ['Included Fields', 'Ticket, resident, status, priority, technician, timestamps'],
            ],
            'confirmLabel' => 'Close',
        ])
    </div>
@endsection
