@php
    $selectedMemberIds = $team?->users?->pluck('id')->map(fn ($id) => (string) $id)->all() ?? [];
@endphp

<div class="visitor-form-grid">
    <label class="resident-filter-field">
        <span>Team Name</span>
        <input type="text" name="name" value="{{ old('name', $team?->name) }}" required>
    </label>
    <label class="resident-filter-field">
        <span>Status</span>
        <select name="is_active">
            <option value="1" @selected(old('is_active', $team?->is_active ?? true))>Active</option>
            <option value="0" @selected((string) old('is_active', (int) ($team?->is_active ?? true)) === '0')>Inactive</option>
        </select>
    </label>
</div>

<label class="resident-filter-field" style="margin-top:14px;">
    <span>Description</span>
    <textarea name="description" rows="3">{{ old('description', $team?->description) }}</textarea>
</label>

<label class="resident-filter-field" style="margin-top:14px;">
    <span>Members</span>
    <select name="member_ids[]" multiple size="6">
        @foreach ($members as $member)
            <option value="{{ $member->id }}" @selected(in_array((string) $member->id, old('member_ids', $selectedMemberIds), true))>{{ $member->name }}{{ $member->email ? ' - '.$member->email : '' }}</option>
        @endforeach
    </select>
</label>
