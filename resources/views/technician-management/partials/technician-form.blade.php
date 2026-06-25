@php
    $profile = $technician?->technicianProfile;
    $selectedTeamIds = $technician?->technicianTeams?->pluck('id')->map(fn ($id) => (string) $id)->all() ?? [];
@endphp

<div class="visitor-form-grid">
    <label class="resident-filter-field">
        <span>Name</span>
        <input type="text" name="name" value="{{ old('name', $technician?->name) }}" required>
    </label>
    <label class="resident-filter-field">
        <span>Username</span>
        <input type="text" name="username" value="{{ old('username', $technician?->username) }}" required>
    </label>
    <label class="resident-filter-field">
        <span>Email</span>
        <input type="email" name="email" value="{{ old('email', $technician?->email) }}">
    </label>
    <label class="resident-filter-field">
        <span>Mobile Number</span>
        <input type="text" name="mobile_no" value="{{ old('mobile_no', $technician?->mobile_no) }}">
    </label>
    <label class="resident-filter-field">
        <span>Password {{ $technician ? '(optional)' : '' }}</span>
        <input type="password" name="password" {{ $technician ? '' : 'required' }}>
    </label>
    <label class="resident-filter-field">
        <span>Confirm Password</span>
        <input type="password" name="password_confirmation" {{ $technician ? '' : 'required' }}>
    </label>
    <label class="resident-filter-field">
        <span>Status</span>
        <select name="is_active">
            <option value="1" @selected(old('is_active', $technician?->is_active ?? true))>Active</option>
            <option value="0" @selected((string) old('is_active', (int) ($technician?->is_active ?? true)) === '0')>Inactive</option>
        </select>
    </label>
    <label class="resident-filter-field">
        <span>Notification</span>
        <select name="notification_enabled">
            <option value="1" @selected(old('notification_enabled', $profile?->notification_enabled ?? true))>Enabled</option>
            <option value="0" @selected((string) old('notification_enabled', (int) ($profile?->notification_enabled ?? true)) === '0')>Disabled</option>
        </select>
    </label>
    <label class="resident-filter-field">
        <span>Profile Photo</span>
        <input type="file" name="profile_photo" accept=".jpg,.jpeg,.png,.webp">
    </label>
</div>

<label class="resident-filter-field" style="margin-top:14px;">
    <span>Teams</span>
    <select name="team_ids[]" multiple size="4">
        @foreach ($teamOptions as $team)
            <option value="{{ $team->id }}" @selected(in_array((string) $team->id, old('team_ids', $selectedTeamIds), true))>{{ $team->name }}</option>
        @endforeach
    </select>
</label>

<label class="resident-filter-field" style="margin-top:14px;">
    <span>Skills</span>
    <textarea name="skills" rows="4" placeholder="Satu skill per baris">{{ old('skills', collect($profile?->skills ?? [])->implode("\n")) }}</textarea>
</label>

<label class="resident-filter-field" style="margin-top:14px;">
    <span>Certifications</span>
    <textarea name="certifications" rows="4" placeholder="Satu sertifikasi per baris">{{ old('certifications', collect($profile?->certifications ?? [])->implode("\n")) }}</textarea>
</label>
