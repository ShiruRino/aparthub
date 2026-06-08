@php
    $activeSteps = collect($activeSteps ?? []);
    $steps = [
        [
            'number' => 1,
            'route' => 'resident-management.residents',
            'title' => 'Resident Registration',
            'copy' => 'Front office mendaftarkan penghuni baru.',
            'icon' => 'M20 21a8 8 0 0 0-16 0M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8M19 8v6M22 11h-6',
        ],
        [
            'number' => 2,
            'route' => 'resident-management.units',
            'title' => 'Unit Assignment',
            'copy' => 'Penghuni dikaitkan dengan unit.',
            'icon' => 'M5 21V4h14v17M9 8h2M13 8h2M9 12h2M13 12h2M9 16h2M13 16h2M8 21v-3h8v3',
        ],
        [
            'number' => 3,
            'route' => 'resident-management.move-in-out',
            'title' => 'Move-In Approval',
            'copy' => 'Dokumen diverifikasi sebelum move-in.',
            'icon' => 'M8 4h8l2 3v13H6V7l2-3ZM9 12l2 2 4-5M9 4v4h6V4',
        ],
        [
            'number' => 4,
            'route' => 'resident-management.residents',
            'title' => 'Active Resident',
            'copy' => 'Akses dan layanan resident aktif.',
            'icon' => 'M3 11l9-8 9 8M5 10v10h14V10M9 20v-6h6v6M8 12h8',
        ],
        [
            'number' => 5,
            'route' => 'resident-management.family-members',
            'title' => 'Family & Vehicle',
            'copy' => 'Keluarga, kendaraan, dan slot parkir.',
            'icon' => 'M7 19a5 5 0 0 1 10 0M9 8a3 3 0 1 0 6 0 3 3 0 0 0-6 0M3 17h3M18 17h3M4 14h16',
        ],
        [
            'number' => 6,
            'route' => 'resident-management.residents',
            'title' => 'Resident Monitoring',
            'copy' => 'Aktivitas penghuni dipantau real-time.',
            'icon' => 'M4 19V5M8 19v-6M12 19V8M16 19v-9M20 19V4',
        ],
        [
            'number' => 7,
            'route' => 'resident-management.move-in-out',
            'title' => 'Move-Out Process',
            'copy' => 'Check-out, inspeksi, dan deposit.',
            'icon' => 'M5 4h12v16H5V4ZM17 8h2l2 2v8h-4M9 8h4M9 12h4M9 16h2',
        ],
        [
            'number' => 8,
            'route' => 'resident-management.residents',
            'title' => 'Resident History',
            'copy' => 'Riwayat tersimpan untuk audit.',
            'icon' => 'M6 3h9l3 3v15H6V3ZM14 3v4h4M9 11h6M9 15h6M9 19h3',
        ],
    ];
@endphp

<section class="resident-flow-panel" aria-label="Resident management flow">
    <div class="flow-strip">
        @foreach ($steps as $step)
            <a href="{{ route($step['route']) }}" @class(['flow-step', 'active' => $activeSteps->contains($step['number'])])>
                <span class="step-badge">Step {{ $step['number'] }}</span>
                <span class="flow-node" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="{{ $step['icon'] }}"/>
                    </svg>
                </span>
                <span class="flow-title">{{ $step['title'] }}</span>
                <span class="flow-copy">{{ $step['copy'] }}</span>
            </a>
        @endforeach
    </div>
</section>
