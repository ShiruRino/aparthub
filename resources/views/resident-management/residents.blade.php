@extends('layouts.app')

@php
    $rows = [
        ['name' => 'Ahmad Rizky', 'unit' => 'A-1808', 'tower' => 'Tower A / 18', 'status' => 'Aktif', 'statusClass' => 'active', 'type' => 'Pemilik', 'date' => '07 Jun 2026', 'avatar' => 'AR', 'avatarClass' => ''],
        ['name' => 'Sarah Lim', 'unit' => 'A-1808', 'tower' => 'Tower A / 18', 'status' => 'Aktif', 'statusClass' => 'active', 'type' => 'Penyewa', 'date' => '07 Jun 2026', 'avatar' => 'SL', 'avatarClass' => 'female'],
        ['name' => 'John Doe', 'unit' => 'B-2001', 'tower' => 'Tower B / 20', 'status' => 'Menunggu Approval', 'statusClass' => 'pending', 'type' => 'Pemilik', 'date' => 'TBD', 'avatar' => 'JD', 'avatarClass' => 'pending'],
        ['name' => 'Jane Smith', 'unit' => 'A-0503', 'tower' => 'Tower A / 05', 'status' => 'Keluar', 'statusClass' => 'out', 'type' => 'Penyewa', 'date' => '15 Mei 2024', 'avatar' => 'JS', 'avatarClass' => 'out'],
        ['name' => 'John Doe', 'unit' => 'A-0503', 'tower' => 'Tower A / 05', 'status' => 'Keluar', 'statusClass' => 'out', 'type' => 'Penyewa', 'date' => '15 Mei 2024', 'avatar' => 'JD', 'avatarClass' => 'female'],
    ];
@endphp

@section('title', 'Residents')
@section('topbar_context', 'Resident Management Flow')
@section('topbar_subtitle', 'Complete Resident Lifecycle Management from Move-In to Move-Out')

@section('content')
    <div class="resident-list-page">
        <header class="resident-page-head">
            <h2>Daftar Residen Aether Residences</h2>
            <button class="btn" type="button" data-modal-open="resident-residents-modal">Tambah Residen Baru</button>
        </header>

        <section class="resident-filter-panel" aria-label="Filter daftar residen">
            <div class="resident-filter-field">
                <label for="resident-search">Search</label>
                <div class="resident-search">
                    <input id="resident-search" type="search" placeholder="Search">
                    <button type="button" aria-label="Search">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                    </button>
                </div>
            </div>
            <div class="resident-filter-field"><label for="resident-tower">Tower</label><select id="resident-tower"><option>Tower A, Tower B</option><option>Tower C</option></select></div>
            <div class="resident-filter-field"><label for="resident-floor">Lantai</label><select id="resident-floor"><option>01-10, 11-20</option><option>21+</option></select></div>
            <div class="resident-filter-field"><label for="resident-status">Status Residen</label><select id="resident-status"><option>Aktif, Menunggu Approval, Keluar</option><option>Aktif</option><option>Keluar</option></select></div>
            <div class="resident-filter-field"><label for="resident-type">Jenis Residen</label><select id="resident-type"><option>Pemilik, Penyewa</option><option>Pemilik</option><option>Penyewa</option></select></div>
        </section>

        <section class="resident-table-panel">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th><input class="resident-check" type="checkbox" aria-label="Select all residents"></th>
                            <th>Foto</th>
                            <th>Nama Residen</th>
                            <th>Unit</th>
                            <th>Tower/Lantai</th>
                            <th>Status</th>
                            <th>Jenis Residen</th>
                            <th>Tanggal Masuk</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr>
                                <td><input class="resident-check" type="checkbox" aria-label="Select {{ $row['name'] }}"></td>
                                <td><div class="resident-avatar {{ $row['avatarClass'] }}">{{ $row['avatar'] }}</div></td>
                                <td>{{ $row['name'] }}</td>
                                <td>{{ $row['unit'] }}</td>
                                <td>{{ $row['tower'] }}</td>
                                <td><span class="resident-status {{ $row['statusClass'] }}">{{ $row['status'] }}</span></td>
                                <td>{{ $row['type'] }}</td>
                                <td>{{ $row['date'] }}</td>
                                <td>
                                    <div class="resident-action-row">
                                        @if ($row['statusClass'] === 'pending')
                                            @include('resident-management.partials.action-button', ['label' => 'Lihat Detail', 'icon' => 'eye', 'modal' => 'resident-residents-modal'])
                                            @include('resident-management.partials.action-button', ['label' => 'Approve', 'icon' => 'check', 'variant' => 'success', 'modal' => 'resident-residents-modal'])
                                            @include('resident-management.partials.action-button', ['label' => 'Reject', 'icon' => 'x', 'variant' => 'danger', 'modal' => 'resident-residents-modal'])
                                        @elseif ($row['statusClass'] === 'out')
                                            @include('resident-management.partials.action-button', ['label' => 'Lihat Riwayat', 'icon' => 'history', 'modal' => 'resident-residents-modal'])
                                        @else
                                            @include('resident-management.partials.action-button', ['label' => 'Lihat Detail', 'icon' => 'eye', 'modal' => 'resident-residents-modal'])
                                            @include('resident-management.partials.action-button', ['label' => 'Edit', 'icon' => 'edit', 'modal' => 'resident-residents-modal'])
                                            @include('resident-management.partials.action-button', ['label' => 'Pindahkan Unit', 'icon' => 'move', 'modal' => 'resident-residents-modal'])
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
                <span>Showing 1-10 of 250</span>
            </div>
        </section>

        @include('resident-management.partials.benefits')
    </div>

    @include('resident-management.partials.action-modal', [
        'id' => 'resident-residents-modal',
        'title' => 'Resident Action Preview',
        'name' => 'Ahmad Rizky',
        'initials' => 'AR',
        'subtitle' => 'Dummy modal untuk tambah, detail, edit, approval, riwayat, dan pindah unit.',
        'rows' => [
            ['Unit', 'A-1808'],
            ['Tower/Lantai', 'Tower A / 18'],
            ['Status', 'Aktif'],
            ['Jenis Residen', 'Pemilik'],
            ['Tanggal Masuk', '07 Jun 2026'],
            ['Mode', 'Static preview only'],
        ],
    ])
@endsection
