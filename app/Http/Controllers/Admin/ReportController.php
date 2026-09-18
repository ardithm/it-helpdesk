<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TicketReportExport;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketRating;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Halaman utama laporan dengan filter.
     */
    public function index(Request $request)
    {
        $query = $this->buildQuery($request);

        $tickets = (clone $query)->latest()->paginate(20)->withQueryString();

        // ── Summary stats ──────────────────────────────────────────────────────
        $baseQuery = $this->buildQuery($request);
        $summary = [
            'total'      => (clone $baseQuery)->count(),
            'resolved'   => (clone $baseQuery)->where('status', Ticket::STATUS_RESOLVED)->count()
                          + (clone $baseQuery)->where('status', Ticket::STATUS_CLOSED)->count(),
            'breached'   => (clone $baseQuery)->where(function ($q) {
                                $q->where(function ($q1) {
                                    $q1->whereNotNull('resolved_at')
                                       ->whereColumn('resolved_at', '>', 'resolution_deadline');
                                })->orWhere(function ($q2) {
                                    $q2->whereNull('resolved_at')
                                       ->whereNotNull('resolution_deadline')
                                       ->where('resolution_deadline', '<', now());
                                });
                            })->count(),
            'avg_rating' => TicketRating::whereIn(
                'ticket_id',
                (clone $baseQuery)->pluck('id')
            )->avg('rating'),
        ];

        $technicians = User::where('role', 'technician')->where('is_active', true)->orderBy('name')->get();
        $categories  = \App\Models\TicketCategory::active()->orderBy('name')->get();

        // ── Chart Data: Tren Tiket Harian ────────────────────────────────────
        $from = $request->filled('from') ? Carbon::parse($request->from) : now()->subDays(13);
        $to   = $request->filled('to')   ? Carbon::parse($request->to)   : now();
        $diffDays = (int) $from->diffInDays($to) + 1;

        $chartQuery = $this->buildQuery($request);
        $ticketsByDate = (clone $chartQuery)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $trendLabels = [];
        $trendData = [];
        for ($i = 0; $i < $diffDays; $i++) {
            $date = $from->copy()->addDays($i)->format('Y-m-d');
            $trendLabels[] = Carbon::parse($date)->translatedFormat('d M');
            $trendData[] = $ticketsByDate[$date] ?? 0;
        }
        $chartTrend = ['labels' => $trendLabels, 'data' => $trendData];

        // ── Chart Data: Komposisi Prioritas ──────────────────────────────────
        $priorityQuery = $this->buildQuery($request);
        $priorityCounts = (clone $priorityQuery)
            ->selectRaw('priority, COUNT(*) as total')
            ->groupBy('priority')
            ->pluck('total', 'priority')
            ->toArray();

        $chartPriority = [
            'labels' => ['Critical', 'High', 'Medium', 'Low'],
            'data'   => [
                $priorityCounts['critical'] ?? 0,
                $priorityCounts['high'] ?? 0,
                $priorityCounts['medium'] ?? 0,
                $priorityCounts['low'] ?? 0,
            ],
        ];

        return view('admin.reports.index', compact('tickets', 'summary', 'technicians', 'categories', 'chartTrend', 'chartPriority'));
    }

    /**
     * Export laporan ke Excel atau PDF.
     */
    public function export(Request $request, string $format)
    {
        abort_unless(in_array($format, ['pdf', 'excel']), 404);

        $filename = 'laporan-tiket-' . now()->format('Y-m-d_His');

        if ($format === 'excel') {
            return Excel::download(new TicketReportExport($request), $filename . '.xlsx');
        }

        // ── PDF ────────────────────────────────────────────────────────────────
        $query   = $this->buildQuery($request);
        $tickets = (clone $query)->latest()->get();

        $baseQuery = $this->buildQuery($request);
        $summary = [
            'total'      => (clone $baseQuery)->count(),
            'resolved'   => (clone $baseQuery)->where('status', Ticket::STATUS_RESOLVED)->count()
                          + (clone $baseQuery)->where('status', Ticket::STATUS_CLOSED)->count(),
            'breached'   => (clone $baseQuery)->where(function ($q) {
                                $q->where(function ($q1) {
                                    $q1->whereNotNull('resolved_at')
                                       ->whereColumn('resolved_at', '>', 'resolution_deadline');
                                })->orWhere(function ($q2) {
                                    $q2->whereNull('resolved_at')
                                       ->whereNotNull('resolution_deadline')
                                       ->where('resolution_deadline', '<', now());
                                });
                            })->count(),
            'avg_rating' => TicketRating::whereIn(
                'ticket_id',
                (clone $baseQuery)->pluck('id')
            )->avg('rating'),
        ];

        $pdf = Pdf::loadView('admin.reports.export-pdf', compact('tickets', 'summary'))
            ->setPaper('a4', 'landscape');

        return $pdf->download($filename . '.pdf');
    }

    /**
     * Build the filtered query — shared by index and export.
     */
    private function buildQuery(Request $request)
    {
        $query = Ticket::with(['user.department', 'category', 'activeAssignment.technician', 'resolution.technician', 'rating']);

        if ($request->filled('from')) {
            $query->where('created_at', '>=', $request->from . ' 00:00:00');
        }

        if ($request->filled('to')) {
            $query->where('created_at', '<=', $request->to . ' 23:59:59');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('technician_id')) {
            $query->whereHas('assignments', fn ($q) => $q->where('technician_id', $request->technician_id));
        }

        return $query;
    }
}

