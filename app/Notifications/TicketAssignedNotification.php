<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TicketAssignedNotification extends Notification
{
    use Queueable;

    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'     => 'Tugas Baru',
            'message'   => "Anda ditugaskan menangani tiket {$this->ticket->ticket_number}.",
            'ticket_id' => $this->ticket->id,
            'url'       => route('technician.tickets.show', $this->ticket->id),
            'icon'      => 'user-check',
            'color'     => 'blue'
        ];
    }
}
