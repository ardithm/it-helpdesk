<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketAssignment;

class TechnicianDashboardController extends Controller
{
    /**
     * Dashboard utama untuk role Teknisi.
     * Menampilkan tiket yang diassign kepada teknisi ini beserta status SLA.
     */
    public function index()
    {
        $user = auth()->user();

        // Tiket yang sedang aktif diassign kepada teknisi ini
        $activeTickets = Ticket::whereHas('activeAssignment', fn ($q) => $q->where('technician_id', $user->id))
            ->with(['user.department', 'category', 'slaPolicy'])
            ->whereNotIn('status', [Ticket::STATUS_RESOLVED, Ticket::STATUS_CLOSED])
            ->orderBy('resolution_deadline')
            ->get();

        $stats = [
            'assigned'    => $activeTickets->where('status', Ticket::STATUS_ASSIGNED)->count(),
            'in_progress' => $activeTickets->where('status', Ticket::STATUS_IN_PROGRESS)->count(),
            'waiting'     => $activeTickets->where('status', Ticket::STATUS_WAITING)->count(),
            'breached'    => $activeTickets->filter(fn ($t) => $t->sla_status === 'breached')->count(),
            'resolved_today' => Ticket::whereHas('assignments', fn ($q) => $q->where('technician_id', $user->id))
                ->where('status', Ticket::STATUS_RESOLVED)
                ->whereDate('resolved_at', today())
                ->count(),
        ];

        // Tiket mendekati deadline (dalam 2 jam)
        $urgentTickets = $activeTickets->filter(fn ($t) => in_array($t->sla_status, ['near_deadline', 'breached']));

        return view('dashboard.technician', compact('stats', 'activeTickets', 'urgentTickets'));
    }
}
