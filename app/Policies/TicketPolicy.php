<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * User hanya bisa lihat tiket miliknya sendiri.
     * Admin, Helpdesk, Technician bisa lihat semua.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        if (in_array($user->role, ['admin', 'helpdesk'])) {
            return true;
        }

        if ($user->role === 'technician') {
            return $ticket->activeAssignment?->technician_id === $user->id
                || $ticket->assignments()->where('technician_id', $user->id)->exists();
        }

        // User biasa hanya bisa lihat tiket miliknya
        return $ticket->user_id === $user->id;
    }

    /**
     * Hanya role User yang bisa membuat tiket baru.
     */
    public function create(User $user): bool
    {
        return $user->role === 'user';
    }
}
