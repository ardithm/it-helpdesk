<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TicketCreatedNotification extends Notification
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
            'title'     => 'Tiket Baru Masuk',
            'message'   => "Tiket {$this->ticket->ticket_number} dilaporkan oleh {$this->ticket->user->name}.",
            'ticket_id' => $this->ticket->id,
            'url'       => route('helpdesk.tickets.show', $this->ticket->id),
            'icon'      => 'plus-circle',
            'color'     => 'purple'
        ];
    }
}
