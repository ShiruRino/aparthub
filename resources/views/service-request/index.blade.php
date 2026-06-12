@extends('layouts.app')

@php
    $pageKey = $pageKey ?? 'ticket-queue';

    $navTabs = [
        ['label' => 'Ticket Queue', 'route' => 'service-request.ticket-queue', 'active' => ['service-request.index', 'service-request.ticket-queue']],
        ['label' => 'New Request', 'route' => 'service-request.new-request', 'active' => ['service-request.new-request']],
        ['label' => 'Assignment Board', 'route' => 'service-request.assignment-board', 'active' => ['service-request.assignment-board']],
        ['label' => 'Work Orders', 'route' => 'service-request.work-orders', 'active' => ['service-request.work-orders']],
        ['label' => 'Technician Schedule', 'route' => 'service-request.technician-schedule', 'active' => ['service-request.technician-schedule']],
        ['label' => 'Work In Progress', 'route' => 'service-request.work-in-progress', 'active' => ['service-request.work-in-progress']],
        ['label' => 'Completed Requests', 'route' => 'service-request.completed-requests', 'active' => ['service-request.completed-requests']],
        ['label' => 'SLA Monitoring', 'route' => 'service-request.sla-monitoring', 'active' => ['service-request.sla-monitoring']],
        ['label' => 'Service History', 'route' => 'service-request.service-history', 'active' => ['service-request.service-history']],
        ['label' => 'Settings', 'route' => 'service-request.settings', 'active' => ['service-request.settings']],
    ];

    $pages = [
        'overview' => [
            'label' => 'Overview',
            'title' => 'Service Request Hub',
            'subtitle' => 'Arahkan operator ke queue, assignment, execution, dan monitoring service request dari satu landing workspace.',
            'metrics' => [],
        ],
        'ticket-queue' => [
            'label' => 'Ticket Queue',
            'title' => 'Ticket Queue',
            'subtitle' => 'Manage incoming resident requests, assignment status, and SLA priority.',
            'metrics' => [
                ['label' => 'New Tickets', 'value' => '18', 'icon' => 'N'],
                ['label' => 'Assigned', 'value' => '24', 'icon' => 'A'],
                ['label' => 'In Progress', 'value' => '16', 'icon' => 'P'],
                ['label' => 'Pending', 'value' => '7', 'icon' => 'W'],
                ['label' => 'Completed Today', 'value' => '32', 'icon' => 'C'],
                ['label' => 'Over SLA', 'value' => '5', 'icon' => '!'],
            ],
        ],
        'new-request' => [
            'label' => 'New Request',
            'title' => 'Create New Service Request',
            'subtitle' => 'Create a static front-office request record for the operations center preview.',
            'metrics' => [],
        ],
        'assignment-board' => [
            'label' => 'Assignment Board',
            'title' => 'Ticket Assignment Board',
            'subtitle' => 'Assign waiting tickets to available technicians using a daily board view.',
            'metrics' => [],
        ],
        'work-orders' => [
            'label' => 'Work Orders',
            'title' => 'Work Orders',
            'subtitle' => 'Track assigned work orders, scheduled time, materials, and start actions.',
            'metrics' => [],
        ],
        'technician-schedule' => [
            'label' => 'Technician Schedule',
            'title' => 'Technician Schedule',
            'subtitle' => 'Daily view of technician jobs, schedule blocks, and active assignment details.',
            'metrics' => [
                ['label' => 'New Tickets', 'value' => '18', 'icon' => 'N'],
                ['label' => 'Pending Assignments', 'value' => '2', 'icon' => 'A'],
                ['label' => 'In Progress Tasks', 'value' => '16', 'icon' => 'P'],
                ['label' => 'Completed Today', 'value' => '2', 'icon' => 'C'],
                ['label' => 'Delayed Tasks', 'value' => '5', 'icon' => '!'],
            ],
        ],
        'work-in-progress' => [
            'label' => 'Work In Progress',
            'title' => 'Active Work In Progress',
            'subtitle' => 'Live status for active service work and the latest technician activity log.',
            'metrics' => [
                ['label' => 'New Tickets', 'value' => '15', 'icon' => 'N'],
                ['label' => 'Pending Assignments', 'value' => '1', 'icon' => 'A'],
                ['label' => 'In Progress Tasks', 'value' => '18', 'icon' => 'P'],
                ['label' => 'Completed Today', 'value' => '2', 'icon' => 'C'],
                ['label' => 'Delayed Tasks', 'value' => '5', 'icon' => '!'],
            ],
        ],
        'completed-requests' => [
            'label' => 'Completed Requests',
            'title' => 'Recently Completed Requests',
            'subtitle' => 'Completed tasks, resident sign-off, and feedback queue.',
            'metrics' => [
                ['label' => 'New Tickets', 'value' => '14', 'icon' => 'N'],
                ['label' => 'Pending Assignments', 'value' => '0', 'icon' => 'A'],
                ['label' => 'In Progress Tasks', 'value' => '12', 'icon' => 'P'],
                ['label' => 'Completed Today', 'value' => '8', 'icon' => 'C'],
                ['label' => 'Delayed Tasks', 'value' => '5', 'icon' => '!'],
            ],
        ],
        'sla-monitoring' => [
            'label' => 'SLA Monitoring',
            'title' => 'SLA Performance Dashboard',
            'subtitle' => 'Detailed performance and compliance metrics for all service requests as of 11:30 AM.',
            'metrics' => [
                ['label' => 'New Tickets', 'value' => '14', 'icon' => 'N'],
                ['label' => 'Pending Assignments', 'value' => '0', 'icon' => 'A'],
                ['label' => 'In Progress Tasks', 'value' => '12', 'icon' => 'P'],
                ['label' => 'Completed Today', 'value' => '8', 'icon' => 'C'],
                ['label' => 'Delayed Tasks', 'value' => '5', 'icon' => '!'],
            ],
        ],
        'service-history' => [
            'label' => 'Service History',
            'title' => 'Service Request History Log',
            'subtitle' => 'Detailed performance and compliance metrics for all service requests as of 11:30 AM.',
            'metrics' => [
                ['label' => 'Historical Requests YTD', 'value' => '1,250', 'icon' => 'H'],
                ['label' => 'Avg. Complete Resolution', 'value' => '2.8 hr', 'icon' => 'R'],
                ['label' => 'Peak Category', 'value' => 'Plumbing', 'icon' => 'P'],
                ['label' => 'Resident Satisfaction', 'value' => '4.7/5', 'icon' => 'S'],
                ['label' => 'Maintenance Cost YTD', 'value' => '$1,020.0', 'icon' => '$'],
            ],
        ],
        'settings' => [
            'label' => 'Settings',
            'title' => 'Suite Settings Configuration',
            'subtitle' => 'Detailed performance and compliance metrics for suite settings.',
            'metrics' => [
                ['label' => 'Total Users Defined', 'value' => '35', 'icon' => 'U'],
                ['label' => 'Service Categories', 'value' => '10', 'icon' => 'C'],
                ['label' => 'Defined SLA Standards', 'value' => '4', 'icon' => 'S'],
                ['label' => 'Active Technicians', 'value' => '12', 'icon' => 'T'],
                ['label' => 'Vendor Profiles', 'value' => '6', 'icon' => 'V'],
            ],
        ],
    ];

    $page = $pages[$pageKey] ?? $pages['ticket-queue'];

    $hubGroups = [
        [
            'title' => 'Intake & Dispatch',
            'summary' => 'Mulai dari ticket intake lalu teruskan ke assignment board untuk distribusi kerja.',
            'links' => [
                ['title' => 'Ticket Queue', 'route' => 'service-request.ticket-queue', 'count' => '18 ticket baru', 'meta' => 'Incoming queue & source tracking'],
                ['title' => 'New Request', 'route' => 'service-request.new-request', 'count' => 'Front office intake', 'meta' => 'Manual request entry form'],
                ['title' => 'Assignment Board', 'route' => 'service-request.assignment-board', 'count' => '24 assignment aktif', 'meta' => 'Dispatcher board & schedule fit'],
            ],
        ],
        [
            'title' => 'Execution Control',
            'summary' => 'Pantau pelaksanaan, work order, dan jadwal teknisi dari satu kelompok operasional.',
            'links' => [
                ['title' => 'Work Orders', 'route' => 'service-request.work-orders', 'count' => '12 work order siap jalan', 'meta' => 'Assigned jobs & materials'],
                ['title' => 'Technician Schedule', 'route' => 'service-request.technician-schedule', 'count' => '16 task terjadwal', 'meta' => 'Daily timeline & load view'],
                ['title' => 'Work In Progress', 'route' => 'service-request.work-in-progress', 'count' => '18 pekerjaan aktif', 'meta' => 'Live execution & activity log'],
            ],
        ],
        [
            'title' => 'Quality & Monitoring',
            'summary' => 'Lihat hasil akhir, kepatuhan SLA, histori request, dan konfigurasi suite.',
            'links' => [
                ['title' => 'Completed Requests', 'route' => 'service-request.completed-requests', 'count' => '8 selesai hari ini', 'meta' => 'Sign-off & resident feedback'],
                ['title' => 'SLA Monitoring', 'route' => 'service-request.sla-monitoring', 'count' => '5 delayed task', 'meta' => 'Breach, compliance, and resolution'],
                ['title' => 'Service History', 'route' => 'service-request.service-history', 'count' => '1,250 history YTD', 'meta' => 'Historical analysis & trends'],
                ['title' => 'Settings', 'route' => 'service-request.settings', 'count' => '10 kategori layanan', 'meta' => 'Technician, vendor, SLA, and notifications'],
            ],
        ],
    ];

    $hubHighlights = [
        ['title' => 'Fast Dispatch', 'copy' => 'Queue dan assignment dipusatkan agar supervisor cepat ambil tindakan.', 'tone' => 'status-approved'],
        ['title' => 'Execution Visibility', 'copy' => 'Work order, schedule, dan progress aktif tetap terhubung.', 'tone' => 'status-pending'],
        ['title' => 'SLA Control', 'copy' => 'Monitoring breach dan completed requests tetap gampang dijangkau.', 'tone' => 'status-rejected'],
        ['title' => 'Cleaner Navigation', 'copy' => 'Sidebar cukup satu entry, detail akses pindah ke hub service.', 'tone' => 'status-expired'],
    ];

    $ticketRows = [
        ['ticket' => 'SR-2026-001', 'resident' => 'Ahmad Rizky', 'unit' => 'A-1808', 'category' => 'Plumbing', 'priority' => 'High', 'status' => 'New', 'source' => 'Mobile App', 'assigned' => '-', 'created' => '07 Jun 2026 - 09:15 AM', 'badge' => 'status-pending'],
        ['ticket' => 'SR-2026-002', 'resident' => 'Sarah Lim', 'unit' => 'B-1205', 'category' => 'AC', 'priority' => 'Medium', 'status' => 'Assigned', 'source' => 'Mobile App', 'assigned' => 'John Technical', 'created' => '07 Jun 2026 - 09:30 AM', 'badge' => 'status-approved'],
        ['ticket' => 'SR-2026-003', 'resident' => 'David Lee', 'unit' => 'C-2501', 'category' => 'Electrical', 'priority' => 'High', 'status' => 'In Progress', 'source' => 'Phone Call', 'assigned' => 'Michael Eng.', 'created' => '07 Jun 2026 - 09:45 AM', 'badge' => 'status-approved'],
        ['ticket' => 'SR-2026-004', 'resident' => 'Nina Putri', 'unit' => 'A-1002', 'category' => 'Housekeeping', 'priority' => 'Low', 'status' => 'Pending', 'source' => 'Mobile App', 'assigned' => 'Housekeeping Team', 'created' => '07 Jun 2026 - 10:05 AM', 'badge' => 'status-expired'],
        ['ticket' => 'SR-2026-005', 'resident' => 'Budi Santoso', 'unit' => 'B-2008', 'category' => 'Plumbing', 'priority' => 'Medium', 'status' => 'In Progress', 'source' => 'Front Office', 'assigned' => 'John Technical', 'created' => '07 Jun 2026 - 10:20 AM', 'badge' => 'status-approved'],
        ['ticket' => 'SR-2026-006', 'resident' => 'Kevin Hartono', 'unit' => 'A-1903', 'category' => 'Internet', 'priority' => 'Low', 'status' => 'Assigned', 'source' => 'Email', 'assigned' => 'Vendor ISP', 'created' => '07 Jun 2026 - 10:45 AM', 'badge' => 'status-approved'],
    ];

    $workOrderRows = [
        ['work' => 'WO-2026-001', 'ticket' => 'SR-2026-001', 'resident' => 'Alex Wong', 'unit' => 'A-1102', 'description' => 'Plumbing - Faucet Leaking', 'category' => 'Plumbing', 'priority' => 'High', 'tech' => 'Michael', 'scheduled' => '07 Jun 10:00 - 12:00', 'due' => '07 Jun 17:00', 'status' => 'Assigned'],
        ['work' => 'WO-2026-002', 'ticket' => 'SR-2026-002', 'resident' => 'John Lee', 'unit' => 'B-1205', 'description' => 'AC Compressor Repair', 'category' => 'AC', 'priority' => 'High', 'tech' => 'John Technical', 'scheduled' => '07 Jun 11:00 - 13:00', 'due' => '07 Jun 17:00', 'status' => 'Assigned'],
        ['work' => 'WO-2026-003', 'ticket' => 'SR-2026-003', 'resident' => 'Sarah Lim', 'unit' => 'C-2501', 'description' => 'Electrical Outlet Check', 'category' => 'Electrical', 'priority' => 'Medium', 'tech' => 'Michael Eng.', 'scheduled' => '07 Jun 12:30 - 13:30', 'due' => '07 Jun 18:00', 'status' => 'Assigned'],
        ['work' => 'WO-2026-004', 'ticket' => 'SR-2026-004', 'resident' => 'Nina Putri', 'unit' => 'A-1002', 'description' => 'Bathroom Cleanup', 'category' => 'Housekeeping', 'priority' => 'Low', 'tech' => 'Housekeeping', 'scheduled' => '07 Jun 15:00 - 16:00', 'due' => '08 Jun 12:00', 'status' => 'Assigned'],
    ];

    $progressRows = [
        ['ticket' => 'SR-2026-002', 'description' => 'AC Service', 'location' => 'Unit B-1205', 'tech' => 'John Technical', 'start' => '10:00 AM', 'phase' => 'Active Fixing', 'timer' => '1h 15m left'],
        ['ticket' => 'SR-2026-003', 'description' => 'Electrical Check', 'location' => 'Unit C-2501', 'tech' => 'Michael Eng.', 'start' => '10:00 AM', 'phase' => 'Active Inspecting', 'timer' => '45m left'],
        ['ticket' => 'SR-2026-004', 'description' => 'AC Service', 'location' => 'Unit 8-1205', 'tech' => 'Michael Eng.', 'start' => '10:00 AM', 'phase' => 'Active Fixing', 'timer' => '45m left'],
        ['ticket' => 'SR-2026-005', 'description' => 'Plumbing Fix', 'location' => 'Unit B-2008', 'tech' => 'John Technical', 'start' => '10:00 AM', 'phase' => 'Active Fixing', 'timer' => '45m left'],
        ['ticket' => 'SR-2026-006', 'description' => 'Electrical Check', 'location' => 'Unit C-2501', 'tech' => 'Housekeeping', 'start' => '10:00 AM', 'phase' => 'Active Fixing', 'timer' => '45m left'],
    ];

    $completedRows = [
        ['ticket' => 'SR-2026-013', 'description' => 'AC Repair', 'location' => 'Unit B-1205', 'completedBy' => 'John Technical', 'time' => '11:30 AM', 'status' => 'Signed Off', 'feedback' => '4.5 Stars'],
        ['ticket' => 'SR-2026-014', 'description' => 'Electrical Outlet', 'location' => 'Unit C-2501', 'completedBy' => 'Michael Eng.', 'time' => '11:45 AM', 'status' => 'Signed Off', 'feedback' => '5 Stars'],
        ['ticket' => 'SR-2026-015', 'description' => 'Leaking Pipe', 'location' => 'Unit 8-1205', 'completedBy' => 'John Technical', 'time' => '12:15 PM', 'status' => 'Completed', 'feedback' => 'Feedback Pending'],
        ['ticket' => 'SR-2026-016', 'description' => 'Light Fixture', 'location' => 'Unit C-2501', 'completedBy' => 'Michael Eng.', 'time' => '12:30 PM', 'status' => 'Completed', 'feedback' => 'Feedback Pending'],
    ];

    $historyRows = [
        ['ticket' => 'SR-2026-001', 'category' => 'AC Service', 'priority' => 'High', 'resident' => 'John Hennen', 'unit' => 'Unit 1', 'issue' => 'Water issue reported.', 'completion' => '07/07/2026', 'time' => '2.1 hrs', 'resolution' => 'Completed', 'score' => '75%'],
        ['ticket' => 'SR-2025-XXX', 'category' => 'Plumbing', 'priority' => 'Medium', 'resident' => 'John Lee', 'unit' => 'Unit 2', 'issue' => 'Pipa plumbing fixture.', 'completion' => '07/07/2026', 'time' => '2.8 hrs', 'resolution' => 'Completed', 'score' => '97.9%'],
        ['ticket' => 'SR-2025-XX2', 'category' => 'Electrical', 'priority' => 'High', 'resident' => 'Michael Eng.', 'unit' => 'Unit 3', 'issue' => 'System tested at replaced.', 'completion' => '07/07/2026', 'time' => '1.5 hrs', 'resolution' => 'Completed', 'score' => '96%'],
        ['ticket' => 'SR-2025-XX3', 'category' => 'Plumbing', 'priority' => 'High', 'resident' => 'Michael Eng.', 'unit' => 'Unit 4', 'issue' => 'Coreon insulation needed.', 'completion' => '07/07/2026', 'time' => '1.8 hrs', 'resolution' => 'Completed', 'score' => '95.5%'],
        ['ticket' => 'SR-2025-XX5', 'category' => 'General', 'priority' => 'Low', 'resident' => 'Housekeeping', 'unit' => 'Unit 5', 'issue' => 'Water issue reported.', 'completion' => '07/07/2026', 'time' => '2.8 hrs', 'resolution' => 'Completed', 'score' => '98%'],
    ];

    $slaRows = [
        ['category' => 'AC Service', 'priority' => 'High', 'total' => 10, 'completed' => 8, 'met' => 7, 'missed' => 1, 'breach' => '12.5%', 'resolution' => '2.1 hrs'],
        ['category' => 'Plumbing', 'priority' => 'Medium', 'total' => 8, 'completed' => 6, 'met' => 5, 'missed' => 1, 'breach' => '16.7%', 'resolution' => '1.8 hrs'],
        ['category' => 'Electrical', 'priority' => 'High', 'total' => 6, 'completed' => 5, 'met' => 5, 'missed' => 0, 'breach' => '0%', 'resolution' => '1.5 hrs'],
        ['category' => 'Housekeeping', 'priority' => 'Low', 'total' => 15, 'completed' => 10, 'met' => 10, 'missed' => 0, 'breach' => '0%', 'resolution' => '0.8 hrs'],
        ['category' => 'Internet', 'priority' => 'Low', 'total' => 4, 'completed' => 3, 'met' => 2, 'missed' => 1, 'breach' => '33.3%', 'resolution' => '3.2 hrs'],
    ];
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
                @if ($pageKey === 'overview')
                    <a class="btn secondary" href="{{ route('service-request.sla-monitoring') }}">Open SLA Monitoring</a>
                    <a class="btn" href="{{ route('service-request.new-request') }}">Create New Request</a>
                @elseif ($pageKey !== 'new-request')
                    <button class="btn secondary" type="button" data-modal-open="service-action-modal">Download History</button>
                    <a class="btn" href="{{ route('service-request.new-request') }}">Create New Request</a>
                @else
                    <button class="btn secondary" type="button" data-modal-open="service-request-modal">Save Draft</button>
                    <button class="btn" type="button" data-modal-open="service-request-modal">Submit Request</button>
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

        @if ($pageKey === 'overview')
            <div class="service-overview">
                <section class="service-overview-hero">
                    <div class="service-overview-summary">
                        <h2>Service Operations Hub</h2>
                        <p>Satu pintu untuk intake, dispatch, eksekusi teknisi, sampai monitoring SLA. Detail workspace tetap ada, tapi navigasi utamanya sekarang lebih rapi.</p>
                        <div class="service-overview-stats">
                            <div class="service-overview-stat">
                                <span>New Tickets</span>
                                <strong>18</strong>
                            </div>
                            <div class="service-overview-stat">
                                <span>Assigned</span>
                                <strong>24</strong>
                            </div>
                            <div class="service-overview-stat">
                                <span>In Progress</span>
                                <strong>16</strong>
                            </div>
                            <div class="service-overview-stat">
                                <span>Over SLA</span>
                                <strong>5</strong>
                            </div>
                        </div>
                    </div>
                    <div class="service-overview-highlights">
                        <h3>Daily Focus</h3>
                        <p>Quick highlights untuk supervisor operasional sebelum masuk ke workspace detail.</p>
                        <div class="service-overview-highlight-grid">
                            @foreach ($hubHighlights as $highlight)
                                <div class="service-overview-highlight">
                                    <span class="badge {{ $highlight['tone'] }}">{{ $highlight['title'] }}</span>
                                    <strong>{{ $highlight['copy'] }}</strong>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                <section class="service-overview-groups">
                    @foreach ($hubGroups as $group)
                        <article class="service-overview-card">
                            <h3>{{ $group['title'] }}</h3>
                            <p>{{ $group['summary'] }}</p>
                            <div class="service-overview-links">
                                @foreach ($group['links'] as $link)
                                    <div class="service-overview-link">
                                        <div>
                                            <strong>{{ $link['title'] }}</strong>
                                            <span>{{ $link['count'] }} | {{ $link['meta'] }}</span>
                                        </div>
                                        <a class="service-overview-cta" href="{{ route($link['route']) }}">Open Page</a>
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </section>
            </div>
        @elseif (! empty($page['metrics']))
            <section class="service-metrics" aria-label="Service request metrics">
                @foreach ($page['metrics'] as $metric)
                    <div class="service-metric">
                        <span class="service-metric-icon">{{ $metric['icon'] }}</span>
                        <div>
                            <span>{{ $metric['label'] }}</span>
                            <strong>{{ $metric['value'] }}</strong>
                        </div>
                    </div>
                @endforeach
            </section>
        @endif

        @if ($pageKey === 'ticket-queue')
            <div class="visitor-grid">
                <section class="visitor-panel visitor-span-9">
                    <div class="visitor-panel-head">
                        <h2 class="visitor-panel-title">Ticket Queue</h2>
                        <span class="badge">Showing 1-6 of 42 entries</span>
                    </div>
                    <div class="visitor-table-filters">
                        <select><option>All Status</option></select>
                        <select><option>All Category</option></select>
                        <select><option>All Priority</option></select>
                        <input type="text" value="07 Jun 2026 - 07 Jun 2026">
                        <input type="search" placeholder="Search ticket, resident, unit...">
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Ticket No.</th>
                                    <th>Resident / Unit</th>
                                    <th>Category</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Source</th>
                                    <th>Assigned To</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ticketRows as $row)
                                    <tr>
                                        <td><strong>{{ $row['ticket'] }}</strong></td>
                                        <td>{{ $row['resident'] }}<br><span class="muted">{{ $row['unit'] }}</span></td>
                                        <td>{{ $row['category'] }}</td>
                                        <td>{{ $row['priority'] }}</td>
                                        <td><span class="badge {{ $row['badge'] }}">{{ $row['status'] }}</span></td>
                                        <td>{{ $row['source'] }}</td>
                                        <td>{{ $row['assigned'] }}</td>
                                        <td>{{ $row['created'] }}</td>
                                        <td>
                                            <div class="visitor-action-buttons">
                                                @include('partials.icon-action-button', ['label' => 'View Ticket Detail', 'icon' => 'eye', 'modal' => 'service-ticket-modal'])
                                                @include('partials.icon-action-button', ['label' => 'Assign Ticket', 'icon' => 'access', 'modal' => 'service-ticket-modal', 'variant' => 'info'])
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination muted">Showing 1 to 6 of 42 entries</div>
                </section>
            </div>

            @include('service-request.partials.widgets')
        @elseif ($pageKey === 'new-request')
            <div class="visitor-grid">
                <section class="visitor-panel visitor-span-9">
                    <div class="visitor-panel-head">
                        <h2 class="visitor-panel-title">Reporter Information</h2>
                    </div>
                    <div class="visitor-panel-body">
                        <form class="visitor-form-grid">
                            <div class="field"><label for="resident-search">Resident Search</label><input id="resident-search" type="search" placeholder="Search or Select Resident"></div>
                            <div class="field"><label for="resident-name">Name</label><input id="resident-name" type="text" value="Ahmad Rizky"></div>
                            <div class="field"><label for="resident-unit">Unit</label><input id="resident-unit" type="text" value="A-1808"></div>
                            <div class="field"><label for="resident-contact">Contact</label><input id="resident-contact" type="text" value="0812-3456-7890"></div>
                            <div class="field full"><label for="request-title">Request Title</label><input id="request-title" type="text" value="Leaking Faucet in Main Bathroom"></div>
                            <div class="field"><label for="request-category">Category</label><select id="request-category"><option>Plumbing</option><option>AC</option><option>Electrical</option></select></div>
                            <div class="field"><label for="request-subcategory">Sub-Category</label><select id="request-subcategory"><option>Faucet Repair</option><option>Pipe Leak</option></select></div>
                            <div class="field full">
                                <label>Priority</label>
                                <div class="row"><input type="radio" name="priority"> Low <input type="radio" name="priority"> Medium <input type="radio" name="priority" checked> High</div>
                            </div>
                            <div class="field full"><label for="request-description">Description</label><textarea id="request-description">Provide specific details about your request...</textarea></div>
                            <div class="field"><label for="preferred-date">Preferred Date</label><input id="preferred-date" type="text" value="07 Jun 2026"></div>
                            <div class="field"><label for="preferred-time">Time Slot</label><select id="preferred-time"><option>10:00 - 12:00</option><option>13:00 - 15:00</option></select></div>
                            <div class="field full">
                                <label>Attachments</label>
                                <div class="service-attachment-row">
                                    <div class="service-upload-box"></div>
                                    <div class="service-upload-box"></div>
                                    <div class="service-upload-box"></div>
                                    <div class="service-upload-box"></div>
                                </div>
                            </div>
                            <div class="field full"><label for="location-detail">Location Detail</label><textarea id="location-detail">Bathroom (Master)</textarea></div>
                        </form>
                        <div class="visitor-form-actions">
                            <button class="btn secondary" type="button" data-modal-open="service-request-modal">Cancel</button>
                            <button class="btn secondary" type="button" data-modal-open="service-request-modal">Save Draft</button>
                            <button class="btn" type="button" data-modal-open="service-request-modal">Submit Request</button>
                        </div>
                    </div>
                </section>

                <aside class="visitor-panel visitor-span-3">
                    <div class="visitor-panel-head">
                        <h2 class="visitor-panel-title">Request Tips</h2>
                    </div>
                    <div class="visitor-panel-body">
                        <p class="muted">Provide specific details and upload images to help technicians resolve issues quickly.</p>
                        <div class="visitor-detail-section">
                            <h3>Checklist</h3>
                            <div class="visitor-info-row"><span>Photo</span><strong>Use clear room lighting</strong></div>
                            <div class="visitor-info-row"><span>Location</span><strong>Write tower, floor, and room</strong></div>
                            <div class="visitor-info-row"><span>Priority</span><strong>High only for urgent issues</strong></div>
                        </div>
                    </div>
                </aside>
            </div>
        @elseif ($pageKey === 'assignment-board')
            <div class="service-kanban">
                <section class="visitor-panel">
                    <div class="visitor-panel-head"><h2 class="visitor-panel-title">Tickets Awaiting Assignment</h2></div>
                    <div class="visitor-panel-body">
                        <div class="service-ticket-card">
                            <strong>SR-2026-001</strong>
                            <span>Alex Wong, Unit A-1102</span>
                            <span>Plumbing - Faucet Leaking</span>
                            <span class="badge status-rejected">High Priority</span>
                            <div class="service-photo-pair"><div class="service-detail-photo"></div><div class="service-detail-photo"></div></div>
                            <div class="visitor-form-actions"><button class="btn" type="button" data-modal-open="service-technician-modal">View Technician</button></div>
                        </div>
                    </div>
                </section>

                <section class="visitor-panel">
                    <div class="visitor-panel-head">
                        <h2 class="visitor-panel-title">Today, 07 Jun 2026</h2>
                        <span class="badge">Show time</span>
                    </div>
                    <div class="visitor-panel-body">
                        <div class="table-wrap">
                            <div class="service-timeline">
                                <div class="service-timeline-head">
                                    <span>Tech-line</span><span>08:00</span><span>09:00</span><span>10:00</span><span>11:00</span><span>12:00</span><span>13:00</span><span>14:00</span><span>15:00</span><span>16:00</span><span>17:00</span>
                                </div>
                                <div class="service-timeline-row">
                                    <span class="service-tech-name">Budi<br><small>Plumbing, Available</small></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                                    <div class="service-task-pill gold" style="left: 44%; width: 170px;">SR-2026-001<br>Requested 10:00 - 12:00</div>
                                </div>
                                <div class="service-timeline-row">
                                    <span class="service-tech-name">Michael<br><small>Plumbing, Available</small></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                                    <div class="service-task-pill" style="left: 28%; width: 190px;">Plumbing<br>Faucet leaking</div>
                                </div>
                                <div class="service-timeline-row">
                                    <span class="service-tech-name">John<br><small>AC, Busy</small></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                                    <div class="service-task-pill red" style="left: 54%; width: 160px;">John AC, Busy</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        @elseif ($pageKey === 'work-orders')
            <div class="visitor-grid">
                <section class="visitor-panel visitor-span-9">
                    <div class="visitor-panel-head"><h2 class="visitor-panel-title">Current Work Orders</h2></div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Work Order ID</th><th>Ticket No.</th><th>Resident</th><th>Unit</th><th>Description</th><th>Category</th><th>Priority</th><th>Assigned Technician</th><th>Scheduled Time</th><th>Due Date</th><th>Status</th><th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($workOrderRows as $row)
                                    <tr>
                                        <td><strong>{{ $row['work'] }}</strong></td>
                                        <td>{{ $row['ticket'] }}</td>
                                        <td>{{ $row['resident'] }}</td>
                                        <td>{{ $row['unit'] }}</td>
                                        <td>{{ $row['description'] }}</td>
                                        <td>{{ $row['category'] }}</td>
                                        <td>{{ $row['priority'] }}</td>
                                        <td>{{ $row['tech'] }}</td>
                                        <td>{{ $row['scheduled'] }}</td>
                                        <td>{{ $row['due'] }}</td>
                                        <td><span class="badge status-pending">{{ $row['status'] }}</span></td>
                                        <td>
                                            <div class="visitor-action-buttons">
                                                @include('partials.icon-action-button', ['label' => 'View Work Order Detail', 'icon' => 'eye', 'modal' => 'service-work-order-modal'])
                                                @include('partials.icon-action-button', ['label' => 'Start Work Order', 'icon' => 'check', 'modal' => 'service-work-order-modal', 'variant' => 'success'])
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

                @include('service-request.partials.detail-work-order')
            </div>
        @elseif ($pageKey === 'technician-schedule')
            <div class="visitor-grid">
                <section class="visitor-panel visitor-span-9">
                    <div class="visitor-panel-head">
                        <h2 class="visitor-panel-title">Technician Schedule - Daily View (07 Jun 2026)</h2>
                        <div class="visitor-tabs"><button class="visitor-tab active" type="button">Day</button><button class="visitor-tab" type="button">Week</button><button class="visitor-tab" type="button">Month</button></div>
                    </div>
                    <div class="visitor-panel-body">
                        <div class="table-wrap">
                            <div class="service-timeline">
                                <div class="service-timeline-head">
                                    <span>Tech-line</span><span>08:00</span><span>09:00</span><span>10:00</span><span>11:00</span><span>12:00</span><span>13:00</span><span>14:00</span><span>15:00</span><span>16:00</span><span>17:00</span>
                                </div>
                                <div class="service-timeline-row"><span class="service-tech-name">John Technical</span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><div class="service-task-pill" style="left: 24%; width: 150px;">AC Service<br>SR-2026-002</div><div class="service-task-pill navy" style="left: 64%; width: 150px;">Plumbing Fix<br>SR-2026-005</div></div>
                                <div class="service-timeline-row"><span class="service-tech-name">Michael Eng.</span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><div class="service-task-pill gold" style="left: 32%; width: 150px;">Electrical Check<br>SR-2026-005</div><div class="service-task-pill" style="left: 48%; width: 150px;">AC Service<br>SR-2026-007</div></div>
                                <div class="service-timeline-row"><span class="service-tech-name">Housekeeping Team</span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><div class="service-task-pill gold" style="left: 34%; width: 150px;">Electrical Check<br>SR-2026-003</div><div class="service-task-pill red" style="left: 58%; width: 150px;">Housebring Fix<br>SR-2026-005</div></div>
                            </div>
                        </div>
                        <div class="visitor-form-actions"><button class="btn secondary" type="button" data-modal-open="service-active-task-modal">Open Selected Task</button></div>
                    </div>
                </section>

                @include('service-request.partials.detail-active-task')
            </div>

            @include('service-request.partials.widgets')
        @elseif ($pageKey === 'work-in-progress')
            <div class="visitor-grid">
                <section class="visitor-panel visitor-span-9">
                    <div class="visitor-panel-head"><h2 class="visitor-panel-title">Active Work In Progress: Live Status (11:00 AM)</h2></div>
                    <div class="table-wrap">
                        <table>
                            <thead><tr><th>Ticket ID</th><th>Description</th><th>Location</th><th>Technician</th><th>Start Time</th><th>Current Phase</th><th>SLA Timer</th><th>Actions</th></tr></thead>
                            <tbody>
                                @foreach ($progressRows as $row)
                                    <tr>
                                        <td><strong>{{ $row['ticket'] }}</strong></td>
                                        <td>{{ $row['description'] }}</td>
                                        <td>{{ $row['location'] }}</td>
                                        <td>{{ $row['tech'] }}</td>
                                        <td>{{ $row['start'] }}</td>
                                        <td><span class="badge status-approved">{{ $row['phase'] }}</span></td>
                                        <td>{{ $row['timer'] }}</td>
                                        <td>
                                            <div class="visitor-action-buttons">
                                                @include('partials.icon-action-button', ['label' => 'View Active Task Detail', 'icon' => 'eye', 'modal' => 'service-active-task-modal'])
                                                @include('partials.icon-action-button', ['label' => 'Update Active Task', 'icon' => 'edit', 'modal' => 'service-active-task-modal', 'variant' => 'gold'])
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

                @include('service-request.partials.detail-active-task')
            </div>

            @include('service-request.partials.widgets')
        @elseif ($pageKey === 'completed-requests')
            <div class="visitor-grid">
                <section class="visitor-panel visitor-span-9">
                    <div class="visitor-panel-head"><h2 class="visitor-panel-title">Recently Completed Requests</h2></div>
                    <div class="table-wrap">
                        <table>
                            <thead><tr><th>Ticket ID</th><th>Description</th><th>Location</th><th>Completed By</th><th>Completion Time</th><th>Completion Status</th><th>Resident Feedback</th></tr></thead>
                            <tbody>
                                @foreach ($completedRows as $row)
                                    <tr>
                                        <td><strong>{{ $row['ticket'] }}</strong></td>
                                        <td>{{ $row['description'] }}</td>
                                        <td>{{ $row['location'] }}</td>
                                        <td>{{ $row['completedBy'] }}</td>
                                        <td>{{ $row['time'] }}</td>
                                        <td><span class="badge status-approved">{{ $row['status'] }}</span></td>
                                        <td>{{ $row['feedback'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

            </div>

            @include('service-request.partials.widgets')
        @elseif ($pageKey === 'sla-monitoring')
            <div class="visitor-grid">
                <section class="visitor-panel visitor-span-6">
                    <div class="visitor-panel-head"><h2 class="visitor-panel-title">Daily SLA Performance Summary</h2></div>
                    <div class="visitor-panel-body">
                        <div class="visitor-donut-wrap">
                            <div class="service-donut" data-value="94%"></div>
                            <div class="legend">
                                <div class="legend-row"><span class="dot green"></span><span>Met SLA</span><strong>94%</strong></div>
                                <div class="legend-row"><span class="dot gold"></span><span>At Risk</span><strong>4%</strong></div>
                                <div class="legend-row"><span class="dot red"></span><span>Missed SLA</span><strong>2%</strong></div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="visitor-panel visitor-span-6">
                    <div class="visitor-panel-head"><h2 class="visitor-panel-title">SLA Miss Count by Priority</h2></div>
                    <div class="visitor-panel-body">
                        <div class="service-bars">
                            <div class="service-bar danger" style="height: 130px;"></div>
                            <div class="service-bar warn" style="height: 94px;"></div>
                            <div class="service-bar" style="height: 46px;"></div>
                            <div class="service-bar" style="height: 24px;"></div>
                            <div class="service-bar" style="height: 20px;"></div>
                            <div class="service-bar" style="height: 18px;"></div>
                        </div>
                    </div>
                </section>
                <section class="visitor-panel visitor-span-9">
                    <div class="visitor-panel-head"><h2 class="visitor-panel-title">Detailed SLA Performance by Request Category & Priority</h2></div>
                    <div class="table-wrap">
                        <table>
                            <thead><tr><th>Category</th><th>Priority</th><th>Tickets Total</th><th>Tickets Completed</th><th>Met SLA</th><th>Missed SLA</th><th>Breach Rate %</th><th>Avg. Resolution Time</th></tr></thead>
                            <tbody>
                                @foreach ($slaRows as $row)
                                    <tr>
                                        <td>{{ $row['category'] }}</td><td>{{ $row['priority'] }}</td><td>{{ $row['total'] }}</td><td>{{ $row['completed'] }}</td><td>{{ $row['met'] }}</td><td>{{ $row['missed'] }}</td><td>{{ $row['breach'] }}</td><td>{{ $row['resolution'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
                <section class="visitor-panel visitor-span-3">
                    <div class="visitor-panel-head"><h2 class="visitor-panel-title">SLA Breach</h2></div>
                    <div class="visitor-panel-body">
                        <div class="visitor-info-row"><span>Highlighted Case</span><strong>SR-2026-003</strong></div>
                        <button class="btn secondary" type="button" data-modal-open="service-sla-modal">Open Breach Detail</button>
                    </div>
                </section>
            </div>

            @include('service-request.partials.widgets')
        @elseif ($pageKey === 'service-history')
            <div class="visitor-grid">
                <section class="visitor-panel visitor-span-9">
                    <div class="visitor-panel-head"><h2 class="visitor-panel-title">Complete Service History Log</h2></div>
                    <div class="visitor-table-filters">
                        <input type="text" value="Date scope of 2026">
                        <select><option>Category</option></select>
                        <select><option>Priority</option></select>
                        <select><option>Status</option></select>
                        <button class="btn secondary" type="button">Filters</button>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead><tr><th>Ticket ID</th><th>Category</th><th>Priority</th><th>Resident Name</th><th>Unit</th><th>Issue Description</th><th>Completion Date</th><th>Total Time</th><th>Resolution</th><th>Resident Satisfaction Score</th></tr></thead>
                            <tbody>
                                @foreach ($historyRows as $row)
                                    <tr>
                                        <td><strong>{{ $row['ticket'] }}</strong></td><td>{{ $row['category'] }}</td><td>{{ $row['priority'] }}</td><td>{{ $row['resident'] }}</td><td>{{ $row['unit'] }}</td><td>{{ $row['issue'] }}</td><td>{{ $row['completion'] }}</td><td>{{ $row['time'] }}</td><td>{{ $row['resolution'] }}</td><td>{{ $row['score'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="visitor-panel visitor-span-3">
                    <div class="visitor-panel-head"><h2 class="visitor-panel-title">Historical Detail</h2></div>
                    <div class="visitor-panel-body">
                        <div class="visitor-info-row"><span>Selected Ticket</span><strong>SR-2026-003</strong></div>
                        <button class="btn secondary" type="button" data-modal-open="service-history-modal">Open History Detail</button>
                    </div>
                </section>
            </div>

            <div class="service-widget-grid">
                <div class="service-widget"><h3>Annual Request Volume Trend</h3><div class="service-mini-chart"></div></div>
                <div class="service-widget"><h3>Completed Time Distribution</h3><div class="service-bars"><div class="service-bar danger" style="height:120px;"></div><div class="service-bar warn" style="height:90px;"></div><div class="service-bar" style="height:56px;"></div><div class="service-bar" style="height:32px;"></div><div class="service-bar hot" style="height:18px;"></div><div class="service-bar" style="height:10px;"></div></div></div>
                <div class="service-widget"><h3>Top Performing Technicians</h3><div class="visitor-info-row"><span>John Technical</span><strong>98.5%</strong></div><div class="visitor-info-row"><span>Michael Eng.</span><strong>97.2%</strong></div><div class="visitor-info-row"><span>Housekeeping</span><strong>96.8%</strong></div></div>
            </div>
        @elseif ($pageKey === 'settings')
            <div class="visitor-grid">
                <section class="visitor-panel visitor-span-9">
                    <div class="visitor-panel-head"><h2 class="visitor-panel-title">Suite Settings Configuration</h2></div>
                    <div class="visitor-panel-body">
                        <div class="visitor-report-grid">
                            <div class="service-widget">
                                <h3>Technician Profiles</h3>
                                <div class="visitor-list-row"><div class="visitor-avatar">J</div><strong>John Hennen</strong><span>AC Expert</span>@include('partials.icon-action-button', ['label' => 'Edit Technician Profile', 'icon' => 'edit', 'modal' => 'service-settings-modal', 'variant' => 'gold'])</div>
                                <div class="visitor-list-row"><div class="visitor-avatar">M</div><strong>Michael Eng.</strong><span>Plumbing Tech</span>@include('partials.icon-action-button', ['label' => 'Edit Technician Profile', 'icon' => 'edit', 'modal' => 'service-settings-modal', 'variant' => 'gold'])</div>
                                <div class="visitor-list-row"><div class="visitor-avatar">H</div><strong>Housekeeping</strong><span>Cleaning Team</span>@include('partials.icon-action-button', ['label' => 'Edit Technician Profile', 'icon' => 'edit', 'modal' => 'service-settings-modal', 'variant' => 'gold'])</div>
                            </div>
                            <div class="service-widget">
                                <h3>Vendor Profiles</h3>
                                <div class="visitor-info-row"><span>Maintenance Vendor</span><strong>Priority</strong></div>
                                <div class="visitor-info-row"><span>Vendor X</span><strong>Completed</strong></div>
                                <div class="visitor-info-row"><span>Vendor Y</span><strong>Completed</strong></div>
                            </div>
                            <div class="service-widget">
                                <h3>SLA Definition Matrix</h3>
                                <div class="visitor-info-row"><span>High</span><strong>20 hr / 50 hr</strong></div>
                                <div class="visitor-info-row"><span>Medium</span><strong>10 hr / 20 hr</strong></div>
                                <div class="visitor-info-row"><span>Low</span><strong>10 hr / 10 hrs</strong></div>
                            </div>
                            <div class="service-widget">
                                <h3>Service Categories</h3>
                                <div class="visitor-info-row"><span>Plumbing</span><strong>Enabled</strong></div>
                                <div class="visitor-info-row"><span>AC</span><strong>Enabled</strong></div>
                                <div class="visitor-info-row"><span>Electrical</span><strong>Enabled</strong></div>
                            </div>
                            <div class="service-widget">
                                <h3>Resident App Integration</h3>
                                <div class="visitor-info-row"><span>Photo Uploads</span><strong>On</strong></div>
                                <div class="visitor-info-row"><span>Direct Chat</span><strong>On</strong></div>
                                <div class="visitor-info-row"><span>Feedback Form</span><strong>Off</strong></div>
                            </div>
                            <div class="service-widget">
                                <h3>Notification Preferences</h3>
                                <div class="visitor-info-row"><span>Email Staff</span><strong>On</strong></div>
                                <div class="visitor-info-row"><span>In-app Staff</span><strong>On</strong></div>
                                <div class="visitor-info-row"><span>Residents</span><strong>Partial</strong></div>
                            </div>
                        </div>
                    </div>
                </section>

                <aside class="visitor-panel visitor-span-3">
                    <div class="visitor-panel-head"><h2 class="visitor-panel-title">System Audit Trail</h2></div>
                    <div class="visitor-panel-body">
                        <div class="service-audit">
                            @foreach (['Manager A updated SLA for High Priority Plumbing', 'Manager A updated SLA for AC profile', 'System added Vendor X profile', 'Manager A updated Vendor X profile', 'Manager A updated SLA for Electrical', 'System added Vendor X profile'] as $audit)
                                <div class="service-audit-row"><span>{{ $audit }}<br><small class="muted">Manager - Manager A</small></span></div>
                            @endforeach
                        </div>
                    </div>
                </aside>
            </div>
        @endif

        @include('partials.action-preview-modal', [
            'id' => 'service-ticket-modal',
            'title' => 'Ticket Detail',
            'summary' => 'SR-2026-001',
            'subtitle' => 'Ahmad Rizky - A-1808',
            'avatar' => 'TD',
            'rows' => [
                ['Category', 'Plumbing'],
                ['Priority', 'High'],
                ['Description', 'Kran air kamar mandi bocor dan tidak bisa ditutup.'],
                ['Location', 'Bathroom (Master)'],
                ['Assignment', 'John Technical / 07 Jun 2026 - 10:00 AM'],
            ],
            'confirmLabel' => 'Assign Ticket',
        ])

        @include('partials.action-preview-modal', [
            'id' => 'service-request-modal',
            'title' => 'Create New Service Request',
            'summary' => 'Front Office Intake',
            'subtitle' => 'Preview popup untuk draft dan submit request. Form tetap dummy.',
            'avatar' => 'NR',
            'rows' => [
                ['Resident', 'Ahmad Rizky / A-1808'],
                ['Category', 'Plumbing'],
                ['Priority', 'High'],
                ['Preferred Date', '07 Jun 2026'],
                ['Location', 'Bathroom (Master)'],
            ],
            'confirmLabel' => 'Submit Request',
        ])

        @include('partials.action-preview-modal', [
            'id' => 'service-technician-modal',
            'title' => 'Technician Details',
            'summary' => 'Michael',
            'subtitle' => 'Plumbing / Available',
            'avatar' => 'TE',
            'rows' => [
                ['Status', 'Green / Available'],
                ['Skill', 'Faucet Repair Certified'],
                ['Phone', '0812-3456-7890'],
            ],
            'confirmLabel' => 'Assign Technician',
        ])

        @include('partials.action-preview-modal', [
            'id' => 'service-sla-modal',
            'title' => 'SLA Breach Detail',
            'summary' => 'SR-2026-003',
            'subtitle' => 'AC Service - High Priority',
            'avatar' => 'SL',
            'rows' => [
                ['Status', 'Breached'],
                ['Delay', '45 minutes over SLA'],
                ['Reason', 'Awaiting resolution'],
            ],
            'confirmLabel' => 'Acknowledge',
        ])

        @include('partials.action-preview-modal', [
            'id' => 'service-history-modal',
            'title' => 'Historical Ticket Detail',
            'summary' => 'SR-2026-003',
            'subtitle' => 'Completed record',
            'avatar' => 'HS',
            'rows' => [
                ['Log 1', 'Water issue reported.'],
                ['Log 2', 'Pipe replaced.'],
                ['Log 3', 'System tested.'],
            ],
            'confirmLabel' => 'Close Record',
        ])

        @include('partials.action-preview-modal', [
            'id' => 'service-settings-modal',
            'title' => 'Service Settings Preview',
            'summary' => 'Technician Profile',
            'subtitle' => 'Static configuration preview.',
            'avatar' => 'ST',
            'rows' => [
                ['Module', 'Service Request'],
                ['Mode', 'Settings Preview'],
                ['Backend Save', 'Belum aktif'],
            ],
            'confirmLabel' => 'Save Preview',
        ])
    </div>
@endsection
