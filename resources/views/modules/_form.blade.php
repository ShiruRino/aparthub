@csrf
@isset($method)
    @method($method)
@endisset

<div class="form-grid">
    <div class="field">
        <label for="name">Nama Module</label>
        <input id="name" name="name" value="{{ old('name', $module->name ?? '') }}" required>
        @error('name')
            <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="field">
        <label for="slug">Slug</label>
        <input id="slug" name="slug" value="{{ old('slug', $module->slug ?? '') }}" @isset($module) @disabled($module->isSystem()) @endisset>
        @isset($module)
            @if ($module->isSystem())
                <div class="muted">Slug module sistem tidak bisa diubah.</div>
            @endif
        @endisset
        @error('slug')
            <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="field">
        <label for="sort_order">Urutan</label>
        <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $module->sort_order ?? 0) }}">
        @error('sort_order')
            <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="field">
        <label>Status</label>
        <label class="row">
            <input type="checkbox" name="is_active" value="1" @checked((bool) old('is_active', $module->is_active ?? true)) @isset($module) @disabled($module->isSystem()) @endisset>
            <span>Aktif</span>
        </label>
        @isset($module)
            @if ($module->isSystem())
                <div class="muted">Module sistem selalu aktif.</div>
            @endif
        @endisset
        @error('is_active')
            <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="field full">
        <label for="description">Deskripsi</label>
        <textarea id="description" name="description">{{ old('description', $module->description ?? '') }}</textarea>
        @error('description')
            <div class="error-text">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-actions">
    <a class="btn secondary" href="{{ route('modules.index') }}">Batal</a>
    <button class="btn" type="submit">{{ $submitLabel }}</button>
</div>
