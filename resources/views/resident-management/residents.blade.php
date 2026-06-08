@extends('layouts.app')

@section('title', 'Resident Management')
@section('topbar_context', 'Resident Management Flow')
@section('topbar_subtitle', 'Complete Resident Lifecycle Management from Move-In to Move-Out')

@section('content')
    <div class="resident-page">
        @include('resident-management._flow', ['activeSteps' => [1, 4, 6, 8]])

        <div class="resident-grid">
            <section class="resident-card res-span-4">
                <div class="resident-card-head">
                    <h2 class="resident-card-title">Resident Registration</h2>
                    <span class="badge yellow">Front Office</span>
                </div>
                <div class="resident-card-body">
                    <form class="resident-form">
                        <div class="field">
                            <label for="resident-name">Full Name</label>
                            <input id="resident-name" type="text" value="Ahmad Rizky">
                        </div>
                        <div class="field">
                            <label for="resident-phone">Phone Number</label>
                            <input id="resident-phone" type="text" value="0812 3456 7890">
                        </div>
                        <div class="field">
                            <label for="resident-id">ID / Passport Number</label>
                            <input id="resident-id" type="text" value="3171 880690 0001">
                        </div>
                        <button class="btn" type="button">Register Resident</button>
                    </form>
                </div>
            </section>

            <section class="resident-card res-span-4">
                <div class="resident-card-head">
                    <h2 class="resident-card-title">Active Resident Profile</h2>
                    <span class="badge green">Active</span>
                </div>
                <div class="resident-card-body">
                    <div class="resident-row">
                        <div><strong>Ahmad Rizky</strong><small>Unit A-1808 - Tower A / 18</small></div>
                        <span class="badge green">Lease</span>
                    </div>
                    <div class="resident-mini-grid">
                        <div class="resident-stat"><span>Move-In Date</span><strong>07 Jun 2026</strong></div>
                        <div class="resident-stat"><span>Access Level</span><strong>Full</strong></div>
                        <div class="resident-stat"><span>Family Members</span><strong>3</strong></div>
                        <div class="resident-stat"><span>Vehicles</span><strong>2</strong></div>
                    </div>
                    <button class="btn secondary" type="button">View Profile</button>
                </div>
            </section>

            <section class="resident-card res-span-4">
                <div class="resident-card-head">
                    <h2 class="resident-card-title">Resident Monitoring</h2>
                    <span class="badge">Today</span>
                </div>
                <div class="resident-card-body">
                    <div class="resident-row">
                        <div><strong>Outstanding Bill</strong><small>Due on 20 Jun 2026</small></div>
                        <span>Rp 850.000</span>
                    </div>
                    <div class="resident-row">
                        <div><strong>Open Service Tickets</strong><small>AC maintenance and water pressure</small></div>
                        <span>2</span>
                    </div>
                    <div class="resident-row">
                        <div><strong>Visitors This Month</strong><small>Approved through front desk</small></div>
                        <span>12</span>
                    </div>
                    <div class="resident-row">
                        <div><strong>Packages Received</strong><small>Locker and desk pickup</small></div>
                        <span>5</span>
                    </div>
                </div>
            </section>

            <section class="resident-card res-span-4">
                <div class="resident-card-head">
                    <h2 class="resident-card-title">Resident History</h2>
                    <span class="badge">Audit Trail</span>
                </div>
                <div class="resident-card-body">
                    <div class="resident-timeline">
                        <div class="timeline-item"><strong>Move-In</strong><span>07 Jun 2026 - Unit access activated</span></div>
                        <div class="timeline-item"><strong>Payment History</strong><span>18 transactions recorded</span></div>
                        <div class="timeline-item"><strong>Visitor History</strong><span>12 visits approved</span></div>
                        <div class="timeline-item"><strong>Service Requests</strong><span>5 tickets completed</span></div>
                    </div>
                </div>
            </section>

            <section class="resident-card res-span-8">
                <div class="resident-card-head">
                    <h2 class="resident-card-title">Resident Data Overview</h2>
                    <span class="badge green">Centralized</span>
                </div>
                <div class="resident-card-body">
                    <div class="resident-visual">
                        <div class="resident-visual-label">
                            <span>Aether Residences</span>
                            <span>1,176 active residents</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="resident-card dark res-span-12">
                <div class="resident-benefits">
                    <div class="benefit-cell"><strong>Centralized Data</strong><span>Semua data penghuni tersimpan dan mudah dilacak.</span></div>
                    <div class="benefit-cell"><strong>Secure Access</strong><span>Akses layanan hanya diberikan untuk resident aktif.</span></div>
                    <div class="benefit-cell"><strong>Fast Monitoring</strong><span>Aktivitas, bill, request, dan paket terlihat ringkas.</span></div>
                    <div class="benefit-cell"><strong>Complete History</strong><span>Riwayat tetap tersedia untuk audit operasional.</span></div>
                    <div class="benefit-cell"><strong>Ready for Data</strong><span>Layout siap dihubungkan ke tabel resident nanti.</span></div>
                </div>
            </section>
        </div>
    </div>
@endsection
