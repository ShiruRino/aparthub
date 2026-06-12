@include('partials.action-preview-modal', [
    'id' => 'service-active-task-modal',
    'title' => 'Selected Active Task Detail',
    'summary' => 'SR-2026-005',
    'subtitle' => 'Ahmad Rizky - Unit B-2008',
    'avatar' => 'AT',
    'rows' => [
        ['Category', 'Plumbing'],
        ['Priority', 'High'],
        ['Created', '07 Jun 2026 09:15 AM'],
        ['Status', 'Active Fixing'],
        ['Latest Log', 'Compressor failure confirmed; replacement initiated.'],
        ['Assignment', 'John Technical / 07 Jun 2026 - 12:00 PM'],
    ],
    'confirmLabel' => 'Assign Ticket',
])
