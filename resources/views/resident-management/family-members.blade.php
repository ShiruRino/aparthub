@extends('layouts.app')

@section('title', 'Family Member')
@section('topbar_context', 'Resident Management Flow')
@section('topbar_subtitle', 'Family member registration and household relationship control')

@section('content')
    <div class="resident-page">
        @include('resident-management._flow', ['activeSteps' => [5]])

        <div class="resident-grid">
            <section class="resident-card res-span-4">
                <div class="resident-card-head">
                    <h2 class="resident-card-title">Family Members</h2>
                    <span class="badge green">3 Active</span>
                </div>
                <div class="resident-card-body">
                    <div class="resident-row"><div><strong>Sarah Lim</strong><small>Spouse - access active</small></div><span class="badge green">Verified</span></div>
                    <div class="resident-row"><div><strong>Alya Rizky</strong><small>Child - school pickup access</small></div><span class="badge green">Verified</span></div>
                    <div class="resident-row"><div><strong>Adam Rizky</strong><small>Child - dependent profile</small></div><span class="badge yellow">Review</span></div>
                    <button class="btn secondary" type="button">Open Household</button>
                </div>
            </section>

            <section class="resident-card res-span-4">
                <div class="resident-card-head">
                    <h2 class="resident-card-title">Add Family Member</h2>
                    <span class="badge yellow">Front Office</span>
                </div>
                <div class="resident-card-body">
                    <form class="resident-form">
                        <div class="field">
                            <label for="family-name">Full Name</label>
                            <input id="family-name" type="text" value="Nadia Rizky">
                        </div>
                        <div class="resident-mini-grid">
                            <div class="field">
                                <label for="family-relation">Relationship</label>
                                <select id="family-relation"><option>Spouse</option><option>Child</option><option>Parent</option></select>
                            </div>
                            <div class="field">
                                <label for="family-access">Access</label>
                                <select id="family-access"><option>Resident App</option><option>Visitor Only</option><option>No Access</option></select>
                            </div>
                        </div>
                        <button class="btn" type="button">Add Family Member</button>
                    </form>
                </div>
            </section>

            <section class="resident-card res-span-4">
                <div class="resident-card-head">
                    <h2 class="resident-card-title">Document Status</h2>
                    <span class="badge">Compliance</span>
                </div>
                <div class="resident-card-body">
                    <div class="status-line">Family card uploaded</div>
                    <div class="status-line">ID document verified</div>
                    <div class="status-line warn">Child profile needs guardian confirmation</div>
                    <div class="resident-mini-grid">
                        <div class="resident-stat"><span>Profiles</span><strong>4</strong></div>
                        <div class="resident-stat"><span>App Access</span><strong>2</strong></div>
                    </div>
                </div>
            </section>

            <section class="resident-card dark res-span-12">
                <div class="resident-benefits">
                    <div class="benefit-cell"><strong>Household View</strong><span>All related profiles stay connected to one primary resident.</span></div>
                    <div class="benefit-cell"><strong>Access Control</strong><span>Family access can be set separately from resident access.</span></div>
                    <div class="benefit-cell"><strong>Verification</strong><span>Document status is visible before access is granted.</span></div>
                    <div class="benefit-cell"><strong>Service Context</strong><span>Front office can identify family members quickly.</span></div>
                    <div class="benefit-cell"><strong>Future Ready</strong><span>Dummy list can become a real family table later.</span></div>
                </div>
            </section>
        </div>
    </div>
@endsection
