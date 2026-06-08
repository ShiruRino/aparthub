@extends('layouts.app')

@php
    $rows = [
        ['request' => 'MOI-2026-001', 'unit' => 'Unit A-1808', 'kind' => 'Pindah Keluar', 'resident' => 'Sarah Lim', 'date' => '15 Jun 2026', 'status' => 'Menunggu Approval', 'statusNote' => 'Kuning', 'statusClass' => 'pending', 'icon' => 'move'],
        ['request' => 'MIO-2026-002', 'unit' => 'Unit B-2001', 'kind' => 'Pindah Masuk', 'resident' => 'John Doe', 'date' => '10 Jun 2026', 'status' => 'Menunggu Approval', 'statusNote' => 'Kuning', 'statusClass' => 'pending', 'icon' => 'slot'],
        ['request' => 'MIO-2026-003', 'unit' => 'Unit A-0503', 'kind' => 'Pindah Masuk', 'resident' => 'Jane Smith', 'date' => 'TBD (After Repair)', 'status' => 'Sedang Berlangsung', 'statusNote' => 'Biru', 'statusClass' => 'process', 'icon' => 'slot'],
        ['request' => 'MOI-2026-004', 'unit' => 'Unit A-2002', 'kind' => 'Pindah Keluar', 'resident' => 'Mark Wang', 'date' => '08 Jun 2026', 'status' => 'Selesai', 'statusNote' => '', 'statusClass' => 'done', 'icon' => 'move'],
        ['request' => 'MIO-2026-005', 'unit' => 'Unit A-1808 (Penyewa Baru)', 'kind' => 'Pindah Masuk', 'resident' => 'Kevin Chen', 'date' => '20 Jun 2026', 'status' => 'Menunggu Approval', 'statusNote' => 'Kuning', 'statusClass' => 'pending', 'icon' => 'slot'],
    ];
@endphp

@section('title', 'Move In / Out')
@section('topbar_context', 'Resident Management Flow')
@section('topbar_subtitle', 'Complete Resident Lifecycle Management from Move-In to Move-Out')

@section('content')
    <div class="resident-list-page">
        <header class="resident-page-head">
            <h2>Manajemen Pindah Masuk & Keluar Aether Residences</h2>
            <button class="btn" type="button" data-modal-open="resident-move-modal">Catat Permohonan Baru</button>
        </header>

        <section class="resident-filter-panel" aria-label="Filter pindah masuk keluar">
            <div class="resident-filter-field">
                <label for="move-search">Search</label>
                <div class="resident-search">
                    <input id="move-search" type="search" placeholder="Search">
                    <button type="button" aria-label="Search">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                    </button>
                </div>
            </div>
            <div class="resident-filter-field"><label for="move-tower">Tower</label><select id="move-tower"><option>Tower A</option><option>Tower B</option></select></div>
            <div class="resident-filter-field"><label for="move-floor">Lantai</label><select id="move-floor"><option>01-10</option><option>11-20</option></select></div>
            <div class="resident-filter-field"><label for="move-kind">Jenis Pindahan</label><select id="move-kind"><option>Semua, Pindah Masuk, Pindah Keluar</option><option>Pindah Masuk</option></select></div>
            <div class="resident-filter-field"><label for="move-status">Status Pindahan</label><select id="move-status"><option>Semua, Menunggu Approval, Sedang Berlangsung, Selesai</option><option>Menunggu Approval</option></select></div>
        </section>

        <section class="resident-table-panel">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th><input class="resident-check" type="checkbox" aria-label="Select all requests"></th>
                            <th>No. Permohonan</th>
                            <th>No. Unit</th>
                            <th>Jenis Pindahan</th>
                            <th>Nama Penghuni</th>
                            <th>Tanggal Rencana</th>
                            <th>Status Pindahan</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr>
                                <td><input class="resident-check" type="checkbox" aria-label="Select {{ $row['request'] }}"></td>
                                <td>{{ $row['request'] }}</td>
                                <td>{{ $row['unit'] }}</td>
                                <td>
                                    <div class="resident-action-row">
                                        @include('resident-management.partials.action-button', ['label' => $row['kind'], 'icon' => $row['icon'], 'modal' => 'resident-move-modal'])
                                    </div>
                                </td>
                                <td>{{ $row['resident'] }}</td>
                                <td>{{ $row['date'] }}</td>
                                <td><span class="resident-status {{ $row['statusClass'] }}">{{ $row['status'] }}@if ($row['statusNote'])<br>({{ $row['statusNote'] }})@endif</span></td>
                                <td>
                                    <div class="resident-action-row">
                                        @if ($row['statusClass'] === 'pending')
                                            @include('resident-management.partials.action-button', ['label' => 'Setujui', 'icon' => 'check', 'variant' => 'success', 'modal' => 'resident-move-modal'])
                                        @endif
                                        @include('resident-management.partials.action-button', ['label' => 'Detail', 'icon' => 'eye', 'modal' => 'resident-move-modal'])
                                        @if ($row['statusClass'] !== 'process')
                                            @include('resident-management.partials.action-button', ['label' => 'Cetak Form', 'icon' => 'print', 'modal' => 'resident-move-modal'])
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
        'id' => 'resident-move-modal',
        'title' => 'Move In / Out Preview',
        'name' => 'Sarah Lim',
        'initials' => 'SL',
        'avatarClass' => 'female',
        'subtitle' => 'Dummy modal untuk permohonan, approval, detail, dan cetak form.',
        'rows' => [
            ['No. Permohonan', 'MOI-2026-001'],
            ['Unit', 'Unit A-1808'],
            ['Jenis Pindahan', 'Pindah Keluar'],
            ['Tanggal Rencana', '15 Jun 2026'],
            ['Status', 'Menunggu Approval'],
            ['Mode', 'Static preview only'],
        ],
    ])
@endsection
