<table>
    <thead>
        <tr>
            <th>No. Tiket</th>
            <th>Judul</th>
            <th>Pelapor</th>
            <th>Departemen</th>
            <th>Kategori</th>
            <th>Tipe</th>
            <th>Prioritas</th>
            <th>Status</th>
            <th>Teknisi</th>
            <th>Tanggal Dibuat</th>
            <th>Tanggal Resolusi</th>
            <th>SLA Deadline</th>
            <th>SLA Status</th>
            <th>Rating</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tickets as $ticket)
            <tr>
                <td>{{ $ticket->ticket_number }}</td>
                <td>{{ $ticket->title }}</td>
                <td>{{ $ticket->user->name }}</td>
                <td>{{ $ticket->user->department?->name ?? '-' }}</td>
                <td>{{ $ticket->category?->name ?? '-' }}</td>
                <td>{{ $ticket->type === 'incident' ? 'Incident' : 'Service Request' }}</td>
                <td>{{ ucfirst($ticket->priority) }}</td>
                <td>{{ str_replace('_', ' ', ucfirst($ticket->status)) }}</td>
                <td>{{ $ticket->activeAssignment?->technician?->name ?? 'Belum ada' }}</td>
                <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $ticket->resolved_at?->format('d/m/Y H:i') ?? '-' }}</td>
                <td>{{ $ticket->resolution_deadline?->format('d/m/Y H:i') ?? '-' }}</td>
                <td>{{ $ticket->resolved_at && $ticket->resolution_deadline ? ($ticket->resolved_at->lte($ticket->resolution_deadline) ? 'On Time' : 'Breached') : ($ticket->resolution_deadline && now()->gt($ticket->resolution_deadline) ? 'Breached' : 'On Track') }}</td>
                <td>{{ $ticket->rating?->rating ? $ticket->rating->rating . '/5' : '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
