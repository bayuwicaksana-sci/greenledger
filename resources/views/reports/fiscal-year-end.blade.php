<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiscal Year {{ $fiscal_year->year }} - Year-End Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #2563eb;
        }

        .header h1 {
            font-size: 20pt;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .header .subtitle {
            font-size: 12pt;
            color: #64748b;
        }

        .meta-info {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f1f5f9;
            border-radius: 4px;
        }

        .meta-info table {
            width: 100%;
        }

        .meta-info td {
            padding: 4px 0;
        }

        .meta-info .label {
            font-weight: bold;
            width: 150px;
            color: #475569;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 14pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #cbd5e1;
        }

        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .stat-card {
            display: table-cell;
            width: 33.33%;
            padding: 10px;
            text-align: center;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
        }

        .stat-card .value {
            font-size: 18pt;
            font-weight: bold;
            color: #1e40af;
        }

        .stat-card .label {
            font-size: 9pt;
            color: #64748b;
            margin-top: 3px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.data-table th {
            background-color: #1e40af;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
        }

        table.data-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9pt;
        }

        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
        }

        .status-draft {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-active {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-completed {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-archived {
            background-color: #f3f4f6;
            color: #374151;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #94a3b8;
            padding: 10px 0;
            border-top: 1px solid #e2e8f0;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Fiscal Year {{ $fiscal_year->year }} - Year-End Report</h1>
        <div class="subtitle">
            {{ \Carbon\Carbon::parse($fiscal_year->start_date)->format('F j, Y') }}
            -
            {{ \Carbon\Carbon::parse($fiscal_year->end_date)->format('F j, Y') }}
        </div>
    </div>

    <div class="meta-info">
        <table>
            <tr>
                <td class="label">Report Generated:</td>
                <td>{{ $generated_at->format('F j, Y \a\t g:i A') }}</td>
            </tr>
            <tr>
                <td class="label">Fiscal Year Status:</td>
                <td>{{ $fiscal_year->is_closed ? 'Closed' : 'Open' }}</td>
            </tr>
            <tr>
                <td class="label">Total Programs:</td>
                <td>{{ $total_programs }}</td>
            </tr>
            <tr>
                <td class="label">Total Budget:</td>
                <td>Rp {{ number_format($total_budget, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Summary Statistics</div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="value">{{ $programs_by_status->get('DRAFT', 0) }}
                </div>
                <div class="label">Draft Programs</div>
            </div>
            <div class="stat-card">
                <div class="value">{{ $programs_by_status->get('ACTIVE', 0) }}
                </div>
                <div class="label">Active Programs</div>
            </div>
            <div class="stat-card">
                <div class="value">
                    {{ $programs_by_status->get('COMPLETED', 0) }}</div>
                <div class="label">Completed Programs</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Budget by Classification</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Classification</th>
                    <th class="text-right">Program Count</th>
                    <th class="text-right">Total Budget</th>
                    <th class="text-right">% of Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($budget_by_classification as $classification => $data)
                    <tr>
                        <td>{{ $classification === 'PROGRAM' ? 'Research Program' : 'Non-Program Activity' }}
                        </td>
                        <td class="text-right">{{ $data['count'] }}</td>
                        <td class="text-right">Rp
                            {{ number_format($data['budget'], 0, ',', '.') }}
                        </td>
                        <td class="text-right">
                            {{ $total_budget > 0 ? number_format(($data['budget'] / $total_budget) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <div class="section">
        <div class="section-title">Program List</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Program Name</th>
                    <th>Site</th>
                    <th>Status</th>
                    <th class="text-right">Budget</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($programs as $program)
                    <tr>
                        <td>{{ $program->program_code }}</td>
                        <td>{{ $program->program_name }}</td>
                        <td>{{ $program->site->site_name ?? 'N/A' }}</td>
                        <td>
                            <span
                                class="status-badge status-{{ strtolower($program->status) }}">
                                {{ ucfirst(strtolower($program->status)) }}
                            </span>
                        </td>
                        <td class="text-right">Rp
                            {{ number_format($program->total_budget, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>GreenLedger - Fiscal Year Management System | Page <span
                class="pagenum"></span></p>
    </div>
</body>

</html>
