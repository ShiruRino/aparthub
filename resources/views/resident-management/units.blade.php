@extends('layouts.app')

@section('title', 'Unit Management')
@section('topbar_context', 'Resident Management Flow')
@section('topbar_subtitle', 'Complete Resident Lifecycle Management from Move-In to Move-Out')

@section('content')
    <div class="resident-list-page">
        <header class="resident-page-head">
            <h2>Daftar Unit & Inventaris Aether Residences</h2>
            <button class="btn" type="button" data-modal-open="resident-unit-form-modal" data-form-mode="create" data-form-title="Tambah Unit Baru" data-form-action="{{ route('resident-management.units.store') }}">Tambah Unit Baru</button>
        </header>

        <form class="resident-filter-panel" method="GET" action="{{ route('resident-management.units') }}" aria-label="Filter daftar unit">
            <div class="resident-filter-field">
                <label for="unit-search">Search</label>
                <div class="resident-search">
                    <input id="unit-search" name="search" type="search" placeholder="Search" value="{{ $filters['search'] ?? '' }}">
                    <button type="submit" aria-label="Search">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                    </button>
                </div>
            </div>
            <div class="resident-filter-field"><label for="unit-tower">Tower</label><select id="unit-tower" name="tower"><option value="">Semua Tower</option>@foreach ($towers as $tower)<option value="{{ $tower }}" @selected(($filters['tower'] ?? '') === $tower)>{{ $tower }}</option>@endforeach</select></div>
            <div class="resident-filter-field"><label for="unit-floor">Lantai</label><select id="unit-floor" name="floor_band"><option value="">Semua Lantai</option>@foreach ($floorBands as $band)<option value="{{ $band }}" @selected(($filters['floor_band'] ?? '') === $band)>{{ $band }}</option>@endforeach</select></div>
            <div class="resident-filter-field"><label for="unit-status">Status Unit</label><select id="unit-status" name="occupancy_status"><option value="">Semua Status</option>@foreach ($occupancyStatuses as $status)<option value="{{ $status }}" @selected(($filters['occupancy_status'] ?? '') === $status)>{{ $status }}</option>@endforeach</select></div>
            <div class="resident-filter-field"><label for="unit-type">Jenis Unit</label><select id="unit-type" name="unit_type"><option value="">Semua Jenis</option>@foreach ($unitTypes as $type)<option value="{{ $type }}" @selected(($filters['unit_type'] ?? '') === $type)>{{ $type }}</option>@endforeach</select></div>
        </form>

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
                        @forelse ($rows as $row)
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
                                        @include('resident-management.partials.action-button', ['label' => 'Detail Unit', 'icon' => 'eye', 'modal' => 'resident-unit-form-modal', 'data' => ['data-form-mode' => 'edit', 'data-form-title' => 'Detail Unit', 'data-form-action' => route('resident-management.units.update', $row['id']), 'data-unit-code' => $row['code'], 'data-unit-tower' => $row['tower_name'], 'data-unit-floor' => $row['floor_number'], 'data-unit-type' => $row['type'], 'data-unit-status' => $row['status'], 'data-unit-payment' => $row['payment'], 'data-unit-thumb' => $row['thumb']]])
                                        @include('resident-management.partials.action-button', ['label' => 'Edit Unit', 'icon' => 'edit', 'modal' => 'resident-unit-form-modal', 'data' => ['data-form-mode' => 'edit', 'data-form-title' => 'Edit Unit', 'data-form-action' => route('resident-management.units.update', $row['id']), 'data-unit-code' => $row['code'], 'data-unit-tower' => $row['tower_name'], 'data-unit-floor' => $row['floor_number'], 'data-unit-type' => $row['type'], 'data-unit-status' => $row['status'], 'data-unit-payment' => $row['payment'], 'data-unit-thumb' => $row['thumb']]])
                                        <form method="POST" action="{{ route('resident-management.units.destroy', $row['id']) }}" onsubmit="return confirm('Hapus data unit ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="icon-action-btn resident-action-btn danger" type="submit" title="Hapus Unit" aria-label="Hapus Unit">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2m-9 0 1 14h6l1-14"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8">Belum ada data unit.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @include('resident-management.partials.pagination', ['paginator' => $units])
        </section>

        @include('resident-management.partials.benefits')
    </div>

    <div class="visitor-modal resident-modal" id="resident-unit-form-modal" aria-hidden="true">
        <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
        <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="resident-unit-form-title">
            <div class="visitor-modal-head">
                <h2 class="visitor-modal-title" id="resident-unit-form-title">Tambah Unit Baru</h2>
                <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
            </div>
            <form class="visitor-modal-body" id="residentUnitForm" method="POST" action="{{ route('resident-management.units.store') }}">
                @csrf
                <input id="residentUnitFormMethod" type="hidden" name="_method" value="POST">
                <div class="visitor-form-grid">
                    <label class="resident-filter-field"><span>Kode Unit</span><input id="unitCode" name="code" type="text" value="{{ old('code') }}" required></label>
                    <label class="resident-filter-field"><span>Tower</span><input id="unitTower" name="tower" type="text" value="{{ old('tower') }}" required></label>
                    <label class="resident-filter-field"><span>Lantai</span><input id="unitFloor" name="floor" type="number" min="1" max="99" value="{{ old('floor') }}" required></label>
                    <label class="resident-filter-field"><span>Jenis Unit</span><select id="unitType" name="unit_type" required>@foreach ($unitTypes as $unitType)<option value="{{ $unitType }}">{{ $unitType }}</option>@endforeach</select></label>
                    <label class="resident-filter-field"><span>Status Unit</span><select id="unitStatus" name="occupancy_status" required>@foreach ($occupancyStatuses as $occupancyStatus)<option value="{{ $occupancyStatus }}">{{ $occupancyStatus }}</option>@endforeach</select></label>
                    <label class="resident-filter-field"><span>Status Pembayaran</span><select id="unitPayment" name="payment_status" required>@foreach ($paymentStatuses as $paymentStatus)<option value="{{ $paymentStatus }}">{{ $paymentStatus }}</option>@endforeach</select></label>
                    <label class="resident-filter-field"><span>Thumbnail Tone</span><select id="unitThumb" name="thumbnail_tone"><option value="default">Default</option><option value="empty">Empty</option><option value="repair">Repair</option></select></label>
                </div>
                <div class="visitor-form-actions"><button class="btn secondary" type="button" data-modal-close>Batal</button><button class="btn" type="submit">Simpan Unit</button></div>
            </form>
        </div>
    </div>

    <script>
        (() => {
            const form = document.getElementById('residentUnitForm');
            if (!form) return;
            const methodInput = document.getElementById('residentUnitFormMethod');
            const title = document.getElementById('resident-unit-form-title');
            const fields = {
                code: document.getElementById('unitCode'),
                tower: document.getElementById('unitTower'),
                floor: document.getElementById('unitFloor'),
                type: document.getElementById('unitType'),
                status: document.getElementById('unitStatus'),
                payment: document.getElementById('unitPayment'),
                thumb: document.getElementById('unitThumb'),
            };
            document.querySelectorAll('[data-modal-open="resident-unit-form-modal"]').forEach((button) => {
                button.addEventListener('click', () => {
                    const isEdit = button.dataset.formMode === 'edit';
                    title.textContent = button.dataset.formTitle || 'Tambah Unit Baru';
                    form.action = button.dataset.formAction || '{{ route('resident-management.units.store') }}';
                    methodInput.value = isEdit ? 'PUT' : 'POST';
                    fields.code.value = button.dataset.unitCode || '';
                    fields.tower.value = button.dataset.unitTower || '';
                    fields.floor.value = button.dataset.unitFloor || '';
                    fields.type.value = button.dataset.unitType || '{{ $unitTypes[0] ?? '' }}';
                    fields.status.value = button.dataset.unitStatus || 'Kosong';
                    fields.payment.value = button.dataset.unitPayment || 'Belum Lunas';
                    fields.thumb.value = button.dataset.unitThumb || 'default';
                });
            });
        })();
    </script>
@endsection
