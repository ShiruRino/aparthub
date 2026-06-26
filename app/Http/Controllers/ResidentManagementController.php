<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Resident;
use App\Models\ResidentFamilyMember;
use App\Models\ResidentMoveRequest;
use App\Models\ResidentVehicle;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ResidentManagementController extends Controller
{
    private const RESIDENT_MAX_CAPACITY_KEY = 'resident_max_capacity';

    /**
     * Show the resident registration and monitoring page.
     */
    public function residents(Request $request): View
    {
        $residents = Resident::query()
            ->with(['unit.residents', 'familyMembers', 'moveRequests.unit'])
            ->when($request->string('search')->toString(), function (Builder $query, string $search) {
                $query->where(function (Builder $residentQuery) use ($search) {
                    $residentQuery->where('name', 'like', '%'.$search.'%')
                        ->orWhereHas('unit', fn (Builder $unitQuery) => $unitQuery->where('code', 'like', '%'.$search.'%'));
                });
            })
            ->when($request->filled('tower'), fn (Builder $query) => $query->whereHas('unit', fn (Builder $unitQuery) => $unitQuery->where('tower', $request->string('tower'))))
            ->when($request->filled('floor_band'), fn (Builder $query) => $query->whereHas('unit', fn (Builder $unitQuery) => $this->applyFloorBand($unitQuery, $request->string('floor_band')->toString())))
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->string('status')))
            ->when($request->filled('resident_type'), fn (Builder $query) => $query->where('resident_type', $request->string('resident_type')))
            ->orderByRaw("case status when 'Aktif' then 1 when 'Menunggu Approval' then 2 when 'Keluar' then 3 else 4 end")
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $residentPreview = $residents->getCollection()->first();
        $residentCapacity = $this->residentCapacitySummary();
        $rows = $residents->getCollection()->map(function (Resident $resident) {
            $owner = $resident->resident_type === 'Penyewa'
                ? $resident->unit?->residents
                    ?->first(fn (Resident $unitResident) => $unitResident->resident_type === 'Pemilik' && $unitResident->id !== $resident->id)
                : null;

            return [
                'id' => $resident->id,
                'name' => $resident->name,
                'email' => $resident->email,
                'mobile_no' => $resident->mobile_no,
                'unit' => $resident->unit?->code ? 'Unit '.$resident->unit->code : '-',
                'unit_id' => $resident->unit_id,
                'tower' => $resident->unit ? $resident->unit->tower.' / '.str_pad((string) $resident->unit->floor, 2, '0', STR_PAD_LEFT) : '-',
                'status' => $resident->status,
                'statusClass' => $this->residentStatusClass($resident->status),
                'type' => $resident->resident_type,
                'gender' => $this->residentGenderLabel($resident->gender),
                'gender_value' => $resident->gender,
                'genderClass' => $this->residentGenderClass($resident->gender),
                'date' => optional($resident->move_in_date)->format('d M Y') ?? 'TBD',
                'move_in_date' => optional($resident->move_in_date)->format('Y-m-d'),
                'move_out_date' => optional($resident->move_out_date)->format('Y-m-d'),
                'contract_end_date' => optional($resident->contract_end_date)->format('Y-m-d'),
                'contract_end_date_label' => optional($resident->contract_end_date)->format('d M Y') ?? '-',
                'avatar' => $this->initials($resident->name),
                'avatarClass' => $resident->avatar_tone,
                'avatar_tone' => $resident->avatar_tone,
                'owner_name' => $owner?->name,
                'family_members' => $resident->familyMembers
                    ->sortBy('name')
                    ->values()
                    ->map(fn (ResidentFamilyMember $familyMember) => [
                        'id' => $familyMember->id,
                        'name' => $familyMember->name,
                        'relationship' => $familyMember->relationship,
                        'birth' => optional($familyMember->birth_date)->format('d M Y') ?? '-',
                        'birth_date' => optional($familyMember->birth_date)->format('Y-m-d'),
                        'access_status' => $familyMember->access_status,
                        'status_class' => $this->residentStatusClass($familyMember->access_status),
                    ])
                    ->all(),
                'move_logs' => $resident->moveRequests
                    ->sortByDesc(fn (ResidentMoveRequest $moveRequest) => $moveRequest->scheduled_date?->timestamp ?? $moveRequest->created_at?->timestamp ?? 0)
                    ->values()
                    ->map(fn (ResidentMoveRequest $moveRequest) => [
                        'id' => $moveRequest->id,
                        'request_number' => $moveRequest->request_number,
                        'request_type' => $moveRequest->request_type,
                        'scheduled_date' => optional($moveRequest->scheduled_date)->format('d M Y') ?? 'Belum dijadwalkan',
                        'status' => $moveRequest->status,
                        'status_class' => $this->moveStatusClass($moveRequest->status),
                        'unit' => $moveRequest->unit?->code ? 'Unit '.$moveRequest->unit->code : '-',
                        'note' => $moveRequest->status_note,
                    ])
                    ->all(),
            ];
        });

        return view('resident-management.residents', [
            'rows' => $rows,
            'residents' => $residents,
            'filters' => $request->only(['search', 'tower', 'floor_band', 'status', 'resident_type']),
            'unitOptions' => $this->unitsForSelect(),
            'towers' => $this->towerOptions(),
            'floorBands' => $this->floorBandOptions(),
            'residentStatuses' => ['Aktif', 'Menunggu Approval', 'Keluar'],
            'residentTypes' => ['Pemilik', 'Penyewa'],
            'genderOptions' => $this->residentGenderOptions(),
            'residentPreview' => $residentPreview,
            'residentCapacity' => $residentCapacity,
        ]);
    }

    /**
     * Show the unit assignment page.
     */
    public function units(Request $request): View
    {
        $units = Unit::query()
            ->when($request->string('search')->toString(), fn (Builder $query, string $search) => $query->where('code', 'like', '%'.$search.'%'))
            ->when($request->filled('tower'), fn (Builder $query) => $query->where('tower', $request->string('tower')))
            ->when($request->filled('floor_band'), fn (Builder $query) => $this->applyFloorBand($query, $request->string('floor_band')->toString()))
            ->when($request->filled('occupancy_status'), fn (Builder $query) => $query->where('occupancy_status', $request->string('occupancy_status')))
            ->when($request->filled('unit_type'), fn (Builder $query) => $query->where('unit_type', $request->string('unit_type')))
            ->orderBy('tower')
            ->orderBy('floor')
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        $unitPreview = $units->getCollection()->first();
        $rows = $units->getCollection()->map(fn (Unit $unit) => [
            'id' => $unit->id,
            'unit' => 'Unit '.$unit->code,
            'code' => $unit->code,
            'tower_name' => $unit->tower,
            'floor_number' => $unit->floor,
            'tower' => $unit->tower.' / '.str_pad((string) $unit->floor, 2, '0', STR_PAD_LEFT),
            'type' => $unit->unit_type,
            'status' => $unit->occupancy_status,
            'statusClass' => $this->unitStatusClass($unit->occupancy_status),
            'payment' => $unit->payment_status,
            'thumb' => $unit->thumbnail_tone,
        ]);

        return view('resident-management.units', [
            'rows' => $rows,
            'units' => $units,
            'filters' => $request->only(['search', 'tower', 'floor_band', 'occupancy_status', 'unit_type']),
            'towers' => $this->towerOptions(),
            'floorBands' => $this->floorBandOptions(),
            'occupancyStatuses' => ['Terisi', 'Kosong', 'Perbaikan', 'Menunggu Inspeksi'],
            'paymentStatuses' => ['Lunas', 'Cicilan/Lunas', 'Belum Lunas'],
            'unitTypes' => $this->unitTypeOptions(),
            'unitPreview' => $unitPreview,
        ]);
    }

    /**
     * Show the move-in and move-out operations page.
     */
    public function moveInOut(Request $request): View
    {
        $requests = ResidentMoveRequest::query()
            ->with(['resident', 'unit'])
            ->when($request->string('search')->toString(), function (Builder $query, string $search) {
                $query->where(function (Builder $moveQuery) use ($search) {
                    $moveQuery->where('request_number', 'like', '%'.$search.'%')
                        ->orWhereHas('resident', fn (Builder $residentQuery) => $residentQuery->where('name', 'like', '%'.$search.'%'))
                        ->orWhereHas('unit', fn (Builder $unitQuery) => $unitQuery->where('code', 'like', '%'.$search.'%'));
                });
            })
            ->when($request->filled('tower'), fn (Builder $query) => $query->whereHas('unit', fn (Builder $unitQuery) => $unitQuery->where('tower', $request->string('tower'))))
            ->when($request->filled('floor_band'), fn (Builder $query) => $query->whereHas('unit', fn (Builder $unitQuery) => $this->applyFloorBand($unitQuery, $request->string('floor_band')->toString())))
            ->when($request->filled('request_type'), fn (Builder $query) => $query->where('request_type', $request->string('request_type')))
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->string('status')))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $movePreview = $requests->getCollection()->first();
        $rows = $requests->getCollection()->map(fn (ResidentMoveRequest $moveRequest) => [
            'id' => $moveRequest->id,
            'request' => $moveRequest->request_number,
            'request_number' => $moveRequest->request_number,
            'unit' => $moveRequest->unit?->code ? 'Unit '.$moveRequest->unit->code : '-',
            'unit_id' => $moveRequest->unit_id,
            'kind' => $moveRequest->request_type,
            'resident_id' => $moveRequest->resident_id,
            'resident' => $moveRequest->resident?->name ?? '-',
            'date' => optional($moveRequest->scheduled_date)->format('d M Y') ?? 'TBD (After Repair)',
            'scheduled_date' => optional($moveRequest->scheduled_date)->format('Y-m-d'),
            'status' => $moveRequest->status,
            'statusNote' => $moveRequest->status_note ?? '',
            'statusClass' => $this->moveStatusClass($moveRequest->status),
            'icon' => str_contains($moveRequest->request_type, 'Keluar') ? 'move' : 'slot',
        ]);

        return view('resident-management.move-in-out', [
            'rows' => $rows,
            'moveRequests' => $requests,
            'filters' => $request->only(['search', 'tower', 'floor_band', 'request_type', 'status']),
            'residentOptions' => $this->residentsForSelect(),
            'unitOptions' => $this->unitsForSelect(),
            'towers' => $this->towerOptions(),
            'floorBands' => $this->floorBandOptions(),
            'requestTypes' => ['Pindah Masuk', 'Pindah Keluar'],
            'moveStatuses' => ['Menunggu Approval', 'Sedang Berlangsung', 'Selesai'],
            'movePreview' => $movePreview,
        ]);
    }

    /**
     * Show the family member management page.
     */
    public function familyMembers(Request $request): View
    {
        $familyMembers = ResidentFamilyMember::query()
            ->with(['resident.unit'])
            ->when($request->string('search')->toString(), function (Builder $query, string $search) {
                $query->where(function (Builder $familyQuery) use ($search) {
                    $familyQuery->where('name', 'like', '%'.$search.'%')
                        ->orWhereHas('resident', fn (Builder $residentQuery) => $residentQuery->where('name', 'like', '%'.$search.'%'))
                        ->orWhereHas('resident.unit', fn (Builder $unitQuery) => $unitQuery->where('code', 'like', '%'.$search.'%'));
                });
            })
            ->when($request->filled('tower'), fn (Builder $query) => $query->whereHas('resident.unit', fn (Builder $unitQuery) => $unitQuery->where('tower', $request->string('tower'))))
            ->when($request->filled('floor_band'), fn (Builder $query) => $query->whereHas('resident.unit', fn (Builder $unitQuery) => $this->applyFloorBand($unitQuery, $request->string('floor_band')->toString())))
            ->when($request->filled('relationship'), fn (Builder $query) => $query->where('relationship', $request->string('relationship')))
            ->when($request->filled('access_status'), fn (Builder $query) => $query->where('access_status', $request->string('access_status')))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $familyPreview = $familyMembers->getCollection()->first();
        $rows = $familyMembers->getCollection()->values()->map(function (ResidentFamilyMember $member, int $index) use ($familyMembers) {
            return [
                'id' => $member->id,
                'no' => (($familyMembers->currentPage() - 1) * $familyMembers->perPage()) + $index + 1,
                'name' => $member->name,
                'unit' => $member->resident?->unit?->code ? 'Unit '.$member->resident->unit->code : '-',
                'resident_id' => $member->resident_id,
                'relation' => $member->relationship,
                'resident' => $member->resident?->name ?? '-',
                'birth' => optional($member->birth_date)->format('d M Y') ?? '-',
                'birth_date' => optional($member->birth_date)->format('Y-m-d'),
                'status' => $member->access_status,
                'statusClass' => $this->residentStatusClass($member->access_status),
                'icon' => $this->familyRelationIcon($member->relationship),
            ];
        });

        return view('resident-management.family-members', [
            'rows' => $rows,
            'familyMembers' => $familyMembers,
            'filters' => $request->only(['search', 'tower', 'floor_band', 'relationship', 'access_status']),
            'residentOptions' => $this->residentsForSelect(),
            'towers' => $this->towerOptions(),
            'floorBands' => $this->floorBandOptions(),
            'relationships' => ['Pasangan', 'Anak', 'Orang Tua'],
            'accessStatuses' => ['Aktif', 'Menunggu Approval'],
            'familyPreview' => $familyPreview,
        ]);
    }

    /**
     * Show the vehicle and parking management page.
     */
    public function vehicles(Request $request): View
    {
        $vehicles = ResidentVehicle::query()
            ->with(['resident', 'unit'])
            ->when($request->string('search')->toString(), function (Builder $query, string $search) {
                $query->where(function (Builder $vehicleQuery) use ($search) {
                    $vehicleQuery->where('plate_number', 'like', '%'.$search.'%')
                        ->orWhere('owner_name', 'like', '%'.$search.'%')
                        ->orWhereHas('unit', fn (Builder $unitQuery) => $unitQuery->where('code', 'like', '%'.$search.'%'));
                });
            })
            ->when($request->filled('tower'), fn (Builder $query) => $query->whereHas('unit', fn (Builder $unitQuery) => $unitQuery->where('tower', $request->string('tower'))))
            ->when($request->filled('floor_band'), fn (Builder $query) => $query->whereHas('unit', fn (Builder $unitQuery) => $this->applyFloorBand($unitQuery, $request->string('floor_band')->toString())))
            ->when($request->filled('vehicle_type'), fn (Builder $query) => $query->where('vehicle_type', $request->string('vehicle_type')))
            ->when($request->filled('parking_status'), fn (Builder $query) => $query->where('parking_status', $request->string('parking_status')))
            ->orderBy('owner_name')
            ->paginate(10)
            ->withQueryString();

        $vehiclePreview = $vehicles->getCollection()->first();
        $rows = $vehicles->getCollection()->values()->map(function (ResidentVehicle $vehicle, int $index) use ($vehicles) {
            return [
                'id' => $vehicle->id,
                'no' => (($vehicles->currentPage() - 1) * $vehicles->perPage()) + $index + 1,
                'plate' => 'Plat '.$vehicle->plate_number,
                'plate_number' => $vehicle->plate_number,
                'unit' => $vehicle->unit?->code ? 'Unit '.$vehicle->unit->code : '-',
                'unit_id' => $vehicle->unit_id,
                'resident_id' => $vehicle->resident_id,
                'kind' => 'Ikon '.$vehicle->vehicle_type,
                'vehicle_type' => $vehicle->vehicle_type,
                'owner' => $vehicle->owner_name,
                'model' => $vehicle->make_model,
                'status' => $vehicle->parking_status,
                'statusClass' => $this->residentStatusClass($vehicle->parking_status),
                'icon' => $this->vehicleIcon($vehicle->vehicle_type),
                'slot_label' => $vehicle->slot_label,
            ];
        });

        return view('resident-management.vehicles', [
            'rows' => $rows,
            'vehicles' => $vehicles,
            'filters' => $request->only(['search', 'tower', 'floor_band', 'vehicle_type', 'parking_status']),
            'residentOptions' => $this->residentsForSelect(),
            'unitOptions' => $this->unitsForSelect(),
            'towers' => $this->towerOptions(),
            'floorBands' => $this->floorBandOptions(),
            'vehicleTypes' => ['Mobil', 'Motor'],
            'parkingStatuses' => ['Aktif', 'Menunggu Approval'],
            'vehiclePreview' => $vehiclePreview,
        ]);
    }

    /**
     * Store a newly created resident.
     */
    public function storeResident(Request $request): RedirectResponse
    {
        $data = $this->validatedResident($request);

        DB::transaction(function () use ($data) {
            $this->ensureResidentCapacityAvailable();
            $resident = Resident::query()->create($data);
            $this->syncUnitOccupancyByResident($resident);
        });

        return redirect()
            ->route('resident-management.residents')
            ->with('status', 'Data residen berhasil ditambahkan.');
    }

    /**
     * Update maximum resident capacity configuration.
     */
    public function updateResidentCapacity(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'max_residents' => ['nullable', 'integer', 'min:1'],
        ]);

        AppSetting::putInteger(
            self::RESIDENT_MAX_CAPACITY_KEY,
            blank($data['max_residents'] ?? null) ? null : (int) $data['max_residents']
        );

        return redirect()
            ->route('resident-management.residents')
            ->with('status', 'Konfigurasi maksimum resident berhasil diperbarui.');
    }

    /**
     * Update the given resident.
     */
    public function updateResident(Request $request, Resident $resident): RedirectResponse
    {
        $data = $this->validatedResident($request, $resident);
        $previousUnitId = $resident->unit_id;

        DB::transaction(function () use ($resident, $data, $previousUnitId) {
            $resident->update($data);
            $resident->refresh();
            $this->syncUnitOccupancy($previousUnitId);
            $this->syncUnitOccupancyByResident($resident);
        });

        return redirect()
            ->route('resident-management.residents')
            ->with('status', 'Data residen berhasil diperbarui.');
    }

    /**
     * Remove the given resident.
     */
    public function destroyResident(Resident $resident): RedirectResponse
    {
        $unitId = $resident->unit_id;
        $resident->delete();
        $this->syncUnitOccupancy($unitId);

        return redirect()
            ->route('resident-management.residents')
            ->with('status', 'Data residen berhasil dihapus.');
    }

    /**
     * Store a newly created unit.
     */
    public function storeUnit(Request $request): RedirectResponse
    {
        Unit::query()->create($this->validatedUnit($request));

        return redirect()
            ->route('resident-management.units')
            ->with('status', 'Data unit berhasil ditambahkan.');
    }

    /**
     * Update the given unit.
     */
    public function updateUnit(Request $request, Unit $unit): RedirectResponse
    {
        $unit->update($this->validatedUnit($request, $unit));

        return redirect()
            ->route('resident-management.units')
            ->with('status', 'Data unit berhasil diperbarui.');
    }

    /**
     * Remove the given unit.
     */
    public function destroyUnit(Unit $unit): RedirectResponse
    {
        if ($unit->residents()->exists()) {
            return redirect()
                ->route('resident-management.units')
                ->withErrors(['unit' => 'Unit yang masih terhubung ke resident tidak bisa dihapus.']);
        }

        $unit->delete();

        return redirect()
            ->route('resident-management.units')
            ->with('status', 'Data unit berhasil dihapus.');
    }

    /**
     * Store a move request.
     */
    public function storeMoveRequest(Request $request): RedirectResponse
    {
        ResidentMoveRequest::query()->create($this->validatedMoveRequest($request));

        return redirect()
            ->route('resident-management.move-in-out')
            ->with('status', 'Permohonan pindah berhasil ditambahkan.');
    }

    /**
     * Update a move request.
     */
    public function updateMoveRequest(Request $request, ResidentMoveRequest $moveRequest): RedirectResponse
    {
        $moveRequest->update($this->validatedMoveRequest($request, $moveRequest));
        $this->applyMoveRequestSideEffects($moveRequest->fresh(['resident', 'unit']));

        return redirect()
            ->route('resident-management.move-in-out')
            ->with('status', 'Permohonan pindah berhasil diperbarui.');
    }

    /**
     * Remove a move request.
     */
    public function destroyMoveRequest(ResidentMoveRequest $moveRequest): RedirectResponse
    {
        $moveRequest->delete();

        return redirect()
            ->route('resident-management.move-in-out')
            ->with('status', 'Permohonan pindah berhasil dihapus.');
    }

    /**
     * Store a family member.
     */
    public function storeFamilyMember(Request $request): RedirectResponse
    {
        ResidentFamilyMember::query()->create($this->validatedFamilyMember($request));

        return redirect($this->familyMemberRedirectTarget($request))
            ->with('status', 'Anggota keluarga berhasil ditambahkan.');
    }

    /**
     * Update the given family member.
     */
    public function updateFamilyMember(Request $request, ResidentFamilyMember $familyMember): RedirectResponse
    {
        $familyMember->update($this->validatedFamilyMember($request));

        return redirect($this->familyMemberRedirectTarget($request))
            ->with('status', 'Anggota keluarga berhasil diperbarui.');
    }

    /**
     * Remove the given family member.
     */
    public function destroyFamilyMember(ResidentFamilyMember $familyMember): RedirectResponse
    {
        $familyMember->delete();

        return redirect(request()->input('redirect_to') === 'resident-management.residents'
            ? route('resident-management.residents')
            : route('resident-management.family-members'))
            ->with('status', 'Anggota keluarga berhasil dihapus.');
    }

    /**
     * Store a resident vehicle.
     */
    public function storeVehicle(Request $request): RedirectResponse
    {
        ResidentVehicle::query()->create($this->validatedVehicle($request));

        return redirect()
            ->route('resident-management.vehicles')
            ->with('status', 'Data kendaraan berhasil ditambahkan.');
    }

    /**
     * Update the given vehicle.
     */
    public function updateVehicle(Request $request, ResidentVehicle $vehicle): RedirectResponse
    {
        $vehicle->update($this->validatedVehicle($request, $vehicle));

        return redirect()
            ->route('resident-management.vehicles')
            ->with('status', 'Data kendaraan berhasil diperbarui.');
    }

    /**
     * Remove the given vehicle.
     */
    public function destroyVehicle(ResidentVehicle $vehicle): RedirectResponse
    {
        $vehicle->delete();

        return redirect()
            ->route('resident-management.vehicles')
            ->with('status', 'Data kendaraan berhasil dihapus.');
    }

    /**
     * Apply a floor band filter to the given unit query.
     */
    private function applyFloorBand(Builder $query, string $band): Builder
    {
        return match ($band) {
            '01-10' => $query->whereBetween('floor', [1, 10]),
            '11-20' => $query->whereBetween('floor', [11, 20]),
            '21+' => $query->where('floor', '>=', 21),
            default => $query,
        };
    }

    /**
     * Get distinct tower options from units.
     *
     * @return list<string>
     */
    private function towerOptions(): array
    {
        return Unit::query()
            ->orderBy('tower')
            ->distinct()
            ->pluck('tower')
            ->all();
    }

    /**
     * Get floor band options.
     *
     * @return list<string>
     */
    private function floorBandOptions(): array
    {
        return ['01-10', '11-20', '21+'];
    }

    /**
     * Get distinct unit types.
     *
     * @return list<string>
     */
    private function unitTypeOptions(): array
    {
        return Unit::query()
            ->orderBy('unit_type')
            ->distinct()
            ->pluck('unit_type')
            ->all();
    }

    /**
     * Get resident gender options.
     *
     * @return list<string>
     */
    private function residentGenderOptions(): array
    {
        return ['Male', 'Female', 'Prefer not to say'];
    }

    /**
     * Get unit select options.
     */
    private function unitsForSelect()
    {
        return Unit::query()
            ->orderBy('tower')
            ->orderBy('floor')
            ->orderBy('code')
            ->get();
    }

    /**
     * Get resident select options.
     */
    private function residentsForSelect()
    {
        return Resident::query()
            ->with('unit')
            ->orderBy('name')
            ->get();
    }

    /**
     * Build initials from a resident or owner name.
     */
    private function initials(string $name): string
    {
        return collect(preg_split('/\s+/', trim($name)) ?: [])
            ->take(2)
            ->map(fn (string $part) => strtoupper(substr($part, 0, 1)))
            ->implode('');
    }

    /**
     * Map status text to existing resident badge classes.
     */
    private function residentStatusClass(string $status): string
    {
        return match ($status) {
            'Aktif' => 'active',
            'Menunggu Approval' => 'pending',
            'Keluar' => 'out',
            default => 'active',
        };
    }

    /**
     * Normalize gender label for resident views.
     */
    private function residentGenderLabel(?string $gender): string
    {
        return blank($gender) ? 'Prefer not to say' : $gender;
    }

    /**
     * Map gender values to compact badge tones.
     */
    private function residentGenderClass(?string $gender): string
    {
        return match ($this->residentGenderLabel($gender)) {
            'Male' => 'status-approved',
            'Female' => 'status-pending',
            default => 'status-expired',
        };
    }

    /**
     * Map unit status text to existing unit badge classes.
     */
    private function unitStatusClass(string $status): string
    {
        return match ($status) {
            'Kosong' => 'empty',
            'Perbaikan' => 'repair',
            default => 'active',
        };
    }

    /**
     * Map move request status to existing classes.
     */
    private function moveStatusClass(string $status): string
    {
        return match ($status) {
            'Menunggu Approval' => 'pending',
            'Sedang Berlangsung' => 'process',
            'Selesai' => 'done',
            default => 'pending',
        };
    }

    /**
     * Return the inline icon path used for family relationship labels.
     */
    private function familyRelationIcon(string $relationship): string
    {
        return match ($relationship) {
            'Pasangan' => 'M7 19a5 5 0 0 1 10 0M9 8a3 3 0 1 0 6 0 3 3 0 0 0-6 0M4 17h16',
            'Anak' => 'M20 21a8 8 0 0 0-16 0M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8',
            'Orang Tua' => 'M8 12h8M7 8h10M6 16h12M7 12a5 5 0 0 0 10 0',
            default => 'M20 21a8 8 0 0 0-16 0M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8',
        };
    }

    /**
     * Return the inline icon path used for vehicles.
     */
    private function vehicleIcon(string $vehicleType): string
    {
        return match ($vehicleType) {
            'Motor' => 'M5 16h8l3-5h3M8 16a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM21 16a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM10 11h4l2 5M13 8h3',
            default => 'M5 16h14M7 16l1-5h8l1 5M7 16v2M17 16v2M6 18h.01M18 18h.01M9 11l1.2-3h3.6L15 11',
        };
    }

    /**
     * Validate resident payload.
     *
     * @return array<string, mixed>
     */
    private function validatedResident(Request $request, ?Resident $resident = null): array
    {
        $data = $request->validate([
            'unit_id' => ['nullable', 'exists:units,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email:rfc,dns', 'max:255', Rule::unique('residents', 'email')->ignore($resident)],
            'mobile_no' => ['nullable', 'string', 'max:30', Rule::unique('residents', 'mobile_no')->ignore($resident)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'resident_type' => ['required', Rule::in(['Pemilik', 'Penyewa'])],
            'status' => ['required', Rule::in(['Aktif', 'Menunggu Approval', 'Keluar'])],
            'gender' => ['nullable', Rule::in($this->residentGenderOptions())],
            'move_in_date' => ['nullable', 'date'],
            'move_out_date' => ['nullable', 'date', 'after_or_equal:move_in_date'],
            'contract_end_date' => ['nullable', 'date'],
            'avatar_tone' => ['nullable', 'string', 'max:50'],
        ]);

        $data['email'] = blank($data['email'] ?? null) ? null : $data['email'];
        $data['mobile_no'] = blank($data['mobile_no'] ?? null) ? null : $data['mobile_no'];
        $data['gender'] = blank($data['gender'] ?? null) ? null : $data['gender'];

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        return $data;
    }

    /**
     * Summarize the active resident capacity settings for the UI.
     *
     * @return array<string, int|bool|null>
     */
    private function residentCapacitySummary(): array
    {
        $current = Resident::query()->count();
        $maximum = AppSetting::getInteger(self::RESIDENT_MAX_CAPACITY_KEY);

        return [
            'current' => $current,
            'maximum' => $maximum,
            'remaining' => $maximum === null ? null : max($maximum - $current, 0),
            'is_limit_reached' => $maximum !== null && $current >= $maximum,
        ];
    }

    /**
     * Prevent resident creation when the configured maximum capacity is reached.
     */
    private function ensureResidentCapacityAvailable(): void
    {
        $maximum = AppSetting::getInteger(self::RESIDENT_MAX_CAPACITY_KEY);

        if ($maximum === null) {
            return;
        }

        $current = Resident::query()->count();

        if ($current >= $maximum) {
            throw ValidationException::withMessages([
                'resident_limit' => 'Batas maksimum resident sudah tercapai. Tambah resident baru diblokir sampai kapasitas dinaikkan.',
            ]);
        }
    }

    /**
     * Validate unit payload.
     *
     * @return array<string, mixed>
     */
    private function validatedUnit(Request $request, ?Unit $unit = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('units', 'code')->ignore($unit)],
            'tower' => ['required', 'string', 'max:255'],
            'floor' => ['required', 'integer', 'min:1', 'max:99'],
            'unit_type' => ['required', 'string', 'max:255'],
            'occupancy_status' => ['required', Rule::in(['Terisi', 'Kosong', 'Perbaikan', 'Menunggu Inspeksi'])],
            'payment_status' => ['required', Rule::in(['Lunas', 'Cicilan/Lunas', 'Belum Lunas'])],
            'thumbnail_tone' => ['nullable', 'string', 'max:50'],
        ]);
    }

    /**
     * Validate move request payload.
     *
     * @return array<string, mixed>
     */
    private function validatedMoveRequest(Request $request, ?ResidentMoveRequest $moveRequest = null): array
    {
        $data = $request->validate([
            'request_number' => ['required', 'string', 'max:50', Rule::unique('resident_move_requests', 'request_number')->ignore($moveRequest)],
            'resident_id' => ['nullable', 'exists:residents,id'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'request_type' => ['required', Rule::in(['Pindah Masuk', 'Pindah Keluar'])],
            'scheduled_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['Menunggu Approval', 'Sedang Berlangsung', 'Selesai'])],
            'status_note' => ['nullable', 'string', 'max:100'],
        ]);

        $data['status_note'] = blank($data['status_note'] ?? null) ? null : $data['status_note'];

        return $data;
    }

    /**
     * Validate family member payload.
     *
     * @return array<string, mixed>
     */
    private function validatedFamilyMember(Request $request): array
    {
        return $request->validate([
            'resident_id' => ['required', 'exists:residents,id'],
            'name' => ['required', 'string', 'max:255'],
            'relationship' => ['required', Rule::in(['Pasangan', 'Anak', 'Orang Tua'])],
            'birth_date' => ['nullable', 'date'],
            'access_status' => ['required', Rule::in(['Aktif', 'Menunggu Approval'])],
        ]);
    }

    /**
     * Validate vehicle payload.
     *
     * @return array<string, mixed>
     */
    private function validatedVehicle(Request $request, ?ResidentVehicle $vehicle = null): array
    {
        return $request->validate([
            'resident_id' => ['nullable', 'exists:residents,id'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'plate_number' => ['required', 'string', 'max:50', Rule::unique('resident_vehicles', 'plate_number')->ignore($vehicle)],
            'vehicle_type' => ['required', Rule::in(['Mobil', 'Motor'])],
            'owner_name' => ['required', 'string', 'max:255'],
            'make_model' => ['required', 'string', 'max:255'],
            'parking_status' => ['required', Rule::in(['Aktif', 'Menunggu Approval'])],
            'slot_label' => ['nullable', 'string', 'max:100'],
        ]);
    }

    /**
     * Resolve redirect target for family member actions.
     */
    private function familyMemberRedirectTarget(Request $request): string
    {
        return $request->input('redirect_to') === 'resident-management.residents'
            ? route('resident-management.residents')
            : route('resident-management.family-members');
    }

    /**
     * Apply simple side effects after a move request changes state.
     */
    private function applyMoveRequestSideEffects(ResidentMoveRequest $moveRequest): void
    {
        $resident = $moveRequest->resident;

        if (! $resident) {
            return;
        }

        if ($moveRequest->status === 'Selesai' && $moveRequest->request_type === 'Pindah Keluar') {
            $resident->update([
                'status' => 'Keluar',
                'move_out_date' => $moveRequest->scheduled_date ?? now()->toDateString(),
            ]);
        }

        if ($moveRequest->status === 'Selesai' && $moveRequest->request_type === 'Pindah Masuk') {
            $resident->update([
                'unit_id' => $moveRequest->unit_id,
                'status' => 'Aktif',
                'move_in_date' => $moveRequest->scheduled_date ?? now()->toDateString(),
            ]);
        }

        $this->syncUnitOccupancyByResident($resident->fresh());
    }

    /**
     * Sync occupancy state for a unit by resident assignment.
     */
    private function syncUnitOccupancyByResident(?Resident $resident): void
    {
        $this->syncUnitOccupancy($resident?->unit_id);
    }

    /**
     * Sync occupancy state for a unit.
     */
    private function syncUnitOccupancy(?int $unitId): void
    {
        if (! $unitId) {
            return;
        }

        $unit = Unit::query()->find($unitId);

        if (! $unit) {
            return;
        }

        $hasActiveResident = $unit->residents()
            ->where('status', 'Aktif')
            ->exists();

        if ($hasActiveResident && $unit->occupancy_status !== 'Terisi') {
            $unit->update(['occupancy_status' => 'Terisi']);

            return;
        }

        if (! $hasActiveResident && $unit->occupancy_status === 'Terisi') {
            $unit->update(['occupancy_status' => 'Kosong']);
        }
    }
}
