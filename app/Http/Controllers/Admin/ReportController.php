<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Halaman utama laporan dengan filter.
     */
    public function index(Request $request)
    {
        $query = Ticket::with(['user.department', 'category', 'resolution.technician', 'rating']);

        // ── Filter ─────────────────────────────────────────────────────────────
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

        $tickets = $query->latest()->paginate(20)->withQueryString();

        // ── Summary stats ──────────────────────────────────────────────────────
        $summary = [
            'total'    => $query->toBase()->count(),
            'resolved' => (clone $query)->where('status', Ticket::STATUS_RESOLVED)->toBase()->count(),
            'breached' => (clone $query)->whereNotNull('resolved_at')->whereColumn('resolved_at', '>', 'resolution_deadline')->toBase()->count(),
            'avg_rating' => \App\Models\TicketRating::whereIn(
                'ticket_id',
                $query->toBase()->pluck('id')
            )->avg('rating'),
        ];

        $technicians = User::where('role', 'technician')->where('is_active', true)->orderBy('name')->get();
        $categories  = \App\Models\TicketCategory::active()->orderBy('name')->get();

        return view('admin.reports.index', compact('tickets', 'summary', 'technicians', 'categories'));
    }

    /**
     * Export laporan ke format tertentu (placeholder untuk maatwebsite/excel atau dompdf).
     */
    public function export(Request $request, string $format)
    {
        abort_unless(in_array($format, ['pdf', 'excel']), 404);

        // TODO: Implementasi export menggunakan Laravel Excel / dompdf
        return back()->with('info', "Export {$format} akan segera tersedia.");
    }
}
