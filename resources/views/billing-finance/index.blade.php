@extends('layouts.app')

@php
    $pageKey = $pageKey ?? 'invoices';

    $pages = [
        'invoices' => [
            'label' => 'Invoice Management',
            'title' => 'Invoice Management',
            'subtitle' => 'Manage and track all unit invoices.',
            'metrics' => [
                ['label' => 'Pending Invoices', 'value' => '85', 'tone' => 'gold', 'trend' => [22, 26, 29, 32, 30, 36, 40]],
                ['label' => 'Paid This Month', 'value' => '210', 'tone' => 'green', 'trend' => [16, 18, 24, 21, 28, 35, 31]],
                ['label' => 'Overdue Invoices', 'value' => '18', 'tone' => 'red', 'trend' => [31, 22, 28, 24, 26, 27, 23]],
            ],
        ],
        'debt-collection' => [
            'label' => 'Debt Collection',
            'title' => 'Bills Collecting',
            'subtitle' => 'Manage and track all pending bills and collections.',
            'metrics' => [
                ['label' => 'Overdue Collecting (DPD 30+)', 'value' => 'Rp 115,000,000', 'tone' => 'red', 'trend' => [28, 30, 24, 26, 23, 25, 21]],
                ['label' => 'Total Due (Current & DPD 1-30)', 'value' => 'Rp 280,000,000', 'tone' => 'gold', 'trend' => [19, 19, 21, 21, 20, 22, 22]],
                ['label' => 'Upcoming PTP Payments', 'value' => 'Rp 340,000,000', 'tone' => 'green', 'trend' => [10, 14, 13, 18, 16, 24, 27]],
            ],
        ],
        'auto-bills' => [
            'label' => 'Auto Bills',
            'title' => 'Auto Billing Control Center',
            'subtitle' => 'View comprehensive auto billing records.',
            'metrics' => [
                ['label' => 'Upcoming Generation Cycle', 'value' => 'Next: 15 Jun 2026', 'tone' => 'blue', 'subvalue' => '7 Unit Cycles', 'trend' => [18, 20, 19, 21, 23, 24, 26]],
                ['label' => 'Automated Dispatch Success (MTD)', 'value' => '98.2%', 'tone' => 'green', 'trend' => [81, 85, 87, 90, 93, 96, 98]],
                ['label' => 'Pending Reconciliations', 'value' => '23', 'tone' => 'gold', 'subvalue' => 'Payments', 'trend' => [29, 27, 26, 24, 23, 23, 22]],
            ],
        ],
        'history-payment' => [
            'label' => 'History Payment',
            'title' => 'History Payment',
            'subtitle' => 'View comprehensive resident payment records.',
            'metrics' => [
                ['label' => 'Total Balance Due', 'value' => 'Rp 1,500,000,000', 'tone' => 'red', 'trend' => [32, 32, 31, 30, 30, 29, 28]],
                ['label' => 'Net Payments (MTD)', 'value' => 'Rp 950,000,000', 'tone' => 'green', 'trend' => [18, 20, 19, 25, 22, 29, 34]],
                ['label' => 'Residents with Unpaid Balances', 'value' => '210', 'tone' => 'green', 'trend' => [14, 15, 14, 14, 13, 13, 12]],
            ],
        ],
    ];

    $page = $pages[$pageKey] ?? $pages['invoices'];

    $invoiceRows = [
        ['id' => '00000007009', 'unit' => 'Tower 1', 'resident' => 'Aether Reddeen', 'amount' => 'Rp 1,250,000', 'due' => '07 Jun 2026', 'status' => 'PAID', 'class' => 'status-approved', 'payment' => 'Payment'],
        ['id' => '00000007011', 'unit' => 'Tower 2', 'resident' => 'Rerana Ranger', 'amount' => 'Rp 1,500,000', 'due' => '07 Jun 2026', 'status' => 'PENDING', 'class' => 'status-pending', 'payment' => 'Payment'],
        ['id' => '00000007012', 'unit' => 'Tower 3', 'resident' => 'John Fiara', 'amount' => 'Rp 2,500,000', 'due' => '07 Jun 2026', 'status' => 'PENDING', 'class' => 'status-pending', 'payment' => 'Payment'],
        ['id' => '00000002013', 'unit' => 'Tower 4', 'resident' => 'Heider Ammionoe', 'amount' => 'Rp 1,200,000', 'due' => '07 Jun 2026', 'status' => 'OVERDUE', 'class' => 'status-rejected', 'payment' => 'Payment'],
        ['id' => '00000007014', 'unit' => 'Tower 5', 'resident' => 'Joltwr Rongan', 'amount' => 'Rp 1,350,000', 'due' => '07 Jun 2026', 'status' => 'OVERDUE', 'class' => 'status-rejected', 'payment' => 'Payment'],
        ['id' => '00000002035', 'unit' => 'Tower 6', 'resident' => 'Anher Para', 'amount' => 'Rp 2,500,000', 'due' => '07 Jun 2026', 'status' => 'OVERDUE', 'class' => 'status-rejected', 'payment' => 'Payment'],
    ];

    $collectionRows = [
        ['id' => 'PAY0000001', 'invoice' => 'IN03000007009', 'unit' => 'Tower 1', 'resident' => 'Aether Reddeen', 'amount' => 'Rp 1,250,000', 'due' => '15 May 2026', 'age' => '23', 'status' => 'Overdue', 'class' => 'status-rejected', 'collector' => 'A. Tan', 'log' => '06 Jun 14:30 (WA) PTP', 'ptp' => '10 Jun'],
        ['id' => 'PAY0000002', 'invoice' => 'IN03000007011', 'unit' => 'Tower 2', 'resident' => 'Rerana Ranger', 'amount' => 'Rp 1,550,000', 'due' => '20 May 2026', 'age' => '13', 'status' => 'Current', 'class' => 'status-approved', 'collector' => 'B. Lee', 'log' => '06 Jun 14:30, Refuse', 'ptp' => '-'],
        ['id' => 'PAY0000003', 'invoice' => 'IN03000007012', 'unit' => 'Tower 3', 'resident' => 'John Fiara', 'amount' => 'Rp 2,500,000', 'due' => '30 May 2026', 'age' => '8', 'status' => 'DPD 1-30', 'class' => 'status-pending', 'collector' => 'B. Lee', 'log' => '06 Jun 14:30 (WA) PTP', 'ptp' => '-'],
        ['id' => 'PAY0000004', 'invoice' => 'IN03000007012', 'unit' => 'Tower 4', 'resident' => 'Heider Ammione', 'amount' => 'Rp 1,550,000', 'due' => '05 Jun 2026', 'age' => '2', 'status' => 'Current', 'class' => 'status-approved', 'collector' => 'A. Tan', 'log' => '05 Jun 14:30, No Answer', 'ptp' => '-'],
        ['id' => 'PAY0000005', 'invoice' => 'IN03000007012', 'unit' => 'Tower 5', 'resident' => 'Joltwr Rongan', 'amount' => 'Rp 1,250,000', 'due' => '05 Jun 2026', 'age' => '2', 'status' => 'DPD 31-60', 'class' => 'status-expired', 'collector' => 'A. Tan', 'log' => '10 Jun 14:30, PTP', 'ptp' => 'PTP'],
        ['id' => 'PAY0000006', 'invoice' => 'IN03000007035', 'unit' => 'Tower 6', 'resident' => 'Anher Fara', 'amount' => 'Rp 1,250,000', 'due' => '05 Jun 2026', 'age' => '2', 'status' => 'DPD 31-60', 'class' => 'status-expired', 'collector' => 'B. Lee', 'log' => '10 Jun 14:30, PTP', 'ptp' => '-'],
    ];

    $autoBillRows = [
        ['run' => 'RUN001', 'description' => 'Monthly IPL - Tower 1', 'date' => '15 Jun 2026', 'units' => '45', 'status' => 'SCHEDULED', 'class' => 'status-slate', 'revenue' => 'Rp 1,125,000,000'],
        ['run' => 'RUN002', 'description' => 'Utility Bills - Tower 2', 'date' => '02 Jun 2026', 'units' => '30', 'status' => 'COMPLETED', 'class' => 'status-approved', 'revenue' => 'Rp 650,000,000'],
        ['run' => 'RUN003', 'description' => 'Utility Bills - Tower 1', 'date' => '02 Jun 2026', 'units' => '30', 'status' => 'COMPLETED', 'class' => 'status-approved', 'revenue' => 'Rp 650,000,000'],
        ['run' => 'RUN004', 'description' => 'Utility Bills - Tower 2', 'date' => '02 Jun 2026', 'units' => '30', 'status' => 'COMPLETED', 'class' => 'status-approved', 'revenue' => 'Rp 650,000,000'],
        ['run' => 'RUN005', 'description' => 'Utility Bills - Tower 2', 'date' => '02 Jun 2026', 'units' => '30', 'status' => 'COMPLETED', 'class' => 'status-approved', 'revenue' => 'Rp 650,000,000'],
        ['run' => 'RUN006', 'description' => 'Utility Bills - Tower 1 (Cintlated)', 'date' => '08 Jun 2026', 'units' => '45', 'status' => 'COMPLETED', 'class' => 'status-approved', 'revenue' => 'Rp 130,000,000'],
    ];

    $historyRows = [
        ['id' => 'PAY0000001', 'name' => 'Aether Reddeen', 'unit' => 'Tower 1', 'type' => '3BR', 'invoiced' => 'Rp 12,000,000', 'paid' => 'Rp 10,750,000', 'balance' => 'Rp 1,250,000', 'status' => 'ACTIVE', 'class' => 'status-approved', 'log' => '06 Jun 14:30 (WA) PTP', 'ptp' => '10 Jun'],
        ['id' => 'PAY0000002', 'name' => 'Rerana Ranger', 'unit' => 'Tower 2', 'type' => '3BR', 'invoiced' => 'Rp 15,500,000', 'paid' => 'Rp 10,750,000', 'balance' => 'Rp 1,250,000', 'status' => 'CURRENT', 'class' => 'status-approved', 'log' => '06 Jun 14:30 (WA) PTP', 'ptp' => '10 Jun'],
        ['id' => 'PAY0000003', 'name' => 'Rerana Ranger', 'unit' => 'Tower 3', 'type' => '3BR', 'invoiced' => 'Rp 15,500,000', 'paid' => 'Rp 10,750,000', 'balance' => 'Rp 1,250,000', 'status' => 'CURRENT', 'class' => 'status-approved', 'log' => '06 Jun 14:30 (WA) PTP', 'ptp' => '10 Jun'],
        ['id' => 'PAY0000004', 'name' => 'John Flara', 'unit' => 'Tower 4', 'type' => '3BR', 'invoiced' => 'Rp 25,000,000', 'paid' => 'Rp 13,250,000', 'balance' => 'Rp 1,250,000', 'status' => 'POSITIVE', 'class' => 'status-approved', 'log' => '06 Jun 14:30 (WA) PTP', 'ptp' => '10 Jun'],
        ['id' => 'PAY0000005', 'name' => 'Heider Ammione', 'unit' => 'Tower 6', 'type' => '3BR', 'invoiced' => 'Rp 12,500,000', 'paid' => 'Rp 11,750,000', 'balance' => 'Rp 1,250,000', 'status' => 'OVERPAID', 'class' => 'status-rejected', 'log' => '06 Jun 14:30 (WA) PTP', 'ptp' => '10 Jun'],
        ['id' => 'PAY0000006', 'name' => 'Anher Fara', 'unit' => 'Tower 6', 'type' => '3BR', 'invoiced' => 'Rp 12,500,000', 'paid' => 'Rp 11,750,000', 'balance' => 'Rp 4,250,000', 'status' => 'ACTIVE', 'class' => 'status-approved', 'log' => '06 Jun 14:30 (WA) PTP', 'ptp' => '10 Jun'],
    ];

    $barSets = [
        'invoices' => [
            ['month' => 'Jan', 'a' => 52, 'b' => 31], ['month' => 'Feb', 'a' => 58, 'b' => 35], ['month' => 'Mar', 'a' => 57, 'b' => 34], ['month' => 'Apr', 'a' => 62, 'b' => 38],
            ['month' => 'May', 'a' => 65, 'b' => 39], ['month' => 'Jun', 'a' => 72, 'b' => 45], ['month' => 'Jul', 'a' => 69, 'b' => 44], ['month' => 'Aug', 'a' => 61, 'b' => 41],
            ['month' => 'Sep', 'a' => 63, 'b' => 47], ['month' => 'Oct', 'a' => 70, 'b' => 44], ['month' => 'Nov', 'a' => 74, 'b' => 46], ['month' => 'Dec', 'a' => 78, 'b' => 49],
        ],
        'debt-collection' => [
            ['month' => 'Jan', 'a' => 40, 'b' => 30, 'c' => 12, 'd' => 5], ['month' => 'Feb', 'a' => 36, 'b' => 25, 'c' => 8, 'd' => 4], ['month' => 'Mar', 'a' => 41, 'b' => 33, 'c' => 12, 'd' => 5],
            ['month' => 'Apr', 'a' => 70, 'b' => 40, 'c' => 10, 'd' => 4], ['month' => 'May', 'a' => 68, 'b' => 28, 'c' => 12, 'd' => 7], ['month' => 'Jun', 'a' => 68, 'b' => 31, 'c' => 10, 'd' => 6],
            ['month' => 'Jul', 'a' => 49, 'b' => 24, 'c' => 16, 'd' => 5], ['month' => 'Aug', 'a' => 53, 'b' => 9, 'c' => 5, 'd' => 2], ['month' => 'Sep', 'a' => 48, 'b' => 43, 'c' => 11, 'd' => 8],
            ['month' => 'Oct', 'a' => 65, 'b' => 41, 'c' => 14, 'd' => 4], ['month' => 'Nov', 'a' => 39, 'b' => 39, 'c' => 18, 'd' => 3], ['month' => 'Dec', 'a' => 26, 'b' => 15, 'c' => 9, 'd' => 2],
        ],
        'auto-bills' => [
            ['month' => 'Jan', 'a' => 34, 'b' => 9, 'c' => 22, 'line' => 72], ['month' => 'Feb', 'a' => 28, 'b' => 7, 'c' => 15, 'line' => 81], ['month' => 'Mar', 'a' => 33, 'b' => 10, 'c' => 21, 'line' => 66],
            ['month' => 'Apr', 'a' => 52, 'b' => 16, 'c' => 34, 'line' => 83], ['month' => 'May', 'a' => 48, 'b' => 15, 'c' => 29, 'line' => 84], ['month' => 'Jun', 'a' => 50, 'b' => 13, 'c' => 28, 'line' => 77],
            ['month' => 'Jul', 'a' => 46, 'b' => 10, 'c' => 21, 'line' => 83], ['month' => 'Aug', 'a' => 39, 'b' => 7, 'c' => 13, 'line' => 60], ['month' => 'Sep', 'a' => 51, 'b' => 16, 'c' => 33, 'line' => 85],
            ['month' => 'Oct', 'a' => 52, 'b' => 17, 'c' => 34, 'line' => 88], ['month' => 'Nov', 'a' => 41, 'b' => 9, 'c' => 26, 'line' => 82], ['month' => 'Dec', 'a' => 26, 'b' => 6, 'c' => 15, 'line' => 86],
        ],
        'history-payment' => [
            ['month' => 'Jan', 'a' => 54, 'b' => 50, 'line' => 43], ['month' => 'Feb', 'a' => 43, 'b' => 42, 'line' => 35], ['month' => 'Mar', 'a' => 53, 'b' => 46, 'line' => 39],
            ['month' => 'Apr', 'a' => 81, 'b' => 69, 'line' => 58], ['month' => 'May', 'a' => 73, 'b' => 60, 'line' => 52], ['month' => 'Jun', 'a' => 75, 'b' => 64, 'line' => 55],
            ['month' => 'Jul', 'a' => 60, 'b' => 49, 'line' => 46], ['month' => 'Aug', 'a' => 47, 'b' => 44, 'line' => 40], ['month' => 'Sep', 'a' => 80, 'b' => 68, 'line' => 56],
            ['month' => 'Oct', 'a' => 81, 'b' => 74, 'line' => 59], ['month' => 'Nov', 'a' => 63, 'b' => 53, 'line' => 41], ['month' => 'Dec', 'a' => 42, 'b' => 36, 'line' => 18],
        ],
    ];
@endphp

@section('title', $page['label'])
@section('topbar_context')
    Billing &amp; Finance > {{ $page['label'] }}
@endsection
@section('topbar_subtitle', $page['subtitle'])

@section('content')
    <div class="billing-page">
        <section class="billing-header">
            <div>
                <h2>{{ $page['title'] }}</h2>
                <p>{{ strtoupper($page['subtitle']) }}</p>
            </div>
            <div class="billing-actions">
                <button class="btn secondary" type="button" data-modal-open="billing-export-modal">Export Excel</button>
            </div>
        </section>

        <section class="billing-metrics">
            @foreach ($page['metrics'] as $metric)
                <article class="billing-metric {{ $metric['tone'] }}">
                    <div>
                        <span>{{ $metric['label'] }}</span>
                        <strong>{{ $metric['value'] }}</strong>
                        @if (! empty($metric['subvalue']))
                            <small>{{ $metric['subvalue'] }}</small>
                        @endif
                    </div>
                    <div class="billing-trend" aria-hidden="true">
                        @foreach ($metric['trend'] as $point)
                            <span style="height: {{ $point }}%;"></span>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </section>

        @if ($pageKey === 'invoices')
            <div class="billing-grid">
                <section class="billing-panel billing-main">
                    <div class="billing-panel-head">
                        <h3>Invoice Ledger</h3>
                        <div class="billing-search">
                            <input type="search" placeholder="Search" aria-label="Search invoices">
                        </div>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Invoice ID</th>
                                    <th>Unit</th>
                                    <th>Resident</th>
                                    <th>Amount (Rp)</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Payment Method</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoiceRows as $row)
                                    <tr>
                                        <td>{{ $row['id'] }}</td>
                                        <td>{{ $row['unit'] }}</td>
                                        <td>{{ $row['resident'] }}</td>
                                        <td>{{ $row['amount'] }}</td>
                                        <td>{{ $row['due'] }}</td>
                                        <td><span class="{{ $row['class'] }}">{{ $row['status'] }}</span></td>
                                        <td>{{ $row['payment'] }}</td>
                                        <td class="billing-row-actions">EMAIL | PRINT | VIEW DETAILS</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="billing-panel">
                    <div class="billing-panel-head"><h3>Invoice Status Distribution</h3></div>
                    <div class="billing-donut-panel">
                        <div class="billing-donut invoices"></div>
                        <div class="billing-legend">
                            <span><i class="blue"></i>PAID</span>
                            <span><i class="gold"></i>PENDING</span>
                            <span><i class="red"></i>OVERDUE</span>
                        </div>
                    </div>
                </section>

                <section class="billing-panel billing-main">
                    <div class="billing-panel-head">
                        <h3>Monthly Invoice Revenue vs. Outstanding</h3>
                        <div class="billing-chart-key"><span class="blue-dot"></span> Monthly <span class="gold-dot"></span> Weekly</div>
                    </div>
                    <div class="billing-bars dual">
                        @foreach ($barSets['invoices'] as $row)
                            <div class="billing-bar-col">
                                <div class="billing-bar-pair">
                                    <span class="blue" style="height: {{ $row['a'] }}%;"></span>
                                    <span class="gold" style="height: {{ $row['b'] }}%;"></span>
                                </div>
                                <small>{{ $row['month'] }}</small>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="billing-summary">
                    <div class="billing-summary-item"><span>Monthly Revenue</span><strong>Rp 2.63 M</strong><small>+ 8.2%</small></div>
                    <div class="billing-summary-item"><span>Monthly Expense</span><strong>Rp 1.28 M</strong><small>- 2.1%</small></div>
                    <div class="billing-summary-item"><span>Net Income</span><strong>Rp 1.35 M</strong><small>+ 12.4%</small></div>
                    <div class="billing-summary-item"><span>Budget vs Actual</span><strong>84.6%</strong><small>On Track</small></div>
                    <div class="billing-summary-item"><span>Resident Satisfaction</span><strong>4.7 / 5.0</strong><small>★★★★★</small></div>
                    <div class="billing-summary-item"><span>System Health</span><strong>100%</strong><small>Operational</small></div>
                </section>
            </div>
        @elseif ($pageKey === 'debt-collection')
            <div class="billing-grid">
                <section class="billing-panel billing-main">
                    <div class="billing-panel-head">
                        <h3>Active Pending Collection Logs</h3>
                        <div class="billing-search">
                            <input type="search" placeholder="Search" aria-label="Search collections">
                        </div>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Collection ID</th>
                                    <th>Invoice ID</th>
                                    <th>Unit</th>
                                    <th>Resident</th>
                                    <th>Amount (Rp)</th>
                                    <th>Due Date</th>
                                    <th>Age (DPD)</th>
                                    <th>Status</th>
                                    <th>Collector</th>
                                    <th>Last Contact Log</th>
                                    <th>PTP Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($collectionRows as $row)
                                    <tr>
                                        <td>{{ $row['id'] }}</td>
                                        <td><a href="#" class="billing-link">{{ $row['invoice'] }}</a></td>
                                        <td>{{ $row['unit'] }}</td>
                                        <td>{{ $row['resident'] }}</td>
                                        <td>{{ $row['amount'] }}</td>
                                        <td>{{ $row['due'] }}</td>
                                        <td>{{ $row['age'] }}</td>
                                        <td><span class="{{ $row['class'] }}">{{ $row['status'] }}</span></td>
                                        <td>{{ $row['collector'] }}</td>
                                        <td>{{ $row['log'] }}</td>
                                        <td>{{ $row['ptp'] }}</td>
                                        <td class="billing-row-actions">View Receipt | Log Call | Record PTP</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="billing-panel">
                    <div class="billing-panel-head"><h3>Collection Assignment &amp; Status</h3></div>
                    <div class="billing-donut-panel">
                        <div class="billing-donut collections"></div>
                        <div class="billing-legend">
                            <span><i class="blue"></i>Collector 1</span>
                            <span><i class="gold"></i>Current</span>
                            <span><i class="green"></i>Collector 2</span>
                            <span><i class="red"></i>Overdue</span>
                        </div>
                    </div>
                </section>

                <section class="billing-panel billing-main">
                    <div class="billing-panel-head"><h3>Monthly Debt Aging Distribution</h3></div>
                    <div class="billing-bars stack">
                        @foreach ($barSets['debt-collection'] as $row)
                            <div class="billing-bar-col">
                                <div class="billing-bar-stack">
                                    <span class="blue" style="height: {{ $row['a'] }}%;"></span>
                                    <span class="gold" style="height: {{ $row['b'] }}%;"></span>
                                    <span class="green" style="height: {{ $row['c'] }}%;"></span>
                                    <span class="red" style="height: {{ $row['d'] }}%;"></span>
                                </div>
                                <small>{{ $row['month'] }}</small>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>
        @elseif ($pageKey === 'auto-bills')
            <div class="billing-grid">
                <section class="billing-panel billing-main">
                    <div class="billing-panel-head">
                        <h3>Upcoming &amp; Recent Auto Billing Runs</h3>
                        <div class="billing-search">
                            <input type="search" placeholder="Search" aria-label="Search runs">
                        </div>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Run ID</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>No. of Units</th>
                                    <th>Status</th>
                                    <th>Revenue (Rp)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($autoBillRows as $row)
                                    <tr>
                                        <td>{{ $row['run'] }}</td>
                                        <td>{{ $row['description'] }}</td>
                                        <td>{{ $row['date'] }}</td>
                                        <td>{{ $row['units'] }}</td>
                                        <td><span class="{{ $row['class'] }}">{{ $row['status'] }}</span></td>
                                        <td>{{ $row['revenue'] }}</td>
                                        <td class="billing-row-actions">View Details | {{ $row['status'] === 'SCHEDULED' ? 'Edit Schedule' : 'Print Kuitansi' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="billing-panel">
                    <div class="billing-panel-head"><h3>Auto Billing Success Rate</h3></div>
                    <div class="billing-donut-panel">
                        <div class="billing-donut autobills"></div>
                        <div class="billing-legend">
                            <span><i class="green"></i>Success</span>
                            <span><i class="red"></i>Failed (Need Review)</span>
                            <span><i class="slate"></i>Cancelled</span>
                        </div>
                    </div>
                </section>

                <section class="billing-panel billing-main">
                    <div class="billing-panel-head"><h3>Auto Billing Accuracy &amp; Volume</h3></div>
                    <div class="billing-bars stack line">
                        @foreach ($barSets['auto-bills'] as $row)
                            <div class="billing-bar-col">
                                <div class="billing-line-point" style="bottom: {{ $row['line'] }}%;"></div>
                                <div class="billing-bar-stack">
                                    <span class="blue" style="height: {{ $row['a'] }}%;"></span>
                                    <span class="red" style="height: {{ $row['b'] }}%;"></span>
                                    <span class="green" style="height: {{ $row['c'] }}%;"></span>
                                </div>
                                <small>{{ $row['month'] }}</small>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>
        @else
            <div class="billing-grid">
                <section class="billing-panel billing-main">
                    <div class="billing-panel-head">
                        <h3>Resident Balance &amp; Payment History</h3>
                        <div class="billing-search">
                            <input type="search" placeholder="Search" aria-label="Search history">
                        </div>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Resident ID</th>
                                    <th>Name</th>
                                    <th>Unit</th>
                                    <th>Unit Type</th>
                                    <th>Total Invoiced</th>
                                    <th>Total Paid</th>
                                    <th>Current Balance</th>
                                    <th>Status</th>
                                    <th>PTP Log</th>
                                    <th>PTP Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($historyRows as $row)
                                    <tr>
                                        <td>{{ $row['id'] }}</td>
                                        <td>{{ $row['name'] }}</td>
                                        <td>{{ $row['unit'] }}</td>
                                        <td>{{ $row['type'] }}</td>
                                        <td>{{ $row['invoiced'] }}</td>
                                        <td>{{ $row['paid'] }}</td>
                                        <td>{{ $row['balance'] }}</td>
                                        <td><span class="{{ $row['class'] }}">{{ $row['status'] }}</span></td>
                                        <td>{{ $row['log'] }}</td>
                                        <td>{{ $row['ptp'] }}</td>
                                        <td class="billing-row-actions">View Receipt | Log Call | Record PTP</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="billing-panel">
                    <div class="billing-panel-head"><h3>Resident Assignment &amp; Status</h3></div>
                    <div class="billing-donut-panel">
                        <div class="billing-donut history"></div>
                        <div class="billing-legend">
                            <span><i class="green"></i>Active</span>
                            <span><i class="red"></i>Delinquent</span>
                            <span><i class="slate"></i>Terminated</span>
                            <span><i class="blue"></i>Overpaid</span>
                        </div>
                    </div>
                </section>

                <section class="billing-panel billing-main">
                    <div class="billing-panel-head"><h3>Monthly Resident Billing vs. Payment Trend</h3></div>
                    <div class="billing-bars dual line">
                        @foreach ($barSets['history-payment'] as $row)
                            <div class="billing-bar-col">
                                <div class="billing-line-point gold-point" style="bottom: {{ $row['line'] }}%;"></div>
                                <div class="billing-bar-pair">
                                    <span class="blue" style="height: {{ $row['a'] }}%;"></span>
                                    <span class="green" style="height: {{ $row['b'] }}%;"></span>
                                </div>
                                <small>{{ $row['month'] }}</small>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>
        @endif

        <div class="visitor-modal" id="billing-export-modal" aria-hidden="true">
            <button class="visitor-modal-backdrop" type="button" data-modal-close aria-label="Close modal"></button>
            <div class="visitor-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="billing-export-title">
                <div class="visitor-modal-head">
                    <h3 class="visitor-modal-title" id="billing-export-title">Export Excel</h3>
                    <button class="visitor-modal-close" type="button" data-modal-close aria-label="Close modal">x</button>
                </div>
                <div class="visitor-modal-body">
                    <div class="billing-export-copy">
                        <strong>{{ $page['title'] }}</strong>
                        <p>Export Excel masih berupa preview statis. Nanti tombol ini bisa dihubungkan ke generator file CSV atau XLSX saat backend billing sudah siap.</p>
                    </div>
                    <div class="billing-export-actions">
                        <button class="btn secondary" type="button" data-modal-close>Close</button>
                        <button class="btn" type="button" data-modal-close>Prepare Export</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
