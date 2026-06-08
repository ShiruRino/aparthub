<aside class="community-side" aria-label="Community side widgets">
    <section class="visitor-panel">
        <div class="visitor-panel-head">
            <h3 class="visitor-panel-title">Resident Engagement</h3>
            <select aria-label="Engagement period">
                <option>This Month</option>
            </select>
        </div>
        <div class="visitor-panel-body">
            <div class="community-donut-panel">
                <div class="community-donut" data-value="68%\A Engagement"></div>
                <div class="community-legend">
                    <span><span><i class="community-dot"></i> Active Residents</span><strong>1,254</strong></span>
                    <span><span><i class="community-dot blue"></i> Participated</span><strong>856</strong></span>
                    <span><span><i class="community-dot gold"></i> Inactive</span><strong>398</strong></span>
                </div>
            </div>
        </div>
    </section>

    <section class="visitor-panel">
        <div class="visitor-panel-head">
            <h3 class="visitor-panel-title">Quick Broadcast</h3>
        </div>
        <div class="visitor-panel-body community-broadcast">
            <label>
                <span class="field-label">Target Audience</span>
                <select>
                    <option>All Residents</option>
                    <option>Owners</option>
                    <option>Tenants</option>
                    <option>Tower A</option>
                </select>
            </label>
            <label>
                <span class="field-label">Message</span>
                <textarea placeholder="Type your message..."></textarea>
            </label>
            <button class="btn" type="button" data-modal-open="community-action-modal">Send Broadcast</button>
        </div>
    </section>

    <section class="visitor-panel">
        <div class="visitor-panel-head">
            <h3 class="visitor-panel-title">Forum Activity</h3>
        </div>
        <div class="visitor-panel-body">
            <div class="community-mini-list">
                @foreach ([
                    ['Lost & Found', '24 Discussions', 'blue'],
                    ['Recommendations', '18 Discussions', 'green'],
                    ['General Discussion', '41 Discussions', 'gold'],
                    ['Marketplace', '12 Discussions', 'purple'],
                ] as [$topic, $count, $tone])
                    <div class="community-mini-row">
                        <span class="community-tile-icon {{ $tone }}" style="width:28px;height:28px;font-size:12px;">{{ substr($topic, 0, 1) }}</span>
                        <strong>{{ $topic }}</strong>
                        <span>{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="visitor-panel">
        <div class="visitor-panel-head">
            <h3 class="visitor-panel-title">Calendar Overview</h3>
            <a href="{{ route('community-management.calendar') }}">View Calendar</a>
        </div>
        <div class="visitor-panel-body">
            <strong style="text-align:center;">June 2026</strong>
            <div class="community-calendar-mini" aria-label="June 2026 mini calendar">
                @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                    <span>{{ $day }}</span>
                @endforeach
                @foreach (range(1, 30) as $date)
                    <span @class(['active' => in_array($date, [7, 15, 22], true)])>{{ $date }}</span>
                @endforeach
            </div>
        </div>
    </section>
</aside>
