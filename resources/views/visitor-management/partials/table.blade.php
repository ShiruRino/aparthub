@php
    $actionIcons = [
        'View' => 'eye',
        'View Details' => 'eye',
        'Approve' => 'check',
        'Reject' => 'x',
        'Arrive' => 'check',
        'Cancel' => 'x',
        'Check-In' => 'check',
        'Check-Out' => 'x',
        'Manage Access' => 'access',
        'Verify Plate' => 'car',
        'Review Record' => 'document',
    ];

    $actionVariants = [
        'success' => 'success',
        'danger' => 'danger',
        'gold' => 'gold',
        'info' => 'info',
        'secondary' => 'neutral',
    ];
@endphp

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                @foreach ($columns as $column)
                    <th>{{ $column['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    @foreach ($columns as $column)
                        @php
                            $key = $column['key'];
                        @endphp
                        <td>
                            @if ($key === 'no')
                                {{ $row['no'] ?? $loop->parent->iteration }}
                            @elseif ($key === 'name')
                                <strong>{{ $row['name'] }}</strong>
                            @elseif ($key === 'unit')
                                {{ $row['unit'] ?? '-' }}
                                @if (! empty($row['resident']))
                                    - {{ $row['resident'] }}
                                @endif
                            @elseif ($key === 'date')
                                {{ $row['date'] ?? '07 Jun 2026 - '.$row['time'] }}
                            @elseif ($key === 'status')
                                <span class="badge {{ $row['statusClass'] ?? 'status-approved' }}">{{ $row['status'] }}</span>
                            @elseif ($key === 'action')
                                <div class="visitor-action-buttons">
                                    @foreach ($row['actions'] ?? [['View', 'info']] as $action)
                                        @php
                                            [$label, $variant, $href] = array_pad($action, 3, null);
                                            $buttonPayload = [
                                                'label' => $label,
                                                'icon' => $actionIcons[$label] ?? 'eye',
                                                'variant' => $actionVariants[$variant] ?? 'neutral',
                                            ];

                                            if ($href) {
                                                $buttonPayload['href'] = $href;
                                            } else {
                                                $buttonPayload['modal'] = $modalId ?? 'visitor-action-modal';
                                            }
                                        @endphp
                                        @include('partials.icon-action-button', $buttonPayload)
                                    @endforeach
                                </div>
                            @else
                                {{ $row[$key] ?? '-' }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if (isset($paginator) && $paginator)
    @include('partials.pagination', ['paginator' => $paginator])
@else
    <div class="pagination">
        <span class="muted">Showing 1 to {{ count($rows) }} of {{ max(count($rows), 20) }} entries</span>
    </div>
@endif
