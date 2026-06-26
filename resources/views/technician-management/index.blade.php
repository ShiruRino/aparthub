@extends('layouts.app')

@php
    $summaryCards = [
        ['label' => 'Assigned', 'value' => $serviceSummary['assigned'] ?? 0, 'tone' => 'status-pending'],
        ['label' => 'On The Way', 'value' => $serviceSummary['on_the_way'] ?? 0, 'tone' => 'status-approved'],
        ['label' => 'In Progress', 'value' => $serviceSummary['in_progress'] ?? 0, 'tone' => 'status-approved'],
        ['label' => 'Completed', 'value' => $serviceSummary['completed'] ?? 0, 'tone' => 'status-approved'],
        ['label' => 'Teams', 'value' => $serviceSummary['teams'] ?? 0, 'tone' => 'status-expired'],
        ['label' => 'Technicians', 'value' => $serviceSummary['technicians'] ?? 0, 'tone' => 'status-expired'],
    ];
@endphp

@section('title', 'Technician Management')
@section('topbar_context')
    Technician Management > Operations
@endsection
@section('topbar_subtitle', 'Kelola akun teknisi, keanggotaan team, dan dukung eksekusi Service Request dari satu backend.')

@section('content')
    <div class="service-page">
        <section class="visitor-toolbar">
            <div class="visitor-heading">
                <span class="visitor-step">OPS</span>
                <div>
                    <h2>Technician Management</h2>
                    <p>Kelola technician roster, assignment team, skill matrix, dan readiness operasional.</p>
                </div>
            </div>

            <div class="visitor-toolbar-actions">
                <button class="btn secondary" type="button" data-modal-open="technician-hotline-modal">Hotline Config</button>
                <button class="btn" type="button" data-modal-open="create-technician-modal">Add Technician</button>
            </div>
        </section>

        <section class="service-metrics">
            @foreach ($summaryCards as $card)
                <article class="visitor-chip" style="display:grid;gap:6px;padding:16px 18px;border:1px solid #dce4ef;border-radius:16px;background:#fff;min-width:160px;">
                    <span style="color:#67758a;font-weight:600;">{{ $card['label'] }}</span>
                    <strong style="font-size:24px;color:#0b2149;">{{ $card['value'] }}</strong>
                    <span class="resident-status {{ $card['tone'] }}">Live</span>
                </article>
            @endforeach
        </section>

        <section class="visitor-grid">
            <section class="visitor-panel visitor-span-12">
                <div class="visitor-panel-head">
                    <h2 class="visitor-panel-title">Technician Roster</h2>
                    <form method="GET" action="{{ route('technician-management.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
                        <label class="resident-filter-field">
                            <span>Search</span>
                            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search technician, email, mobile, team">
                        </label>
                        <button class="btn secondary" type="submit">Apply</button>
                    </form>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Technician</th>
                                <th>Login</th>
                                <th>Teams</th>
                                <th>Skills</th>
                                <th>Status</th>
                                <th>Notifications</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($technicians as $technician)
                                @php
                                    $profile = $technician->technicianProfile;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $technician->name }}</strong>
                                        <div style="color:#67758a;">{{ $technician->username }}</div>
                                    </td>
                                    <td>
                                        {{ $technician->email ?: '-' }}
                                        <br>
                                        <span style="color:#67758a;">{{ $technician->mobile_no ?: 'Belum ada mobile number' }}</span>
                                    </td>
                                    <td>
                                        @forelse ($technician->technicianTeams as $team)
                                            <span class="resident-status status-approved" style="margin:0 6px 6px 0;">{{ $team->name }}</span>
                                        @empty
                                            <span style="color:#67758a;">Belum ada team</span>
                                        @endforelse
                                    </td>
                                    <td>{{ collect($profile?->skills ?? [])->take(3)->implode(', ') ?: 'Belum diisi' }}</td>
                                    <td>
                                        <span class="resident-status {{ $technician->is_active ? 'status-approved' : 'status-expired' }}">
                                            {{ $technician->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="resident-status {{ ($profile?->notification_enabled ?? true) ? 'status-approved' : 'status-expired' }}">
                                            {{ ($profile?->notification_enabled ?? true) ? 'Enabled' : 'Disabled' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="resident-action-row">
                                            @include('partials.icon-action-button', ['icon' => 'eye', 'label' => 'View Technician Detail', 'modal' => 'technician-detail-modal-'.$technician->id])
                                            @include('partials.icon-action-button', ['icon' => 'edit', 'label' => 'Edit Technician', 'modal' => 'edit-technician-modal-'.$technician->id])
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">Belum ada technician.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="display:flex;justify-content:space-between;gap:14px;align-items:center;flex-wrap:wrap;margin-top:18px;">
                    @include('partials.pagination', ['paginator' => $technicians])
                </div>
            </section>

            <section class="visitor-panel visitor-span-9">
                <div class="visitor-panel-head">
                    <h2 class="visitor-panel-title">Technician Teams</h2>
                    <button class="btn" type="button" data-modal-open="create-team-modal">Create Team</button>
                </div>
                <div class="visitor-panel-body" style="display:grid;gap:14px;">
                    @forelse ($teams as $team)
                        <article style="border:1px solid #dce4ef;border-radius:16px;padding:16px;background:#fff;">
                            <div style="display:flex;justify-content:space-between;gap:12px;align-items:flex-start;">
                                <div>
                                    <strong style="display:block;color:#0b2149;">{{ $team->name }}</strong>
                                    <span style="display:block;margin-top:4px;color:#67758a;">{{ $team->description ?: 'Belum ada deskripsi team.' }}</span>
                                </div>
                                <span class="resident-status {{ $team->is_active ? 'status-approved' : 'status-expired' }}">{{ $team->is_active ? 'Active' : 'Inactive' }}</span>
                            </div>
                            <div style="margin-top:12px;color:#67758a;">{{ $team->users_count }} technician</div>
                            <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:12px;">
                                @foreach ($team->users as $member)
                                    <span class="visitor-chip">{{ $member->name }}</span>
                                @endforeach
                            </div>
                            <div class="resident-action-row" style="margin-top:14px;">
                                @include('partials.icon-action-button', ['icon' => 'edit', 'label' => 'Edit Team', 'modal' => 'edit-team-modal-'.$team->id])
                            </div>
                        </article>
                    @empty
                        <p class="muted" style="margin:0;">Belum ada technician team.</p>
                    @endforelse
                </div>
            </section>

            <section class="visitor-panel visitor-span-9">
                <div class="visitor-panel-head">
                    <h2 class="visitor-panel-title">Execution Policy</h2>
                </div>
                <div class="visitor-panel-body">
                    <div class="visitor-info-row"><span>Assignment Source</span><strong>Technician Team</strong></div>
                    <div class="visitor-info-row"><span>Before Evidence</span><strong>Max 3 photos before start</strong></div>
                    <div class="visitor-info-row"><span>After Evidence</span><strong>Max 3 photos + completion notes</strong></div>
                    <div class="visitor-info-row"><span>ETA</span><strong>Required on On The Way</strong></div>
                    <div class="visitor-info-row"><span>Audit Trail</span><strong>Actor recorded per lifecycle action</strong></div>
                </div>
            </section>
        </section>

        <div class="visitor-modal" id="create-technician-modal" aria-hidden="true">
            <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
            <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="create-technician-title">
                <div class="visitor-modal-head">
                    <h2 class="visitor-modal-title" id="create-technician-title">Add Technician</h2>
                    <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
                </div>
                <form class="visitor-modal-body" method="POST" action="{{ route('technician-management.technicians.store') }}" enctype="multipart/form-data">
                    @csrf
                    @include('technician-management.partials.technician-form', ['technician' => null, 'teamOptions' => $teamOptions])
                    <div class="visitor-form-actions">
                        <button class="btn secondary" type="button" data-modal-close>Batal</button>
                        <button class="btn" type="submit">Save Technician</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="visitor-modal" id="create-team-modal" aria-hidden="true">
            <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
            <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="create-team-title">
                <div class="visitor-modal-head">
                    <h2 class="visitor-modal-title" id="create-team-title">Create Technician Team</h2>
                    <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
                </div>
                <form class="visitor-modal-body" method="POST" action="{{ route('technician-management.teams.store') }}">
                    @csrf
                    @include('technician-management.partials.team-form', ['team' => null, 'members' => $technicians->getCollection()])
                    <div class="visitor-form-actions">
                        <button class="btn secondary" type="button" data-modal-close>Batal</button>
                        <button class="btn" type="submit">Save Team</button>
                    </div>
                </form>
            </div>
        </div>

        @foreach ($technicians as $technician)
            @php $profile = $technician->technicianProfile; @endphp
            <div class="visitor-modal" id="technician-detail-modal-{{ $technician->id }}" aria-hidden="true">
                <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
                <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="technician-detail-title-{{ $technician->id }}">
                    <div class="visitor-modal-head">
                        <h2 class="visitor-modal-title" id="technician-detail-title-{{ $technician->id }}">Technician Detail</h2>
                        <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
                    </div>
                    <div class="visitor-modal-body">
                        <div class="visitor-detail-top">
                            <div class="visitor-detail-avatar">{{ strtoupper(substr($technician->name, 0, 2)) }}</div>
                            <div>
                                <strong class="visitor-detail-name">{{ $technician->name }}</strong>
                                <small class="muted">{{ $technician->email ?: $technician->username }}</small>
                            </div>
                        </div>

                        <div class="visitor-modal-grid">
                            <div class="visitor-info-row"><span>Username</span><strong>{{ $technician->username }}</strong></div>
                            <div class="visitor-info-row"><span>Email</span><strong>{{ $technician->email ?: '-' }}</strong></div>
                            <div class="visitor-info-row"><span>Mobile</span><strong>{{ $technician->mobile_no ?: '-' }}</strong></div>
                            <div class="visitor-info-row"><span>Status</span><strong>{{ $technician->is_active ? 'Active' : 'Inactive' }}</strong></div>
                            <div class="visitor-info-row"><span>Notifications</span><strong>{{ ($profile?->notification_enabled ?? true) ? 'Enabled' : 'Disabled' }}</strong></div>
                            <div class="visitor-info-row"><span>Teams</span><strong>{{ $technician->technicianTeams->pluck('name')->implode(', ') ?: 'Belum ada team' }}</strong></div>
                            <div class="visitor-info-row"><span>Skills</span><strong>{{ collect($profile?->skills ?? [])->implode(', ') ?: 'Belum diisi' }}</strong></div>
                            <div class="visitor-info-row"><span>Certifications</span><strong>{{ collect($profile?->certifications ?? [])->implode(', ') ?: 'Belum diisi' }}</strong></div>
                        </div>
                        <div class="visitor-form-actions">
                            <button class="btn secondary" type="button" data-modal-close>Close</button>
                            <button class="btn" type="button" data-modal-open="edit-technician-modal-{{ $technician->id }}">Edit Technician</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="visitor-modal" id="edit-technician-modal-{{ $technician->id }}" aria-hidden="true">
                <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
                <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="edit-technician-title-{{ $technician->id }}">
                    <div class="visitor-modal-head">
                        <h2 class="visitor-modal-title" id="edit-technician-title-{{ $technician->id }}">Edit Technician</h2>
                        <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
                    </div>
                    <form class="visitor-modal-body" method="POST" action="{{ route('technician-management.technicians.update', $technician) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @include('technician-management.partials.technician-form', ['technician' => $technician, 'teamOptions' => $teamOptions])
                        <div class="visitor-form-actions">
                            <button class="btn secondary" type="button" data-modal-close>Batal</button>
                            <button class="btn" type="submit">Update Technician</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach

        @foreach ($teams as $team)
            <div class="visitor-modal" id="edit-team-modal-{{ $team->id }}" aria-hidden="true">
                <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
                <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="edit-team-title-{{ $team->id }}">
                    <div class="visitor-modal-head">
                        <h2 class="visitor-modal-title" id="edit-team-title-{{ $team->id }}">Edit Technician Team</h2>
                        <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
                    </div>
                    <form class="visitor-modal-body" method="POST" action="{{ route('technician-management.teams.update', $team) }}">
                        @csrf
                        @method('PUT')
                        @include('technician-management.partials.team-form', ['team' => $team, 'members' => $technicians->getCollection()])
                        <div class="visitor-form-actions">
                            <button class="btn secondary" type="button" data-modal-close>Batal</button>
                            <button class="btn" type="submit">Update Team</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach

        @include('partials.action-preview-modal', [
            'id' => 'technician-hotline-modal',
            'title' => 'Technician Hotline Configuration',
            'summary' => 'Read-only operational contact',
            'subtitle' => 'Nilai hotline dibaca dari app_settings untuk kebutuhan aplikasi teknisi.',
            'avatar' => 'TL',
            'rows' => [
                ['Hotline Name', \App\Models\AppSetting::query()->where('key', 'technician_hotline_name')->value('value') ?: 'Service Dispatch Hotline'],
                ['Phone', \App\Models\AppSetting::query()->where('key', 'technician_hotline_phone')->value('value') ?: '021-1500-112'],
                ['Note', \App\Models\AppSetting::query()->where('key', 'technician_hotline_note')->value('value') ?: 'Hubungi hotline bila ada eskalasi onsite atau kendala akses unit.'],
            ],
            'confirmLabel' => 'Close',
        ])
    </div>
@endsection
