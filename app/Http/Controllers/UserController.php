<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(): View
    {
        return view('users.index', [
            'users' => User::query()
                ->with('role')
                ->orderBy('name')
                ->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a user.
     */
    public function create(): View
    {
        return view('users.create', [
            'roles' => $this->roles(),
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'username' => Str::lower((string) $request->input('username')),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', 'unique:users,username'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::query()->create($data);

        return redirect()
            ->route('users.index')
            ->with('status', 'User berhasil dibuat.');
    }

    /**
     * Show the form for editing a user.
     */
    public function edit(User $user): View
    {
        return view('users.edit', [
            'user' => $user,
            'roles' => $this->roles(),
        ]);
    }

    /**
     * Update the user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->merge([
            'username' => Str::lower((string) $request->input('username')),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[A-Za-z0-9._-]+$/',
                Rule::unique('users', 'username')->ignore($user),
            ],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('users.index')
            ->with('status', 'User berhasil diperbarui.');
    }

    /**
     * Remove the user.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return redirect()
                ->route('users.index')
                ->withErrors(['user' => 'Akun yang sedang login tidak bisa dihapus.']);
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('status', 'User berhasil dihapus.');
    }

    /**
     * Get available roles for user forms.
     */
    private function roles()
    {
        return Role::query()
            ->orderBy('name')
            ->get();
    }
}
