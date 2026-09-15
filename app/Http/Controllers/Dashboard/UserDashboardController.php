<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    /**
     * Dashboard utama untuk role User.
     * Menampilkan ringkasan tiket milik sendiri berdasarkan status.
     */
    public function index()
    {
        $user = auth()->user();

        $stats = [
            'open'        => Ticket::where('user_id', $user->id)->where('status', Ticket::STATUS_OPEN)->count(),
            'in_progress' => Ticket::where('user_id', $user->id)->whereIn('status', [Ticket::STATUS_ASSIGNED, Ticket::STATUS_IN_PROGRESS, Ticket::STATUS_WAITING])->count(),
            'resolved'    => Ticket::where('user_id', $user->id)->where('status', Ticket::STATUS_RESOLVED)->count(),
            'closed'      => Ticket::where('user_id', $user->id)->where('status', Ticket::STATUS_CLOSED)->count(),
        ];

        $recentTickets = Ticket::where('user_id', $user->id)
            ->with(['category', 'activeAssignment.technician'])
            ->latest()
            ->take(5)
            ->get();

        $pendingRating = Ticket::where('user_id', $user->id)
            ->where('status', Ticket::STATUS_RESOLVED)
            ->whereDoesntHave('rating')
            ->count();

        return view('dashboard.user', compact('stats', 'recentTickets', 'pendingRating'));
    }
}
