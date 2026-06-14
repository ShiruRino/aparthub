@extends('layouts.app')

@section('title', 'Family Member')
@section('topbar_context', 'Resident Management Flow')
@section('topbar_subtitle', 'Complete Resident Lifecycle Management from Move-In to Move-Out')

@section('content')
    <div class="resident-list-page">
        <header class="resident-page-head">
            <h2>Daftar Anggota Keluarga Aether Residences</h2>
            <button class="btn" type="button" data-modal-open="resident-family-form-modal" data-form-mode="create" data-form-title="Tambah Anggota Baru" data-form-action="{{ route('resident-management.family-members.store') }}">Tambah Anggota Baru</button>
        </header>

        <form class="resident-filter-panel" method="GET" action="{{ route('resident-management.family-members') }}" aria-label="Filter anggota keluarga">
            <div class="resident-filter-field">
                <label for="family-search">Search</label>
                <div class="resident-search">
                    <input id="family-search" name="search" type="search" placeholder="Search" value="{{ $filters['search'] ?? '' }}">
                    <button type="submit" aria-label="Search">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                    </button>
                </div>
            </div>
            <div class="resident-filter-field"><label for="family-tower">Tower</label><select id="family-tower" name="tower"><option value="">Semua Tower</option>@foreach ($towers as $tower)<option value="{{ $tower }}" @selected(($filters['tower'] ?? '') === $tower)>{{ $tower }}</option>@endforeach</select></div>
            <div class="resident-filter-field"><label for="family-floor">Lantai</label><select id="family-floor" name="floor_band"><option value="">Semua Lantai</option>@foreach ($floorBands as $band)<option value="{{ $band }}" @selected(($filters['floor_band'] ?? '') === $band)>{{ $band }}</option>@endforeach</select></div>
            <div class="resident-filter-field"><label for="family-relation">Hubungan</label><select id="family-relation" name="relationship"><option value="">Semua Hubungan</option>@foreach ($relationships as $relationship)<option value="{{ $relationship }}" @selected(($filters['relationship'] ?? '') === $relationship)>{{ $relationship }}</option>@endforeach</select></div>
            <div class="resident-filter-field"><label for="family-access">Status Akses</label><select id="family-access" name="access_status"><option value="">Semua Status</option>@foreach ($accessStatuses as $accessStatus)<option value="{{ $accessStatus }}" @selected(($filters['access_status'] ?? '') === $accessStatus)>{{ $accessStatus }}</option>@endforeach</select></div>
        </form>

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
                        @forelse ($rows as $row)
                            <tr>
                                <td><input class="resident-check" type="checkbox" aria-label="Select {{ $row['name'] }}"></td>
                                <td>{{ $row['no'] }}</td>
                                <td>{{ $row['name'] }}</td>
                                <td>{{ $row['unit'] }}</td>
                                <td>
                                    @if ($row['relation'])
                                        <div class="resident-action-row">
                                            <button class="resident-action-btn" type="button" data-modal-open="resident-family-form-modal" data-form-mode="edit" data-form-title="Detail Anggota Keluarga" data-form-action="{{ route('resident-management.family-members.update', $row['id']) }}" data-family-name="{{ $row['name'] }}" data-family-resident-id="{{ $row['resident_id'] }}" data-family-relationship="{{ $row['relation'] }}" data-family-birth-date="{{ $row['birth_date'] ?? '' }}" data-family-access-status="{{ $row['status'] }}">
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
                                        @include('resident-management.partials.action-button', ['label' => 'Detail Anggota', 'icon' => 'eye', 'modal' => 'resident-family-form-modal', 'data' => ['data-form-mode' => 'edit', 'data-form-title' => 'Detail Anggota Keluarga', 'data-form-action' => route('resident-management.family-members.update', $row['id']), 'data-family-name' => $row['name'], 'data-family-resident-id' => $row['resident_id'], 'data-family-relationship' => $row['relation'], 'data-family-birth-date' => $row['birth_date'] ?? '', 'data-family-access-status' => $row['status']]])
                                        @include('resident-management.partials.action-button', ['label' => 'Edit Anggota', 'icon' => 'edit', 'modal' => 'resident-family-form-modal', 'data' => ['data-form-mode' => 'edit', 'data-form-title' => 'Edit Anggota Keluarga', 'data-form-action' => route('resident-management.family-members.update', $row['id']), 'data-family-name' => $row['name'], 'data-family-resident-id' => $row['resident_id'], 'data-family-relationship' => $row['relation'], 'data-family-birth-date' => $row['birth_date'] ?? '', 'data-family-access-status' => $row['status']]])
                                        <form method="POST" action="{{ route('resident-management.family-members.destroy', $row['id']) }}" onsubmit="return confirm('Hapus anggota keluarga ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="icon-action-btn resident-action-btn danger" type="submit" title="Hapus Anggota" aria-label="Hapus Anggota"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2m-9 0 1 14h6l1-14"/></svg></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9">Belum ada data anggota keluarga.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @include('resident-management.partials.pagination', ['paginator' => $familyMembers])
        </section>

        @include('resident-management.partials.benefits')
    </div>

    <div class="visitor-modal resident-modal" id="resident-family-form-modal" aria-hidden="true">
        <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
        <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="resident-family-form-title">
            <div class="visitor-modal-head"><h2 class="visitor-modal-title" id="resident-family-form-title">Tambah Anggota Baru</h2><button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button></div>
            <form class="visitor-modal-body" id="residentFamilyForm" method="POST" action="{{ route('resident-management.family-members.store') }}">
                @csrf
                <input id="residentFamilyFormMethod" type="hidden" name="_method" value="POST">
                <div class="visitor-form-grid">
                    <label class="resident-filter-field"><span>Nama Anggota</span><input id="familyName" name="name" type="text" value="{{ old('name') }}" required></label>
                    <label class="resident-filter-field"><span>Residen Utama</span><select id="familyResidentId" name="resident_id" required>@foreach ($residentOptions as $residentOption)<option value="{{ $residentOption->id }}">{{ $residentOption->name }}@if($residentOption->unit) - {{ $residentOption->unit->code }}@endif</option>@endforeach</select></label>
                    <label class="resident-filter-field"><span>Hubungan</span><select id="familyRelationship" name="relationship" required>@foreach ($relationships as $relationship)<option value="{{ $relationship }}">{{ $relationship }}</option>@endforeach</select></label>
                    <label class="resident-filter-field"><span>Tanggal Lahir</span><input id="familyBirthDate" name="birth_date" type="date" value="{{ old('birth_date') }}"></label>
                    <label class="resident-filter-field"><span>Status Akses</span><select id="familyAccessStatus" name="access_status" required>@foreach ($accessStatuses as $accessStatus)<option value="{{ $accessStatus }}">{{ $accessStatus }}</option>@endforeach</select></label>
                </div>
                <div class="visitor-form-actions"><button class="btn secondary" type="button" data-modal-close>Batal</button><button class="btn" type="submit">Simpan Anggota</button></div>
            </form>
        </div>
    </div>

    <script>
        (() => {
            const form = document.getElementById('residentFamilyForm');
            if (!form) return;
            const methodInput = document.getElementById('residentFamilyFormMethod');
            const title = document.getElementById('resident-family-form-title');
            const fields = {
                name: document.getElementById('familyName'),
                residentId: document.getElementById('familyResidentId'),
                relationship: document.getElementById('familyRelationship'),
                birthDate: document.getElementById('familyBirthDate'),
                accessStatus: document.getElementById('familyAccessStatus'),
            };
            document.querySelectorAll('[data-modal-open="resident-family-form-modal"]').forEach((button) => {
                button.addEventListener('click', () => {
                    const isEdit = button.dataset.formMode === 'edit';
                    title.textContent = button.dataset.formTitle || 'Tambah Anggota Baru';
                    form.action = button.dataset.formAction || '{{ route('resident-management.family-members.store') }}';
                    methodInput.value = isEdit ? 'PUT' : 'POST';
                    fields.name.value = button.dataset.familyName || '';
                    fields.residentId.value = button.dataset.familyResidentId || '';
                    fields.relationship.value = button.dataset.familyRelationship || 'Pasangan';
                    fields.birthDate.value = button.dataset.familyBirthDate || '';
                    fields.accessStatus.value = button.dataset.familyAccessStatus || 'Aktif';
                });
            });
        })();
    </script>
@endsection
