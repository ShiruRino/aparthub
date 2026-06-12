@include('partials.icon-action-button', [
    'icon' => $icon ?? 'eye',
    'label' => $label,
    'modal' => $modal ?? 'resident-action-modal',
    'variant' => match ($variant ?? null) {
        'success' => 'success',
        'danger' => 'danger',
        default => 'neutral',
    },
    'class' => 'resident-action-btn',
])
