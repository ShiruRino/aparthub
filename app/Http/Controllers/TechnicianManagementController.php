<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Role;
use App\Models\ServiceRequest;
use App\Models\TechnicianTeam;
use App\Models\User;
use App\Models\UserModule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TechnicianManagementController extends Controller
{
    public function index(Request $request): View
    {
        $technicians = $this->technicianQuery()
            ->with(['role', 'technicianProfile', 'technicianTeams'])
            ->when($request->string('search')->toString(), function (Builder $query, string $search) {
                $query->where(function (Builder $builder) use ($search) {
                    $builder->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('mobile_no', 'like', '%'.$search.'%')
                        ->orWhereHas('technicianTeams', function (Builder $teamQuery) use ($search) {
                            $teamQuery->where('name', 'like', '%'.$search.'%');
                        });
                });
            })
            ->orderBy('name')
            ->paginate(10, ['*'], 'technicians_page')
            ->withQueryString();

        $teams = TechnicianTeam::query()
            ->withCount([
                'users' => function ($query) {
                    $this->applyTechnicianConstraints($query);
                },
            ])
            ->with([
                'users' => function ($query) {
                    $this->applyTechnicianConstraints($query)
                        ->orderBy('name');
                },
            ])
            ->orderBy('name')
            ->get();

        $serviceSummary = [
            'assigned' => ServiceRequest::query()
                ->canonicalStatus(ServiceRequest::STATUS_ASSIGNED)
                ->count(),

            'on_the_way' => ServiceRequest::query()
                ->canonicalStatus(ServiceRequest::STATUS_ON_THE_WAY)
                ->count(),

            'in_progress' => ServiceRequest::query()
                ->canonicalStatus(ServiceRequest::STATUS_IN_PROGRESS)
                ->count(),

            'completed' => ServiceRequest::query()
                ->canonicalStatus(ServiceRequest::STATUS_COMPLETED)
                ->count(),

            'teams' => $teams->count(),
            'technicians' => $technicians->total(),
        ];

        return view('technician-management.index', [
            'technicians' => $technicians,
            'teams' => $teams,

            'teamOptions' => TechnicianTeam::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),

            'technicianOptions' => $this->technicianQuery()
                ->where('is_active', true)
                ->with(['technicianProfile'])
                ->orderBy('name')
                ->get(),

            'serviceSummary' => $serviceSummary,
        ]);
    }

    public function storeTechnician(Request $request): RedirectResponse
    {
        $data = $this->validatedTechnician($request);

        DB::transaction(function () use ($request, $data) {
            $technicianRole = Role::query()->firstOrCreate(
                ['slug' => 'technician'],
                ['name' => 'Technician']
            );

            $user = User::query()->create([
                'role_id' => $technicianRole->id,
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'] ?: null,
                'mobile_no' => $data['mobile_no'] ?: null,
                'is_active' => (bool) $data['is_active'],
                'password' => $data['password'],
            ]);

            $profile = $user->technicianProfile()->create([
                'skills' => $this->linesToArray($data['skills'] ?? null),
                'certifications' => $this->linesToArray($data['certifications'] ?? null),
                'notification_enabled' => (bool) ($data['notification_enabled'] ?? true),
            ]);

            if ($request->hasFile('profile_photo')) {
                $profile->profile_photo_path = $request->file('profile_photo')
                    ->store('technicians/profile-photos', 'public');

                $profile->save();
            }

            $user->technicianTeams()->sync($data['team_ids'] ?? []);

            $this->grantTechnicianModules($user);
        });

        return redirect()
            ->route('technician-management.index')
            ->with('status', 'Technician berhasil ditambahkan.');
    }

    public function updateTechnician(Request $request, User $technician): RedirectResponse
    {
        abort_unless(
            $this->technicianQuery()
                ->whereKey($technician->getKey())
                ->exists(),
            404
        );

        $data = $this->validatedTechnician($request, $technician);

        DB::transaction(function () use ($request, $technician, $data) {
            $technician->update([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'] ?: null,
                'mobile_no' => $data['mobile_no'] ?: null,
                'is_active' => (bool) $data['is_active'],
                'password' => $data['password'] ?: $technician->password,
            ]);

            $profile = $technician->technicianProfile;

            if (! $profile) {
                throw ValidationException::withMessages([
                    'technician' => 'Profil teknisi tidak ditemukan.',
                ]);
            }

            $profile->skills = $this->linesToArray($data['skills'] ?? null);
            $profile->certifications = $this->linesToArray($data['certifications'] ?? null);
            $profile->notification_enabled = (bool) ($data['notification_enabled'] ?? true);

            if ($request->hasFile('profile_photo')) {
                $profile->profile_photo_path = $request->file('profile_photo')
                    ->store('technicians/profile-photos', 'public');
            }

            $profile->save();

            $technician->technicianTeams()->sync($data['team_ids'] ?? []);

            $this->grantTechnicianModules($technician);
        });

        return redirect()
            ->route('technician-management.index')
            ->with('status', 'Technician berhasil diperbarui.');
    }

    public function storeTeam(Request $request): RedirectResponse
    {
        $data = $this->validatedTeam($request);

        DB::transaction(function () use ($data) {
            $team = TechnicianTeam::query()->create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'is_active' => (bool) $data['is_active'],
            ]);

            $team->users()->sync($data['member_ids']);
        });

        return redirect()
            ->route('technician-management.index')
            ->with('status', 'Team technician berhasil ditambahkan.');
    }

    public function updateTeam(Request $request, TechnicianTeam $team): RedirectResponse
    {
        $data = $this->validatedTeam($request, $team);

        DB::transaction(function () use ($team, $data) {
            $team->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'is_active' => (bool) $data['is_active'],
            ]);

            $team->users()->sync($data['member_ids']);
        });

        return redirect()
            ->route('technician-management.index')
            ->with('status', 'Team technician berhasil diperbarui.');
    }

    private function validatedTechnician(Request $request, ?User $technician = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($technician?->id),
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($technician?->id),
            ],

            'mobile_no' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('users', 'mobile_no')->ignore($technician?->id),
            ],

            'password' => [
                $technician ? 'nullable' : 'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'is_active' => ['required', 'boolean'],

            'profile_photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'skills' => ['nullable', 'string'],
            'certifications' => ['nullable', 'string'],
            'notification_enabled' => ['nullable', 'boolean'],

            'team_ids' => ['nullable', 'array'],
            'team_ids.*' => ['exists:technician_teams,id'],
        ]);
    }

    private function validatedTeam(Request $request, ?TechnicianTeam $team = null): array
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                $team
                    ? Rule::unique('technician_teams', 'name')->ignore($team->id)
                    : Rule::unique('technician_teams', 'name'),
            ],

            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],

            'member_ids' => ['nullable', 'array'],
            'member_ids.*' => ['exists:users,id'],
        ]);

        $data['member_ids'] = $this->validatedTechnicianMemberIds(
            $data['member_ids'] ?? []
        );

        return $data;
    }

    private function validatedTechnicianMemberIds(array $memberIds): array
    {
        $memberIds = collect($memberIds)
            ->filter(fn ($id) => filled($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if ($memberIds === []) {
            return [];
        }

        $validMemberCount = $this->technicianQuery()
            ->whereIn('id', $memberIds)
            ->count();

        if ($validMemberCount !== count($memberIds)) {
            throw ValidationException::withMessages([
                'member_ids' => 'Anggota team hanya boleh berasal dari akun technician yang memiliki profil technician.',
            ]);
        }

        return $memberIds;
    }

    private function technicianQuery(): Builder
    {
        return $this->applyTechnicianConstraints(User::query());
    }

    /**
     * Supports both ordinary Eloquent Builder and BelongsToMany relation.
     *
     * TechnicianTeam::users() is a BelongsToMany relation, therefore it
     * cannot be type-hinted as Builder directly inside eager-load closures.
     */
    private function applyTechnicianConstraints(
        Builder|BelongsToMany $query
    ): Builder {
        $builder = $query instanceof BelongsToMany
            ? $query->getQuery()
            : $query;

        return $builder
            ->whereHas('role', function (Builder $roleQuery) {
                $roleQuery->where('slug', 'technician');
            })
            ->whereHas('technicianProfile');
    }

    private function linesToArray(?string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $value))
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    }

    private function grantTechnicianModules(User $user): void
    {
        $slugs = [
            'technician-management',
            'service-request',
        ];

        Module::query()
            ->whereIn('slug', $slugs)
            ->get()
            ->each(function (Module $module) use ($user) {
                UserModule::query()->updateOrCreate([
                    'user_id' => $user->id,
                    'module_id' => $module->id,
                ], [
                    'can_create' => $module->slug === 'technician-management',
                    'can_read' => true,
                    'can_update' => true,
                    'can_delete' => false,
                ]);
            });
    }
}
