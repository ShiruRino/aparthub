<aside class="visitor-panel {{ $panelClass ?? 'visitor-span-3' }}">
    <div class="visitor-panel-head">
        <h2 class="visitor-panel-title">{{ $title }}</h2>
        <span class="muted">x</span>
    </div>
    <div class="visitor-panel-body">
        <div class="visitor-detail-top">
            <div class="visitor-detail-avatar" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div>
                <span class="visitor-detail-name">{{ $status[0] }} <span class="badge {{ $status[2] }}">{{ $status[1] }}</span></span>
                <small class="muted">Guest - Personal Visit</small>
            </div>
        </div>

        <div class="visitor-detail-section">
            <h3>Visit Information</h3>
            <div class="visitor-info-row"><span>Unit</span><strong>B-1002</strong></div>
            <div class="visitor-info-row"><span>Date</span><strong>07 Jun 2026 - 10:00 AM</strong></div>
            <div class="visitor-info-row"><span>Purpose</span><strong>Meeting</strong></div>
            <div class="visitor-info-row"><span>Registered By</span><strong>Front Office - Siti Aisyah</strong></div>
        </div>

        <div class="visitor-detail-section">
            <h3>Visitor Contact</h3>
            <div class="visitor-info-row"><span>Mobile</span><strong>0812 9999 0888</strong></div>
            <div class="visitor-info-row"><span>Email</span><strong>michaelchen@email.com</strong></div>
            <div class="visitor-info-row"><span>ID</span><strong>KTP / 3122 0000 7777</strong></div>
        </div>

        <div class="visitor-detail-section">
            <h3>Vehicle Information</h3>
            <div class="visitor-info-row"><span>Number</span><strong>B 4578 XYZ</strong></div>
            <div class="visitor-info-row"><span>Type</span><strong>Car</strong></div>
        </div>

        <div class="visitor-detail-section">
            <h3>{{ $actionsTitle }}</h3>
            <div class="visitor-form-actions">
                @foreach ($actions as [$label, $variant])
                    <button class="btn {{ $variant }}" type="button">{{ $label }}</button>
                @endforeach
            </div>
        </div>
    </div>
</aside>
