<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use App\Models\TicketRating;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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

        // ── Chart Data: Tren Tiket 7 Hari Terakhir ───────────────────────────
        $trendDays = 7;
        $trendLabels = [];
        $trendData = [];
        $startDate = now()->subDays($trendDays - 1)->startOfDay();

        $ticketsByDate = Ticket::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        for ($i = 0; $i < $trendDays; $i++) {
            $date = now()->subDays($trendDays - 1 - $i)->format('Y-m-d');
            $trendLabels[] = Carbon::parse($date)->translatedFormat('d M');
            $trendData[] = $ticketsByDate[$date] ?? 0;
        }

        $chartTrend = ['labels' => $trendLabels, 'data' => $trendData];

        // ── Chart Data: Distribusi Status ─────────────────────────────────────
        $statusCounts = Ticket::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $chartStatus = [
            'labels' => ['Open', 'Assigned', 'In Progress', 'Waiting', 'Resolved', 'Closed'],
            'data'   => [
                $statusCounts['open'] ?? 0,
                $statusCounts['assigned'] ?? 0,
                $statusCounts['in_progress'] ?? 0,
                $statusCounts['waiting'] ?? 0,
                $statusCounts['resolved'] ?? 0,
                $statusCounts['closed'] ?? 0,
            ],
        ];

        // ── Chart Data: Distribusi Kategori ───────────────────────────────────
        $categoryData = Ticket::join('ticket_categories', 'tickets.category_id', '=', 'ticket_categories.id')
            ->selectRaw('ticket_categories.name, COUNT(*) as total')
            ->groupBy('ticket_categories.name')
            ->orderByDesc('total')
            ->take(6)
            ->pluck('total', 'name')
            ->toArray();

        $chartCategory = [
            'labels' => array_keys($categoryData),
            'data'   => array_values($categoryData),
        ];

        return view('dashboard.admin', compact(
            'stats',
            'slaCompliance',
            'avgRating',
            'byPriority',
            'technicianPerformance',
            'recentTickets',
            'chartTrend',
            'chartStatus',
            'chartCategory'
        ));
    }
}
