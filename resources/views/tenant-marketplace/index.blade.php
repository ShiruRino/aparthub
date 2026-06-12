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
                            <button
                                class="btn secondary"
                                type="button"
                                data-modal-open="tenant-action-modal"
                                data-modal-title="Export Tenant Directory"
                                data-modal-headline="Tenant Directory Export"
                                data-modal-summary="Export Excel masih berupa preview dummy, tetapi alur aksinya sudah memakai popup yang konsisten."
                                data-modal-section-title="Export Context"
                                data-modal-workspace="Tenant Directory"
                                data-modal-entity="48 tenant aktif dan pending"
                                data-modal-status="Export Excel"
                                data-modal-next-step="Pilih format export dan lanjutkan unduhan saat backend sudah tersedia."
                                data-modal-copy="Belum ada file XLSX yang dihasilkan. Popup ini dipakai untuk menjaga pola interaksi header action tetap konsisten."
                                data-modal-confirm-label="Preview Export"
                                data-modal-accent="blue"
                            >
                                Export
                            </button>
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
                                                    @include('partials.icon-action-button', [
                                                        'label' => 'View Tenant Detail',
                                                        'icon' => 'eye',
                                                        'modal' => 'tenant-action-modal',
                                                        'data' => [
                                                            'data-modal-title' => 'Tenant Detail',
                                                            'data-modal-headline' => $tenant['name'],
                                                            'data-modal-summary' => 'Ringkasan tenant marketplace ditampilkan sebagai popup, bukan detail yang menempel di table workspace.',
                                                            'data-modal-section-title' => 'Tenant Profile',
                                                            'data-modal-workspace' => 'Tenant Directory',
                                                            'data-modal-entity' => $tenant['category'] . ' - ' . $tenant['tower'] . ' ' . $tenant['location'],
                                                            'data-modal-status' => $tenant['status'],
                                                            'data-modal-next-step' => 'Tinjau aktivitas tenant, status approval, dan lokasi unit sebelum aksi lanjutan.',
                                                            'data-modal-copy' => $tenant['desc'] . ' pada ' . $tenant['tower'] . ' ' . $tenant['location'] . '. Data masih dummy preview.',
                                                            'data-modal-confirm-label' => 'Close Preview',
                                                            'data-modal-accent' => $tenant['tone'],
                                                        ],
                                                    ])
                                                    @include('partials.icon-action-button', [
                                                        'label' => 'Edit Tenant Entry',
                                                        'icon' => 'edit',
                                                        'modal' => 'tenant-action-modal',
                                                        'variant' => 'gold',
                                                        'data' => [
                                                            'data-modal-title' => 'Edit Tenant Entry',
                                                            'data-modal-headline' => 'Update ' . $tenant['name'],
                                                            'data-modal-summary' => 'Form edit tenant dibuka sebagai popup dummy agar workspace tetap compact.',
                                                            'data-modal-section-title' => 'Edit Context',
                                                            'data-modal-workspace' => 'Tenant Directory',
                                                            'data-modal-entity' => $tenant['name'],
                                                            'data-modal-status' => 'Edit Draft',
                                                            'data-modal-next-step' => 'Perbarui kategori, lokasi, dan status tenant saat backend form sudah diaktifkan.',
                                                            'data-modal-copy' => 'Belum ada submit backend. Tombol ini hanya menampilkan preview alur edit tenant.',
                                                            'data-modal-confirm-label' => 'Open Edit Preview',
                                                            'data-modal-accent' => $tenant['tone'],
                                                        ],
                                                    ])
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
                                                    @include('partials.icon-action-button', [
                                                        'label' => 'Pin Tenant Location',
                                                        'icon' => 'pin',
                                                        'modal' => 'tenant-action-modal',
                                                        'variant' => 'info',
                                                        'data' => [
                                                            'data-modal-title' => 'Pin Tenant Location',
                                                            'data-modal-headline' => 'Target Location Preview',
                                                            'data-modal-summary' => 'Penentuan tower dan lot tenant dibuka sebagai popup dummy agar tidak terasa kaku.',
                                                            'data-modal-section-title' => 'Location Context',
                                                            'data-modal-workspace' => 'Add New Tenant',
                                                            'data-modal-entity' => 'Tower / Lot / Shop',
                                                            'data-modal-status' => 'Location Pin',
                                                            'data-modal-next-step' => 'Pilih koordinat lokasi tenant setelah integrasi map aktif.',
                                                            'data-modal-copy' => 'Saat ini pin lokasi hanya berupa preview UI tanpa penyimpanan peta atau koordinat.',
                                                            'data-modal-confirm-label' => 'Use Location Preview',
                                                            'data-modal-accent' => 'blue',
                                                        ],
                                                    ])
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
                                    <button class="tenant-upload" type="button" data-modal-open="tenant-action-modal" data-modal-title="Upload NIB" data-modal-headline="Business Document Upload" data-modal-summary="Upload dokumen tenant masih dummy preview." data-modal-section-title="Document Context" data-modal-workspace="Add New Tenant" data-modal-entity="NIB / Business License" data-modal-status="Document Upload" data-modal-next-step="Siapkan file legal tenant untuk diunggah saat backend aktif." data-modal-copy="Belum ada penyimpanan file. Popup ini menjaga pola upload tetap konsisten dengan modal action lain." data-modal-confirm-label="Preview Upload" data-modal-accent="gold">Upload NIB <span>file</span></button>
                                    <button class="tenant-upload" type="button" data-modal-open="tenant-action-modal" data-modal-title="Upload Akta Pendirian" data-modal-headline="Legal Document Upload" data-modal-summary="Upload akta pendirian masih berupa popup preview." data-modal-section-title="Document Context" data-modal-workspace="Add New Tenant" data-modal-entity="Akta Pendirian Tenant" data-modal-status="Document Upload" data-modal-next-step="Lanjutkan upload legal document setelah storage backend tersedia." data-modal-copy="Belum ada penyimpanan file atau validasi dokumen nyata." data-modal-confirm-label="Preview Upload" data-modal-accent="gold">Upload Akta Pendirian <span>file</span></button>
                                </div>
                            </section>

                            <section class="tenant-form-section">
                                <h3>4. Visuals & Branding</h3>
                                <div class="tenant-form-grid two">
                                    <label>
                                        <span class="field-label">Upload Tenant Logo</span>
                                        <button class="tenant-upload" type="button" data-modal-open="tenant-action-modal" data-modal-title="Upload Tenant Logo" data-modal-headline="Branding Asset Preview" data-modal-summary="Upload logo tenant dibuka sebagai popup dummy." data-modal-section-title="Branding Context" data-modal-workspace="Add New Tenant" data-modal-entity="Tenant Logo" data-modal-status="Brand Asset" data-modal-next-step="Pilih file logo resolusi tinggi saat upload backend sudah aktif." data-modal-copy="Belum ada penyimpanan image. Saat ini hanya preview alur branding asset." data-modal-confirm-label="Preview Upload" data-modal-accent="blue">Upload Image <span>image</span></button>
                                    </label>
                                    <label>
                                        <span class="field-label">Upload Storefront Photo</span>
                                        <button class="tenant-upload" type="button" data-modal-open="tenant-action-modal" data-modal-title="Upload Storefront Photo" data-modal-headline="Storefront Visual Preview" data-modal-summary="Upload storefront tenant masih berupa popup preview." data-modal-section-title="Branding Context" data-modal-workspace="Add New Tenant" data-modal-entity="Storefront Photo" data-modal-status="Brand Asset" data-modal-next-step="Tambahkan foto facade tenant setelah media upload backend aktif." data-modal-copy="Belum ada penyimpanan image atau validasi ukuran file." data-modal-confirm-label="Preview Upload" data-modal-accent="blue">Upload Image <span>image</span></button>
                                    </label>
                                </div>
                            </section>
                        </form>

                        <div class="tenant-form-footer">
                            <a class="btn secondary" href="{{ route('tenant-marketplace.directory') }}">Cancel</a>
                            <div class="tenant-actions">
                                <button class="btn secondary" type="button" data-modal-open="tenant-action-modal" data-modal-title="Save Tenant Draft" data-modal-headline="Draft Tenant Entry" data-modal-summary="Draft tenant disimpan sebagai preview dummy agar alur form tetap terasa rapi." data-modal-section-title="Draft Context" data-modal-workspace="Add New Tenant" data-modal-entity="Draft Business Profile" data-modal-status="Save Draft" data-modal-next-step="Lanjutkan pengisian profil tenant sebelum submit final." data-modal-copy="Belum ada penyimpanan draft ke database. Popup ini hanya memberi feedback interaksi." data-modal-confirm-label="Close Draft Preview" data-modal-accent="gold">Save Draft</button>
                                <button class="btn" type="button" data-modal-open="tenant-action-modal" data-modal-title="Save & Submit Tenant" data-modal-headline="Submit Tenant Onboarding" data-modal-summary="Submit tenant dibuka sebagai popup preview agar tidak langsung terasa seperti submit backend sungguhan." data-modal-section-title="Submission Context" data-modal-workspace="Add New Tenant" data-modal-entity="New Tenant Onboarding" data-modal-status="Ready to Submit" data-modal-next-step="Validasi profil bisnis, legal document, dan branding sebelum submit final nanti." data-modal-copy="Belum ada create tenant nyata. Tombol ini menjaga UX submit tetap jelas dan smooth." data-modal-confirm-label="Confirm Submission Preview" data-modal-accent="green">Save & Submit Tenant</button>
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
