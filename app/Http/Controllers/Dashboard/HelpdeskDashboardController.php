<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;

class HelpdeskDashboardController extends Controller
{
    /**
     * Dashboard utama untuk role Helpdesk.
     * Menampilkan semua tiket aktif, SLA summary, dan tiket yang perlu perhatian.
     */
    public function index()
    {
        $stats = [
            'open'        => Ticket::where('status', Ticket::STATUS_OPEN)->count(),
            'assigned'    => Ticket::where('status', Ticket::STATUS_ASSIGNED)->count(),
            'in_progress' => Ticket::where('status', Ticket::STATUS_IN_PROGRESS)->count(),
            'waiting'     => Ticket::where('status', Ticket::STATUS_WAITING)->count(),
            'resolved'    => Ticket::where('status', Ticket::STATUS_RESOLVED)->count(),
            'closed'      => Ticket::where('status', Ticket::STATUS_CLOSED)->count(),
            'breached'    => Ticket::slaBreached()->count(),
        ];

        // Tiket open yang belum diassign — butuh tindakan segera
        $pendingTickets = Ticket::where('status', Ticket::STATUS_OPEN)
            ->with(['user.department', 'category'])
            ->latest()
            ->take(8)
            ->get();

        // Tiket mendekati atau melewati SLA deadline
        $urgentTickets = Ticket::nearDeadline(120)
            ->with(['user', 'category', 'activeAssignment.technician'])
            ->orderBy('resolution_deadline')
            ->take(5)
            ->get();

        // Daftar teknisi aktif untuk keperluan assignment
        $technicians = User::where('role', 'technician')
            ->where('is_active', true)
            ->withCount(['assignments as active_tickets' => fn ($q) => $q->whereNull('unassigned_at')])
            ->orderBy('name')
            ->get();

        return view('dashboard.helpdesk', compact('stats', 'pendingTickets', 'urgentTickets', 'technicians'));
    }
}
