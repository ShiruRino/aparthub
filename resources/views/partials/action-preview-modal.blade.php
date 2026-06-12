<div class="visitor-modal" id="{{ $id }}" aria-hidden="true">
    <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
    <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
        <div class="visitor-modal-head">
            <h2 class="visitor-modal-title" id="{{ $id }}-title">{{ $title }}</h2>
            <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
        </div>
        <div class="visitor-modal-body">
            @if (! empty($summary ?? null))
                <div class="visitor-detail-top">
                    <div class="visitor-detail-avatar {{ $avatarClass ?? '' }}">{{ $avatar ?? 'UI' }}</div>
                    <div>
                        <strong class="visitor-detail-name">{{ $summary }}</strong>
                        @if (! empty($subtitle ?? null))
                            <small class="muted">{{ $subtitle }}</small>
                        @endif
                    </div>
                </div>
            @endif

            @if (! empty($rows ?? null))
                <div class="visitor-modal-grid">
                    @foreach ($rows as [$label, $value])
                        <div class="visitor-info-row">
                            <span>{{ $label }}</span>
                            <strong>{{ $value }}</strong>
                        </div>
                    @endforeach
                </div>
            @endif

            @if (! empty($copy ?? null))
                <p class="muted" style="margin:18px 0 0;">{{ $copy }}</p>
            @endif

            <div class="visitor-form-actions">
                <button class="btn secondary" type="button" data-modal-close>{{ $closeLabel ?? 'Close' }}</button>
                <button class="btn" type="button" data-modal-close>{{ $confirmLabel ?? 'Confirm Preview' }}</button>
            </div>
        </div>
    </div>
</div>
