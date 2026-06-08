@extends('layouts.app')

@section('title', 'Vehicle Management')
@section('topbar_context', 'Resident Management Flow')
@section('topbar_subtitle', 'Vehicle registration, parking slot, and access permit monitoring')

@section('content')
    <div class="resident-page">
        @include('resident-management._flow', ['activeSteps' => [5]])

        <div class="resident-grid">
            <section class="resident-card res-span-4">
                <div class="resident-card-head">
                    <h2 class="resident-card-title">Vehicle Registration</h2>
                    <span class="badge yellow">Parking Desk</span>
                </div>
                <div class="resident-card-body">
                    <form class="resident-form">
                        <div class="field">
                            <label for="vehicle-owner">Resident</label>
                            <input id="vehicle-owner" type="text" value="Ahmad Rizky">
                        </div>
                        <div class="resident-mini-grid">
                            <div class="field">
                                <label for="vehicle-plate">Plate Number</label>
                                <input id="vehicle-plate" type="text" value="B 1808 AR">
                            </div>
                            <div class="field">
                                <label for="vehicle-type">Type</label>
                                <select id="vehicle-type"><option>Car</option><option>Motorcycle</option><option>Bicycle</option></select>
                            </div>
                        </div>
                        <button class="btn" type="button">Register Vehicle</button>
                    </form>
                </div>
            </section>

            <section class="resident-card res-span-4">
                <div class="resident-card-head">
                    <h2 class="resident-card-title">Parking Slots</h2>
                    <span class="badge green">2 Assigned</span>
                </div>
                <div class="resident-card-body">
                    <div class="resident-row"><div><strong>P2-A-081</strong><small>Primary car slot</small></div><span class="badge green">Active</span></div>
                    <div class="resident-row"><div><strong>P1-M-042</strong><small>Motorcycle slot</small></div><span class="badge green">Active</span></div>
                    <div class="resident-row"><div><strong>Visitor Bay</strong><small>Temporary request available</small></div><span class="badge">Optional</span></div>
                    <button class="btn secondary" type="button">Manage Slots</button>
                </div>
            </section>

            <section class="resident-card res-span-4">
                <div class="resident-card-head">
                    <h2 class="resident-card-title">Access & Permit</h2>
                    <span class="badge">Gate System</span>
                </div>
                <div class="resident-card-body">
                    <div class="status-line">RFID card active for main gate</div>
                    <div class="status-line">Parking permit valid until 31 Dec 2026</div>
                    <div class="status-line warn">Second vehicle permit review due next month</div>
                    <div class="resident-mini-grid">
                        <div class="resident-stat"><span>Active Vehicles</span><strong>2</strong></div>
                        <div class="resident-stat"><span>Pending Review</span><strong>1</strong></div>
                    </div>
                </div>
            </section>

            <section class="resident-card dark res-span-12">
                <div class="resident-benefits">
                    <div class="benefit-cell"><strong>Vehicle Registry</strong><span>Plate number and owner data are grouped per unit.</span></div>
                    <div class="benefit-cell"><strong>Slot Control</strong><span>Assigned slots are visible before parking staff confirms access.</span></div>
                    <div class="benefit-cell"><strong>Gate Permit</strong><span>RFID and permit status can be audited quickly.</span></div>
                    <div class="benefit-cell"><strong>Capacity View</strong><span>Parking usage can connect to tower occupancy later.</span></div>
                    <div class="benefit-cell"><strong>Resident Lifecycle</strong><span>Vehicle access follows move-in and move-out status.</span></div>
                </div>
            </section>
        </div>
    </div>
@endsection
