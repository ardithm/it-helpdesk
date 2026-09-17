<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Tiket ITDesk</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #334155; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #8B5CF6; }
        .header h1 { font-size: 18px; color: #1E293B; margin-bottom: 4px; }
        .header p { font-size: 11px; color: #64748B; }
        .summary { display: flex; margin-bottom: 16px; }
        .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .summary-table td { padding: 8px 12px; border: 1px solid #E2E8F0; text-align: center; }
        .summary-table .label { font-size: 9px; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.5px; }
        .summary-table .value { font-size: 16px; font-weight: 700; color: #1E293B; }
        table.data { width: 100%; border-collapse: collapse; font-size: 9px; }
        table.data th { background: #F3E8FF; color: #6D28D9; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; padding: 7px 6px; text-align: left; border-bottom: 2px solid #DDD6FE; }
        table.data td { padding: 6px; border-bottom: 1px solid #F1F5F9; vertical-align: top; }
        table.data tr:nth-child(even) td { background: #FAFAFA; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 8px; font-weight: 600; }
        .badge-critical { background: #FEE2E2; color: #DC2626; }
        .badge-high { background: #FFEDD5; color: #EA580C; }
        .badge-medium { background: #FEF3C7; color: #D97706; }
        .badge-low { background: #F0FDF4; color: #16A34A; }
        .badge-breached { background: #FEE2E2; color: #DC2626; }
        .badge-ontime { background: #F0FDF4; color: #16A34A; }
        .footer { margin-top: 20px; text-align: center; font-size: 9px; color: #94A3B8; padding-top: 10px; border-top: 1px solid #E2E8F0; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Tiket — ITDesk</h1>
        <p>
            Periode: {{ request('from', 'Semua') }} s/d {{ request('to', 'Semua') }}
            &nbsp;|&nbsp; Dicetak: {{ now()->format('d M Y, H:i') }}
        </p>
    </div>

    {{-- Summary --}}
    <table class="summary-table">
        <tr>
            <td>
                <div class="label">Total Tiket</div>
                <div class="value">{{ $summary['total'] }}</div>
            </td>
            <td>
                <div class="label">Terselesaikan</div>
                <div class="value" style="color: #16A34A;">{{ $summary['resolved'] }}</div>
            </td>
            <td>
                <div class="label">SLA Breach</div>
                <div class="value" style="color: #DC2626;">{{ $summary['breached'] }}</div>
            </td>
            <td>
                <div class="label">Rata-rata Rating</div>
                <div class="value" style="color: #D97706;">{{ $summary['avg_rating'] ? number_format($summary['avg_rating'], 1) . '/5' : '-' }}</div>
            </td>
        </tr>
    </table>

    {{-- Data Table --}}
    <table class="data">
        <thead>
            <tr>
                <th>No. Tiket</th>
                <th>Judul</th>
                <th>Pelapor</th>
                <th>Kategori</th>
                <th>Prioritas</th>
                <th>Status</th>
                <th>Teknisi</th>
                <th>Dibuat</th>
                <th>Resolusi</th>
                <th>SLA</th>
                <th>Rating</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tickets as $ticket)
                @php
                    $slaStatus = 'On Track';
                    if ($ticket->resolved_at && $ticket->resolution_deadline) {
                        $slaStatus = $ticket->resolved_at->lte($ticket->resolution_deadline) ? 'On Time' : 'Breached';
                    } elseif ($ticket->resolution_deadline && now()->gt($ticket->resolution_deadline)) {
                        $slaStatus = 'Breached';
                    }
                @endphp
                <tr>
                    <td>{{ $ticket->ticket_number }}</td>
                    <td style="max-width: 140px;">{{ Str::limit($ticket->title, 40) }}</td>
                    <td>{{ $ticket->user->name }}</td>
                    <td>{{ $ticket->category?->name ?? '-' }}</td>
                    <td><span class="badge badge-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span></td>
                    <td>{{ str_replace('_', ' ', ucfirst($ticket->status)) }}</td>
                    <td>{{ $ticket->activeAssignment?->technician?->name ?? '-' }}</td>
                    <td>{{ $ticket->created_at->format('d/m/Y') }}</td>
                    <td>{{ $ticket->resolved_at?->format('d/m/Y') ?? '-' }}</td>
                    <td><span class="badge {{ $slaStatus === 'Breached' ? 'badge-breached' : 'badge-ontime' }}">{{ $slaStatus }}</span></td>
                    <td>{{ $ticket->rating?->rating ? $ticket->rating->rating . '/5' : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        ITDesk &mdash; Helpdesk & IT Ticketing System | Total {{ $tickets->count() }} tiket dalam laporan ini
    </div>
</body>
</html>
