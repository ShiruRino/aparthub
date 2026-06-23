@php($modalId = $modalId ?? 'visitor-registration-modal')

<div class="visitor-modal{{ $autoOpen ?? false ? ' is-open' : '' }}" id="{{ $modalId }}" aria-hidden="{{ ($autoOpen ?? false) ? 'false' : 'true' }}">
    <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
    <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="{{ $modalId }}-title">
        <div class="visitor-modal-head">
            <h2 class="visitor-modal-title" id="{{ $modalId }}-title">Register Walk-In Visitor</h2>
            <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
        </div>
        <form class="visitor-modal-body visitor-form-grid" method="POST" action="{{ route('visitor-management.walk-in.store') }}" enctype="multipart/form-data">
            @csrf
            <label>
                <span>Resident / Unit</span>
                <select name="resident_id" required>
                    <option value="">Select resident</option>
                    @foreach ($residentOptions as $residentOption)
                        <option value="{{ $residentOption->id }}">{{ $residentOption->name }}{{ $residentOption->unit?->code ? ' - '.$residentOption->unit->code : '' }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span>Visitor Name</span>
                <input type="text" name="visitor_name" required>
            </label>
            <label>
                <span>Visitor Phone</span>
                <input type="text" name="visitor_phone" required>
            </label>
            <label>
                <span>Visit Date</span>
                <input type="date" name="visit_date" required>
            </label>
            <label>
                <span>Estimated Arrival Time</span>
                <input type="time" name="estimated_arrival_time" required>
            </label>
            <label>
                <span>Guest Count</span>
                <input type="number" name="guest_count" min="1" max="{{ $guestLimit }}" value="1" required>
            </label>
            <label style="grid-column:1 / -1;">
                <span>Visit Purpose</span>
                <input type="text" name="visit_purpose" required>
            </label>
            <label style="grid-column:1 / -1;">
                <span>Identity Photo (Optional)</span>
                <input type="file" name="identity_photo" accept=".jpg,.jpeg,.png,.webp">
            </label>
            <div class="visitor-form-actions" style="grid-column:1 / -1;">
                <button class="btn secondary" type="button" data-modal-close>Cancel</button>
                <button class="btn" type="submit">Save Walk-In Visitor</button>
            </div>
        </form>
    </div>
</div>
