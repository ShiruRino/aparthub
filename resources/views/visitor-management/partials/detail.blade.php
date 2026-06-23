@php($modalId = $modalId ?? 'visitor-action-modal')

<div class="visitor-modal{{ $autoOpen ?? false ? ' is-open' : '' }}" id="{{ $modalId }}" aria-hidden="{{ ($autoOpen ?? false) ? 'false' : 'true' }}">
    <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
    <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="{{ $modalId }}-title">
        <div class="visitor-modal-head">
            <h2 class="visitor-modal-title" id="{{ $modalId }}-title">{{ $title }}</h2>
            <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
        </div>
        <div class="visitor-modal-body">
            @if ($visitor)
                <div class="visitor-detail-top">
                    <div class="visitor-detail-avatar" aria-hidden="true">
                        {{ strtoupper(substr($visitor->visitor_name, 0, 1)) }}
                    </div>
                    <div>
                        <span class="visitor-detail-name">{{ $visitor->visitor_name }} <span class="badge {{ $statusClass }}">{{ $visitor->status }}</span></span>
                        <small class="muted">{{ $visitor->registration_source }}</small>
                    </div>
                </div>

                <div class="visitor-detail-section">
                    <h3>Visit Information</h3>
                    <div class="visitor-info-row"><span>Resident</span><strong>{{ $visitor->resident?->name ?? '-' }}</strong></div>
                    <div class="visitor-info-row"><span>Unit</span><strong>{{ $visitor->resident?->unit?->code ?? '-' }}</strong></div>
                    <div class="visitor-info-row"><span>Visit Date</span><strong>{{ $visitor->visit_date?->format('d M Y') ?? '-' }}</strong></div>
                    <div class="visitor-info-row"><span>Arrival Time</span><strong>{{ $visitor->estimated_arrival_time?->format('H:i') ?? '-' }}</strong></div>
                    <div class="visitor-info-row"><span>Guest Count</span><strong>{{ $visitor->guest_count }}</strong></div>
                    <div class="visitor-info-row"><span>Purpose</span><strong>{{ $visitor->visit_purpose }}</strong></div>
                    <div class="visitor-info-row"><span>Access Code</span><strong style="word-break: break-all;">{{ $visitor->access_code }}</strong></div>
                </div>

                <div class="visitor-detail-section">
                    <h3>Visitor Contact</h3>
                    <div class="visitor-info-row"><span>Mobile</span><strong>{{ $visitor->visitor_phone }}</strong></div>
                    <div class="visitor-info-row"><span>Access Card</span><strong>{{ $visitor->access_card_number ?: '-' }}</strong></div>
                    <div class="visitor-info-row"><span>Expires At</span><strong>{{ $visitor->expires_at?->format('d M Y H:i') ?? '-' }}</strong></div>
                </div>

                @if ($visitor->identity_photo_path)
                    <div class="visitor-detail-section">
                        <h3>Identity Photo</h3>
                        <img src="{{ route('visitor-management.identity-photo', $visitor) }}" alt="Identity photo {{ $visitor->visitor_name }}" style="width:100%;border-radius:18px;border:1px solid rgba(15,23,42,.08);">
                    </div>
                @endif

                <div class="visitor-detail-section">
                    <h3>Lifecycle Timeline</h3>
                    @foreach ($visitor->timeline() as $event)
                        <div class="visitor-info-row">
                            <span>{{ $event['label'] }}</span>
                            <strong>{{ $event['timestamp'] ? \Illuminate\Support\Carbon::parse($event['timestamp'])->timezone(config('app.timezone'))->format('d M Y H:i') : '-' }}</strong></div>
                    @endforeach
                </div>

                @if ($visitor->rejection_reason || $visitor->cancellation_reason)
                    <div class="visitor-detail-section">
                        <h3>Notes</h3>
                        @if ($visitor->rejection_reason)
                            <div class="visitor-info-row"><span>Reject Reason</span><strong>{{ $visitor->rejection_reason }}</strong></div>
                        @endif
                        @if ($visitor->cancellation_reason)
                            <div class="visitor-info-row"><span>Cancel Reason</span><strong>{{ $visitor->cancellation_reason }}</strong></div>
                        @endif
                    </div>
                @endif

                <div class="visitor-detail-section">
                    <h3>{{ $actionsTitle }}</h3>
                    <div class="visitor-form-actions" style="flex-wrap:wrap;">
                        @if ($visitor->canAdminApprove())
                            <form method="POST" action="{{ route('visitor-management.approve', $visitor) }}">
                                @csrf
                                <button class="btn success" type="submit">Approve</button>
                            </form>
                        @endif

                        @if ($visitor->canAdminReject())
                            <form method="POST" action="{{ route('visitor-management.reject', $visitor) }}">
                                @csrf
                                <button class="btn danger" type="submit">Reject</button>
                            </form>
                        @endif

                        @if ($visitor->canAdminCheckIn())
                            <form method="POST" action="{{ route('visitor-management.check-in', $visitor) }}" class="visitor-form-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;width:100%;">
                                @csrf
                                <input type="text" name="access_code" value="{{ $visitor->access_code }}" placeholder="Access Code">
                                <input type="text" name="access_card_number" placeholder="Access Card Number (Optional)">
                                <button class="btn success" type="submit" style="grid-column:1 / -1;">Confirm Check-In</button>
                            </form>
                        @endif

                        @if ($visitor->canAdminCheckOut())
                            <form method="POST" action="{{ route('visitor-management.check-out', $visitor) }}">
                                @csrf
                                <button class="btn danger" type="submit">Confirm Check-Out</button>
                            </form>
                        @endif
                    </div>
                </div>
            @else
                <p class="muted">Pilih visitor dari daftar untuk melihat detail.</p>
            @endif
        </div>
    </div>
</div>
