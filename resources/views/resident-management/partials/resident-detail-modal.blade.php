<div class="visitor-modal resident-modal" id="resident-detail-modal-{{ $row['id'] }}" aria-hidden="true">
    <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
    <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="resident-detail-title-{{ $row['id'] }}">
        <div class="visitor-modal-head">
            <h2 class="visitor-modal-title" id="resident-detail-title-{{ $row['id'] }}">Detail Residen</h2>
            <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
        </div>

        <div class="visitor-modal-body">
            <section class="visitor-panel" style="margin-bottom:16px;">
                <div class="visitor-profile-head" style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
                    <div class="resident-avatar {{ $row['avatarClass'] }}" style="width:54px;height:54px;font-size:20px;">{{ $row['avatar'] }}</div>
                    <div style="display:grid;gap:4px;">
                        <strong style="font-size:18px;color:#0b2149;">{{ $row['name'] }}</strong>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                            <span class="resident-status {{ $row['statusClass'] }}">{{ $row['status'] }}</span>
                            <span class="visitor-chip">{{ $row['type'] }}</span>
                            <span class="visitor-chip">{{ $row['unit'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="visitor-info-grid" style="margin-top:18px;">
                    <div class="visitor-info-row"><span>Tower / Lantai</span><strong>{{ $row['tower'] }}</strong></div>
                    <div class="visitor-info-row"><span>Email</span><strong>{{ $row['email'] ?: '-' }}</strong></div>
                    <div class="visitor-info-row"><span>Mobile Number</span><strong>{{ $row['mobile_no'] ?: '-' }}</strong></div>
                    <div class="visitor-info-row"><span>Gender</span><strong>{{ $row['gender'] }}</strong></div>
                    <div class="visitor-info-row"><span>Tanggal Masuk</span><strong>{{ $row['date'] }}</strong></div>
                    <div class="visitor-info-row"><span>Tanggal Keluar</span><strong>{{ $row['move_out_date'] ? \Carbon\Carbon::parse($row['move_out_date'])->format('d M Y') : '-' }}</strong></div>
                    <div class="visitor-info-row"><span>Contract End Date</span><strong>{{ $row['contract_end_date_label'] }}</strong></div>
                    @if ($row['type'] === 'Penyewa')
                        <div class="visitor-info-row"><span>Owner Name</span><strong>{{ $row['owner_name'] ?? 'Belum ada owner pada unit ini' }}</strong></div>
                    @endif
                </div>
            </section>

            <section class="visitor-panel" style="margin-bottom:16px;">
                <div class="visitor-panel-head">
                    <h3 class="visitor-panel-title">Move In / Move Out Log</h3>
                </div>
                <div class="visitor-panel-body" style="display:grid;gap:12px;">
                    @forelse ($row['move_logs'] as $moveLog)
                        <article style="padding:14px 16px;border:1px solid #dce4ef;border-radius:16px;background:#f8fbff;">
                            <div style="display:flex;justify-content:space-between;gap:12px;align-items:flex-start;flex-wrap:wrap;">
                                <div>
                                    <strong style="display:block;color:#0b2149;">{{ $moveLog['request_type'] }}</strong>
                                    <span style="display:block;color:#67758a;font-size:12px;">{{ $moveLog['request_number'] }} · {{ $moveLog['unit'] }}</span>
                                </div>
                                <span class="resident-status {{ $moveLog['status_class'] }}">{{ $moveLog['status'] }}</span>
                            </div>
                            <div style="margin-top:10px;color:#0b2149;font-weight:600;">{{ $moveLog['scheduled_date'] }}</div>
                            @if ($moveLog['note'])
                                <div style="margin-top:6px;color:#67758a;">{{ $moveLog['note'] }}</div>
                            @endif
                        </article>
                    @empty
                        <p style="margin:0;color:#67758a;">Belum ada log pindah masuk / keluar untuk residen ini.</p>
                    @endforelse
                </div>
            </section>

            <section class="visitor-panel">
                <div class="visitor-panel-head">
                    <h3 class="visitor-panel-title">Family Members</h3>
                </div>
                <div class="visitor-panel-body" style="display:grid;gap:18px;">
                    <div style="display:grid;gap:12px;">
                        @forelse ($row['family_members'] as $familyMember)
                            <article style="padding:14px 16px;border:1px solid #dce4ef;border-radius:16px;background:#fff;">
                                <div style="display:flex;justify-content:space-between;gap:12px;align-items:flex-start;flex-wrap:wrap;">
                                    <div>
                                        <strong style="display:block;color:#0b2149;">{{ $familyMember['name'] }}</strong>
                                        <span style="display:block;color:#67758a;font-size:12px;">{{ $familyMember['relationship'] }} · {{ $familyMember['birth'] }}</span>
                                    </div>
                                    <span class="resident-status {{ $familyMember['status_class'] }}">{{ $familyMember['access_status'] }}</span>
                                </div>

                                <div class="visitor-form-grid" style="margin-top:12px;">
                                    <form method="POST" action="{{ route('resident-management.family-members.update', $familyMember['id']) }}" style="display:grid;gap:12px;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="resident_id" value="{{ $row['id'] }}">
                                        <input type="hidden" name="redirect_to" value="resident-management.residents">
                                        <label class="resident-filter-field"><span>Nama</span><input type="text" name="name" value="{{ $familyMember['name'] }}" required></label>
                                        <label class="resident-filter-field">
                                            <span>Hubungan</span>
                                            <select name="relationship" required>
                                                @foreach (['Pasangan', 'Anak', 'Orang Tua'] as $relationship)
                                                    <option value="{{ $relationship }}" @selected($familyMember['relationship'] === $relationship)>{{ $relationship }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                        <label class="resident-filter-field"><span>Tanggal Lahir</span><input type="date" name="birth_date" value="{{ $familyMember['birth_date'] }}"></label>
                                        <label class="resident-filter-field">
                                            <span>Status Akses</span>
                                            <select name="access_status" required>
                                                @foreach (['Aktif', 'Menunggu Approval'] as $accessStatus)
                                                    <option value="{{ $accessStatus }}" @selected($familyMember['access_status'] === $accessStatus)>{{ $accessStatus }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                        <div style="display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;">
                                            <button class="btn secondary" type="submit">Update Family Member</button>
                                        </div>
                                    </form>

                                    <form method="POST" action="{{ route('resident-management.family-members.destroy', $familyMember['id']) }}" onsubmit="return confirm('Hapus anggota keluarga ini?')" style="display:flex;align-items:end;justify-content:flex-end;">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="redirect_to" value="resident-management.residents">
                                        <button class="btn danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </article>
                        @empty
                            <p style="margin:0;color:#67758a;">Belum ada anggota keluarga untuk residen ini.</p>
                        @endforelse
                    </div>

                    <form method="POST" action="{{ route('resident-management.family-members.store') }}" style="display:grid;gap:14px;padding:16px;border:1px solid #dce4ef;border-radius:18px;background:#f8fbff;">
                        @csrf
                        <input type="hidden" name="resident_id" value="{{ $row['id'] }}">
                        <input type="hidden" name="redirect_to" value="resident-management.residents">
                        <h4 style="margin:0;color:#0b2149;font-size:15px;">Tambah Anggota Keluarga</h4>
                        <div class="visitor-form-grid">
                            <label class="resident-filter-field"><span>Nama</span><input type="text" name="name" required></label>
                            <label class="resident-filter-field">
                                <span>Hubungan</span>
                                <select name="relationship" required>
                                    @foreach (['Pasangan', 'Anak', 'Orang Tua'] as $relationship)
                                        <option value="{{ $relationship }}">{{ $relationship }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="resident-filter-field"><span>Tanggal Lahir</span><input type="date" name="birth_date"></label>
                            <label class="resident-filter-field">
                                <span>Status Akses</span>
                                <select name="access_status" required>
                                    @foreach (['Aktif', 'Menunggu Approval'] as $accessStatus)
                                        <option value="{{ $accessStatus }}">{{ $accessStatus }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                        <div class="visitor-form-actions">
                            <button class="btn" type="submit">Tambah Family Member</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>
