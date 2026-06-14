@extends('layouts.app')

@section('title', 'Residents')
@section('topbar_context', 'Resident Management Flow')
@section('topbar_subtitle', 'Complete Resident Lifecycle Management from Move-In to Move-Out')

@section('content')
    <div class="resident-list-page">
        <header class="resident-page-head">
            <h2>Daftar Residen Aether Residences</h2>
            <button
                class="btn"
                type="button"
                data-modal-open="resident-resident-form-modal"
                data-form-mode="create"
                data-form-title="Tambah Residen Baru"
                data-form-action="{{ route('resident-management.residents.store') }}"
            >
                Tambah Residen Baru
            </button>
        </header>

        <form class="resident-filter-panel" method="GET" action="{{ route('resident-management.residents') }}" aria-label="Filter daftar residen">
            <div class="resident-filter-field">
                <label for="resident-search">Search</label>
                <div class="resident-search">
                    <input id="resident-search" name="search" type="search" placeholder="Search" value="{{ $filters['search'] ?? '' }}">
                    <button type="submit" aria-label="Search">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                    </button>
                </div>
            </div>
            <div class="resident-filter-field">
                <label for="resident-tower">Tower</label>
                <select id="resident-tower" name="tower">
                    <option value="">Semua Tower</option>
                    @foreach ($towers as $tower)
                        <option value="{{ $tower }}" @selected(($filters['tower'] ?? '') === $tower)>{{ $tower }}</option>
                    @endforeach
                </select>
            </div>
            <div class="resident-filter-field">
                <label for="resident-floor">Lantai</label>
                <select id="resident-floor" name="floor_band">
                    <option value="">Semua Lantai</option>
                    @foreach ($floorBands as $band)
                        <option value="{{ $band }}" @selected(($filters['floor_band'] ?? '') === $band)>{{ $band }}</option>
                    @endforeach
                </select>
            </div>
            <div class="resident-filter-field">
                <label for="resident-status">Status Residen</label>
                <select id="resident-status" name="status">
                    <option value="">Semua Status</option>
                    @foreach ($residentStatuses as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div class="resident-filter-field">
                <label for="resident-type">Jenis Residen</label>
                <select id="resident-type" name="resident_type">
                    <option value="">Semua Jenis</option>
                    @foreach ($residentTypes as $type)
                        <option value="{{ $type }}" @selected(($filters['resident_type'] ?? '') === $type)>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
        </form>

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
                        @forelse ($rows as $row)
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
                                        @include('resident-management.partials.action-button', [
                                            'label' => 'Detail Residen',
                                            'icon' => 'eye',
                                            'modal' => 'resident-resident-form-modal',
                                            'data' => [
                                                'data-form-mode' => 'edit',
                                                'data-form-title' => 'Detail Residen',
                                                'data-form-action' => route('resident-management.residents.update', $row['id']),
                                                'data-resident-name' => $row['name'],
                                                'data-resident-unit-id' => $row['unit_id'] ?? '',
                                                'data-resident-type' => $row['type'],
                                                'data-resident-status' => $row['status'],
                                                'data-resident-move-in-date' => $row['move_in_date'] ?? '',
                                                'data-resident-move-out-date' => $row['move_out_date'] ?? '',
                                                'data-resident-avatar-tone' => $row['avatar_tone'] ?? '',
                                            ],
                                        ])
                                        @include('resident-management.partials.action-button', [
                                            'label' => 'Edit Residen',
                                            'icon' => 'edit',
                                            'modal' => 'resident-resident-form-modal',
                                            'data' => [
                                                'data-form-mode' => 'edit',
                                                'data-form-title' => 'Edit Residen',
                                                'data-form-action' => route('resident-management.residents.update', $row['id']),
                                                'data-resident-name' => $row['name'],
                                                'data-resident-unit-id' => $row['unit_id'] ?? '',
                                                'data-resident-type' => $row['type'],
                                                'data-resident-status' => $row['status'],
                                                'data-resident-move-in-date' => $row['move_in_date'] ?? '',
                                                'data-resident-move-out-date' => $row['move_out_date'] ?? '',
                                                'data-resident-avatar-tone' => $row['avatar_tone'] ?? '',
                                            ],
                                        ])
                                        <form method="POST" action="{{ route('resident-management.residents.destroy', $row['id']) }}" onsubmit="return confirm('Hapus data residen ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="icon-action-btn resident-action-btn danger" type="submit" title="Hapus Residen" aria-label="Hapus Residen">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M3 6h18M8 6V4h8v2m-9 0 1 14h6l1-14"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">Belum ada data residen.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @include('resident-management.partials.pagination', ['paginator' => $residents])
        </section>

        @include('resident-management.partials.benefits')
    </div>

    <div class="visitor-modal resident-modal" id="resident-resident-form-modal" aria-hidden="true">
        <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
        <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="resident-resident-form-title">
            <div class="visitor-modal-head">
                <h2 class="visitor-modal-title" id="resident-resident-form-title">Tambah Residen Baru</h2>
                <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
            </div>
            <form class="visitor-modal-body" id="residentResidentForm" method="POST" action="{{ route('resident-management.residents.store') }}">
                @csrf
                <input id="residentResidentFormMethod" type="hidden" name="_method" value="POST">
                <div class="visitor-form-grid">
                    <label class="resident-filter-field">
                        <span>Nama Residen</span>
                        <input id="residentName" name="name" type="text" value="{{ old('name') }}" required>
                    </label>
                    <label class="resident-filter-field">
                        <span>Unit</span>
                        <select id="residentUnitId" name="unit_id">
                            <option value="">Belum ditentukan</option>
                            @foreach ($unitOptions as $unitOption)
                                <option value="{{ $unitOption->id }}">{{ $unitOption->code }} - {{ $unitOption->tower }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="resident-filter-field">
                        <span>Jenis Residen</span>
                        <select id="residentType" name="resident_type" required>
                            @foreach ($residentTypes as $residentType)
                                <option value="{{ $residentType }}">{{ $residentType }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="resident-filter-field">
                        <span>Status</span>
                        <select id="residentStatus" name="status" required>
                            @foreach ($residentStatuses as $residentStatus)
                                <option value="{{ $residentStatus }}">{{ $residentStatus }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="resident-filter-field">
                        <span>Tanggal Masuk</span>
                        <input id="residentMoveInDate" name="move_in_date" type="date" value="{{ old('move_in_date') }}">
                    </label>
                    <label class="resident-filter-field">
                        <span>Tanggal Keluar</span>
                        <input id="residentMoveOutDate" name="move_out_date" type="date" value="{{ old('move_out_date') }}">
                    </label>
                    <label class="resident-filter-field">
                        <span>Avatar Tone</span>
                        <select id="residentAvatarTone" name="avatar_tone">
                            <option value="default">Default</option>
                            <option value="female">Female</option>
                            <option value="pending">Pending</option>
                            <option value="out">Out</option>
                        </select>
                    </label>
                </div>

                <div class="visitor-form-actions">
                    <button class="btn secondary" type="button" data-modal-close>Batal</button>
                    <button class="btn" type="submit">Simpan Residen</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (() => {
            const modal = document.getElementById('resident-resident-form-modal');
            const form = document.getElementById('residentResidentForm');
            const methodInput = document.getElementById('residentResidentFormMethod');
            const title = document.getElementById('resident-resident-form-title');
            const nameInput = document.getElementById('residentName');
            const unitInput = document.getElementById('residentUnitId');
            const typeInput = document.getElementById('residentType');
            const statusInput = document.getElementById('residentStatus');
            const moveInInput = document.getElementById('residentMoveInDate');
            const moveOutInput = document.getElementById('residentMoveOutDate');
            const avatarInput = document.getElementById('residentAvatarTone');

            if (!modal || !form) return;

            document.querySelectorAll('[data-modal-open="resident-resident-form-modal"]').forEach((button) => {
                button.addEventListener('click', () => {
                    const isEdit = button.dataset.formMode === 'edit';

                    title.textContent = button.dataset.formTitle || 'Tambah Residen Baru';
                    form.action = button.dataset.formAction || '{{ route('resident-management.residents.store') }}';
                    methodInput.value = isEdit ? 'PUT' : 'POST';

                    nameInput.value = button.dataset.residentName || '';
                    unitInput.value = button.dataset.residentUnitId || '';
                    typeInput.value = button.dataset.residentType || 'Pemilik';
                    statusInput.value = button.dataset.residentStatus || 'Menunggu Approval';
                    moveInInput.value = button.dataset.residentMoveInDate || '';
                    moveOutInput.value = button.dataset.residentMoveOutDate || '';
                    avatarInput.value = button.dataset.residentAvatarTone || 'default';
                });
            });
        })();
    </script>
@endsection
