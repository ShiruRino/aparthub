@include('partials.action-preview-modal', [
    'id' => 'service-work-order-modal',
    'title' => 'Work Order Details',
    'summary' => 'Alex Wong',
    'subtitle' => 'Resident - A-1102',
    'avatar' => 'WO',
    'rows' => [
        ['Work Order ID', 'WO-2026-001'],
        ['Associated Ticket', 'SR-2026-001'],
        ['Issue', 'Plumbing - Faucet Leaking'],
        ['Technician', 'Michael'],
        ['Time', '07 Jun 10:00 - 12:00'],
        ['Required Material', 'Mixer Tap - New / Rubber Seals'],
    ],
    'confirmLabel' => 'Start Work',
])
