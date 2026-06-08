@extends('layouts.app')

@php
    $pageKey = $pageKey ?? 'directory';

    $pages = [
        'directory' => [
            'label' => 'Tenant Directory',
            'title' => 'Tenant Directory',
            'subtitle' => 'Pemantauan seluruh tenant yang terdaftar di marketplace.',
        ],
        'add-input' => [
            'label' => 'Add New Tenant',
            'title' => 'Add New Tenant',
            'subtitle' => 'Tenant Directory > Add New Tenant',
        ],
    ];

    $page = $pages[$pageKey] ?? $pages['directory'];

    $metrics = [
        ['label' => 'Total Tenants', 'value' => '48', 'sub' => 'Tenant Aktif', 'icon' => 'SHOP', 'tone' => 'blue'],
        ['label' => 'Active Tenants', 'value' => '42', 'sub' => 'Tenant', 'icon' => 'OK', 'tone' => 'green'],
        ['label' => 'Pending Approval', 'value' => '4', 'sub' => 'Tenant', 'icon' => 'WAIT', 'tone' => 'gold'],
        ['label' => 'Inactive Tenants', 'value' => '2', 'sub' => 'Tenant', 'icon' => 'OFF', 'tone' => 'red'],
        ['label' => 'Total Categories', 'value' => '9', 'sub' => 'Kategori', 'icon' => 'CAT', 'tone' => 'purple'],
    ];

    $tenants = [
        ['name' => 'Brew Cabin Coffee', 'desc' => 'Cafe & Beverages', 'category' => 'Cafe', 'tower' => 'Tower A', 'location' => 'GF-01', 'status' => 'Active', 'class' => 'status-approved', 'added' => '12 May 2026', 'activity' => "07 Jun 2026\n10:25 AM", 'logo' => 'BREW', 'tone' => 'black'],
        ['name' => 'Fresh Laundry', 'desc' => 'Laundry', 'category' => 'Laundry', 'tower' => 'Tower B', 'location' => 'LG-07', 'status' => 'Active', 'class' => 'status-approved', 'added' => '15 May 2026', 'activity' => "07 Jun 2026\n09:15 AM", 'logo' => 'FRESH', 'tone' => 'blue'],
        ['name' => 'GreenMart', 'desc' => 'Minimarket', 'category' => 'Minimarket', 'tower' => 'Tower A', 'location' => 'GF-03', 'status' => 'Active', 'class' => 'status-approved', 'added' => '18 May 2026', 'activity' => "07 Jun 2026\n08:45 AM", 'logo' => 'MART', 'tone' => 'green'],
        ['name' => 'CleanPro Service', 'desc' => 'Cleaning Service', 'category' => 'Cleaning', 'tower' => 'Tower C', 'location' => 'LG-02', 'status' => 'Active', 'class' => 'status-approved', 'added' => '20 May 2026', 'activity' => "07 Jun 2026\n11:10 AM", 'logo' => 'CLEAN', 'tone' => 'teal'],
        ['name' => 'Bloom Beauty Studio', 'desc' => 'Beauty & Salon', 'category' => 'Beauty', 'tower' => 'Tower B', 'location' => 'GF-05', 'status' => 'Active', 'class' => 'status-approved', 'added' => '25 May 2026', 'activity' => "07 Jun 2026\n10:05 AM", 'logo' => 'BLOOM', 'tone' => 'pink'],
        ['name' => 'PawCare Clinic', 'desc' => 'Pet Care', 'category' => 'Pet Care', 'tower' => 'Tower C', 'location' => 'GF-04', 'status' => 'Pending', 'class' => 'status-pending', 'added' => '02 Jun 2026', 'activity' => '-', 'logo' => 'PAW', 'tone' => 'gold'],
        ['name' => 'Quick Bites', 'desc' => 'Food & Beverages', 'category' => 'Food', 'tower' => 'Tower A', 'location' => 'GF-06', 'status' => 'Active', 'class' => 'status-approved', 'added' => '03 Jun 2026', 'activity' => "07 Jun 2026\n09:30 AM", 'logo' => 'BITES', 'tone' => 'black'],
        ['name' => 'Repair Plus', 'desc' => 'Maintenance', 'category' => 'Maintenance', 'tower' => 'Tower D', 'location' => 'LG-01', 'status' => 'Inactive', 'class' => 'status-rejected', 'added' => '10 Apr 2026', 'activity' => "25 May 2026\n02:20 PM", 'logo' => 'PLUS', 'tone' => 'teal'],
    ];

    $inputFields = [
        'basic' => [
            ['Business Trade Name', 'Enter Trade Name', 'text'],
            ['Legal Business Name', 'Text Input', 'text'],
            ['Tenant Category', 'select...', 'select'],
            ['Tenant Category', 'Cafe & Beverages', 'select'],
            ['Target Location', 'Tower / Lot/Shop', 'location'],
            ['Estimated Move-In Date', 'Date Picker', 'date'],
        ],
        'contact' => [
            ['Primary Contact Person Full Name', 'Text Input', 'text'],
            ['Designation/Title', 'Text Input', 'text'],
            ['Direct Email Address', 'Direct Email Address', 'email'],
            ['Direct Email Address', 'Enter Input', 'email'],
            ['Cell Number', 'Cell Input', 'phone'],
            ['Office Business Phone', 'Text Input', 'text'],
        ],
        'legal' => [
            ['NIB (Nomor Induk Berusaha) Number', 'Text Input', 'text'],
            ['NPWP (Tax ID) Number', 'Text Input', 'text'],
        ],
    ];
@endphp

@section('title', $page['label'])
@section('topbar_context')
    Tenant Marketplace > {{ $page['label'] }}
@endsection
@section('topbar_subtitle', $pageKey === 'directory' ? 'Tenant Marketplace > Tenant Directory' : 'Tenant Marketplace > Add New Tenant')

@section('content')
    <div class="tenant-page">
        @if ($pageKey === 'directory')
            <div class="tenant-workspace">
                <main class="tenant-main">
                    <section class="tenant-titlebar">
                        <div>
                            <h2>Tenant Directory</h2>
                            <p>{{ $page['subtitle'] }}</p>
                        </div>
                        <div class="tenant-actions">
                            <a class="btn" href="{{ route('tenant-marketplace.add-input') }}">+ Add / Input Tenant</a>
                            <button class="btn secondary" type="button" data-modal-open="tenant-action-modal">Export</button>
                        </div>
                    </section>

                    <section class="tenant-metrics" aria-label="Tenant metrics">
                        @foreach ($metrics as $metric)
                            <article class="tenant-metric {{ $metric['tone'] }}">
                                <span class="tenant-metric-icon">{{ $metric['icon'] }}</span>
                                <div>
                                    <span>{{ $metric['label'] }}</span>
                                    <strong>{{ $metric['value'] }}</strong>
                                    <span>{{ $metric['sub'] }}</span>
                                </div>
                            </article>
                        @endforeach
                    </section>

                    <section class="visitor-panel">
                        <div class="tenant-filter-bar">
                            <input type="search" placeholder="Search Tenant..." aria-label="Search tenant">
                            <select aria-label="Tenant status"><option>All Status</option></select>
                            <select aria-label="Tenant category"><option>All Category</option></select>
                            <select aria-label="Tower"><option>All Tower</option></select>
                            <button class="btn secondary" type="button">Filter</button>
                        </div>

                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Tenant</th>
                                        <th>Category</th>
                                        <th>Tower / Location</th>
                                        <th>Status</th>
                                        <th>Date Added</th>
                                        <th>Last Activity</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tenants as $tenant)
                                        <tr>
                                            <td>
                                                <div style="display:grid;grid-template-columns:46px minmax(0,1fr);gap:12px;align-items:center;">
                                                    <span class="tenant-logo {{ $tenant['tone'] }}">{{ $tenant['logo'] }}</span>
                                                    <span class="tenant-name">
                                                        <strong>{{ $tenant['name'] }}</strong>
                                                        <span>{{ $tenant['desc'] }}</span>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>{{ $tenant['category'] }}</td>
                                            <td>{{ $tenant['tower'] }}<br><span class="muted">{{ $tenant['location'] }}</span></td>
                                            <td><span class="{{ $tenant['class'] }}" style="display:inline-block;padding:5px 10px;border-radius:7px;">{{ $tenant['status'] }}</span></td>
                                            <td>{{ $tenant['added'] }}</td>
                                            <td>{!! nl2br(e($tenant['activity'])) !!}</td>
                                            <td>
                                                <div class="visitor-action-buttons">
                                                    <button class="btn compact secondary" type="button" data-modal-open="tenant-action-modal">View</button>
                                                    <button class="community-icon-btn" type="button" data-modal-open="tenant-action-modal">:</button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="resident-pagination">
                            <span>Showing 1 to 8 of 48 tenants</span>
                            <span class="resident-page-btn">&lt;</span>
                            <span class="resident-page-btn active">1</span>
                            <span class="resident-page-btn">2</span>
                            <span class="resident-page-btn">3</span>
                            <span class="resident-page-btn">4</span>
                            <span class="resident-page-btn">5</span>
                            <span class="resident-page-btn">&gt;</span>
                            <select aria-label="Rows per page"><option>10 / page</option></select>
                        </div>
                    </section>
                </main>

                @include('tenant-marketplace.partials.side-widgets')
            </div>
        @else
            <div class="tenant-workspace">
                <main class="tenant-main">
                    <section class="tenant-titlebar">
                        <div>
                            <h2>Add New Tenant</h2>
                            <p>Tenant Directory > Add New Tenant</p>
                        </div>
                    </section>

                    <section class="visitor-panel">
                        <form class="tenant-form-panel" action="#" method="POST">
                            <section class="tenant-form-section">
                                <h3>1. Basic Business Profile</h3>
                                <div class="tenant-form-grid">
                                    @foreach ($inputFields['basic'] as [$label, $placeholder, $type])
                                        <label>
                                            <span class="field-label">{{ $label }}</span>
                                            @if ($type === 'select')
                                                <select><option>{{ $placeholder }}</option></select>
                                            @elseif ($type === 'date')
                                                <input type="text" value="{{ $placeholder }}">
                                            @elseif ($type === 'location')
                                                <div class="tenant-location-grid">
                                                    <select><option>Tower</option></select>
                                                    <input type="text" placeholder="Lot/Shop">
                                                    <button class="btn compact secondary" type="button">Pin</button>
                                                </div>
                                            @else
                                                <input type="text" placeholder="{{ $placeholder }}">
                                            @endif
                                        </label>
                                    @endforeach
                                </div>
                            </section>

                            <section class="tenant-form-section">
                                <h3>2. Contact Information</h3>
                                <div class="tenant-form-grid">
                                    @foreach ($inputFields['contact'] as [$label, $placeholder, $type])
                                        <label>
                                            <span class="field-label">{{ $label }}</span>
                                            <input type="{{ $type === 'email' ? 'email' : 'text' }}" placeholder="{{ $placeholder }}">
                                        </label>
                                    @endforeach
                                </div>
                            </section>

                            <section class="tenant-form-section">
                                <h3>3. Business Registration & Legal Documents</h3>
                                <div class="tenant-form-grid two">
                                    @foreach ($inputFields['legal'] as [$label, $placeholder])
                                        <label>
                                            <span class="field-label">{{ $label }}</span>
                                            <input type="text" placeholder="{{ $placeholder }}">
                                        </label>
                                    @endforeach
                                </div>
                                <span class="field-label">Modern File upload for Business Documents</span>
                                <div class="tenant-form-grid two">
                                    <button class="tenant-upload" type="button">Upload NIB <span>file</span></button>
                                    <button class="tenant-upload" type="button">Upload Akta Pendirian <span>file</span></button>
                                </div>
                            </section>

                            <section class="tenant-form-section">
                                <h3>4. Visuals & Branding</h3>
                                <div class="tenant-form-grid two">
                                    <label>
                                        <span class="field-label">Upload Tenant Logo</span>
                                        <button class="tenant-upload" type="button">Upload Image <span>image</span></button>
                                    </label>
                                    <label>
                                        <span class="field-label">Upload Storefront Photo</span>
                                        <button class="tenant-upload" type="button">Upload Image <span>image</span></button>
                                    </label>
                                </div>
                            </section>
                        </form>

                        <div class="tenant-form-footer">
                            <a class="btn secondary" href="{{ route('tenant-marketplace.directory') }}">Cancel</a>
                            <div class="tenant-actions">
                                <button class="btn secondary" type="button" data-modal-open="tenant-action-modal">Save Draft</button>
                                <button class="btn" type="button" data-modal-open="tenant-action-modal">Save & Submit Tenant</button>
                            </div>
                        </div>
                    </section>
                </main>

                @include('tenant-marketplace.partials.side-widgets')
            </div>
        @endif

        @include('tenant-marketplace.partials.action-modal')
    </div>
@endsection
