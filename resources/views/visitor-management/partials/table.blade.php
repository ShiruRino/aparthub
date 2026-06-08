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
                        @php($key = $column['key'])
                        <td>
                            @if ($key === 'no')
                                {{ $row['no'] ?? $loop->parent->iteration }}
                            @elseif ($key === 'name')
                                <strong>{{ $row['name'] }}</strong>
                            @elseif ($key === 'unit')
                                {{ $row['unit'] ?? '-' }}@isset($row['resident']) - {{ $row['resident'] }}@endisset
                            @elseif ($key === 'date')
                                {{ $row['date'] ?? '07 Jun 2026 - '.$row['time'] }}
                            @elseif ($key === 'status')
                                <span class="badge {{ $row['statusClass'] ?? 'status-approved' }}">{{ $row['status'] }}</span>
                            @elseif ($key === 'action')
                                <div class="visitor-action-buttons">
                                    @foreach ($row['actions'] ?? [['View', 'info']] as [$label, $variant])
                                        <button class="btn compact {{ $variant }}" type="button">{{ $label }}</button>
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

<div class="pagination">
    <span class="muted">Showing 1 to {{ count($rows) }} of {{ max(count($rows), 20) }} entries</span>
</div>
