@csrf
@isset($method)
    @method($method)
@endisset

<div class="form-grid">
    <div class="field">
        <label for="name">Nama</label>
        <input id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required>
        @error('name')
            <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="field">
        <label for="username">Username</label>
        <input id="username" name="username" value="{{ old('username', $user->username ?? '') }}" required>
        @error('username')
            <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="field">
        <label for="role_id">Role</label>
        <select id="role_id" name="role_id" required>
            <option value="">Pilih role</option>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}" @selected((string) old('role_id', $user->role_id ?? '') === (string) $role->id)>
                    {{ $role->name }}
                </option>
            @endforeach
        </select>
        @error('role_id')
            <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="field">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="new-password" @isset($passwordRequired) required @endisset>
        @isset($user)
            <div class="muted">Kosongkan jika tidak ingin mengganti password.</div>
        @endisset
        @error('password')
            <div class="error-text">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-actions">
    <a class="btn secondary" href="{{ route('users.index') }}">Batal</a>
    <button class="btn" type="submit">{{ $submitLabel }}</button>
</div>
