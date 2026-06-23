@extends('layouts.app')

@php
    $navTabs = [
        ['label' => 'Registration', 'route' => 'visitor-management.registration', 'active' => ['visitor-management.index', 'visitor-management.registration']],
        ['label' => 'Pending Approval', 'route' => 'visitor-management.pending-approval', 'active' => ['visitor-management.pending-approval']],
        ['label' => 'Expected Visitors', 'route' => 'visitor-management.expected-visitors', 'active' => ['visitor-management.expected-visitors']],
        ['label' => 'Check-In / Out', 'route' => 'visitor-management.check-in-out', 'active' => ['visitor-management.check-in-out']],
        ['label' => 'History', 'route' => 'visitor-management.history', 'active' => ['visitor-management.history']],
        ['label' => 'Blacklist', 'route' => 'visitor-management.blacklist', 'active' => ['visitor-management.blacklist']],
    ];

    $queueColumns = [
        ['label' => 'No.', 'key' => 'no'],
        ['label' => 'Visitor Name', 'key' => 'name'],
        ['label' => 'To Unit', 'key' => 'unit'],
        ['label' => 'Visit Date & Time', 'key' => 'date'],
        ['label' => 'Purpose', 'key' => 'purpose'],
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
                @if ($pageKey === 'check-in-out')
                    <form method="POST" action="{{ route('visitor-management.lookup') }}" class="visitor-table-filters" style="margin:0;min-width:min(420px,100%);">
                        @csrf
                        <input type="text" name="access_code" value="{{ old('access_code') }}" placeholder="Lookup QR / Access Code" aria-label="Lookup access code">
                        <button class="btn secondary" type="submit">Lookup Code</button>
                    </form>
                @else
                    <button class="btn secondary" type="button" data-modal-open="visitor-registration-modal">Download Template</button>
                    <button class="btn" type="button" data-modal-open="visitor-registration-modal">Register Walk-In Visitor</button>
                @endif
            </div>
        </section>

        <nav class="visitor-tabs" aria-label="Visitor management navigation">
            @foreach ($navTabs as $tab)
                <a href="{{ route($tab['route']) }}" @class(['visitor-tab', 'active' => request()->routeIs(...$tab['active'])])>
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </nav>

        @if ($pageKey === 'check-in-out')
            <div class="visitor-tabs" aria-label="Check-in and check-out mode">
                <a
                    href="{{ route('visitor-management.check-in-out', array_filter(array_merge($filters, ['mode' => 'check-in']))) }}"
                    @class(['visitor-tab', 'active' => ($checkMode ?? 'check-in') === 'check-in'])
                >
                    Check-In Queue
                </a>
                <a
                    href="{{ route('visitor-management.check-in-out', array_filter(array_merge($filters, ['mode' => 'check-out']))) }}"
                    @class(['visitor-tab', 'active' => ($checkMode ?? 'check-in') === 'check-out'])
                >
                    Check-Out Queue
                </a>
            </div>

            <div class="visitor-grid">
                <section class="visitor-panel visitor-span-12">
                    <div class="visitor-panel-head">
                        <h2 class="visitor-panel-title">Visitor Check-In Queue</h2>
                    </div>
                    @include('visitor-management.partials.filters', [
                        'action' => route('visitor-management.check-in-out'),
                        'filters' => $filters,
                        'search' => 'Search expected, unit, resident...',
                        'statusOptions' => $statusOptions,
                        'registrationSources' => $registrationSources,
                        'residentOptions' => $residentOptions,
                        'showSource' => true,
                        'showResident' => true,
                        'showStatus' => false,
                    ])
                    @include('visitor-management.partials.table', ['columns' => $queueColumns, 'rows' => $checkInRows, 'paginator' => $checkInQueue, 'modalId' => 'visitor-action-modal'])
                </section>

                <section class="visitor-panel visitor-span-12">
                    <div class="visitor-panel-head">
                        <h2 class="visitor-panel-title">Visitor Check-Out Queue</h2>
                    </div>
                    @include('visitor-management.partials.table', ['columns' => $queueColumns, 'rows' => $checkOutRows, 'paginator' => $checkOutQueue, 'modalId' => 'visitor-action-modal'])
                </section>
            </div>
        @elseif ($pageKey === 'blacklist')
            <div class="visitor-grid">
                <section class="visitor-panel visitor-span-12">
                    <div class="visitor-panel-head">
                        <h2 class="visitor-panel-title">{{ $page['tableTitle'] }}</h2>
                    </div>
                    @include('visitor-management.partials.table', ['columns' => $blacklistColumns, 'rows' => $blacklistRows, 'modalId' => 'visitor-action-modal'])
                </section>
            </div>
        @else
            <div class="visitor-grid">
                <section class="visitor-panel visitor-span-12">
                    <div class="visitor-panel-head">
                        <h2 class="visitor-panel-title">{{ $page['tableTitle'] }}</h2>
                    </div>
                    @include('visitor-management.partials.filters', [
                        'action' => route(match($pageKey) {
                            'registration' => 'visitor-management.registration',
                            'pending-approval' => 'visitor-management.pending-approval',
                            'expected-visitors' => 'visitor-management.expected-visitors',
                            'history' => 'visitor-management.history',
                            default => 'visitor-management.index',
                        }),
                        'filters' => $filters,
                        'search' => 'Search visitor, unit, resident...',
                        'statusOptions' => $statusOptions,
                        'registrationSources' => $registrationSources,
                        'residentOptions' => $residentOptions,
                        'showSource' => true,
                        'showResident' => true,
                        'showStatus' => $pageKey === 'registration',
                    ])
                    @include('visitor-management.partials.table', [
                        'columns' => $pageKey === 'history' ? $historyColumns : $queueColumns,
                        'rows' => $rows,
                        'paginator' => $records,
                        'modalId' => 'visitor-action-modal',
                    ])
                </section>
            </div>
        @endif

        @include('visitor-management.partials.walk-in-modal', [
            'modalId' => 'visitor-registration-modal',
            'residentOptions' => $residentOptions,
            'guestLimit' => $guestLimit,
            'autoOpen' => $errors->hasAny(['resident_id', 'visitor_name', 'visitor_phone', 'visit_date', 'estimated_arrival_time', 'guest_count', 'visit_purpose', 'identity_photo']),
        ])

        @include('visitor-management.partials.detail', [
            'modalId' => 'visitor-action-modal',
            'title' => $page['label'].' Detail',
            'visitor' => $selectedVisitor,
            'statusClass' => $selectedVisitor
                ? match($selectedVisitor->status) {
                    'Pending' => 'status-pending',
                    'Rejected', 'Cancelled' => 'status-rejected',
                    'Expired' => 'status-expired',
                    default => 'status-approved',
                }
                : 'status-approved',
            'actionsTitle' => $pageKey === 'check-in-out' ? 'Check-In / Check-Out Actions' : 'Visitor Actions',
            'autoOpen' => (bool) $selectedVisitor,
        ])

        @if ($selectedVisitor)
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    document.body.classList.add('modal-open');
                });
            </script>
        @endif
    </div>
@endsection
