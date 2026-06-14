@extends('layouts.app')

@section('title', 'Move In / Out')
@section('topbar_context', 'Resident Management Flow')
@section('topbar_subtitle', 'Complete Resident Lifecycle Management from Move-In to Move-Out')

@section('content')
    <div class="resident-list-page">
        <header class="resident-page-head">
            <h2>Manajemen Pindah Masuk & Keluar Aether Residences</h2>
            <button class="btn" type="button" data-modal-open="resident-move-form-modal" data-form-mode="create" data-form-title="Catat Permohonan Baru" data-form-action="{{ route('resident-management.move-in-out.store') }}">Catat Permohonan Baru</button>
        </header>

        <form class="resident-filter-panel" method="GET" action="{{ route('resident-management.move-in-out') }}" aria-label="Filter pindah masuk keluar">
            <div class="resident-filter-field">
                <label for="move-search">Search</label>
                <div class="resident-search">
                    <input id="move-search" name="search" type="search" placeholder="Search" value="{{ $filters['search'] ?? '' }}">
                    <button type="submit" aria-label="Search">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                    </button>
                </div>
            </div>
            <div class="resident-filter-field"><label for="move-tower">Tower</label><select id="move-tower" name="tower"><option value="">Semua Tower</option>@foreach ($towers as $tower)<option value="{{ $tower }}" @selected(($filters['tower'] ?? '') === $tower)>{{ $tower }}</option>@endforeach</select></div>
            <div class="resident-filter-field"><label for="move-floor">Lantai</label><select id="move-floor" name="floor_band"><option value="">Semua Lantai</option>@foreach ($floorBands as $band)<option value="{{ $band }}" @selected(($filters['floor_band'] ?? '') === $band)>{{ $band }}</option>@endforeach</select></div>
            <div class="resident-filter-field"><label for="move-kind">Jenis Pindahan</label><select id="move-kind" name="request_type"><option value="">Semua Jenis</option>@foreach ($requestTypes as $requestType)<option value="{{ $requestType }}" @selected(($filters['request_type'] ?? '') === $requestType)>{{ $requestType }}</option>@endforeach</select></div>
            <div class="resident-filter-field"><label for="move-status">Status Pindahan</label><select id="move-status" name="status"><option value="">Semua Status</option>@foreach ($moveStatuses as $moveStatus)<option value="{{ $moveStatus }}" @selected(($filters['status'] ?? '') === $moveStatus)>{{ $moveStatus }}</option>@endforeach</select></div>
        </form>

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
                        @forelse ($rows as $row)
                            <tr>
                                <td><input class="resident-check" type="checkbox" aria-label="Select {{ $row['request'] }}"></td>
                                <td>{{ $row['request'] }}</td>
                                <td>{{ $row['unit'] }}</td>
                                <td>
                                    <div class="resident-action-row">
                                        @include('resident-management.partials.action-button', ['label' => $row['kind'], 'icon' => $row['icon'], 'modal' => 'resident-move-form-modal', 'data' => ['data-form-mode' => 'edit', 'data-form-title' => 'Detail Permohonan', 'data-form-action' => route('resident-management.move-in-out.update', $row['id']), 'data-move-request-number' => $row['request_number'], 'data-move-resident-id' => $row['resident_id'] ?? '', 'data-move-unit-id' => $row['unit_id'] ?? '', 'data-move-request-type' => $row['kind'], 'data-move-scheduled-date' => $row['scheduled_date'] ?? '', 'data-move-status' => $row['status'], 'data-move-status-note' => $row['statusNote'] ?? '']])
                                    </div>
                                </td>
                                <td>{{ $row['resident'] }}</td>
                                <td>{{ $row['date'] }}</td>
                                <td><span class="resident-status {{ $row['statusClass'] }}">{{ $row['status'] }}@if ($row['statusNote'])<br>({{ $row['statusNote'] }})@endif</span></td>
                                <td>
                                    <div class="resident-action-row">
                                        @include('resident-management.partials.action-button', ['label' => 'Detail Permohonan', 'icon' => 'eye', 'modal' => 'resident-move-form-modal', 'data' => ['data-form-mode' => 'edit', 'data-form-title' => 'Detail Permohonan', 'data-form-action' => route('resident-management.move-in-out.update', $row['id']), 'data-move-request-number' => $row['request_number'], 'data-move-resident-id' => $row['resident_id'] ?? '', 'data-move-unit-id' => $row['unit_id'] ?? '', 'data-move-request-type' => $row['kind'], 'data-move-scheduled-date' => $row['scheduled_date'] ?? '', 'data-move-status' => $row['status'], 'data-move-status-note' => $row['statusNote'] ?? '']])
                                        @include('resident-management.partials.action-button', ['label' => 'Edit Permohonan', 'icon' => 'edit', 'modal' => 'resident-move-form-modal', 'data' => ['data-form-mode' => 'edit', 'data-form-title' => 'Edit Permohonan', 'data-form-action' => route('resident-management.move-in-out.update', $row['id']), 'data-move-request-number' => $row['request_number'], 'data-move-resident-id' => $row['resident_id'] ?? '', 'data-move-unit-id' => $row['unit_id'] ?? '', 'data-move-request-type' => $row['kind'], 'data-move-scheduled-date' => $row['scheduled_date'] ?? '', 'data-move-status' => $row['status'], 'data-move-status-note' => $row['statusNote'] ?? '']])
                                        <form method="POST" action="{{ route('resident-management.move-in-out.destroy', $row['id']) }}" onsubmit="return confirm('Hapus permohonan pindah ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="icon-action-btn resident-action-btn danger" type="submit" title="Hapus Permohonan" aria-label="Hapus Permohonan"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2m-9 0 1 14h6l1-14"/></svg></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8">Belum ada data permohonan pindah.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @include('resident-management.partials.pagination', ['paginator' => $moveRequests])
        </section>

        @include('resident-management.partials.benefits')
    </div>

    <div class="visitor-modal resident-modal" id="resident-move-form-modal" aria-hidden="true">
        <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
        <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="resident-move-form-title">
            <div class="visitor-modal-head"><h2 class="visitor-modal-title" id="resident-move-form-title">Catat Permohonan Baru</h2><button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button></div>
            <form class="visitor-modal-body" id="residentMoveForm" method="POST" action="{{ route('resident-management.move-in-out.store') }}">
                @csrf
                <input id="residentMoveFormMethod" type="hidden" name="_method" value="POST">
                <div class="visitor-form-grid">
                    <label class="resident-filter-field"><span>No. Permohonan</span><input id="moveRequestNumber" name="request_number" type="text" value="{{ old('request_number') }}" required></label>
                    <label class="resident-filter-field"><span>Nama Penghuni</span><select id="moveResidentId" name="resident_id">@foreach ($residentOptions as $residentOption)<option value="{{ $residentOption->id }}">{{ $residentOption->name }}</option>@endforeach</select></label>
                    <label class="resident-filter-field"><span>Unit</span><select id="moveUnitId" name="unit_id">@foreach ($unitOptions as $unitOption)<option value="{{ $unitOption->id }}">{{ $unitOption->code }} - {{ $unitOption->tower }}</option>@endforeach</select></label>
                    <label class="resident-filter-field"><span>Jenis Pindahan</span><select id="moveRequestType" name="request_type">@foreach ($requestTypes as $requestType)<option value="{{ $requestType }}">{{ $requestType }}</option>@endforeach</select></label>
                    <label class="resident-filter-field"><span>Tanggal Rencana</span><input id="moveScheduledDate" name="scheduled_date" type="date" value="{{ old('scheduled_date') }}"></label>
                    <label class="resident-filter-field"><span>Status</span><select id="moveStatus" name="status">@foreach ($moveStatuses as $moveStatus)<option value="{{ $moveStatus }}">{{ $moveStatus }}</option>@endforeach</select></label>
                    <label class="resident-filter-field"><span>Status Note</span><input id="moveStatusNote" name="status_note" type="text" value="{{ old('status_note') }}" placeholder="Kuning / Biru / catatan lain"></label>
                </div>
                <div class="visitor-form-actions"><button class="btn secondary" type="button" data-modal-close>Batal</button><button class="btn" type="submit">Simpan Permohonan</button></div>
            </form>
        </div>
    </div>

    <script>
        (() => {
            const form = document.getElementById('residentMoveForm');
            if (!form) return;
            const methodInput = document.getElementById('residentMoveFormMethod');
            const title = document.getElementById('resident-move-form-title');
            const fields = {
                requestNumber: document.getElementById('moveRequestNumber'),
                residentId: document.getElementById('moveResidentId'),
                unitId: document.getElementById('moveUnitId'),
                requestType: document.getElementById('moveRequestType'),
                scheduledDate: document.getElementById('moveScheduledDate'),
                status: document.getElementById('moveStatus'),
                statusNote: document.getElementById('moveStatusNote'),
            };
            document.querySelectorAll('[data-modal-open="resident-move-form-modal"]').forEach((button) => {
                button.addEventListener('click', () => {
                    const isEdit = button.dataset.formMode === 'edit';
                    title.textContent = button.dataset.formTitle || 'Catat Permohonan Baru';
                    form.action = button.dataset.formAction || '{{ route('resident-management.move-in-out.store') }}';
                    methodInput.value = isEdit ? 'PUT' : 'POST';
                    fields.requestNumber.value = button.dataset.moveRequestNumber || '';
                    fields.residentId.value = button.dataset.moveResidentId || '';
                    fields.unitId.value = button.dataset.moveUnitId || '';
                    fields.requestType.value = button.dataset.moveRequestType || 'Pindah Masuk';
                    fields.scheduledDate.value = button.dataset.moveScheduledDate || '';
                    fields.status.value = button.dataset.moveStatus || 'Menunggu Approval';
                    fields.statusNote.value = button.dataset.moveStatusNote || '';
                });
            });
        })();
    </script>
@endsection
