@extends('layouts.app')

@php
    $pageKey = $pageKey ?? 'task-assignment';

    $navTabs = [
        ['label' => 'Live Monitoring', 'route' => 'security-management.live-monitoring', 'active' => ['security-management.live-monitoring']],
        ['label' => 'Patrol Monitoring', 'route' => 'security-management.patrol-monitoring', 'active' => ['security-management.patrol-monitoring']],
        ['label' => 'Task Assignment', 'route' => 'security-management.task-assignment', 'active' => ['security-management.index', 'security-management.task-assignment']],
        ['label' => 'Security Officer', 'route' => 'security-management.officers', 'active' => ['security-management.officers']],
        ['label' => 'Schedule', 'route' => 'security-management.schedule', 'active' => ['security-management.schedule']],
        ['label' => 'Incident Management', 'route' => 'security-management.incidents', 'active' => ['security-management.incidents']],
        ['label' => 'Device Management', 'route' => 'security-management.devices', 'active' => ['security-management.devices']],
        ['label' => 'Reports & Analytics', 'route' => 'security-management.reports', 'active' => ['security-management.reports']],
        ['label' => 'Settings', 'route' => 'security-management.settings', 'active' => ['security-management.settings']],
    ];

    $pages = [
        'live-monitoring' => [
            'label' => 'Live Monitoring',
            'title' => 'Live Monitoring',
            'subtitle' => 'Pantau officer, checkpoint, dan alert keamanan secara real-time.',
        ],
        'patrol-monitoring' => [
            'label' => 'Patrol Monitoring',
            'title' => 'Patrol Monitoring',
            'subtitle' => 'Monitor rute patroli aktif, checkpoint, dan penyelesaian ronde.',
        ],
        'task-assignment' => [
            'label' => 'Task Assignment',
            'title' => 'Task Assignment',
            'subtitle' => 'Kelola dan berikan tugas patroli atau tugas khusus kepada petugas keamanan.',
        ],
        'officers' => [
            'label' => 'Security Officer',
            'title' => 'Security Officer',
            'subtitle' => 'Lihat roster petugas, availability, dan assignment aktif.',
        ],
        'schedule' => [
            'label' => 'Schedule',
            'title' => 'Security Schedule',
            'subtitle' => 'Atur dan review jadwal jaga, shift, dan task coverage.',
        ],
        'incidents' => [
            'label' => 'Incident Management',
            'title' => 'Incident Management',
            'subtitle' => 'Kelola insiden, assign follow-up, dan monitor severity level.',
        ],
        'devices' => [
            'label' => 'Device Management',
            'title' => 'Device Management',
            'subtitle' => 'Pantau kesehatan CCTV, access control, alarm, dan perangkat keamanan lain.',
        ],
        'reports' => [
            'label' => 'Reports & Analytics',
            'title' => 'Reports & Analytics',
            'subtitle' => 'Ringkasan patroli, insiden, SLA respons, dan kepatuhan checkpoint.',
        ],
        'settings' => [
            'label' => 'Settings',
            'title' => 'Security Settings',
            'subtitle' => 'Konfigurasi notifikasi, kebijakan patroli, dan rule perangkat keamanan.',
        ],
    ];

    $page = $pages[$pageKey] ?? $pages['task-assignment'];

    $metrics = [
        ['label' => 'Total Task', 'value' => '24', 'sub' => 'Semua Tugas', 'tone' => 'blue'],
        ['label' => 'Assigned', 'value' => '8', 'sub' => 'Tugas Ditugaskan', 'tone' => 'green'],
        ['label' => 'In Progress', 'value' => '6', 'sub' => 'Sedang Berjalan', 'tone' => 'blue'],
        ['label' => 'Completed', 'value' => '15', 'sub' => 'Selesai', 'tone' => 'green'],
        ['label' => 'Overdue', 'value' => '3', 'sub' => 'Terlambat', 'tone' => 'gold'],
        ['label' => 'Critical Task', 'value' => '2', 'sub' => 'Prioritas Tinggi', 'tone' => 'red'],
    ];

    $tasks = [
        ['id' => 'PAT-001', 'name' => 'Patrol Area Tower A', 'route' => 'Tower A Route', 'type' => 'Patrol Routine', 'typeClass' => 'security-chip blue', 'officer' => 'Budi Santoso', 'role' => 'Security Officer', 'date' => '20 May 2026', 'time' => '08:00 - 16:00', 'checkpoint' => '5 Checkpoint', 'priority' => 'High', 'priorityClass' => 'status-rejected', 'status' => 'Assigned', 'statusClass' => 'status-blue', 'progress' => 0, 'actions' => [['View Task Detail', 'eye', 'neutral'], ['Open Task Menu', 'document', 'neutral']]],
        ['id' => 'PAT-002', 'name' => 'Patrol Area Tower B', 'route' => 'Tower B Route', 'type' => 'Patrol Routine', 'typeClass' => 'security-chip blue', 'officer' => 'Andi Pratama', 'role' => 'Security Officer', 'date' => '20 May 2026', 'time' => '08:00 - 16:00', 'checkpoint' => '4 Checkpoint', 'priority' => 'Medium', 'priorityClass' => 'status-pending', 'status' => 'In Progress', 'statusClass' => 'status-approved', 'progress' => 65, 'actions' => [['View Live Monitor', 'eye', 'neutral'], ['Inspect Task', 'access', 'info']]],
        ['id' => 'PAT-003', 'name' => 'Patrol Parking Basement', 'route' => 'Parking Route', 'type' => 'Patrol Routine', 'typeClass' => 'security-chip blue', 'officer' => 'Rizky Maulana', 'role' => 'Security Officer', 'date' => '20 May 2026', 'time' => '16:00 - 00:00', 'checkpoint' => '6 Checkpoint', 'priority' => 'High', 'priorityClass' => 'status-rejected', 'status' => 'Scheduled', 'statusClass' => 'status-pending', 'progress' => 0, 'actions' => [['Edit Patrol Task', 'edit', 'gold'], ['Open Task Menu', 'document', 'neutral']]],
        ['id' => 'TSK-001', 'name' => 'Lift Inspection', 'route' => 'Maintenance Check', 'type' => 'Maintenance', 'typeClass' => 'security-chip purple', 'officer' => 'Ahmad Fauzi', 'role' => 'Maintenance', 'date' => '20 May 2026', 'time' => '09:00 - 10:00', 'checkpoint' => '-', 'priority' => 'Medium', 'priorityClass' => 'status-pending', 'status' => 'Completed', 'statusClass' => 'status-approved', 'progress' => 100, 'actions' => [['View Task Report', 'document', 'info'], ['Open Task Menu', 'document', 'neutral']]],
        ['id' => 'INC-001', 'name' => 'Suspicious Activity Follow Up', 'route' => 'Incident Follow Up', 'type' => 'Incident', 'typeClass' => 'security-chip red', 'officer' => 'Budi Santoso', 'role' => 'Security Officer', 'date' => '20 May 2026', 'time' => '10:00 - 12:00', 'checkpoint' => '-', 'priority' => 'High', 'priorityClass' => 'status-rejected', 'status' => 'In Progress', 'statusClass' => 'status-approved', 'progress' => 40, 'actions' => [['View Incident Detail', 'eye', 'neutral'], ['Escalate Incident', 'access', 'danger']]],
        ['id' => 'TSK-002', 'name' => 'Fire Extinguisher Check', 'route' => 'Maintenance Check', 'type' => 'Maintenance', 'typeClass' => 'security-chip purple', 'officer' => 'Dewi Anggraini', 'role' => 'Maintenance', 'date' => '21 May 2026', 'time' => '08:00 - 11:00', 'checkpoint' => '-', 'priority' => 'Low', 'priorityClass' => 'status-approved', 'status' => 'Scheduled', 'statusClass' => 'status-pending', 'progress' => 0, 'actions' => [['Edit Task Schedule', 'edit', 'gold'], ['Open Task Menu', 'document', 'neutral']]],
        ['id' => 'PAT-004', 'name' => 'Patrol Area Pool & Garden', 'route' => 'Outdoor Route', 'type' => 'Patrol Routine', 'typeClass' => 'security-chip blue', 'officer' => 'Satria Putra', 'role' => 'Security Officer', 'date' => '21 May 2026', 'time' => '08:00 - 16:00', 'checkpoint' => '4 Checkpoint', 'priority' => 'Medium', 'priorityClass' => 'status-pending', 'status' => 'Assigned', 'statusClass' => 'status-blue', 'progress' => 0, 'actions' => [['View Task Detail', 'eye', 'neutral'], ['Open Task Menu', 'document', 'neutral']]],
        ['id' => 'INC-002', 'name' => 'Door Access Issue', 'route' => 'Incident Follow Up', 'type' => 'Incident', 'typeClass' => 'security-chip red', 'officer' => 'Andi Pratama', 'role' => 'Security Officer', 'date' => '21 May 2026', 'time' => '14:00 - 15:00', 'checkpoint' => '-', 'priority' => 'High', 'priorityClass' => 'status-rejected', 'status' => 'Open', 'statusClass' => 'status-rejected', 'progress' => 0, 'actions' => [['View Incident Detail', 'eye', 'neutral'], ['Assign Follow Up', 'edit', 'gold']]],
    ];

    $lightPages = [
        'live-monitoring' => [
            'cards' => [['Active Patrols', '6'], ['Open Alerts', '4'], ['Checkpoint Missed', '2'], ['Online Officers', '14']],
            'tableTitle' => 'Live Security Feed',
            'columns' => ['Officer', 'Zone', 'Current Status', 'Updated'],
            'rows' => [
                ['Andi Pratama', 'Tower B Lobby', 'Monitoring CCTV', '2 min ago'],
                ['Budi Santoso', 'Tower A Route', 'Patrol Started', '5 min ago'],
                ['Dewi Anggraini', 'Service Corridor', 'Device Check', '7 min ago'],
            ],
        ],
        'patrol-monitoring' => [
            'cards' => [['Scheduled Routes', '12'], ['Completed Routes', '8'], ['Skipped Checkpoints', '3'], ['On Time', '92%']],
            'tableTitle' => 'Patrol Route Progress',
            'columns' => ['Route', 'Officer', 'Checkpoint Progress', 'Status'],
            'rows' => [
                ['Tower A Route', 'Budi Santoso', '5 / 5', 'Completed'],
                ['Tower B Route', 'Andi Pratama', '3 / 4', 'In Progress'],
                ['Parking Route', 'Rizky Maulana', '0 / 6', 'Scheduled'],
            ],
        ],
        'officers' => [
            'cards' => [['Total Officers', '18'], ['On Duty', '11'], ['Standby', '4'], ['Off Shift', '3']],
            'tableTitle' => 'Security Officer Roster',
            'columns' => ['Officer', 'Role', 'Shift', 'Assignment'],
            'rows' => [
                ['Budi Santoso', 'Security Officer', '08:00 - 16:00', 'Tower A Route'],
                ['Andi Pratama', 'Security Officer', '08:00 - 16:00', 'Tower B Route'],
                ['Dewi Anggraini', 'Maintenance', '08:00 - 17:00', 'Device Check'],
            ],
        ],
        'schedule' => [
            'cards' => [['Shifts Today', '4'], ['Covered Posts', '12'], ['Open Slots', '2'], ['Overtime', '1']],
            'tableTitle' => 'Duty Schedule Overview',
            'columns' => ['Shift', 'Time', 'Assigned Team', 'Coverage'],
            'rows' => [
                ['Morning', '06:00 - 14:00', 'Lobby & Gate Team', 'Full'],
                ['Day', '08:00 - 16:00', 'Patrol Team A', 'Full'],
                ['Night', '22:00 - 06:00', 'Patrol Team B', '1 slot open'],
            ],
        ],
        'incidents' => [
            'cards' => [['Open Incidents', '5'], ['Critical', '2'], ['Resolved Today', '7'], ['Escalated', '1']],
            'tableTitle' => 'Incident Queue',
            'columns' => ['Incident', 'Zone', 'Severity', 'Assigned'],
            'rows' => [
                ['Door Access Issue', 'Tower D', 'High', 'Andi Pratama'],
                ['Suspicious Activity', 'Parking Basement', 'High', 'Budi Santoso'],
                ['Alarm Fault', 'Tower C', 'Medium', 'Dewi Anggraini'],
            ],
        ],
        'devices' => [
            'cards' => [['Online Devices', '128'], ['Offline', '6'], ['Needs Service', '4'], ['Access Nodes', '32']],
            'tableTitle' => 'Security Device Status',
            'columns' => ['Device', 'Zone', 'Health', 'Last Sync'],
            'rows' => [
                ['CCTV-LBY-01', 'Main Lobby', 'Online', '1 min ago'],
                ['ACC-GATE-03', 'North Gate', 'Warning', '5 min ago'],
                ['ALM-BSM-02', 'Basement', 'Needs Service', '18 min ago'],
            ],
        ],
        'reports' => [
            'cards' => [['Patrol Compliance', '94%'], ['Avg Response', '6 min'], ['Resolved Incidents', '28'], ['Officer Utilization', '88%']],
            'tableTitle' => 'Security Performance Snapshot',
            'columns' => ['Metric', 'Current', 'Target', 'Notes'],
            'rows' => [
                ['Checkpoint Completion', '94%', '95%', '2 missed checkpoint this week'],
                ['Incident Response', '6 min', '8 min', 'Above target'],
                ['Device Uptime', '97.4%', '99%', '6 devices under maintenance'],
            ],
        ],
        'settings' => [
            'cards' => [['Patrol Rules', '12'], ['Alert Policies', '6'], ['Device Integrations', '4'], ['Shift Templates', '5']],
            'tableTitle' => 'Security Configuration',
            'columns' => ['Setting', 'Current Value', 'Type', 'Action'],
            'rows' => [
                ['Patrol Reminder', 'Enabled', 'Notification', 'Edit'],
                ['Critical Incident Escalation', '5 Minutes', 'SLA', 'Configure'],
                ['Checkpoint Validation', 'QR + NFC', 'Policy', 'Review'],
            ],
        ],
    ];
@endphp

@section('title', $page['label'])
@section('topbar_context')
    Security Management > {{ $page['label'] }}
@endsection
@section('topbar_subtitle', $page['subtitle'])

@section('content')
    <div class="security-page">
        <section class="security-hero">
            <div>
                <h2>Security Operations Center</h2>
                <p>Monitor, Manage, and Assign Security Tasks in Real-Time</p>
            </div>
        </section>

        <nav class="security-tabs" aria-label="Security management navigation">
            @foreach ($navTabs as $tab)
                <a href="{{ route($tab['route']) }}" @class(['security-tab', 'active' => request()->routeIs(...$tab['active'])])>{{ $tab['label'] }}</a>
            @endforeach
        </nav>

        @if ($pageKey === 'task-assignment')
            <section class="security-toolbar">
                <div>
                    <h3>{{ $page['title'] }}</h3>
                    <p>{{ $page['subtitle'] }}</p>
                </div>
                <div class="security-toolbar-actions">
                    <button class="btn secondary" type="button" data-modal-open="security-task-modal">Export</button>
                    <button class="btn secondary" type="button" data-modal-open="security-route-modal">Manage Route</button>
                    <button class="btn" type="button" data-modal-open="security-task-modal">Create Task</button>
                </div>
            </section>

            <section class="security-metrics" aria-label="Security metrics">
                @foreach ($metrics as $metric)
                    <article class="security-metric {{ $metric['tone'] }}">
                        <span class="security-metric-icon"></span>
                        <div>
                            <span>{{ $metric['label'] }}</span>
                            <strong>{{ $metric['value'] }}</strong>
                            <small>{{ $metric['sub'] }}</small>
                        </div>
                    </article>
                @endforeach
            </section>

            <div class="security-workspace">
                <main class="security-main">
                    <section class="visitor-panel">
                        <div class="security-filter-row">
                            <label><input type="search" placeholder="Cari tugas..."></label>
                            <select><option>Semua Status</option></select>
                            <select><option>Semua Tipe</option></select>
                            <select><option>Semua Prioritas</option></select>
                            <input type="text" value="20/05/2026" aria-label="Task date">
                            <button class="btn secondary" type="button">Filter</button>
                        </div>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Task ID</th>
                                        <th>Task Name</th>
                                        <th>Type</th>
                                        <th>Officer</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Checkpoint</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tasks as $task)
                                        <tr>
                                            <td><strong>{{ $task['id'] }}</strong></td>
                                            <td>
                                                <strong>{{ $task['name'] }}</strong>
                                                <div class="muted">{{ $task['route'] }}</div>
                                            </td>
                                            <td><span class="{{ $task['typeClass'] }}">{{ $task['type'] }}</span></td>
                                            <td>
                                                <div class="security-officer-cell">
                                                    <span class="security-officer-avatar">{{ strtoupper(substr($task['officer'], 0, 1)) }}</span>
                                                    <div>
                                                        <strong>{{ $task['officer'] }}</strong>
                                                        <div class="muted">{{ $task['role'] }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $task['date'] }}</td>
                                            <td>{{ $task['time'] }}</td>
                                            <td>{{ $task['checkpoint'] }}</td>
                                            <td><span class="badge {{ $task['priorityClass'] }}">{{ $task['priority'] }}</span></td>
                                            <td><span class="badge {{ $task['statusClass'] }}">{{ $task['status'] }}</span></td>
                                            <td>
                                                <strong>{{ $task['progress'] }}%</strong>
                                                <div class="security-progress"><span style="width: {{ $task['progress'] }}%;"></span></div>
                                            </td>
                                            <td>
                                                <div class="visitor-action-buttons">
                                                    @foreach ($task['actions'] as [$label, $icon, $variant])
                                                        @include('partials.icon-action-button', [
                                                            'label' => $label,
                                                            'icon' => $icon,
                                                            'variant' => $variant,
                                                            'modal' => 'security-task-modal',
                                                        ])
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="resident-pagination">
                            <span>Menampilkan 1 - 8 dari 24 tugas</span>
                            <span class="resident-page-btn">&lt;</span>
                            <span class="resident-page-btn active">1</span>
                            <span class="resident-page-btn">2</span>
                            <span class="resident-page-btn">3</span>
                            <span class="resident-page-btn">&gt;</span>
                        </div>
                    </section>
                </main>

                <aside class="security-side">
                    <section class="visitor-panel">
                        <div class="visitor-panel-head">
                            <h3 class="visitor-panel-title">Peta Route Patrol</h3>
                            <button class="btn secondary" type="button" data-modal-open="security-route-modal">Lihat Semua</button>
                        </div>
                        <div class="visitor-panel-body">
                            <div class="security-map-preview">
                                <span class="security-map-point p1">1</span>
                                <span class="security-map-point p2">2</span>
                                <span class="security-map-point p3">3</span>
                                <span class="security-map-point p4">4</span>
                                <span class="security-map-point p5">5</span>
                            </div>
                        </div>
                    </section>
                    <section class="visitor-panel">
                        <div class="visitor-panel-head">
                            <h3 class="visitor-panel-title">Kalender Tugas</h3>
                            <a href="#">Lihat Kalender</a>
                        </div>
                        <div class="visitor-panel-body">
                            <strong style="display:block;text-align:center;margin-bottom:8px;">May 2026</strong>
                            <div class="community-calendar-mini">
                                @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                                    <span>{{ $day }}</span>
                                @endforeach
                                @foreach (range(1, 31) as $date)
                                    <span @class(['active' => in_array($date, [12, 20, 24], true)])>{{ $date }}</span>
                                @endforeach
                            </div>
                        </div>
                    </section>
                    <section class="visitor-panel">
                        <div class="visitor-panel-head">
                            <h3 class="visitor-panel-title">Ringkasan Tugas Hari Ini</h3>
                        </div>
                        <div class="visitor-panel-body security-summary-grid">
                            <div><span>Total Tugas</span><strong>12</strong></div>
                            <div><span>Selesai</span><strong>5</strong></div>
                            <div><span>In Progress</span><strong>4</strong></div>
                            <div><span>Critical</span><strong>1</strong></div>
                        </div>
                    </section>
                </aside>
            </div>

            <section class="security-benefits">
                @foreach ([
                    ['Real-Time Assignment', 'Tugas diberikan secara real-time'],
                    ['Route & Checkpoint', 'Verifikasi checkpoint QR/NFC'],
                    ['Officer Tracking', 'Monitor petugas secara live'],
                    ['Task & Incident Integration', 'Terhubung dengan insiden & laporan'],
                    ['Data & Analytics', 'Laporan kinerja tugas & kepatuhan'],
                ] as [$title, $copy])
                    <div class="security-benefit">
                        <strong>{{ $title }}</strong>
                        <span>{{ $copy }}</span>
                    </div>
                @endforeach
            </section>
        @else
            @php($lightPage = $lightPages[$pageKey])
            <section class="security-toolbar">
                <div>
                    <h3>{{ $page['title'] }}</h3>
                    <p>{{ $page['subtitle'] }}</p>
                </div>
                <div class="security-toolbar-actions">
                    <button class="btn secondary" type="button" data-modal-open="security-task-modal">Export</button>
                    <button class="btn" type="button" data-modal-open="security-task-modal">{{ $pageKey === 'incidents' ? 'Create Incident' : 'Create Task' }}</button>
                </div>
            </section>

            <section class="security-lite-metrics">
                @foreach ($lightPage['cards'] as [$label, $value])
                    <article class="security-lite-card">
                        <span>{{ $label }}</span>
                        <strong>{{ $value }}</strong>
                    </article>
                @endforeach
            </section>

            <div class="security-workspace">
                <main class="security-main">
                    <section class="visitor-panel">
                        <div class="visitor-panel-head">
                            <h3 class="visitor-panel-title">{{ $lightPage['tableTitle'] }}</h3>
                            <button class="btn secondary" type="button" data-modal-open="security-task-modal">Open Preview</button>
                        </div>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        @foreach ($lightPage['columns'] as $column)
                                            <th>{{ $column }}</th>
                                        @endforeach
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lightPage['rows'] as $row)
                                        <tr>
                                            @foreach ($row as $value)
                                                <td>{{ $value }}</td>
                                            @endforeach
                                            <td>
                                                <div class="visitor-action-buttons">
                                                    @include('partials.icon-action-button', ['label' => 'View Security Detail', 'icon' => 'eye', 'modal' => 'security-task-modal'])
                                                    @include('partials.icon-action-button', ['label' => 'Edit Security Assignment', 'icon' => 'edit', 'variant' => 'gold', 'modal' => 'security-task-modal'])
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                </main>
                <aside class="security-side">
                    <section class="visitor-panel">
                        <div class="visitor-panel-head"><h3 class="visitor-panel-title">Operational Summary</h3></div>
                        <div class="visitor-panel-body security-summary-grid">
                            <div><span>Alerts</span><strong>4</strong></div>
                            <div><span>On Duty</span><strong>11</strong></div>
                            <div><span>Coverage</span><strong>92%</strong></div>
                            <div><span>Escalation</span><strong>1</strong></div>
                        </div>
                    </section>
                    <section class="visitor-panel">
                        <div class="visitor-panel-head"><h3 class="visitor-panel-title">Activity Feed</h3></div>
                        <div class="visitor-panel-body">
                            <div class="community-mini-list">
                                @foreach ([
                                    'Andi Pratama completed checkpoint Tower B',
                                    'Critical incident escalated to supervisor',
                                    'CCTV maintenance task scheduled',
                                    'Night patrol route published',
                                ] as $line)
                                    <div class="community-mini-row">
                                        <span class="community-tile-icon blue" style="width:28px;height:28px;font-size:12px;">S</span>
                                        <strong>{{ $line }}</strong>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                </aside>
            </div>
        @endif

        @include('partials.action-preview-modal', [
            'id' => 'security-task-modal',
            'title' => 'Create Patrol Task',
            'summary' => 'Task Assignment Preview',
            'subtitle' => 'Kelola task patroli, assignment officer, dan jadwal sebagai popup dummy.',
            'avatar' => 'SM',
            'rows' => [
                ['Task Name', 'Patrol Area Tower A'],
                ['Task Type', 'Patrol Routine'],
                ['Patrol Date', '20/05/2026'],
                ['Shift', 'Morning Shift'],
                ['Officer', 'Budi Santoso'],
                ['Priority', 'High'],
            ],
            'copy' => 'Tombol Manage Route di popup ini dapat membuka preview Route Builder. Semua input masih dummy dan belum tersimpan ke backend.',
            'confirmLabel' => 'Save Task',
        ])

        @include('partials.action-preview-modal', [
            'id' => 'security-route-modal',
            'title' => 'Route Builder - Tower A Route',
            'summary' => 'Checkpoint Route Builder',
            'subtitle' => 'Preview penyusunan checkpoint dan estimasi durasi patrol route.',
            'avatar' => 'RB',
            'rows' => [
                ['Checkpoint', 'Lobby Utama, Lift Lobby, Area Kolam Renang'],
                ['Add Checkpoint', 'Tower A, Parking Basement'],
                ['Estimated Duration', '90 Minutes'],
                ['Route Save', 'Static Preview'],
            ],
            'confirmLabel' => 'Save Route',
        ])
@endsection
