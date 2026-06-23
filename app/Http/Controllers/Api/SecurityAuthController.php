<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SecurityAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->with('role')->where('username', $credentials['username'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password) || ! $user->canAccessModule('security-management', 'read')) {
            return response()->json([
                'message' => 'Kredensial security tidak valid.',
            ], 401);
        }

        $token = $user->createToken($request->userAgent() ?: 'security-api')->plainTextToken;

        return response()->json([
            'message' => 'Login security berhasil.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'role' => $user->role?->name,
                'token' => $token,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logout security berhasil.',
        ]);
    }
}
