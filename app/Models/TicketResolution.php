<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketResolution extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'technician_id',
        'diagnosis',
        'action_taken',
        'sparepart',
        'resolution',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * Tiket yang diselesaikan.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Teknisi yang menyelesaikan tiket.
     */
    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
