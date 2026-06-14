@extends('layouts.app')

@section('title', 'Vehicle Management')
@section('topbar_context', 'Resident Management Flow')
@section('topbar_subtitle', 'Complete Resident Lifecycle Management from Move-In to Move-Out')

@section('content')
    <div class="resident-list-page">
        <header class="resident-page-head">
            <h2>Daftar Kendaraan Penghuni Aether Residences</h2>
            <button class="btn" type="button" data-modal-open="resident-vehicle-form-modal" data-form-mode="create" data-form-title="Tambah Kendaraan Baru" data-form-action="{{ route('resident-management.vehicles.store') }}">Tambah Kendaraan Baru</button>
        </header>

        <form class="resident-filter-panel" method="GET" action="{{ route('resident-management.vehicles') }}" aria-label="Filter kendaraan penghuni">
            <div class="resident-filter-field">
                <label for="vehicle-search">Search</label>
                <div class="resident-search">
                    <input id="vehicle-search" name="search" type="search" placeholder="Search" value="{{ $filters['search'] ?? '' }}">
                    <button type="submit" aria-label="Search">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                    </button>
                </div>
            </div>
            <div class="resident-filter-field"><label for="vehicle-tower">Tower</label><select id="vehicle-tower" name="tower"><option value="">Semua Tower</option>@foreach ($towers as $tower)<option value="{{ $tower }}" @selected(($filters['tower'] ?? '') === $tower)>{{ $tower }}</option>@endforeach</select></div>
            <div class="resident-filter-field"><label for="vehicle-floor">Lantai</label><select id="vehicle-floor" name="floor_band"><option value="">Semua Lantai</option>@foreach ($floorBands as $band)<option value="{{ $band }}" @selected(($filters['floor_band'] ?? '') === $band)>{{ $band }}</option>@endforeach</select></div>
            <div class="resident-filter-field"><label for="vehicle-kind">Jenis Kendaraan</label><select id="vehicle-kind" name="vehicle_type"><option value="">Semua Kendaraan</option>@foreach ($vehicleTypes as $vehicleType)<option value="{{ $vehicleType }}" @selected(($filters['vehicle_type'] ?? '') === $vehicleType)>{{ $vehicleType }}</option>@endforeach</select></div>
            <div class="resident-filter-field"><label for="vehicle-status">Status Parkir</label><select id="vehicle-status" name="parking_status"><option value="">Semua Status</option>@foreach ($parkingStatuses as $parkingStatus)<option value="{{ $parkingStatus }}" @selected(($filters['parking_status'] ?? '') === $parkingStatus)>{{ $parkingStatus }}</option>@endforeach</select></div>
        </form>

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
                        @forelse ($rows as $row)
                            <tr>
                                <td><input class="resident-check" type="checkbox" aria-label="Select {{ $row['plate'] }}"></td>
                                <td>{{ $row['no'] }}</td>
                                <td>{{ $row['plate'] }}</td>
                                <td>{{ $row['unit'] }}</td>
                                <td>
                                    <button class="resident-action-btn" type="button" data-modal-open="resident-vehicle-form-modal" data-form-mode="edit" data-form-title="Detail Kendaraan" data-form-action="{{ route('resident-management.vehicles.update', $row['id']) }}" data-vehicle-plate-number="{{ $row['plate_number'] }}" data-vehicle-resident-id="{{ $row['resident_id'] ?? '' }}" data-vehicle-unit-id="{{ $row['unit_id'] ?? '' }}" data-vehicle-type="{{ $row['vehicle_type'] }}" data-vehicle-owner-name="{{ $row['owner'] }}" data-vehicle-make-model="{{ $row['model'] }}" data-vehicle-status="{{ $row['status'] }}" data-vehicle-slot-label="{{ $row['slot_label'] ?? '' }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="{{ $row['icon'] }}"/></svg>
                                        <span>{{ $row['kind'] }}</span>
                                    </button>
                                </td>
                                <td>{{ $row['owner'] }}</td>
                                <td>{{ $row['model'] }}</td>
                                <td><span class="resident-status {{ $row['statusClass'] }}">{{ $row['status'] }}</span></td>
                                <td>
                                    <div class="resident-action-row">
                                        @include('resident-management.partials.action-button', ['label' => 'Detail Kendaraan', 'icon' => 'eye', 'modal' => 'resident-vehicle-form-modal', 'data' => ['data-form-mode' => 'edit', 'data-form-title' => 'Detail Kendaraan', 'data-form-action' => route('resident-management.vehicles.update', $row['id']), 'data-vehicle-plate-number' => $row['plate_number'], 'data-vehicle-resident-id' => $row['resident_id'] ?? '', 'data-vehicle-unit-id' => $row['unit_id'] ?? '', 'data-vehicle-type' => $row['vehicle_type'], 'data-vehicle-owner-name' => $row['owner'], 'data-vehicle-make-model' => $row['model'], 'data-vehicle-status' => $row['status'], 'data-vehicle-slot-label' => $row['slot_label'] ?? '']])
                                        @include('resident-management.partials.action-button', ['label' => 'Edit Kendaraan', 'icon' => 'edit', 'modal' => 'resident-vehicle-form-modal', 'data' => ['data-form-mode' => 'edit', 'data-form-title' => 'Edit Kendaraan', 'data-form-action' => route('resident-management.vehicles.update', $row['id']), 'data-vehicle-plate-number' => $row['plate_number'], 'data-vehicle-resident-id' => $row['resident_id'] ?? '', 'data-vehicle-unit-id' => $row['unit_id'] ?? '', 'data-vehicle-type' => $row['vehicle_type'], 'data-vehicle-owner-name' => $row['owner'], 'data-vehicle-make-model' => $row['model'], 'data-vehicle-status' => $row['status'], 'data-vehicle-slot-label' => $row['slot_label'] ?? '']])
                                        <form method="POST" action="{{ route('resident-management.vehicles.destroy', $row['id']) }}" onsubmit="return confirm('Hapus data kendaraan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="icon-action-btn resident-action-btn danger" type="submit" title="Hapus Kendaraan" aria-label="Hapus Kendaraan"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2m-9 0 1 14h6l1-14"/></svg></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9">Belum ada data kendaraan penghuni.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @include('resident-management.partials.pagination', ['paginator' => $vehicles])
        </section>

        @include('resident-management.partials.benefits')
    </div>

    <div class="visitor-modal resident-modal" id="resident-vehicle-form-modal" aria-hidden="true">
        <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
        <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="resident-vehicle-form-title">
            <div class="visitor-modal-head"><h2 class="visitor-modal-title" id="resident-vehicle-form-title">Tambah Kendaraan Baru</h2><button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button></div>
            <form class="visitor-modal-body" id="residentVehicleForm" method="POST" action="{{ route('resident-management.vehicles.store') }}">
                @csrf
                <input id="residentVehicleFormMethod" type="hidden" name="_method" value="POST">
                <div class="visitor-form-grid">
                    <label class="resident-filter-field"><span>Plat Nomor</span><input id="vehiclePlateNumber" name="plate_number" type="text" value="{{ old('plate_number') }}" required></label>
                    <label class="resident-filter-field"><span>Pemilik</span><input id="vehicleOwnerName" name="owner_name" type="text" value="{{ old('owner_name') }}" required></label>
                    <label class="resident-filter-field"><span>Resident</span><select id="vehicleResidentId" name="resident_id">@foreach ($residentOptions as $residentOption)<option value="{{ $residentOption->id }}">{{ $residentOption->name }}</option>@endforeach</select></label>
                    <label class="resident-filter-field"><span>Unit</span><select id="vehicleUnitId" name="unit_id">@foreach ($unitOptions as $unitOption)<option value="{{ $unitOption->id }}">{{ $unitOption->code }} - {{ $unitOption->tower }}</option>@endforeach</select></label>
                    <label class="resident-filter-field"><span>Jenis Kendaraan</span><select id="vehicleType" name="vehicle_type">@foreach ($vehicleTypes as $vehicleType)<option value="{{ $vehicleType }}">{{ $vehicleType }}</option>@endforeach</select></label>
                    <label class="resident-filter-field"><span>Merk & Model</span><input id="vehicleMakeModel" name="make_model" type="text" value="{{ old('make_model') }}" required></label>
                    <label class="resident-filter-field"><span>Status Parkir</span><select id="vehicleStatus" name="parking_status">@foreach ($parkingStatuses as $parkingStatus)<option value="{{ $parkingStatus }}">{{ $parkingStatus }}</option>@endforeach</select></label>
                    <label class="resident-filter-field"><span>Slot Parkir</span><input id="vehicleSlotLabel" name="slot_label" type="text" value="{{ old('slot_label') }}"></label>
                </div>
                <div class="visitor-form-actions"><button class="btn secondary" type="button" data-modal-close>Batal</button><button class="btn" type="submit">Simpan Kendaraan</button></div>
            </form>
        </div>
    </div>

    <script>
        (() => {
            const form = document.getElementById('residentVehicleForm');
            if (!form) return;
            const methodInput = document.getElementById('residentVehicleFormMethod');
            const title = document.getElementById('resident-vehicle-form-title');
            const fields = {
                plateNumber: document.getElementById('vehiclePlateNumber'),
                ownerName: document.getElementById('vehicleOwnerName'),
                residentId: document.getElementById('vehicleResidentId'),
                unitId: document.getElementById('vehicleUnitId'),
                type: document.getElementById('vehicleType'),
                makeModel: document.getElementById('vehicleMakeModel'),
                status: document.getElementById('vehicleStatus'),
                slotLabel: document.getElementById('vehicleSlotLabel'),
            };
            document.querySelectorAll('[data-modal-open="resident-vehicle-form-modal"]').forEach((button) => {
                button.addEventListener('click', () => {
                    const isEdit = button.dataset.formMode === 'edit';
                    title.textContent = button.dataset.formTitle || 'Tambah Kendaraan Baru';
                    form.action = button.dataset.formAction || '{{ route('resident-management.vehicles.store') }}';
                    methodInput.value = isEdit ? 'PUT' : 'POST';
                    fields.plateNumber.value = button.dataset.vehiclePlateNumber || '';
                    fields.ownerName.value = button.dataset.vehicleOwnerName || '';
                    fields.residentId.value = button.dataset.vehicleResidentId || '';
                    fields.unitId.value = button.dataset.vehicleUnitId || '';
                    fields.type.value = button.dataset.vehicleType || 'Mobil';
                    fields.makeModel.value = button.dataset.vehicleMakeModel || '';
                    fields.status.value = button.dataset.vehicleStatus || 'Aktif';
                    fields.slotLabel.value = button.dataset.vehicleSlotLabel || '';
                });
            });
        })();
    </script>
@endsection
