@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="dash-grid">
        <article class="stat-card">
            <div class="stat-icon">
                <span class="tiny-donut" aria-hidden="true"></span>
            </div>
            <div>
                <div class="stat-label">Occupancy Rate</div>
                <div class="stat-value">91.9%</div>
                <div class="stat-sub">1,152 / 1,254 Units</div>
                <div class="trend-up">+ 2.4%</div>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h14a3 3 0 0 1 3 3v8H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2Z"/><path d="M18 7V5a2 2 0 0 0-2-2H5a3 3 0 0 0-3 3v3"/><path d="M16 13h5"/></svg>
            </div>
            <div>
                <div class="stat-label">Collection Rate</div>
                <div class="stat-value">94.7%</div>
                <div class="stat-sub">Rp 2.63 M / Rp 2.78 M</div>
                <div class="trend-up">+ 3.1%</div>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 3h9l4 4v14H6z"/><path d="M15 3v5h5M9 12h6M9 16h4"/></svg>
            </div>
            <div>
                <div class="stat-label">Outstanding Bills</div>
                <div class="stat-value">Rp 1.25 M</div>
                <div class="stat-sub">Total Outstanding</div>
                <div class="trend-down">- 1.2%</div>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m14.7 6.3 3-3a2.1 2.1 0 0 1 3 3l-3 3M4 20l7.3-7.3M8 4l12 12M5 8l3-3"/></svg>
            </div>
            <div>
                <div class="stat-label">Open Requests</div>
                <div class="stat-value">{{ $serviceSummary['open'] ?? 18 }}</div>
                <div class="stat-sub">Total Open Tickets</div>
                <div class="trend-warn">Emergency {{ $serviceSummary['emergency'] ?? 0 }}</div>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="10" cy="7" r="4"/><path d="M21 21v-2a4 4 0 0 0-3-3.87M17 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <div class="stat-label">Visitors Today</div>
                <div class="stat-value">127</div>
                <div class="stat-sub">Checked In</div>
                <div class="trend-up">+ 12%</div>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m12 2 9 5-9 5-9-5 9-5Z"/><path d="M3 7v10l9 5 9-5V7M12 12v10"/></svg>
            </div>
            <div>
                <div class="stat-label">Packages Today</div>
                <div class="stat-value">42</div>
                <div class="stat-sub">Incoming Packages</div>
                <div class="trend-up">+ 8%</div>
            </div>
        </article>

        <section class="ops-panel span-4">
            <div class="panel-head">
                <h2 class="panel-title">Building Overview</h2>
            </div>
            <div class="panel-body building-overview">
                <div class="building-scene">
                    <span class="tower-third"></span>
                    <div class="podium">
                        <div class="tower-tab active"><strong>Tower A</strong><span>612 Units</span></div>
                        <div class="tower-tab"><strong>Tower B</strong><span>426 Units</span></div>
                        <div class="tower-tab"><strong>Tower C</strong><span>216 Units</span></div>
                    </div>
                </div>
                <div class="overview-list">
                    <div class="overview-item">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="#0b2149" stroke-width="1.8"><path d="M4 21V7l8-4 8 4v14M9 21v-8h6v8"/></svg>
                        <div><span>Total Units</span><strong>1,254</strong></div>
                    </div>
                    <div class="overview-item">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="#0b2149" stroke-width="1.8"><path d="M6 21V3h12v18M9 7h2M13 7h2M9 11h2M13 11h2M9 15h2M13 15h2"/></svg>
                        <div><span>Occupied Units</span><strong>1,152</strong></div>
                    </div>
                    <div class="overview-item">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="#0b2149" stroke-width="1.8"><path d="M3 11 12 4l9 7M5 10v10h14V10"/></svg>
                        <div><span>Vacant Units</span><strong>102</strong></div>
                    </div>
                    <div class="overview-item">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="#0b2149" stroke-width="1.8"><path d="M14.7 6.3 18 3l3 3-3.3 3.3M4 20l7.5-7.5M8 4l12 12"/></svg>
                        <div><span>Maintenance</span><strong>18</strong></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="ops-panel span-4">
            <div class="panel-head">
                <h2 class="panel-title">Collection Trend</h2>
                <span class="badge">This Year</span>
            </div>
            <div class="panel-body">
                <div class="chart">
                    <div class="chart-grid"></div>
                    <div class="bars" aria-label="Collection chart">
                        <div class="bar-pair"><span class="bar" style="height: 42px"></span><span class="bar target" style="height: 30px"></span></div>
                        <div class="bar-pair"><span class="bar" style="height: 74px"></span><span class="bar target" style="height: 58px"></span></div>
                        <div class="bar-pair"><span class="bar" style="height: 112px"></span><span class="bar target" style="height: 88px"></span></div>
                        <div class="bar-pair"><span class="bar" style="height: 138px"></span><span class="bar target" style="height: 118px"></span></div>
                        <div class="bar-pair"><span class="bar" style="height: 126px"></span><span class="bar target" style="height: 112px"></span></div>
                        <div class="bar-pair"><span class="bar" style="height: 148px"></span><span class="bar target" style="height: 132px"></span></div>
                    </div>
                    <div class="chart-labels">
                        <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>May</span><span>Jun</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="ops-panel span-2">
            <div class="panel-head">
                <h2 class="panel-title">Resident Activity</h2>
            </div>
            <div class="panel-body mini-list">
                <div class="mini-row"><span class="mini-label">Move In (This Month)</span><strong class="mini-value">12</strong><span class="spark"></span></div>
                <div class="mini-row"><span class="mini-label">Move Out (This Month)</span><strong class="mini-value">3</strong><span class="spark red"></span></div>
                <div class="mini-row"><span class="mini-label">New Registration</span><strong class="mini-value">25</strong><span class="spark green"></span></div>
                <div class="mini-row"><span class="mini-label">Active Residents</span><strong class="mini-value">1,176</strong><span class="spark"></span></div>
            </div>
        </section>

        <section class="ops-panel span-2">
            <div class="panel-head">
                <h2 class="panel-title">Latest Alerts</h2>
                <span class="muted">View All</span>
            </div>
            <div class="panel-body">
                <div class="alert-row"><span class="dot red"></span><div><div class="alert-title" style="color:#e04c4c">High Priority</div><div class="alert-desc">Fire Alarm Test<br>Tower A - Level 12</div></div><span class="alert-time">09:30</span></div>
                <div class="alert-row"><span class="dot gold"></span><div><div class="alert-title" style="color:#b87924">Medium Priority</div><div class="alert-desc">Elevator Maintenance<br>Tower B - Lift 2</div></div><span class="alert-time">08:15</span></div>
                <div class="alert-row"><span class="dot"></span><div><div class="alert-title" style="color:#1f7bef">Information</div><div class="alert-desc">Water Shutdown<br>08 Jun 2026, 10:00 - 14:00</div></div><span class="alert-time">Yesterday</span></div>
                <div class="alert-row"><span class="dot green"></span><div><div class="alert-title" style="color:#168d51">Resolved</div><div class="alert-desc">AC Maintenance<br>Unit A-1205</div></div><span class="alert-time">2 Days Ago</span></div>
            </div>
        </section>

        <section class="ops-panel span-3">
            <div class="panel-head">
                <h2 class="panel-title">Service Request Status</h2>
            </div>
            <div class="panel-body donut-row">
                <div class="donut" aria-hidden="true"></div>
                <div class="legend">
                    <div class="legend-row"><span class="dot red"></span><span>Open</span><strong>{{ $serviceSummary['open'] ?? 0 }}</strong></div>
                    <div class="legend-row"><span class="dot gold"></span><span>Assigned</span><strong>{{ $serviceSummary['assigned'] ?? 0 }}</strong></div>
                    <div class="legend-row"><span class="dot"></span><span>In Progress</span><strong>{{ $serviceSummary['in_progress'] ?? 0 }}</strong></div>
                    <div class="legend-row"><span class="dot green"></span><span>Resolved</span><strong>{{ $serviceSummary['resolved'] ?? 0 }}</strong></div>
                    <div class="legend-row"><span></span><span>Over SLA</span><strong>{{ $serviceSummary['over_sla'] ?? 0 }}</strong></div>
                </div>
            </div>
        </section>

        <section class="ops-panel span-3">
            <div class="panel-head">
                <h2 class="panel-title">Service Dispatch</h2>
                <span class="badge">Live</span>
            </div>
            <div class="panel-body metric-list">
                <div class="metric-row"><span class="dot"></span><span>Assigned Queue</span><strong>{{ $serviceSummary['assigned'] ?? 0 }}</strong></div>
                <div class="metric-row"><span class="dot gold"></span><span>In Progress</span><strong>{{ $serviceSummary['in_progress'] ?? 0 }}</strong></div>
                <div class="metric-row"><span class="dot red"></span><span>Over SLA</span><strong>{{ $serviceSummary['over_sla'] ?? 0 }}</strong></div>
                <div class="metric-row"><span class="dot green"></span><span>Completed</span><strong>{{ $serviceSummary['resolved'] ?? 0 }}</strong></div>
            </div>
        </section>

        <section class="ops-panel span-3">
            <div class="panel-head">
                <h2 class="panel-title">Facility Utilization</h2>
                <span class="badge">This Week</span>
            </div>
            <div class="panel-body progress-list">
                <div class="progress-row"><span>Gym</span><div class="progress-track"><div class="progress-fill" style="width:87%"></div></div><strong>87%</strong></div>
                <div class="progress-row"><span>Swimming Pool</span><div class="progress-track"><div class="progress-fill" style="width:76%"></div></div><strong>76%</strong></div>
                <div class="progress-row"><span>Meeting Room</span><div class="progress-track"><div class="progress-fill" style="width:63%"></div></div><strong>63%</strong></div>
                <div class="progress-row"><span>Sky Lounge</span><div class="progress-track"><div class="progress-fill" style="width:58%"></div></div><strong>58%</strong></div>
            </div>
        </section>

        <section class="ops-panel span-3">
            <div class="panel-head">
                <h2 class="panel-title">Package Center</h2>
                <span class="badge">Today</span>
            </div>
            <div class="panel-body metric-list">
                <div class="metric-row"><span class="dot gold"></span><span>Incoming Packages</span><strong>42</strong></div>
                <div class="metric-row"><span class="dot gold"></span><span>Waiting Pickup</span><strong>6</strong></div>
                <div class="metric-row"><span class="dot"></span><span>Collected Today</span><strong>36</strong></div>
                <div class="metric-row"><span class="dot green"></span><span>Total This Month</span><strong>208</strong></div>
            </div>
        </section>

        <section class="ops-panel span-3">
            <div class="panel-head">
                <h2 class="panel-title">Visitor Monitoring</h2>
                <span class="badge">Today</span>
            </div>
            <div class="panel-body metric-list">
                <div class="metric-row"><span class="dot"></span><span>Visitors Today</span><strong>127</strong></div>
                <div class="metric-row"><span class="dot green"></span><span>Checked In</span><strong style="color:#168d51">118</strong></div>
                <div class="metric-row"><span class="dot gold"></span><span>Pending Approval</span><strong style="color:#b87924">9</strong></div>
                <div class="metric-row"><span class="dot"></span><span>Expected Today</span><strong>32</strong></div>
            </div>
        </section>

        <section class="ops-panel dark-section span-8">
            <div class="panel-head">
                <h2 class="panel-title">Quick Summary</h2>
            </div>
            <div class="quick-summary">
                <div class="summary-cell"><span>Monthly Revenue</span><strong>Rp 2.63 M</strong><div class="trend-up">+ 8.2%</div></div>
                <div class="summary-cell"><span>Monthly Expense</span><strong>Rp 1.28 M</strong><div class="trend-down">- 2.1%</div></div>
                <div class="summary-cell"><span>Net Income</span><strong>Rp 1.35 M</strong><div class="trend-up">+ 12.4%</div></div>
                <div class="summary-cell"><span>Budget vs Actual</span><strong>84.6%</strong><div class="tiny-donut"></div></div>
                <div class="summary-cell"><span>Resident Satisfaction</span><strong>4.7 / 5.0</strong><div class="stars">*****</div></div>
                <div class="summary-cell"><span>System Health</span><strong>100%</strong><div class="trend-up">All Systems Operational</div></div>
            </div>
        </section>
        <section class="ops-panel span-4">
            <div class="panel-head">
                <h2 class="panel-title">Recent Activities</h2>
                <span class="muted">View All</span>
            </div>
            <div class="panel-body">
                @foreach ([
                    ['John Technical', 'Completed service request #SR-0621', '10 min ago'],
                    ['Reception Desk', 'New package received from Shopee', '25 min ago'],
                    ['Finance Staff', 'Payment received from Unit A-1808', '1 hour ago'],
                    ['Security Staff', 'Visitor John Doe has checked in', '1 hour ago'],
                    ['System', 'Backup completed successfully', '3 hours ago'],
                ] as [$name, $activity, $time])
                    <div class="alert-row">
                        <span class="avatar" style="width:28px;height:28px;flex-basis:28px;font-size:11px">{{ substr($name, 0, 1) }}</span>
                        <div><div class="alert-title">{{ $name }}</div><div class="alert-desc">{{ $activity }}</div></div>
                        <span class="alert-time">{{ $time }}</span>
                    </div>
                @endforeach

                @foreach (($recentServiceRequests ?? collect()) as $recentServiceRequest)
                    <div class="alert-row">
                        <span class="avatar" style="width:28px;height:28px;flex-basis:28px;font-size:11px">SR</span>
                        <div>
                            <div class="alert-title">{{ $recentServiceRequest->ticket_number }}</div>
                            <div class="alert-desc">{{ $recentServiceRequest->title }} · {{ $recentServiceRequest->priority }}</div>
                        </div>
                        <span class="alert-time">{{ $recentServiceRequest->created_at?->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
@endsection
