<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TicketStatusUpdatedNotification extends Notification
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
            'title'     => 'Update Status Tiket',
            'message'   => "Status tiket {$this->ticket->ticket_number} berubah menjadi " . strtoupper($this->ticket->status) . ".",
            'ticket_id' => $this->ticket->id,
            'url'       => route('user.tickets.show', $this->ticket->id),
            'icon'      => 'refresh-cw',
            'color'     => 'orange'
        ];
    }
}
