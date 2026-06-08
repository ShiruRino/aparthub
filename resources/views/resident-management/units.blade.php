@extends('layouts.app')

@php
    $rows = [
        ['unit' => 'Unit A-1808', 'tower' => 'Tower A / 18', 'type' => '2BR Premium', 'status' => 'Terisi', 'statusClass' => 'active', 'payment' => 'Lunas', 'thumb' => ''],
        ['unit' => 'Unit B-2001', 'tower' => 'Tower B / 20', 'type' => '1BR Deluxe', 'status' => 'Kosong', 'statusClass' => 'empty', 'payment' => 'Cicilan/Lunas', 'thumb' => 'empty'],
        ['unit' => 'Unit A-0503', 'tower' => 'Tower A / 05', 'type' => 'Studio', 'status' => 'Perbaikan', 'statusClass' => 'repair', 'payment' => 'Cicilan/Lunas', 'thumb' => 'repair'],
        ['unit' => 'Unit A-1808', 'tower' => 'Tower A / 18', 'type' => '2BR Premium', 'status' => 'Terisi', 'statusClass' => 'active', 'payment' => 'Cicilan/Lunas', 'thumb' => ''],
        ['unit' => 'Unit B-2001', 'tower' => 'Tower B / 20', 'type' => '1BR Deluxe', 'status' => 'Kosong', 'statusClass' => 'empty', 'payment' => 'Cicilan/Lunas', 'thumb' => 'empty'],
    ];
@endphp

@section('title', 'Unit Management')
@section('topbar_context', 'Resident Management Flow')
@section('topbar_subtitle', 'Complete Resident Lifecycle Management from Move-In to Move-Out')

@section('content')
    <div class="resident-list-page">
        <header class="resident-page-head">
            <h2>Daftar Unit & Inventaris Aether Residences</h2>
            <button class="btn" type="button" data-modal-open="resident-units-modal">Tambah Unit Baru</button>
        </header>

        <section class="resident-filter-panel" aria-label="Filter daftar unit">
            <div class="resident-filter-field">
                <label for="unit-search">Search</label>
                <div class="resident-search">
                    <input id="unit-search" type="search" placeholder="Search">
                    <button type="button" aria-label="Search">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                    </button>
                </div>
            </div>
            <div class="resident-filter-field"><label for="unit-tower">Tower</label><select id="unit-tower"><option>Tower A, B, C, D</option><option>Tower A</option></select></div>
            <div class="resident-filter-field"><label for="unit-floor">Lantai</label><select id="unit-floor"><option>01-10, 11-20, 21+</option><option>01-10</option></select></div>
            <div class="resident-filter-field"><label for="unit-status">Status Unit</label><select id="unit-status"><option>Terisi, Kosong, Perbaikan, Menunggu Inspeksi</option><option>Terisi</option></select></div>
            <div class="resident-filter-field"><label for="unit-type">Jenis Unit</label><select id="unit-type"><option>Studio, 1BR, 2BR Premium, 3BR Deluxe</option><option>2BR Premium</option></select></div>
        </section>

        <section class="resident-table-panel">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th><input class="resident-check" type="checkbox" aria-label="Select all units"></th>
                            <th>Foto Unit</th>
                            <th>Nomor Unit</th>
                            <th>Tower/Lantai</th>
                            <th>Jenis Unit</th>
                            <th>Status Unit</th>
                            <th>Status Pembayaran</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr>
                                <td><input class="resident-check" type="checkbox" aria-label="Select {{ $row['unit'] }}"></td>
                                <td><div class="resident-unit-thumb {{ $row['thumb'] }}">Unit</div></td>
                                <td>{{ $row['unit'] }}</td>
                                <td>{{ $row['tower'] }}</td>
                                <td>{{ $row['type'] }}</td>
                                <td><span class="resident-status {{ $row['statusClass'] }}">{{ $row['status'] }}</span></td>
                                <td>{{ $row['payment'] }}</td>
                                <td>
                                    <div class="resident-action-row">
                                        @include('resident-management.partials.action-button', ['label' => 'Lihat Detail', 'icon' => 'eye', 'modal' => 'resident-units-modal'])
                                        @if ($row['statusClass'] === 'empty')
                                            @include('resident-management.partials.action-button', ['label' => 'Promosikan', 'icon' => 'move', 'modal' => 'resident-units-modal'])
                                        @elseif ($row['statusClass'] === 'repair')
                                            @include('resident-management.partials.action-button', ['label' => 'Edit', 'icon' => 'edit', 'modal' => 'resident-units-modal'])
                                            @include('resident-management.partials.action-button', ['label' => 'Lacak Perbaikan', 'icon' => 'tool', 'modal' => 'resident-units-modal'])
                                        @else
                                            @include('resident-management.partials.action-button', ['label' => 'Edit', 'icon' => 'edit', 'modal' => 'resident-units-modal'])
                                            @include('resident-management.partials.action-button', ['label' => 'Kelola Penyewa', 'icon' => 'access', 'modal' => 'resident-units-modal'])
                                        @endif
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
        'id' => 'resident-units-modal',
        'title' => 'Unit Inventory Preview',
        'name' => 'Unit A-1808',
        'initials' => 'UA',
        'subtitle' => 'Dummy modal untuk tambah unit, detail unit, edit, kelola penyewa, promosi, dan lacak perbaikan.',
        'rows' => [
            ['Tower/Lantai', 'Tower A / 18'],
            ['Jenis Unit', '2BR Premium'],
            ['Status Unit', 'Terisi'],
            ['Status Pembayaran', 'Lunas'],
            ['Occupancy', 'Ahmad Rizky'],
            ['Mode', 'Static preview only'],
        ],
    ])
@endsection
