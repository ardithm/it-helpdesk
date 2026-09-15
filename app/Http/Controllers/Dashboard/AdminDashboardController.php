<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Models\TicketRating;

class AdminDashboardController extends Controller
{
    /**
     * Dashboard utama untuk role Admin.
     * Menampilkan KPI lengkap, SLA compliance, performa teknisi, dan distribusi tiket.
     */
    public function index()
    {
        // ── Statistik Tiket ────────────────────────────────────────────────────
        $totalTickets = Ticket::count();
        $stats = [
            'total'       => $totalTickets,
            'open'        => Ticket::where('status', Ticket::STATUS_OPEN)->count(),
            'in_progress' => Ticket::whereIn('status', [Ticket::STATUS_ASSIGNED, Ticket::STATUS_IN_PROGRESS, Ticket::STATUS_WAITING])->count(),
            'resolved'    => Ticket::where('status', Ticket::STATUS_RESOLVED)->count(),
            'closed'      => Ticket::where('status', Ticket::STATUS_CLOSED)->count(),
            'breached'    => Ticket::slaBreached()->count(),
        ];

        // ── SLA Compliance (tiket closed/resolved bulan ini) ──────────────────
        $thisMonth = now()->startOfMonth();
        $resolvedThisMonth = Ticket::whereIn('status', [Ticket::STATUS_RESOLVED, Ticket::STATUS_CLOSED])
            ->where('updated_at', '>=', $thisMonth)
            ->count();

        $onTimeThisMonth = Ticket::whereIn('status', [Ticket::STATUS_RESOLVED, Ticket::STATUS_CLOSED])
            ->where('updated_at', '>=', $thisMonth)
            ->whereColumn('resolved_at', '<=', 'resolution_deadline')
            ->whereNotNull('resolved_at')
            ->count();

        $slaCompliance = $resolvedThisMonth > 0
            ? round(($onTimeThisMonth / $resolvedThisMonth) * 100, 1)
            : 0;

        // ── Customer Satisfaction ─────────────────────────────────────────────
        $avgRating = TicketRating::avg('rating');

        // ── Distribusi per Prioritas ───────────────────────────────────────────
        $byPriority = Ticket::selectRaw('priority, COUNT(*) as total')
            ->groupBy('priority')
            ->pluck('total', 'priority');

        // ── Performa Teknisi (Top 5 berdasarkan tiket resolved) ───────────────
        $technicianPerformance = User::where('role', 'technician')
            ->where('is_active', true)
            ->withCount([
                'assignments as total_handled' => fn ($q) => $q->whereHas('ticket', fn ($t) => $t->whereIn('status', [Ticket::STATUS_RESOLVED, Ticket::STATUS_CLOSED])),
            ])
            ->orderByDesc('total_handled')
            ->take(5)
            ->get();

        // ── Tiket Terbaru ──────────────────────────────────────────────────────
        $recentTickets = Ticket::with(['user.department', 'category', 'activeAssignment.technician'])
            ->latest()
            ->take(8)
            ->get();

        return view('dashboard.admin', compact(
            'stats',
            'slaCompliance',
            'avgRating',
            'byPriority',
            'technicianPerformance',
            'recentTickets'
        ));
    }
}
