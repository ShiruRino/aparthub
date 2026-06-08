@csrf
@isset($method)
    @method($method)
@endisset

<div class="form-grid">
    <div class="field">
        <label for="name">Nama Role</label>
        <input id="name" name="name" value="{{ old('name', $role->name ?? '') }}" required>
        @error('name')
            <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="field">
        <label for="slug">Slug</label>
        <input id="slug" name="slug" value="{{ old('slug', $role->slug ?? '') }}" @isset($role) @disabled($role->isSystem()) @endisset>
        @isset($role)
            @if ($role->isSystem())
                <div class="muted">Slug role sistem tidak bisa diubah.</div>
            @endif
        @endisset
        @error('slug')
            <div class="error-text">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-actions">
    <a class="btn secondary" href="{{ route('roles.index') }}">Batal</a>
    <button class="btn" type="submit">{{ $submitLabel }}</button>
</div>
