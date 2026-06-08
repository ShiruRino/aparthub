<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ModuleController extends Controller
{
    /**
     * Display a listing of modules.
     */
    public function index(): View
    {
        return view('modules.index', [
            'modules' => Module::query()
                ->withCount('userModules')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a module.
     */
    public function create(): View
    {
        return view('modules.create');
    }

    /**
     * Store a newly created module.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $slug = $this->slugFrom($data['slug'] ?? null, $data['name']);

        $this->validateSlug($slug);

        Module::query()->create(array_merge($data, [
            'slug' => $slug,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]));

        return redirect()
            ->route('modules.index')
            ->with('status', 'Module berhasil dibuat.');
    }

    /**
     * Show the form for editing a module.
     */
    public function edit(Module $module): View
    {
        return view('modules.edit', [
            'module' => $module,
        ]);
    }

    /**
     * Update the module.
     */
    public function update(Request $request, Module $module): RedirectResponse
    {
        $data = $this->validated($request);
        $payload = [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $module->isSystem() ? true : $request->boolean('is_active'),
        ];

        if (! $module->isSystem()) {
            $slug = $this->slugFrom($data['slug'] ?? null, $data['name']);
            $this->validateSlug($slug, $module);
            $payload['slug'] = $slug;
        }

        $module->update($payload);

        return redirect()
            ->route('modules.index')
            ->with('status', 'Module berhasil diperbarui.');
    }

    /**
     * Remove the module.
     */
    public function destroy(Module $module): RedirectResponse
    {
        if ($module->isSystem()) {
            return redirect()
                ->route('modules.index')
                ->withErrors(['module' => 'Module sistem tidak bisa dihapus.']);
        }

        $module->delete();

        return redirect()
            ->route('modules.index')
            ->with('status', 'Module berhasil dihapus.');
    }

    /**
     * Validate request data.
     *
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    /**
     * Build a normalized slug.
     */
    private function slugFrom(?string $slug, string $name): string
    {
        return Str::slug(filled($slug) ? $slug : $name);
    }

    /**
     * Validate a module slug.
     */
    private function validateSlug(string $slug, ?Module $module = null): void
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
                Rule::unique('modules', 'slug')->ignore($module),
            ],
        ])->validate();
    }
}
