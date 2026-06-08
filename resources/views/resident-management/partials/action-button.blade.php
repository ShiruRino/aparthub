@php
    $icon = $icon ?? 'eye';
    $modal = $modal ?? 'resident-action-modal';

    $paths = [
        'eye' => 'M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12ZM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z',
        'edit' => 'M4 20h4l11-11-4-4L4 16v4ZM13 6l4 4',
        'move' => 'M4 7h12M12 3l4 4-4 4M20 17H8M12 13l-4 4 4 4',
        'check' => 'M20 6 9 17l-5-5',
        'x' => 'M6 6l12 12M18 6 6 18',
        'history' => 'M3 12a9 9 0 1 0 3-6.7M3 4v6h6M12 7v6l4 2',
        'print' => 'M6 9V3h12v6M6 18H4v-7h16v7h-2M7 14h10v7H7v-7Z',
        'access' => 'M4 5h16v14H4V5ZM8 9h4M8 13h8M15 9h1',
        'tool' => 'M14.7 6.3a4 4 0 0 0-5 5L4 17v3h3l5.7-5.7a4 4 0 0 0 5-5l-3 3-3-3 3-3Z',
        'slot' => 'M6 4h12v16H6V4ZM9 8h6M9 12h3M9 16h6',
    ];
@endphp

<button class="resident-action-btn {{ $variant ?? '' }}" type="button" data-modal-open="{{ $modal }}" title="{{ $label }}">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="{{ $paths[$icon] ?? $paths['eye'] }}"/>
    </svg>
    <span>{{ $label }}</span>
</button>
