@extends('layouts.app')

@section('title', 'Dashboard')

@php
    $overviewCards = [
        ['label' => 'Total Residents', 'value' => $residentSummary['total'], 'sub' => 'Active '.$residentSummary['active']],
        ['label' => 'Total Units', 'value' => $unitSummary['total'], 'sub' => 'Occupied '.$unitSummary['occupied']],
        ['label' => 'Visitors Today', 'value' => $visitorSummary['today'], 'sub' => 'Checked In '.$visitorSummary['checked_in']],
        ['label' => 'Facilities', 'value' => $facilitySummary['total'], 'sub' => 'Active Bookings '.$facilitySummary['active_bookings']],
        ['label' => 'Published Announcements', 'value' => $announcementSummary['published'], 'sub' => 'Pinned '.$announcementSummary['pinned']],
    ];

    $serviceCards = [
        ['label' => 'Submitted', 'value' => $serviceSummary['submitted']],
        ['label' => 'Assigned', 'value' => $serviceSummary['assigned']],
        ['label' => 'On The Way', 'value' => $serviceSummary['on_the_way']],
        ['label' => 'In Progress', 'value' => $serviceSummary['in_progress']],
        ['label' => 'Completed Today', 'value' => $serviceSummary['completed_today']],
        ['label' => 'Over SLA', 'value' => $serviceSummary['over_sla']],
        ['label' => 'Emergency', 'value' => $serviceSummary['emergency']],
    ];
@endphp

@section('content')
    <div class="dash-grid">
        <section class="ops-panel span-12">
            <div class="panel-head">
                <h2 class="panel-title">Operational Overview</h2>
                <span class="badge">Live DB</span>
            </div>
            <div class="panel-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;">
                @foreach ($overviewCards as $card)
                    <article style="border:1px solid #dce4ef;border-radius:16px;padding:16px;background:#fff;display:grid;gap:6px;">
                        <span class="stat-label" style="color:#53647d;">{{ $card['label'] }}</span>
                        <strong class="stat-value" style="font-size:28px;color:#0b2149;">{{ $card['value'] }}</strong>
                        <span class="stat-sub">{{ $card['sub'] }}</span>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="ops-panel span-12">
            <div class="panel-head">
                <h2 class="panel-title">Service Request Snapshot</h2>
                <span class="badge">Live DB</span>
            </div>
            <div class="panel-body" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;">
                @foreach ($serviceCards as $card)
                    <article style="border:1px solid #dce4ef;border-radius:14px;padding:14px 12px;background:#f8fbff;display:grid;gap:6px;">
                        <span class="stat-label" style="color:#53647d;">{{ $card['label'] }}</span>
                        <strong class="stat-value" style="font-size:24px;color:#0b2149;">{{ $card['value'] }}</strong>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="ops-panel span-3">
            <div class="panel-head">
                <h2 class="panel-title">Resident Activity</h2>
            </div>
            <div class="panel-body mini-list">
                <div class="mini-row"><span class="mini-label">Move In (This Month)</span><strong class="mini-value">{{ $residentSummary['move_in_this_month'] }}</strong><span class="spark green"></span></div>
                <div class="mini-row"><span class="mini-label">Move Out (This Month)</span><strong class="mini-value">{{ $residentSummary['move_out_this_month'] }}</strong><span class="spark red"></span></div>
                <div class="mini-row"><span class="mini-label">Pending Approval</span><strong class="mini-value">{{ $residentSummary['pending'] }}</strong><span class="spark"></span></div>
                <div class="mini-row"><span class="mini-label">Active Residents</span><strong class="mini-value">{{ $residentSummary['active'] }}</strong><span class="spark"></span></div>
            </div>
        </section>

        <section class="ops-panel span-3">
            <div class="panel-head">
                <h2 class="panel-title">Latest Alerts</h2>
            </div>
            <div class="panel-body">
                @forelse ($latestAlerts as $alert)
                    <div class="alert-row">
                        <span @class([
                            'dot',
                            'red' => $alert['tone'] === 'red',
                            'gold' => $alert['tone'] === 'gold',
                            'green' => $alert['tone'] === 'green',
                        ])></span>
                        <div>
                            <div class="alert-title" style="color:#0b2149;">
                                @if ($alert['url'])
                                    <a href="{{ $alert['url'] }}" style="color:inherit;text-decoration:none;">{{ $alert['title'] }}</a>
                                @else
                                    {{ $alert['title'] }}
                                @endif
                            </div>
                            <div class="alert-desc">{{ $alert['description'] }}</div>
                        </div>
                        <span class="alert-time">{{ $alert['time'] }}</span>
                    </div>
                @empty
                    <p class="muted" style="margin:0;">Belum ada alert operasional yang valid dari data aktif.</p>
                @endforelse
            </div>
        </section>

        <section class="ops-panel span-3">
            <div class="panel-head">
                <h2 class="panel-title">Service Request Status</h2>
            </div>
            <div class="panel-body donut-row">
                <div class="donut" aria-hidden="true"></div>
                <div class="legend">
                    <div class="legend-row"><span class="dot red"></span><span>Open</span><strong>{{ $serviceSummary['open'] }}</strong></div>
                    <div class="legend-row"><span class="dot gold"></span><span>Assigned</span><strong>{{ $serviceSummary['assigned'] }}</strong></div>
                    <div class="legend-row"><span class="dot"></span><span>In Progress</span><strong>{{ $serviceSummary['in_progress'] }}</strong></div>
                    <div class="legend-row"><span class="dot green"></span><span>Resolved</span><strong>{{ $serviceSummary['resolved'] }}</strong></div>
                    <div class="legend-row"><span></span><span>Over SLA</span><strong>{{ $serviceSummary['over_sla'] }}</strong></div>
                </div>
            </div>
        </section>

        <section class="ops-panel span-3">
            <div class="panel-head">
                <h2 class="panel-title">Visitor Monitoring</h2>
                <span class="badge">Today</span>
            </div>
            <div class="panel-body metric-list">
                <div class="metric-row"><span class="dot"></span><span>Visitors Today</span><strong>{{ $visitorSummary['today'] }}</strong></div>
                <div class="metric-row"><span class="dot green"></span><span>Checked In</span><strong style="color:#168d51">{{ $visitorSummary['checked_in'] }}</strong></div>
                <div class="metric-row"><span class="dot gold"></span><span>Pending Approval</span><strong style="color:#b87924">{{ $visitorSummary['pending'] }}</strong></div>
                <div class="metric-row"><span class="dot"></span><span>Expected Today</span><strong>{{ $visitorSummary['expected_today'] }}</strong></div>
            </div>
        </section>

        <section class="ops-panel span-4">
            <div class="panel-head">
                <h2 class="panel-title">Recent Activities</h2>
            </div>
            <div class="panel-body">
                @forelse ($recentActivities as $activity)
                    <div class="alert-row">
                        <span class="avatar" style="width:28px;height:28px;flex-basis:28px;font-size:11px">{{ strtoupper(substr($activity['actor'], 0, 1)) }}</span>
                        <div>
                            <div class="alert-title">
                                @if ($activity['url'])
                                    <a href="{{ $activity['url'] }}" style="color:inherit;text-decoration:none;">{{ $activity['actor'] }}</a>
                                @else
                                    {{ $activity['actor'] }}
                                @endif
                            </div>
                            <div class="alert-desc"><strong>{{ $activity['title'] }}</strong> · {{ $activity['description'] }}</div>
                        </div>
                        <span class="alert-time">{{ $activity['time']?->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="muted" style="margin:0;">Belum ada aktivitas terbaru dari modul yang sudah aktif.</p>
                @endforelse
            </div>
        </section>

        <section class="ops-panel span-4">
            <div class="panel-head">
                <h2 class="panel-title">Facility Load</h2>
                <span class="badge">Booking Based</span>
            </div>
            <div class="panel-body progress-list">
                @forelse ($facilityLoad as $facility)
                    <div class="progress-row">
                        <span>{{ $facility->name }}</span>
                        <div class="progress-track">
                            <div class="progress-fill" style="width: {{ min(($facility->active_bookings * 20), 100) }}%"></div>
                        </div>
                        <strong>{{ $facility->active_bookings }} active / {{ $facility->total_bookings }} total</strong>
                    </div>
                @empty
                    <p class="muted" style="margin:0;">Belum ada data booking facility yang cukup untuk ditampilkan.</p>
                @endforelse
            </div>
        </section>

        <section class="ops-panel span-4">
            <div class="panel-head">
                <h2 class="panel-title">Unit Snapshot</h2>
            </div>
            <div class="panel-body metric-list">
                <div class="metric-row"><span class="dot"></span><span>Occupied</span><strong>{{ $unitSummary['occupied'] }}</strong></div>
                <div class="metric-row"><span class="dot green"></span><span>Vacant</span><strong style="color:#168d51">{{ $unitSummary['vacant'] }}</strong></div>
                <div class="metric-row"><span class="dot red"></span><span>Maintenance</span><strong style="color:#b42318">{{ $unitSummary['maintenance'] }}</strong></div>
                <div class="metric-row"><span class="dot gold"></span><span>Technician Teams</span><strong>{{ $technicianSummary['teams'] }}</strong></div>
            </div>
        </section>
    </div>
@endsection
