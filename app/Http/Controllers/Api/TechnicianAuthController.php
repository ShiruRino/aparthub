<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TechnicianAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()
            ->with(['role', 'technicianProfile', 'technicianTeams'])
            ->where(function ($query) use ($credentials) {
                $query->where('email', $credentials['login'])
                    ->orWhere('mobile_no', $credentials['login']);
            })
            ->first();

        if (
            ! $user
            || ! Hash::check($credentials['password'], $user->password)
            || ! $user->isTechnician()
            || ! $user->activeForApi()
            || ! $user->canAccessModule('service-request', 'read')
        ) {
            return response()->json([
                'message' => 'Kredensial technician tidak valid.',
            ], 401);
        }

        $token = $user->createToken($request->userAgent() ?: 'technician-api')->plainTextToken;

        return response()->json([
            'message' => 'Login technician berhasil.',
            'data' => $this->profilePayload($user) + [
                'token' => $token,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->loadMissing(['role', 'technicianProfile', 'technicianTeams']);

        return response()->json([
            'data' => $this->profilePayload($user),
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->loadMissing('technicianProfile');

        $validated = $request->validate([
            'mobile_no' => ['nullable', 'string', 'max:255', Rule::unique('users', 'mobile_no')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'notification_enabled' => ['nullable', 'boolean'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'name' => ['prohibited'],
            'email' => ['prohibited'],
            'team' => ['prohibited'],
            'team_ids' => ['prohibited'],
            'role' => ['prohibited'],
            'skills' => ['prohibited'],
            'certifications' => ['prohibited'],
        ]);

        DB::transaction(function () use ($request, $user, $validated) {
            if (array_key_exists('mobile_no', $validated)) {
                $user->mobile_no = $validated['mobile_no'] ?: null;
            }

            if (! empty($validated['password'])) {
                $user->password = $validated['password'];
            }

            $user->save();

            $profile = $user->technicianProfile ?: $user->technicianProfile()->create();

            if ($request->hasFile('profile_photo')) {
                $profile->profile_photo_path = $request->file('profile_photo')->store('technicians/profile-photos', 'public');
            }

            if (array_key_exists('notification_enabled', $validated)) {
                $profile->notification_enabled = (bool) $validated['notification_enabled'];
            }

            $profile->save();
        });

        $user->refresh()->load(['role', 'technicianProfile', 'technicianTeams']);

        return response()->json([
            'message' => 'Profil technician berhasil diperbarui.',
            'data' => $this->profilePayload($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logout technician berhasil.',
        ]);
    }

    private function profilePayload(User $user): array
    {
        $profile = $user->technicianProfile;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'mobile_no' => $user->mobile_no,
            'username' => $user->username,
            'is_active' => $user->activeForApi(),
            'role' => $user->role?->name,
            'teams' => $user->technicianTeams->map(fn ($team) => [
                'id' => $team->id,
                'name' => $team->name,
            ])->values(),
            'profile' => [
                'profile_photo_url' => $profile?->profile_photo_url,
                'notification_enabled' => $profile?->notification_enabled ?? true,
                'skills' => $profile?->skills ?? [],
                'certifications' => $profile?->certifications ?? [],
            ],
        ];
    }
}
