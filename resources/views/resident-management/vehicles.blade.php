@extends('layouts.app')

@php
    $rows = [
        ['no' => 1, 'plate' => 'Plat B 1234 ABC', 'unit' => 'Unit A-1808', 'kind' => 'Ikon Mobil', 'owner' => 'Ahmad Rizky', 'model' => 'Toyota Fortuner', 'status' => 'Aktif', 'statusClass' => 'active', 'icon' => 'M5 16h14M7 16l1-5h8l1 5M7 16v2M17 16v2M6 18h.01M18 18h.01M9 11l1.2-3h3.6L15 11'],
        ['no' => 2, 'plate' => 'Plat B 5678 DEF', 'unit' => 'Unit A-1808', 'kind' => 'Ikon Mobil', 'owner' => 'Sarah Lim', 'model' => 'Honda HR-V', 'status' => 'Aktif', 'statusClass' => 'active', 'icon' => 'M5 16h14M7 16l1-5h8l1 5M7 16v2M17 16v2M6 18h.01M18 18h.01M9 11l1.2-3h3.6L15 11'],
        ['no' => 3, 'plate' => 'Plat B 9012 GHI', 'unit' => 'Unit B-2001', 'kind' => 'Ikon Mobil', 'owner' => 'John Doe', 'model' => 'BMW 3 Series', 'status' => 'Aktif', 'statusClass' => 'active', 'icon' => 'M5 16h14M7 16l1-5h8l1 5M7 16v2M17 16v2M6 18h.01M18 18h.01M9 11l1.2-3h3.6L15 11'],
        ['no' => 4, 'plate' => 'Plat B 3456 JKL', 'unit' => 'Unit A-2002', 'kind' => 'Ikon Mobil', 'owner' => 'Mark Wang', 'model' => 'Nissan X-Trail', 'status' => 'Aktif', 'statusClass' => 'active', 'icon' => 'M5 16h14M7 16l1-5h8l1 5M7 16v2M17 16v2M6 18h.01M18 18h.01M9 11l1.2-3h3.6L15 11'],
        ['no' => 5, 'plate' => 'Plat B 7890 MNOP', 'unit' => 'Unit A-0503', 'kind' => 'Ikon Motor', 'owner' => '[Contoh Nama]', 'model' => 'Yamaha NMax', 'status' => 'Menunggu Approval', 'statusClass' => 'pending', 'icon' => 'M5 16h8l3-5h3M8 16a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM21 16a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM10 11h4l2 5M13 8h3'],
    ];
@endphp

@section('title', 'Vehicle Management')
@section('topbar_context', 'Resident Management Flow')
@section('topbar_subtitle', 'Complete Resident Lifecycle Management from Move-In to Move-Out')

@section('content')
    <div class="resident-list-page">
        <header class="resident-page-head">
            <h2>Daftar Kendaraan Penghuni Aether Residences</h2>
            <button class="btn" type="button" data-modal-open="resident-vehicles-modal">Tambah Kendaraan Baru</button>
        </header>

        <section class="resident-filter-panel" aria-label="Filter kendaraan penghuni">
            <div class="resident-filter-field">
                <label for="vehicle-search">Search</label>
                <div class="resident-search">
                    <input id="vehicle-search" type="search" placeholder="Search">
                    <button type="button" aria-label="Search">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                    </button>
                </div>
            </div>
            <div class="resident-filter-field"><label for="vehicle-tower">Tower</label><select id="vehicle-tower"><option>Tower A</option><option>Tower B</option></select></div>
            <div class="resident-filter-field"><label for="vehicle-floor">Lantai</label><select id="vehicle-floor"><option>01-10</option><option>11-20</option></select></div>
            <div class="resident-filter-field"><label for="vehicle-kind">Jenis Kendaraan</label><select id="vehicle-kind"><option>Semua, Mobil, Motor, dll.</option><option>Mobil</option></select></div>
            <div class="resident-filter-field"><label for="vehicle-status">Status Parkir</label><select id="vehicle-status"><option>Aktif, Menunggu Approval, dll.</option><option>Aktif</option></select></div>
        </section>

        <section class="resident-table-panel">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th><input class="resident-check" type="checkbox" aria-label="Select all vehicles"></th>
                            <th>No.</th>
                            <th>Plat Nomor</th>
                            <th>Unit</th>
                            <th>Jenis Kendaraan</th>
                            <th>Pemilik Kendaraan</th>
                            <th>Merk & Model</th>
                            <th>Status Parkir</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr>
                                <td><input class="resident-check" type="checkbox" aria-label="Select {{ $row['plate'] }}"></td>
                                <td>{{ $row['no'] }}</td>
                                <td>{{ $row['plate'] }}</td>
                                <td>{{ $row['unit'] }}</td>
                                <td>
                                    <button class="resident-action-btn" type="button" data-modal-open="resident-vehicles-modal">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="{{ $row['icon'] }}"/></svg>
                                        <span>{{ $row['kind'] }}</span>
                                    </button>
                                </td>
                                <td>{{ $row['owner'] }}</td>
                                <td>{{ $row['model'] }}</td>
                                <td><span class="resident-status {{ $row['statusClass'] }}">{{ $row['status'] }}</span></td>
                                <td>
                                    <div class="resident-action-row">
                                        @if ($row['statusClass'] === 'pending')
                                            @include('resident-management.partials.action-button', ['label' => 'Approve', 'icon' => 'check', 'variant' => 'success', 'modal' => 'resident-vehicles-modal'])
                                            @include('resident-management.partials.action-button', ['label' => 'Reject', 'icon' => 'x', 'variant' => 'danger', 'modal' => 'resident-vehicles-modal'])
                                        @else
                                            @include('resident-management.partials.action-button', ['label' => 'Detail', 'icon' => 'eye', 'modal' => 'resident-vehicles-modal'])
                                            @include('resident-management.partials.action-button', ['label' => 'Edit', 'icon' => 'edit', 'modal' => 'resident-vehicles-modal'])
                                        @endif
                                        @include('resident-management.partials.action-button', ['label' => 'Manage Slot', 'icon' => 'slot', 'modal' => 'resident-vehicles-modal'])
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="resident-pagination">
                <span class="resident-page-btn">&lt;</span>
                <span class="resident-page-btn active">1</span>
                <span class="resident-page-btn">2</span>
                <span class="resident-page-btn">3</span>
                <span class="resident-page-btn">&gt;</span>
                <span>Showing 1-10 of 500</span>
            </div>
        </section>

        @include('resident-management.partials.benefits')
    </div>

    @include('resident-management.partials.action-modal', [
        'id' => 'resident-vehicles-modal',
        'title' => 'Vehicle Management Preview',
        'name' => 'Plat B 1234 ABC',
        'initials' => 'VH',
        'subtitle' => 'Dummy modal untuk tambah kendaraan, detail, edit, approve/reject, dan manage slot.',
        'rows' => [
            ['Unit', 'Unit A-1808'],
            ['Jenis Kendaraan', 'Mobil'],
            ['Pemilik', 'Ahmad Rizky'],
            ['Merk & Model', 'Toyota Fortuner'],
            ['Status Parkir', 'Aktif'],
            ['Mode', 'Static preview only'],
        ],
    ])
@endsection
