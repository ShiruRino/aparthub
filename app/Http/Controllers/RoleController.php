<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index(): View
    {
        return view('roles.index', [
            'roles' => Role::query()
                ->withCount('users')
                ->orderBy('name')
                ->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a role.
     */
    public function create(): View
    {
        return view('roles.create');
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:100'],
        ]);

        $slug = $this->slugFrom($data['slug'] ?? null, $data['name']);

        $this->validateSlug($slug);

        Role::query()->create([
            'name' => $data['name'],
            'slug' => $slug,
        ]);

        return redirect()
            ->route('roles.index')
            ->with('status', 'Role berhasil dibuat.');
    }

    /**
     * Show the form for editing a role.
     */
    public function edit(Role $role): View
    {
        return view('roles.edit', [
            'role' => $role,
        ]);
    }

    /**
     * Update the role.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:100'],
        ]);

        $payload = [
            'name' => $data['name'],
        ];

        if (! $role->isSystem()) {
            $slug = $this->slugFrom($data['slug'] ?? null, $data['name']);
            $this->validateSlug($slug, $role);
            $payload['slug'] = $slug;
        }

        $role->update($payload);

        return redirect()
            ->route('roles.index')
            ->with('status', 'Role berhasil diperbarui.');
    }

    /**
     * Remove the role.
     */
    public function destroy(Role $role): RedirectResponse
    {
        if ($role->isSystem()) {
            return redirect()
                ->route('roles.index')
                ->withErrors(['role' => 'Role sistem tidak bisa dihapus.']);
        }

        if ($role->users()->exists()) {
            return redirect()
                ->route('roles.index')
                ->withErrors(['role' => 'Role masih dipakai oleh user.']);
        }

        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('status', 'Role berhasil dihapus.');
    }

    /**
     * Build a normalized slug.
     */
    private function slugFrom(?string $slug, string $name): string
    {
        return Str::slug(filled($slug) ? $slug : $name);
    }

    /**
     * Validate a role slug.
     */
    private function validateSlug(string $slug, ?Role $role = null): void
    {
        if ($slug === '') {
            throw ValidationException::withMessages([
                'slug' => 'Slug tidak valid.',
            ]);
        }

        validator(['slug' => $slug], [
            'slug' => [
                'required',
                'max:100',
                Rule::unique('roles', 'slug')->ignore($role),
            ],
        ])->validate();
    }
}
