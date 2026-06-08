@extends('layouts.app')

@php
    $metrics = [
        ['label' => 'Paket Masuk Hari Ini', 'value' => '18', 'sub' => '+ 4 dari kemarin', 'icon' => 'BOX', 'class' => '', 'change' => true],
        ['label' => 'Menunggu Diambil', 'value' => '26', 'sub' => 'Total paket', 'icon' => 'BELL', 'class' => 'gold', 'change' => false],
        ['label' => 'Diambil Hari Ini', 'value' => '12', 'sub' => '+ 3 dari kemarin', 'icon' => 'CHECK', 'class' => 'green', 'change' => true],
        ['label' => 'Riwayat Paket', 'value' => '1.248', 'sub' => 'Total paket', 'icon' => 'DOC', 'class' => 'purple', 'change' => false],
    ];

    $packages = [
        ['date' => '24 Mei 2024', 'time' => '10:21', 'receiver' => 'Andi Pratama', 'unit' => 'A-12-03', 'courier' => 'JNE', 'tracking' => 'JNE123456789', 'status' => 'Menunggu Diambil', 'statusClass' => 'waiting', 'action' => 'pickup'],
        ['date' => '24 Mei 2024', 'time' => '09:45', 'receiver' => 'Siti Aisyah', 'unit' => 'B-07-05', 'courier' => 'J&T', 'tracking' => 'JT987654321', 'status' => 'Menunggu Diambil', 'statusClass' => 'waiting', 'action' => 'pickup'],
        ['date' => '24 Mei 2024', 'time' => '09:10', 'receiver' => 'Budi Santoso', 'unit' => 'C-15-02', 'courier' => 'SiCepat', 'tracking' => 'SC123987456', 'status' => 'Diambil', 'statusClass' => 'collected', 'action' => 'pickup'],
        ['date' => '23 Mei 2024', 'time' => '16:30', 'receiver' => 'Dewi Lestari', 'unit' => 'A-08-01', 'courier' => 'JNE', 'tracking' => 'JNE564738291', 'status' => 'Siap Diambil', 'statusClass' => 'ready', 'action' => 'pickup'],
        ['date' => '23 Mei 2024', 'time' => '15:20', 'receiver' => 'Rizky Febrian', 'unit' => 'B-11-09', 'courier' => 'J&T', 'tracking' => 'JT456987123', 'status' => 'Expired', 'statusClass' => 'expired', 'action' => 'register'],
    ];

    $benefits = [
        ['title' => 'Notifikasi Otomatis', 'copy' => 'Sistem mengirim notifikasi real-time ke penghuni saat paket sudah diterima.', 'icon' => 'MAIL'],
        ['title' => 'Pemantauan Status', 'copy' => 'Pantau status paket secara real-time: menunggu, siap diambil, atau expired.', 'icon' => 'SEARCH'],
        ['title' => 'Informasi Lengkap', 'copy' => 'Detail paket, kurir, nomor resi, lokasi penyimpanan, dan foto paket tersimpan.', 'icon' => 'PACKAGE'],
        ['title' => 'Riwayat Tersimpan', 'copy' => 'Semua riwayat paket tersimpan secara digital dan dapat dicari kapan saja.', 'icon' => 'CLOCK'],
        ['title' => 'Keamanan Data', 'copy' => 'Data paket dan penghuni aman dengan kontrol akses yang terstruktur.', 'icon' => 'SHIELD'],
    ];
@endphp

@section('title', 'Package Center')
@section('topbar_context')
    Package Center > Incoming Packages
@endsection
@section('topbar_subtitle', 'Pusat operasional penerimaan, notifikasi, dan pengambilan paket penghuni.')

@section('content')
    <div class="package-page">
        <section class="package-hero">
            <div class="package-hero-main">
                <div class="package-hero-icon" aria-hidden="true">
                    <svg viewBox="0 0 64 64" width="58" height="58" fill="none" stroke="currentColor" stroke-width="2.8">
                        <path d="M32 7 12 17v30l20 10 20-10V17L32 7Z" stroke-linejoin="round"/>
                        <path d="M12 17l20 10 20-10M32 27v30" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M45 22a7 7 0 1 1 0 14h-1" stroke-linecap="round"/>
                        <path d="M48 40h.01" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="package-hero-copy">
                    <h2>Package Center</h2>
                    <p>Mencatat paket masuk, mengirim notifikasi ke penghuni, memantau status pengambilan, serta menyimpan riwayat paket.</p>
                </div>
            </div>

            <div class="package-illustration" aria-hidden="true">
                <div class="package-box one"></div>
                <div class="package-box two"></div>
                <div class="package-box three"></div>
                <div class="package-plant"></div>
            </div>
        </section>

        <section class="package-metrics" aria-label="Ringkasan paket">
            @foreach ($metrics as $metric)
                <article class="package-metric">
                    <div class="package-metric-icon {{ $metric['class'] }}">
                        @if ($metric['icon'] === 'BOX')
                            <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2 3 7v10l9 5 9-5V7l-9-5ZM3 7l9 5 9-5M12 12v10" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @elseif ($metric['icon'] === 'BELL')
                            <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg>
                        @elseif ($metric['icon'] === 'CHECK')
                            <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="m8.5 12.5 2.5 2.5 4.5-5"/></svg>
                        @else
                            <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 3h10v18H7z"/><path d="M9 7h6M9 11h6M9 15h4"/></svg>
                        @endif
                    </div>
                    <div>
                        <span>{{ $metric['label'] }}</span>
                        <strong>{{ $metric['value'] }}</strong>
                        <span @class(['package-change' => $metric['change']])>{{ $metric['sub'] }}</span>
                    </div>
                </article>
            @endforeach
        </section>

        <div class="package-main">
            <section class="package-panel">
                <div class="package-panel-head">
                    <h3>Incoming Packages</h3>
                </div>

                <div class="package-toolbar">
                    <div class="package-search">
                        <input type="search" placeholder="Cari nama penghuni / nomor unit / nomor resi" aria-label="Cari paket">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    </div>
                    <div class="package-toolbar-actions">
                        <button class="btn secondary" type="button">Filter</button>
                        <button class="btn" type="button" data-modal-open="package-register-modal">+ Register Package</button>
                    </div>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal Masuk</th>
                                <th>Penerima</th>
                                <th>Unit</th>
                                <th>Kurir</th>
                                <th>Nomor Resi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($packages as $package)
                                <tr>
                                    <td>{{ $package['date'] }}<br><span class="muted">{{ $package['time'] }}</span></td>
                                    <td>{{ $package['receiver'] }}</td>
                                    <td>{{ $package['unit'] }}</td>
                                    <td>{{ $package['courier'] }}</td>
                                    <td>{{ $package['tracking'] }}</td>
                                    <td><span class="package-status {{ $package['statusClass'] }}">{{ $package['status'] }}</span></td>
                                    <td>
                                        <div class="package-table-actions">
                                            <button class="btn compact secondary" type="button" data-modal-open="{{ $package['action'] === 'pickup' ? 'package-pickup-modal' : 'package-register-modal' }}">
                                                {{ $package['action'] === 'pickup' ? 'Pickup Status' : 'Detail Paket' }}
                                            </button>
                                            <button class="package-kebab" type="button" data-modal-open="{{ $package['action'] === 'pickup' ? 'package-pickup-modal' : 'package-register-modal' }}" aria-label="Opsi paket">:</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="resident-pagination">
                    <div class="resident-page-list">
                        <span class="resident-page-btn">&lt;</span>
                        <span class="resident-page-btn active">1</span>
                        <span class="resident-page-btn">2</span>
                        <span class="resident-page-btn">3</span>
                        <span class="resident-page-btn">4</span>
                        <span class="resident-page-btn">5</span>
                        <span class="resident-page-btn">&gt;</span>
                    </div>
                    <span>Showing 1 to 5 of 26 entries</span>
                </div>
            </section>

            <section class="package-bottom-grid">
                <div class="package-benefits">
                    @foreach ($benefits as $benefit)
                        <article class="package-benefit">
                            <div class="package-benefit-icon" aria-hidden="true">
                                @if ($benefit['icon'] === 'MAIL')
                                    <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16v12H4z"/><path d="m4 8 8 6 8-6"/><circle cx="18.5" cy="5.5" r="3"/></svg>
                                @elseif ($benefit['icon'] === 'SEARCH')
                                    <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                                @elseif ($benefit['icon'] === 'PACKAGE')
                                    <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2 3 7v10l9 5 9-5V7l-9-5ZM3 7l9 5 9-5M12 12v10"/></svg>
                                @elseif ($benefit['icon'] === 'CLOCK')
                                    <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                                @else
                                    <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3 5 6v6c0 5 3.5 7.5 7 9 3.5-1.5 7-4 7-9V6l-7-3Z"/><path d="m9.5 12 2 2 3.5-4"/></svg>
                                @endif
                            </div>
                            <div>
                                <h4>{{ $benefit['title'] }}</h4>
                                <p>{{ $benefit['copy'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        </div>
    </div>

    <div class="visitor-modal package-modal" id="package-register-modal" aria-hidden="true">
        <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
        <div class="visitor-modal-dialog package-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="package-register-title">
            <div class="package-modal-head">
                <div class="package-modal-titlewrap">
                    <div class="package-modal-symbol" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2 3 7v10l9 5 9-5V7l-9-5ZM3 7l9 5 9-5M12 12v10"/></svg>
                    </div>
                    <div>
                        <h3 id="package-register-title">Register Package</h3>
                        <p>Lengkapi data paket yang diterima dari kurir.</p>
                    </div>
                </div>
                <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
            </div>
            <div class="package-modal-body">
                <div class="package-modal-form">
                    <section class="package-modal-section">
                        <h4>1. Informasi Paket</h4>
                        <div class="package-modal-grid">
                            <label><span class="field-label">Nomor Resi / Tracking No. *</span><input type="text" placeholder="Masukkan nomor resi"></label>
                            <label><span class="field-label">Kurir / Ekspedisi *</span><select><option>Pilih kurir / ekspedisi</option></select></label>
                            <label><span class="field-label">Layanan</span><select><option>Pilih layanan (opsional)</option></select></label>
                            <label><span class="field-label">Tanggal Diterima *</span><input type="text" value="24/05/2024"></label>
                            <label><span class="field-label">Waktu Diterima *</span><input type="text" value="10:20"></label>
                            <label><span class="field-label">Jumlah Paket *</span><input type="text" value="1"></label>
                            <label><span class="field-label">Jenis Paket *</span><select><option>Pilih jenis paket</option></select></label>
                        </div>
                        <div class="package-modal-grid two">
                            <label><span class="field-label">Deskripsi Paket</span><input type="text" placeholder="Contoh: Buku, Dokumen, Elektronik, Pakaian"></label>
                            <label><span class="field-label">Catatan (Opsional)</span><input type="text" placeholder="Catatan tambahan untuk paket ini"></label>
                        </div>
                    </section>

                    <section class="package-modal-section">
                        <h4>2. Informasi Pengirim</h4>
                        <div class="package-modal-grid">
                            <label><span class="field-label">Nama Pengirim</span><input type="text" placeholder="Masukkan nama pengirim"></label>
                            <label><span class="field-label">Telepon Pengirim</span><input type="text" placeholder="Masukkan nomor telepon"></label>
                            <label><span class="field-label">Asal Pengiriman</span><input type="text" placeholder="Kota / Negara asal"></label>
                        </div>
                    </section>

                    <section class="package-modal-section">
                        <h4>3. Penerima Paket</h4>
                        <div class="package-radio-row">
                            <label><input type="radio" checked> Pilih Resident</label>
                            <label><input type="radio"> Tamu / Non Resident</label>
                        </div>
                        <div class="package-modal-grid">
                            <label><span class="field-label">Nama Penghuni *</span><select><option>Cari nama penghuni / nomor unit</option></select></label>
                            <label><span class="field-label">Unit / Tower *</span><select><option>Pilih unit / tower</option></select></label>
                            <label><span class="field-label">Nomor Telepon</span><input type="text" placeholder="Nomor telepon penghuni"></label>
                        </div>
                    </section>

                    <section class="package-modal-section">
                        <h4>4. Lokasi Penyimpanan</h4>
                        <div class="package-modal-grid">
                            <label><span class="field-label">Lokasi Penyimpanan *</span><select><option>Pilih lokasi penyimpanan</option></select></label>
                            <label><span class="field-label">Rak / Shelf</span><input type="text" placeholder="Contoh: Rak A - 01"></label>
                            <label><span class="field-label">Catatan Lokasi</span><input type="text" placeholder="Catatan lokasi penyimpanan"></label>
                        </div>
                    </section>

                    <section class="package-modal-section">
                        <h4>5. Lampiran (Opsional)</h4>
                        <div class="package-modal-grid full">
                            <button class="package-upload" type="button">
                                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 16 4-4-4-4"/><path d="M8 12h8"/><path d="M4 20h16"/></svg>
                                <span>Klik atau drag file ke sini untuk upload foto paket</span>
                                <small>Format: JPG, PNG (Maks. 2MB)</small>
                            </button>
                        </div>
                    </section>
                </div>

                <div class="package-modal-actions">
                    <button class="btn secondary" type="button" data-modal-close>Batal</button>
                    <button class="btn" type="button" data-modal-close>Simpan Paket</button>
                </div>
            </div>
        </div>
    </div>

    <div class="visitor-modal package-modal" id="package-pickup-modal" aria-hidden="true">
        <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
        <div class="visitor-modal-dialog package-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="package-pickup-title">
            <div class="package-modal-head">
                <div class="package-modal-titlewrap">
                    <div class="package-modal-symbol" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2 3 7v10l9 5 9-5V7l-9-5ZM3 7l9 5 9-5M12 12v10"/></svg>
                    </div>
                    <div>
                        <h3 id="package-pickup-title">Collection / Pick Up</h3>
                        <p>Catat paket yang diambil oleh penghuni.</p>
                    </div>
                </div>
                <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
            </div>
            <div class="package-modal-body">
                <div class="package-steps" aria-label="Pickup steps">
                    <div class="package-step active"><span class="package-step-index">1</span><span>Informasi Paket</span></div>
                    <div class="package-step"><span class="package-step-index">2</span><span>Penerima</span></div>
                    <div class="package-step"><span class="package-step-index">3</span><span>Verifikasi</span></div>
                    <div class="package-step"><span class="package-step-index">4</span><span>Selesai</span></div>
                </div>

                <div class="package-modal-form">
                    <section class="package-modal-section">
                        <h4>1. Informasi Paket</h4>
                        <div class="package-modal-grid">
                            <label><span class="field-label">Nomor Resi / Tracking No.</span><input type="text" value="JNE123456789"></label>
                            <label><span class="field-label">Kurir / Ekspedisi</span><input type="text" value="JNE Express"></label>
                            <label><span class="field-label">Tanggal Diterima</span><input type="text" value="20/05/2024 11:20"></label>
                        </div>
                        <div class="package-modal-grid two">
                            <label><span class="field-label">Deskripsi Paket</span><input type="text" value="Dokumen - Map Kantor"></label>
                            <label><span class="field-label">Lokasi Penyimpanan</span><input type="text" value="Rak A - 01"></label>
                        </div>
                        <div class="package-modal-grid two">
                            <label><span class="field-label">Jenis Paket</span><select><option>Dokumen</option></select></label>
                            <div>
                                <span class="field-label">Status Saat Ini</span>
                                <span class="package-status ready package-inline-status">Siap Diambil</span>
                            </div>
                        </div>
                    </section>

                    <section class="package-modal-section">
                        <h4>2. Penerima Paket</h4>
                        <div class="package-radio-row">
                            <label><input type="radio" checked> Resident</label>
                            <label><input type="radio"> Perwakilan</label>
                        </div>
                        <div class="package-modal-grid">
                            <label><span class="field-label">Nama Penghuni</span><select><option>Budi Santoso (Unit A-12-03)</option></select></label>
                            <label><span class="field-label">Nomor Telepon</span><input type="text" value="0812 3456 7890"></label>
                            <label><span class="field-label">Nomor Identitas (KTP/Passport)</span><input type="text" value="3175101234567890"></label>
                        </div>
                    </section>

                    <section class="package-modal-section">
                        <h4>3. Verifikasi Pengambilan</h4>
                        <div class="package-modal-grid">
                            <label><span class="field-label">Metode Verifikasi</span><select><option>Tanda Tangan Digital</option></select></label>
                            <div>
                                <span class="field-label">Tanda Tangan Penerima</span>
                                <div class="package-signature">Glinka</div>
                            </div>
                            <label><span class="field-label">Waktu Pengambilan</span><input type="text" value="24/05/2024 14:35"></label>
                            <label><span class="field-label">Dicatat Oleh</span><select><option>Security - Andi Pratama</option></select></label>
                        </div>
                    </section>
                </div>

                <div class="package-modal-actions">
                    <button class="btn secondary" type="button" data-modal-close>Batal</button>
                    <button class="btn" type="button" data-modal-close>Konfirmasi Pengambilan</button>
                </div>
            </div>
        </div>
    </div>
@endsection
