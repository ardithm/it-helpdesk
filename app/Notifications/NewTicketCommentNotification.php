<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewTicketCommentNotification extends Notification
{
    use Queueable;

    public $ticket;
    public $commenterName;
    public $url;

    public function __construct(Ticket $ticket, string $commenterName, string $url)
    {
        $this->ticket = $ticket;
        $this->commenterName = $commenterName;
        $this->url = $url;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'     => 'Balasan Baru',
            'message'   => "{$this->commenterName} membalas tiket {$this->ticket->ticket_number}.",
            'ticket_id' => $this->ticket->id,
            'url'       => $this->url,
            'icon'      => 'message-circle',
            'color'     => 'green'
        ];
    }
}
