@extends('layouts.app')

@php
    $pageKey = $pageKey ?? 'registration';

    $navTabs = [
        ['label' => 'Registration', 'route' => 'visitor-management.registration', 'active' => ['visitor-management.index', 'visitor-management.registration']],
        ['label' => 'Pending Approval', 'route' => 'visitor-management.pending-approval', 'active' => ['visitor-management.pending-approval']],
        ['label' => 'Expected Visitors', 'route' => 'visitor-management.expected-visitors', 'active' => ['visitor-management.expected-visitors']],
        ['label' => 'Check-In / Out', 'route' => 'visitor-management.check-in-out', 'active' => ['visitor-management.check-in-out']],
        ['label' => 'History', 'route' => 'visitor-management.history', 'active' => ['visitor-management.history']],
        ['label' => 'Blacklist', 'route' => 'visitor-management.blacklist', 'active' => ['visitor-management.blacklist']],
    ];

    $visitors = [
        ['name' => 'John Doe', 'unit' => 'A-1808', 'resident' => 'Ahmad Rizky', 'time' => '10:00 AM', 'purpose' => 'Meeting', 'vehicle' => 'B 1234 ABC', 'status' => 'Pending', 'statusClass' => 'status-pending'],
        ['name' => 'Michael Tan', 'unit' => 'B-1205', 'resident' => 'Sarah Lim', 'time' => '11:30 AM', 'purpose' => 'Family Visit', 'vehicle' => '-', 'status' => 'Approved', 'statusClass' => 'status-approved'],
        ['name' => 'Sarah Lim', 'unit' => 'C-2501', 'resident' => 'Jason Lee', 'time' => '01:00 PM', 'purpose' => 'Delivery', 'vehicle' => 'B 5678 DEF', 'status' => 'Pending', 'statusClass' => 'status-pending'],
        ['name' => 'David Lee', 'unit' => 'A-1002', 'resident' => 'Nina Putri', 'time' => '03:00 PM', 'purpose' => 'Contractor', 'vehicle' => '-', 'status' => 'Rejected', 'statusClass' => 'status-rejected'],
        ['name' => 'Kevin Hartono', 'unit' => 'B-2008', 'resident' => 'Budi Santoso', 'time' => '04:30 PM', 'purpose' => 'Maintenance', 'vehicle' => 'D 2345 GHI', 'status' => 'Expired', 'statusClass' => 'status-expired'],
    ];

    $queueColumns = [
        ['label' => 'No.', 'key' => 'no'],
        ['label' => 'Visitor Name', 'key' => 'name'],
        ['label' => 'To Unit', 'key' => 'unit'],
        ['label' => 'Visit Date & Time', 'key' => 'date'],
        ['label' => 'Purpose', 'key' => 'purpose'],
        ['label' => 'Vehicle', 'key' => 'vehicle'],
        ['label' => 'Status', 'key' => 'status'],
        ['label' => 'Action', 'key' => 'action'],
    ];

    $historyColumns = [
        ['label' => 'No.', 'key' => 'no'],
        ['label' => 'Visitor Name', 'key' => 'name'],
        ['label' => 'To Unit', 'key' => 'unit'],
        ['label' => 'Visit Date & Time', 'key' => 'date'],
        ['label' => 'Check-Out Time', 'key' => 'checkout'],
        ['label' => 'Status', 'key' => 'status'],
        ['label' => 'Action', 'key' => 'action'],
    ];

    $vehicleColumns = [
        ['label' => 'No.', 'key' => 'no'],
        ['label' => 'Plate Number', 'key' => 'plate'],
        ['label' => 'Vehicle Type', 'key' => 'type'],
        ['label' => 'Assigned Lot', 'key' => 'lot'],
        ['label' => 'Visitor Name', 'key' => 'name'],
        ['label' => 'Unit', 'key' => 'unit'],
        ['label' => 'Entry Date & Time', 'key' => 'date'],
        ['label' => 'Parking Status', 'key' => 'status'],
        ['label' => 'Action', 'key' => 'action'],
    ];

    $blacklistColumns = [
        ['label' => 'No.', 'key' => 'no'],
        ['label' => 'Visitor Name', 'key' => 'name'],
        ['label' => 'Phone / Email', 'key' => 'contact'],
        ['label' => 'Reason for Blacklisting', 'key' => 'reason'],
        ['label' => 'Blocked Date', 'key' => 'blocked'],
        ['label' => 'Blocked By', 'key' => 'blockedBy'],
        ['label' => 'Expiry Date', 'key' => 'expiry'],
        ['label' => 'Status', 'key' => 'status'],
        ['label' => 'Action', 'key' => 'action'],
    ];

    $rows = [
        'pending-approval' => [
            ['no' => 1, 'name' => 'John Doe', 'unit' => 'A-1808', 'date' => '07 Jun 2026 - 10:00 AM', 'purpose' => 'Meeting', 'vehicle' => 'B 1234 ABC', 'status' => 'Pending', 'statusClass' => 'status-pending', 'actions' => [['Approve', 'success'], ['Reject', 'danger'], ['View', 'info']]],
            ['no' => 2, 'name' => 'Sarah Lim', 'unit' => 'C-2501', 'date' => '07 Jun 2026 - 12:50 AM', 'purpose' => 'Delivery', 'vehicle' => 'B 5678 DEF', 'status' => 'Pending', 'statusClass' => 'status-pending', 'actions' => [['Approve', 'success'], ['Reject', 'danger'], ['View', 'info']]],
            ['no' => 3, 'name' => 'Jane Doe', 'unit' => 'B-1205', 'date' => '08 Jun 2026 - 02:50 PM', 'purpose' => 'Private Visit', 'vehicle' => '-', 'status' => 'Pending', 'statusClass' => 'status-pending', 'actions' => [['Approve', 'success'], ['Reject', 'danger'], ['View', 'info']]],
            ['no' => 4, 'name' => 'Alex Wong', 'unit' => 'D-1002', 'date' => '08 Jun 2026 - 05:30 PM', 'purpose' => 'Maintenance', 'vehicle' => 'D 2345 GHI', 'status' => 'Pending', 'statusClass' => 'status-pending', 'actions' => [['Approve', 'success'], ['Reject', 'danger'], ['View', 'info']]],
            ['no' => 5, 'name' => 'Ahmad Rizky', 'unit' => 'B-1002', 'date' => '08 Jun 2026 - 05:30 PM', 'purpose' => 'Family Visit', 'vehicle' => '-', 'status' => 'Pending', 'statusClass' => 'status-pending', 'actions' => [['Approve', 'success'], ['Reject', 'danger'], ['View', 'info']]],
        ],
        'expected-visitors' => [
            ['no' => 1, 'name' => 'Alice Smith', 'unit' => 'A-1808', 'date' => '07 Jun 2026 - 10:00 AM', 'purpose' => 'Meeting', 'vehicle' => 'B 1234 ABC', 'status' => 'Confirmed', 'statusClass' => 'status-approved', 'actions' => [['Arrive', 'success'], ['Cancel', 'danger'], ['View', 'info']]],
            ['no' => 2, 'name' => 'Bob Jones', 'unit' => 'C-2501', 'date' => '07 Jun 2026 - 12:50 AM', 'purpose' => 'Delivery', 'vehicle' => 'B 5677 DEF', 'status' => 'Confirmed', 'statusClass' => 'status-approved', 'actions' => [['Arrive', 'success'], ['Cancel', 'danger'], ['View', 'info']]],
            ['no' => 3, 'name' => 'Charlie Brown', 'unit' => 'B-1205', 'date' => '08 Jun 2026 - 12:50 PM', 'purpose' => 'Private Visit', 'vehicle' => '-', 'status' => 'Expected', 'statusClass' => 'status-approved', 'actions' => [['Arrive', 'success'], ['Cancel', 'danger'], ['View', 'info']]],
            ['no' => 4, 'name' => 'Alex Wong', 'unit' => 'B-1002', 'date' => '08 Jun 2026 - 08:30 PM', 'purpose' => 'Maintenance', 'vehicle' => 'B 2345 GHI', 'status' => 'Confirmed', 'statusClass' => 'status-approved', 'actions' => [['Arrive', 'success'], ['Cancel', 'danger'], ['View', 'info']]],
            ['no' => 5, 'name' => 'Marcia Lee', 'unit' => 'A-1002', 'date' => '08 Jun 2026 - 08:30 PM', 'purpose' => 'Family Visit', 'vehicle' => '-', 'status' => 'Expected', 'statusClass' => 'status-approved', 'actions' => [['Arrive', 'success'], ['Cancel', 'danger'], ['View', 'info']]],
        ],
        'check-in' => [
            ['no' => 1, 'name' => 'Michael Chen', 'unit' => 'C-2201', 'date' => '07 Jun 2026 - 11:00 AM', 'purpose' => 'Maintenance', 'vehicle' => 'B 4567 XYZ', 'status' => 'Expected', 'statusClass' => 'status-approved', 'actions' => [['Check-In', 'success'], ['View', 'info']]],
            ['no' => 2, 'name' => 'Jessica Wong', 'unit' => 'B-1505', 'date' => '07 Jun 2026 - 02:00 PM', 'purpose' => 'Guest', 'vehicle' => '-', 'status' => 'Expected', 'statusClass' => 'status-approved', 'actions' => [['Check-In', 'success'], ['View', 'info']]],
            ['no' => 3, 'name' => 'David Lee', 'unit' => 'A-1808', 'date' => '07 Jun 2026 - 04:30 PM', 'purpose' => 'Delivery', 'vehicle' => 'B 9012 AB', 'status' => 'Expected', 'statusClass' => 'status-approved', 'actions' => [['Check-In', 'success'], ['View', 'info']]],
        ],
        'check-out' => [
            ['no' => 1, 'name' => 'Lisa Adams', 'unit' => 'B-1002', 'date' => '07 Jun 2026 - 09:30 AM', 'purpose' => 'Maintenance', 'vehicle' => 'B 1234 CD', 'status' => 'Checked-In', 'statusClass' => 'status-approved', 'actions' => [['Check-Out', 'danger'], ['View', 'info']]],
            ['no' => 2, 'name' => 'Kenji Tanaka', 'unit' => 'C-2201', 'date' => '07 Jun 2026 - 10:15 AM', 'purpose' => 'Guest', 'vehicle' => '-', 'status' => 'Checked-In', 'statusClass' => 'status-approved', 'actions' => [['Check-Out', 'danger'], ['View', 'info']]],
            ['no' => 3, 'name' => 'Maria Garcia', 'unit' => 'A-1808', 'date' => '07 Jun 2026 - 11:00 AM', 'purpose' => 'Delivery', 'vehicle' => 'B 5678 FG', 'status' => 'Checked-In', 'statusClass' => 'status-approved', 'actions' => [['Check-Out', 'danger'], ['View', 'info']]],
        ],
        'history' => [
            ['no' => 1, 'name' => 'Lisa Adams', 'unit' => 'B-1002', 'date' => '07 Jun 2026 - 09:30 AM', 'checkout' => '07 Jun 2026 - 11:45 AM', 'status' => 'Visit Complete', 'statusClass' => 'status-approved', 'actions' => [['View Details', 'info']]],
            ['no' => 2, 'name' => 'Kenji Tanaka', 'unit' => 'C-2201', 'date' => '07 Jun 2026 - 10:15 AM', 'checkout' => '07 Jun 2026 - 12:30 PM', 'status' => 'Visit Complete', 'statusClass' => 'status-approved', 'actions' => [['View Details', 'info']]],
            ['no' => 3, 'name' => 'Maria Garcia', 'unit' => 'A-1808', 'date' => '07 Jun 2026 - 11:00 AM', 'checkout' => '07 Jun 2026 - 02:30 PM', 'status' => 'Visit Complete', 'statusClass' => 'status-approved', 'actions' => [['View Details', 'info']]],
            ['no' => 4, 'name' => 'David Johnson', 'unit' => 'D-1002', 'date' => '07 Jun 2026 - 11:45 AM', 'checkout' => '07 Jun 2026 - 12:30 AM', 'status' => 'Visit Complete', 'statusClass' => 'status-approved', 'actions' => [['View Details', 'info']]],
        ],
        'vehicles' => [
            ['no' => 1, 'plate' => 'B 4578 XYZ', 'type' => 'Mobil', 'lot' => 'V-12', 'name' => 'Lisa Adams', 'unit' => 'B-1002', 'date' => '07 Jun 2026 - 10:15 AM', 'status' => 'Parked', 'statusClass' => 'status-approved', 'actions' => [['Manage Access', 'info'], ['Verify Plate', 'secondary']]],
            ['no' => 2, 'plate' => 'D 1234 XY', 'type' => 'Mobil', 'lot' => 'V-14', 'name' => 'Kenji Tanaka', 'unit' => 'C-2201', 'date' => '07 Jun 2026 - 11:30 AM', 'status' => 'Parked', 'statusClass' => 'status-approved', 'actions' => [['Manage Access', 'info'], ['Verify Plate', 'secondary']]],
            ['no' => 3, 'plate' => 'B 2345 HI', 'type' => 'Mobil', 'lot' => 'V-13', 'name' => 'Maria Garcia', 'unit' => 'A-1808', 'date' => '07 Jun 2026 - 11:45 AM', 'status' => 'Parked', 'statusClass' => 'status-approved', 'actions' => [['Manage Access', 'info'], ['Verify Plate', 'secondary']]],
            ['no' => 4, 'plate' => 'D 9012 AB', 'type' => 'Motor', 'lot' => 'M-08', 'name' => 'David Johnson', 'unit' => 'D-1002', 'date' => '07 Jun 2026 - 12:10 PM', 'status' => 'Parked', 'statusClass' => 'status-approved', 'actions' => [['Manage Access', 'info'], ['Verify Plate', 'secondary']]],
        ],
        'blacklist' => [
            ['no' => 1, 'name' => 'Mike Thompson', 'contact' => '08111222333', 'reason' => 'Unauthorized access', 'blocked' => '01 Mar 2026', 'blockedBy' => 'Security Chief', 'expiry' => 'Indefinite', 'status' => 'Active', 'statusClass' => 'status-approved', 'actions' => [['Review Record', 'info']]],
            ['no' => 2, 'name' => 'Jane Fisher', 'contact' => 'jane.f@email.com', 'reason' => 'Property Damage', 'blocked' => '15 Apr 2026', 'blockedBy' => 'Ops Manager', 'expiry' => '15 Apr 2027', 'status' => 'Active', 'statusClass' => 'status-approved', 'actions' => [['Review Record', 'info']]],
            ['no' => 3, 'name' => 'Alex Wong', 'contact' => '08556677889', 'reason' => 'Theft', 'blocked' => '20 May 2026', 'blockedBy' => 'Building Mgr.', 'expiry' => 'Indefinite', 'status' => 'Active', 'statusClass' => 'status-approved', 'actions' => [['Review Record', 'info']]],
            ['no' => 4, 'name' => 'David Johnson', 'contact' => '08778899000', 'reason' => 'Unauthorized parking violation', 'blocked' => '05 Jun 2026', 'blockedBy' => 'Traffic Control', 'expiry' => '05 Dec 2026', 'status' => 'Active', 'statusClass' => 'status-approved', 'actions' => [['Review Record', 'info']]],
        ],
    ];

    $pages = [
        'registration' => [
            'label' => 'Visitor Registration',
            'title' => 'Visitor Registration',
            'subtitle' => 'Input data visitor atau walk-in visitor oleh front office / security.',
            'stats' => ['Today Registrations: 42', 'Pending Approvals: 7', 'Visitors Inside: 18'],
        ],
        'pending-approval' => [
            'label' => 'Pending Approval',
            'title' => 'Pending Approval',
            'subtitle' => 'Input data visitor atau walk-in visitor oleh front office / security.',
            'stats' => ['Total Pending Requests: 7'],
            'tableTitle' => 'Pending Visitor Approval Queue',
            'columns' => $queueColumns,
            'rows' => $rows['pending-approval'],
            'detailTitle' => 'Pending Request Details',
            'detailStatus' => ['John Doe', 'Pending', 'status-pending'],
            'detailActionsTitle' => 'Approval Actions',
            'detailActions' => [['Reject Request', 'danger'], ['Approve Request', 'success']],
        ],
        'expected-visitors' => [
            'label' => 'Expected Visitors',
            'title' => 'Expected Visitors',
            'subtitle' => 'Input data visitor atau walk-in visitor oleh front office / security.',
            'stats' => ['Total Expected Visitors: 20'],
            'tableTitle' => 'Expected Visitors Queue',
            'columns' => $queueColumns,
            'rows' => $rows['expected-visitors'],
            'detailTitle' => 'Visitor Detail (Expected)',
            'detailStatus' => ['Alice Smith', 'Confirmed', 'status-approved'],
            'detailActionsTitle' => 'Arrival Actions',
            'detailActions' => [['Cancel Expected Arrival', 'danger'], ['Confirm Arrival & Check-In', 'success']],
        ],
        'check-in-out' => [
            'label' => 'Check-In / Check-Out',
            'title' => 'Visitor Check-In / Check-Out',
            'subtitle' => 'Manage check-ins and check-outs for expected and approved visitors.',
            'stats' => ['Total Expected Today: 12', 'Total Visitors Currently Inside: 18'],
            'detailTitle' => 'Visitor Check-In / Check-Out Details',
            'detailStatus' => ['Michael Chen', 'Ready to Check-In', 'status-approved'],
        ],
        'history' => [
            'label' => 'Visitor History',
            'title' => 'Visitor History Log',
            'subtitle' => 'Manage completed visitor records and visit audit trail.',
            'stats' => ['Total History Records (This Month): 350'],
            'tableTitle' => 'Visitor History Log',
            'columns' => $historyColumns,
            'rows' => $rows['history'],
            'detailTitle' => 'Visitor History Details',
            'detailStatus' => ['Lisa Adams', 'Visit Complete', 'status-approved'],
            'detailActionsTitle' => 'History Actions',
            'detailActions' => [['Re-print Visit Pass', 'info'], ['View Linked Service Report', 'gold']],
        ],
        'blacklist' => [
            'label' => 'Blacklist Management',
            'title' => 'Visitor Blacklist Management',
            'subtitle' => 'Manage visitor blacklist records and incident notes.',
            'stats' => ['Total Visitors Currently Blacklisted: 12 / 50', 'Last Blacklist Activity: Alex Wong (Theft)'],
            'tableTitle' => 'Visitor Blacklist Management',
            'columns' => $blacklistColumns,
            'rows' => $rows['blacklist'],
            'detailTitle' => 'Visitor Blacklist Details',
            'detailStatus' => ['Mike Thompson', 'Blacklisted', 'status-rejected'],
            'detailActionsTitle' => 'Blacklist Actions',
            'detailActions' => [['Add Incident Report', 'info'], ['Revoke Blacklist', 'gold'], ['Edit Blacklist Record', 'danger']],
        ],
    ];

    $page = $pages[$pageKey] ?? $pages['registration'];
@endphp

@section('title', $page['label'])
@section('topbar_context')
    Visitor Management > {{ $page['label'] }}
@endsection
@section('topbar_subtitle', $page['subtitle'])

@section('content')
    <div class="visitor-page">
        <section class="visitor-toolbar">
            <div class="visitor-heading">
                <span class="visitor-step">OPS</span>
                <div>
                    <h2>{{ $page['title'] }}</h2>
                    <p>{{ $page['subtitle'] }}</p>
                </div>
            </div>

            <div class="visitor-toolbar-actions">
                @if (! empty($page['stats']))
                    <div class="visitor-stat-strip">
                        @foreach ($page['stats'] as $stat)
                            <span class="visitor-chip">{{ $stat }}</span>
                        @endforeach
                    </div>
                @endif
                <button class="btn secondary" type="button" data-modal-open="visitor-registration-modal">Download Template</button>
                <button class="btn" type="button" data-modal-open="visitor-registration-modal">Register Walk-In Visitor</button>
            </div>
        </section>

        <nav class="visitor-tabs" aria-label="Visitor management navigation">
            @foreach ($navTabs as $tab)
                <a href="{{ route($tab['route']) }}" @class(['visitor-tab', 'active' => request()->routeIs(...$tab['active'])])>
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </nav>

        @if ($pageKey === 'registration')
            <div class="visitor-grid">

                <section class="visitor-panel visitor-span-12">
                    <div class="visitor-panel-head">
                        <h2 class="visitor-panel-title">All Visitor Registration</h2>
                        @include('visitor-management.partials.filters', ['search' => 'Search visitor, unit, resident...'])
                    </div>
                    @include('visitor-management.partials.table', ['columns' => $queueColumns, 'rows' => $visitors, 'modalId' => 'visitor-action-modal'])
                </section>

            </div>
        @elseif ($pageKey === 'check-in-out')
            <div class="visitor-tabs" aria-label="Check-in and check-out mode">
                <button class="visitor-tab active" type="button">Check-In Queue</button>
                <button class="visitor-tab" type="button">Check-Out Queue</button>
            </div>

            <div class="visitor-grid">
                <section class="visitor-panel visitor-span-9">
                    <div class="visitor-panel-head">
                        <h2 class="visitor-panel-title">Visitor Check-In Queue</h2>
                        <button class="btn success" type="button" data-modal-open="visitor-action-modal">Check-In Selected</button>
                    </div>
                    @include('visitor-management.partials.filters', ['search' => 'Search expected, unit, resident...'])
                    @include('visitor-management.partials.table', ['columns' => $queueColumns, 'rows' => $rows['check-in'], 'modalId' => 'visitor-action-modal'])
                </section>

                <section class="visitor-panel visitor-span-12">
                    <div class="visitor-panel-head">
                        <h2 class="visitor-panel-title">Visitor Check-Out Queue</h2>
                        <button class="btn danger" type="button" data-modal-open="visitor-action-modal">Check-Out Selected</button>
                    </div>
                    @include('visitor-management.partials.filters', ['search' => 'Search currently inside...'])
                    @include('visitor-management.partials.table', ['columns' => $queueColumns, 'rows' => $rows['check-out'], 'modalId' => 'visitor-action-modal'])
                </section>

            </div>
        @else
            <div class="visitor-grid">
                <section class="visitor-panel visitor-span-12">
                    <div class="visitor-panel-head">
                        <h2 class="visitor-panel-title">{{ $page['tableTitle'] }}</h2>
                        @if ($pageKey === 'pending-approval')
                            <button class="btn success" type="button" data-modal-open="visitor-action-modal">Approve Selected</button>
                        @elseif ($pageKey === 'expected-visitors')
                            <button class="btn success" type="button" data-modal-open="visitor-action-modal">Confirm Arrival Selected</button>
                        @endif
                    </div>
                    @include('visitor-management.partials.filters', ['search' => $pageKey === 'vehicles' ? 'Search by plate number / type' : 'Search visitor, unit, resident...'])
                    @include('visitor-management.partials.table', ['columns' => $page['columns'], 'rows' => $page['rows'], 'modalId' => 'visitor-action-modal'])
                </section>

            </div>
        @endif

        @include('partials.action-preview-modal', [
            'id' => 'visitor-registration-modal',
            'title' => 'Visitor Registration Form',
            'summary' => 'Walk-In Visitor Registration',
            'subtitle' => 'Popup form preview untuk registrasi visitor baru. Belum tersimpan ke backend.',
            'avatar' => 'VR',
            'rows' => [
                ['Visitor Type', 'Guest / Personal Visit'],
                ['Visitor Name', 'John Doe'],
                ['Resident', 'A-1808 - Ahmad Rizky'],
                ['Visit Date', '07 Jun 2026 - 10:00 AM'],
                ['Vehicle', 'B 1234 ABC'],
                ['Notes', 'Meeting with unit owner.'],
            ],
            'confirmLabel' => 'Submit Registration',
        ])

        @include('visitor-management.partials.detail', [
            'modalId' => 'visitor-action-modal',
            'title' => $page['detailTitle'] ?? 'Visitor Detail',
            'status' => $page['detailStatus'] ?? ['John Doe', 'Pending', 'status-pending'],
            'actionsTitle' => $pageKey === 'check-in-out'
                ? 'Check-In / Check-Out Actions'
                : ($page['detailActionsTitle'] ?? 'Visitor Actions'),
            'actions' => $pageKey === 'check-in-out'
                ? [['Confirm Check-In', 'success'], ['Confirm Check-Out', 'danger']]
                : ($page['detailActions'] ?? [['Edit', 'secondary'], ['Cancel Registration', 'danger']]),
            'subtext' => 'Guest - Personal Visit',
        ])
    </div>
@endsection
