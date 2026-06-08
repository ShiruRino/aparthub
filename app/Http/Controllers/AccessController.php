<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\User;
use App\Models\UserModule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccessController extends Controller
{
    /**
     * Redirect the old access page to user management.
     */
    public function index(): RedirectResponse
    {
        return redirect()->route('users.index');
    }

    /**
     * Display module permissions for a single user.
     */
    public function show(User $user): View
    {
        return view('access.show', [
            'user' => $user->load(['role', 'userModules']),
            'modules' => Module::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Update permissions for a user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        if ($user->isAdmin()) {
            return redirect()
                ->route('users.access.show', $user)
                ->withErrors(['access' => 'User admin selalu punya akses penuh dan tidak perlu diubah.']);
        }

        $permissions = $request->input('permissions', []);

        Module::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->each(function (Module $module) use ($permissions, $user) {
                $row = $permissions[$module->id] ?? [];

                UserModule::query()->updateOrCreate([
                    'user_id' => $user->id,
                    'module_id' => $module->id,
                ], [
                    'can_create' => isset($row['can_create']),
                    'can_read' => isset($row['can_read']),
                    'can_update' => isset($row['can_update']),
                    'can_delete' => isset($row['can_delete']),
                ]);
            });

        return redirect()
            ->route('users.access.show', $user)
            ->with('status', "Hak akses {$user->name} berhasil diperbarui.");
    }
}
