@extends('layouts.app')

@php
    $rows = [
        ['no' => 1, 'name' => 'Sarah Lim', 'unit' => 'Unit A-1808', 'relation' => 'Pasangan', 'resident' => 'Ahmad Rizky', 'birth' => '12 Sep 1995', 'status' => 'Aktif', 'statusClass' => 'active', 'icon' => 'M7 19a5 5 0 0 1 10 0M9 8a3 3 0 1 0 6 0 3 3 0 0 0-6 0M4 17h16'],
        ['no' => 2, 'name' => 'Alya Rizky', 'unit' => 'Unit A-1808', 'relation' => 'Anak', 'resident' => 'Ahmad Rizky', 'birth' => '05 Mar 2020', 'status' => 'Aktif', 'statusClass' => 'active', 'icon' => 'M20 21a8 8 0 0 0-16 0M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8'],
        ['no' => 3, 'name' => 'Sarah Lim', 'unit' => 'Unit B-2001', 'relation' => 'Pasangan', 'resident' => 'John Doe', 'birth' => '', 'status' => 'Aktif', 'statusClass' => 'active', 'icon' => 'M7 19a5 5 0 0 1 10 0M9 8a3 3 0 1 0 6 0 3 3 0 0 0-6 0M4 17h16'],
        ['no' => 4, 'name' => '[Contoh Nama Orang Tua Mark Wang]', 'unit' => 'Unit A-2002', 'relation' => 'Orang Tua', 'resident' => 'Mark Wang', 'birth' => '', 'status' => 'Aktif', 'statusClass' => 'active', 'icon' => 'M8 12h8M7 8h10M6 16h12M7 12a5 5 0 0 0 10 0'],
        ['no' => 5, 'name' => '[Contoh Anggota Baru Unit A-0503]', 'unit' => 'Unit A-0503', 'relation' => '', 'resident' => '', 'birth' => '', 'status' => 'Menunggu Approval', 'statusClass' => 'pending', 'icon' => 'M20 21a8 8 0 0 0-16 0M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8'],
    ];
@endphp

@section('title', 'Family Member')
@section('topbar_context', 'Resident Management Flow')
@section('topbar_subtitle', 'Complete Resident Lifecycle Management from Move-In to Move-Out')

@section('content')
    <div class="resident-list-page">
        <header class="resident-page-head">
            <h2>Daftar Anggota Keluarga Aether Residences</h2>
            <button class="btn" type="button" data-modal-open="resident-family-modal">Tambah Anggota Baru</button>
        </header>

        <section class="resident-filter-panel" aria-label="Filter anggota keluarga">
            <div class="resident-filter-field">
                <label for="family-search">Search</label>
                <div class="resident-search">
                    <input id="family-search" type="search" placeholder="Search">
                    <button type="button" aria-label="Search">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                    </button>
                </div>
            </div>
            <div class="resident-filter-field"><label for="family-tower">Tower</label><select id="family-tower"><option>Tower A</option><option>Tower B</option></select></div>
            <div class="resident-filter-field"><label for="family-floor">Lantai</label><select id="family-floor"><option>01-10</option><option>11-20</option></select></div>
            <div class="resident-filter-field"><label for="family-relation">Hubungan</label><select id="family-relation"><option>Semua, Pasangan, Anak, Orang Tua, dll.</option><option>Anak</option></select></div>
            <div class="resident-filter-field"><label for="family-access">Status Akses</label><select id="family-access"><option>Semua, Aktif, Menunggu Approval, dll.</option><option>Aktif</option></select></div>
        </section>

        <section class="resident-table-panel">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th><input class="resident-check" type="checkbox" aria-label="Select all family members"></th>
                            <th>No.</th>
                            <th>Nama Anggota</th>
                            <th>Unit</th>
                            <th>Hubungan</th>
                            <th>Nama Residen Utama</th>
                            <th>Tanggal Lahir</th>
                            <th>Status Akses</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr>
                                <td><input class="resident-check" type="checkbox" aria-label="Select {{ $row['name'] }}"></td>
                                <td>{{ $row['no'] }}</td>
                                <td>{{ $row['name'] }}</td>
                                <td>{{ $row['unit'] }}</td>
                                <td>
                                    @if ($row['relation'])
                                        <div class="resident-action-row">
                                            <button class="resident-action-btn" type="button" data-modal-open="resident-family-modal">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="{{ $row['icon'] }}"/></svg>
                                                <span>{{ $row['relation'] }}</span>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $row['resident'] }}</td>
                                <td>{{ $row['birth'] }}</td>
                                <td><span class="resident-status {{ $row['statusClass'] }}">{{ $row['status'] }}</span></td>
                                <td>
                                    <div class="resident-action-row">
                                        @include('resident-management.partials.action-button', ['label' => 'Detail', 'icon' => 'eye', 'modal' => 'resident-family-modal'])
                                        @include('resident-management.partials.action-button', ['label' => 'Edit', 'icon' => 'edit', 'modal' => 'resident-family-modal'])
                                        @include('resident-management.partials.action-button', ['label' => 'Manage Access', 'icon' => 'access', 'modal' => 'resident-family-modal'])
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
        'id' => 'resident-family-modal',
        'title' => 'Family Member Preview',
        'name' => 'Sarah Lim',
        'initials' => 'SL',
        'avatarClass' => 'female',
        'subtitle' => 'Dummy modal untuk tambah anggota, detail, edit, dan manage access.',
        'rows' => [
            ['Unit', 'Unit A-1808'],
            ['Hubungan', 'Pasangan'],
            ['Residen Utama', 'Ahmad Rizky'],
            ['Tanggal Lahir', '12 Sep 1995'],
            ['Status Akses', 'Aktif'],
            ['Mode', 'Static preview only'],
        ],
    ])
@endsection
