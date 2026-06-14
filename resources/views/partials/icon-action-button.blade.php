@php
    $icon = $icon ?? 'eye';
    $label = $label ?? 'Open action';
    $variant = $variant ?? 'neutral';
    $modal = $modal ?? null;
    $extraClass = trim('icon-action-btn ' . ($variant !== 'neutral' ? 'is-' . $variant : '') . ' ' . ($class ?? ''));

    $paths = [
        'eye' => 'M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Zm10 4a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z',
        'edit' => 'M4 20h4l11-11-4-4L4 16v4Zm9-13 4 4',
        'move' => 'M4 7h12m-4-4 4 4-4 4M20 17H8m4-4-4 4 4 4',
        'check' => 'M20 6 9 17l-5-5',
        'x' => 'm6 6 12 12M18 6 6 18',
        'history' => 'M3 12a9 9 0 1 0 3-6.7M3 4v6h6m3-3v5l3 2',
        'print' => 'M6 9V3h12v6M6 18H4v-7h16v7h-2M7 14h10v7H7v-7Z',
        'access' => 'M4 5h16v14H4V5Zm4 4h4m-4 4h8m3-4h.01',
        'tool' => 'M14.7 6.3a4 4 0 0 0-5 5L4 17v3h3l5.7-5.7a4 4 0 0 0 5-5l-3 3-3-3 3-3Z',
        'slot' => 'M6 4h12v16H6V4Zm3 4h6m-6 4h3m-3 4h6',
        'mail' => 'M4 6h16v12H4V6Zm0 1 8 6 8-6',
        'phone' => 'M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1A19.5 19.5 0 0 1 5.2 12.8 19.8 19.8 0 0 1 2.1 4.1 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7l.4 3a2 2 0 0 1-.6 1.8L7.6 9.8a16 16 0 0 0 6.6 6.6l1.3-1.3a2 2 0 0 1 1.8-.6l3 .4a2 2 0 0 1 1.7 2Z',
        'download' => 'M12 3v12m0 0 4-4m-4 4-4-4M4 21h16',
        'document' => 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z M14 2v6h6',
        'car' => 'M5 11l1.5-4.5A2 2 0 0 1 8.4 5h7.2a2 2 0 0 1 1.9 1.5L19 11m-14 0h14M7 15h.01M17 15h.01M6 19v-2m12 2v-2',
        'pin' => 'M12 17v5m0-5 6-6a4 4 0 0 0-5.7-5.6L12 6l-.3-.3A4 4 0 0 0 6 11l6 6Z',
        'chart' => 'M4 19h16M7 16V8m5 8V5m5 11v-6',
        'plus' => 'M12 5v14M5 12h14',
        'trash' => 'M3 6h18M8 6V4h8v2m-9 0 1 14h6l1-14',
    ];

    $attributes = array_merge($modal ? ['data-modal-open' => $modal] : [], $data ?? []);
@endphp

<button
    class="{{ $extraClass }}"
    type="button"
    title="{{ $label }}"
    aria-label="{{ $label }}"
    @foreach ($attributes as $attribute => $value)
        {{ $attribute }}="{{ $value }}"
    @endforeach
>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="{{ $paths[$icon] ?? $paths['eye'] }}"/>
    </svg>
</button>
