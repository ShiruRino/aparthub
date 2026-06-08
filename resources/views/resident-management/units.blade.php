@extends('layouts.app')

@section('title', 'Unit Management')
@section('topbar_context', 'Resident Management Flow')
@section('topbar_subtitle', 'Unit assignment, availability, and occupancy control')

@section('content')
    <div class="resident-page">
        @include('resident-management._flow', ['activeSteps' => [2]])

        <div class="resident-grid">
            <section class="resident-card res-span-4">
                <div class="resident-card-head">
                    <h2 class="resident-card-title">Unit Assignment</h2>
                    <span class="badge yellow">Step 2</span>
                </div>
                <div class="resident-card-body">
                    <form class="resident-form">
                        <div class="field">
                            <label for="unit-resident">Resident</label>
                            <input id="unit-resident" type="text" value="Ahmad Rizky">
                        </div>
                        <div class="resident-mini-grid">
                            <div class="field">
                                <label for="unit-tower">Tower</label>
                                <select id="unit-tower"><option>Tower A</option><option>Tower B</option><option>Tower C</option></select>
                            </div>
                            <div class="field">
                                <label for="unit-floor">Floor</label>
                                <select id="unit-floor"><option>18</option><option>19</option><option>20</option></select>
                            </div>
                        </div>
                        <div class="field">
                            <label for="unit-number">Unit Number</label>
                            <input id="unit-number" type="text" value="A-1808">
                        </div>
                        <button class="btn" type="button">Assign Unit</button>
                    </form>
                </div>
            </section>

            <section class="resident-card res-span-4">
                <div class="resident-card-head">
                    <h2 class="resident-card-title">Availability Summary</h2>
                    <span class="badge green">102 Vacant</span>
                </div>
                <div class="resident-card-body">
                    <div class="resident-mini-grid">
                        <div class="resident-stat"><span>Tower A</span><strong>612</strong></div>
                        <div class="resident-stat"><span>Tower B</span><strong>426</strong></div>
                        <div class="resident-stat"><span>Tower C</span><strong>216</strong></div>
                        <div class="resident-stat"><span>Maintenance</span><strong>18</strong></div>
                    </div>
                    <div class="status-line">A-1808 is ready for owner handover</div>
                    <div class="status-line warn">B-0902 waiting for final cleaning</div>
                    <div class="status-line danger">C-1205 blocked by maintenance</div>
                </div>
            </section>

            <section class="resident-card res-span-4">
                <div class="resident-card-head">
                    <h2 class="resident-card-title">Waiting Assignment</h2>
                    <span class="badge">Queue</span>
                </div>
                <div class="resident-card-body">
                    <div class="resident-row"><div><strong>Sarah Lim</strong><small>2 BR Premium - Tower A/B</small></div><span>Today</span></div>
                    <div class="resident-row"><div><strong>Budi Santoso</strong><small>Studio - high floor</small></div><span>Today</span></div>
                    <div class="resident-row"><div><strong>Maya Putri</strong><small>3 BR - family occupancy</small></div><span>Mon</span></div>
                    <button class="btn secondary" type="button">Review Queue</button>
                </div>
            </section>

            <section class="resident-card dark res-span-12">
                <div class="resident-benefits">
                    <div class="benefit-cell"><strong>Unit Inventory</strong><span>Total, occupied, vacant, and maintenance status in one place.</span></div>
                    <div class="benefit-cell"><strong>Owner / Lease Link</strong><span>Resident profile can be linked to the correct owner or lease.</span></div>
                    <div class="benefit-cell"><strong>Faster Handover</strong><span>Front office can assign units without jumping screens.</span></div>
                    <div class="benefit-cell"><strong>Occupancy Control</strong><span>Availability snapshot helps reduce double assignment.</span></div>
                    <div class="benefit-cell"><strong>Static for Now</strong><span>Data is dummy and ready for future unit tables.</span></div>
                </div>
            </section>
        </div>
    </div>
@endsection
