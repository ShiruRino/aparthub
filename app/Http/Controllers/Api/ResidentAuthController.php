<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResidentAuthController extends Controller
{
    /**
     * Authenticate a resident using email or mobile number and issue a Sanctum token.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $resident = Resident::query()
            ->with('unit')
            ->where('email', $credentials['login'])
            ->orWhere('mobile_no', $credentials['login'])
            ->first();

        if (! $resident || ! $resident->password || ! Hash::check($credentials['password'], $resident->password)) {
            return response()->json([
                'message' => 'Kredensial resident tidak valid.',
                'errors' => [
                    'login' => ['Email / mobile number atau password tidak cocok.'],
                ],
            ], 401);
        }

        $token = $resident->createToken($request->userAgent() ?: 'resident-mobile')->plainTextToken;

        return response()->json([
            'message' => 'Login resident berhasil.',
            'data' => array_merge($this->residentPayload($resident), [
                'token' => $token,
            ]),
        ]);
    }

    /**
     * Return the currently authenticated resident profile.
     */
    public function me(Request $request): JsonResponse
    {
        /** @var Resident $resident */
        $resident = $request->user();
        $resident->loadMissing('unit');

        return response()->json([
            'data' => $this->residentPayload($resident),
        ]);
    }

    /**
     * Revoke the current Sanctum access token for the resident.
     */
    public function logout(Request $request): JsonResponse
    {
        /** @var Resident $resident */
        $resident = $request->user();

        $resident->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logout resident berhasil.',
        ]);
    }

    /**
     * Transform a resident into a safe API payload.
     *
     * @return array<string, mixed>
     */
    private function residentPayload(Resident $resident): array
    {
        return [
            'id' => $resident->id,
            'name' => $resident->name,
            'resident_type' => $resident->resident_type,
            'email' => $resident->email,
            'mobile_no' => $resident->mobile_no,
            'contract_end_date' => optional($resident->contract_end_date)->toDateString(),
            'unit' => $resident->unit ? [
                'id' => $resident->unit->id,
                'code' => $resident->unit->code,
                'tower' => $resident->unit->tower,
                'floor' => $resident->unit->floor,
            ] : null,
        ];
    }
}
