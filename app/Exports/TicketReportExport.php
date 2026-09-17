<?php

namespace App\Exports;

use App\Models\Ticket;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TicketReportExport implements FromView, ShouldAutoSize, WithStyles
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $tickets = $this->getFilteredTickets();

        return view('admin.reports.export-table', compact('tickets'));
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F3E8FF'],
                ],
            ],
        ];
    }

    public function getFilteredTickets()
    {
        $query = Ticket::with(['user.department', 'category', 'activeAssignment.technician', 'resolution', 'rating']);

        if ($this->request->filled('from')) {
            $query->where('created_at', '>=', $this->request->from . ' 00:00:00');
        }

        if ($this->request->filled('to')) {
            $query->where('created_at', '<=', $this->request->to . ' 23:59:59');
        }

        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        if ($this->request->filled('priority')) {
            $query->where('priority', $this->request->priority);
        }

        if ($this->request->filled('category_id')) {
            $query->where('category_id', $this->request->category_id);
        }

        if ($this->request->filled('technician_id')) {
            $query->whereHas('assignments', fn ($q) => $q->where('technician_id', $this->request->technician_id));
        }

        return $query->latest()->get();
    }
}
