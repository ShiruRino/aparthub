<div class="visitor-modal resident-modal" id="{{ $id }}" aria-hidden="true">
    <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
    <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
        <div class="visitor-modal-head">
            <h2 class="visitor-modal-title" id="{{ $id }}-title">{{ $title }}</h2>
            <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
        </div>
        <div class="visitor-modal-body">
            <div class="resident-modal-summary">
                <div class="resident-avatar {{ $avatarClass ?? '' }}">{{ $initials ?? 'AR' }}</div>
                <div>
                    <strong class="visitor-detail-name">{{ $name ?? 'Ahmad Rizky' }}</strong>
                    <small class="muted">{{ $subtitle ?? 'Static preview only. Data belum tersimpan ke backend.' }}</small>
                </div>
            </div>

            <div class="resident-modal-fields">
                @foreach ($rows as [$label, $value])
                    <div class="visitor-info-row">
                        <span>{{ $label }}</span>
                        <strong>{{ $value }}</strong>
                    </div>
                @endforeach
            </div>

            <div class="visitor-form-actions">
                <button class="btn secondary" type="button" data-modal-close>Close</button>
                <button class="btn" type="button">Confirm Preview</button>
            </div>
        </div>
    </div>
</div>
