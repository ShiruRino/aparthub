@extends('layouts.app')

@section('title', 'Move In / Out')
@section('topbar_context', 'Resident Management Flow')
@section('topbar_subtitle', 'Move-in approval and move-out settlement operations')

@section('content')
    <div class="resident-page">
        @include('resident-management._flow', ['activeSteps' => [3, 7]])

        <div class="resident-grid">
            <section class="resident-card res-span-4">
                <div class="resident-card-head">
                    <h2 class="resident-card-title">Move-In Approval</h2>
                    <span class="badge green">Ready</span>
                </div>
                <div class="resident-card-body">
                    <div class="resident-row"><div><strong>Document Verification</strong><small>ID, lease, and owner approval</small></div><span class="badge green">Verified</span></div>
                    <div class="resident-row"><div><strong>Lease Agreement</strong><small>Digital copy stored</small></div><span class="badge green">Verified</span></div>
                    <div class="resident-row"><div><strong>Deposit Payment</strong><small>Confirmed by finance</small></div><span class="badge green">Paid</span></div>
                    <div class="resident-row"><div><strong>Move-In Date</strong><small>07 Jun 2026</small></div><span class="badge">Set</span></div>
                    <button class="btn" type="button">Approve Move-In</button>
                </div>
            </section>

            <section class="resident-card res-span-4">
                <div class="resident-card-head">
                    <h2 class="resident-card-title">Move-Out Process</h2>
                    <span class="badge yellow">In Progress</span>
                </div>
                <div class="resident-card-body">
                    <form class="resident-form">
                        <div class="field">
                            <label for="moveout-date">Move-Out Date</label>
                            <input id="moveout-date" type="text" value="20 Jun 2026">
                        </div>
                        <div class="status-line">Unit inspection completed</div>
                        <div class="status-line warn">Deposit settlement waiting finance</div>
                        <div class="status-line">Access revocation scheduled</div>
                        <div class="field">
                            <label for="moveout-note">Notes</label>
                            <textarea id="moveout-note">Unit in good condition</textarea>
                        </div>
                        <button class="btn" type="button">Complete Move-Out</button>
                    </form>
                </div>
            </section>

            <section class="resident-card res-span-4">
                <div class="resident-card-head">
                    <h2 class="resident-card-title">Settlement Snapshot</h2>
                    <span class="badge">Finance</span>
                </div>
                <div class="resident-card-body">
                    <div class="resident-mini-grid">
                        <div class="resident-stat"><span>Deposit</span><strong>Rp 8.5 M</strong></div>
                        <div class="resident-stat"><span>Deductions</span><strong>Rp 0</strong></div>
                        <div class="resident-stat"><span>Open Bills</span><strong>Rp 850 K</strong></div>
                        <div class="resident-stat"><span>Refund ETA</span><strong>3 days</strong></div>
                    </div>
                    <div class="resident-timeline">
                        <div class="timeline-item"><strong>Inspection</strong><span>Building manager approved unit condition</span></div>
                        <div class="timeline-item"><strong>Refund Review</strong><span>Finance staff verifies billing settlement</span></div>
                        <div class="timeline-item"><strong>Access Close</strong><span>System removes service and parking access</span></div>
                    </div>
                </div>
            </section>

            <section class="resident-card dark res-span-12">
                <div class="resident-benefits">
                    <div class="benefit-cell"><strong>Controlled Move-In</strong><span>Approval only happens after required documents are complete.</span></div>
                    <div class="benefit-cell"><strong>Inspection Trail</strong><span>Move-out checklist keeps unit handover accountable.</span></div>
                    <div class="benefit-cell"><strong>Finance Sync</strong><span>Deposit and outstanding bills are visible together.</span></div>
                    <div class="benefit-cell"><strong>Access Safety</strong><span>Resident access can be revoked after completion.</span></div>
                    <div class="benefit-cell"><strong>Lifecycle Close</strong><span>History remains available after resident leaves.</span></div>
                </div>
            </section>
        </div>
    </div>
@endsection
