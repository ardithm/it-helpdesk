<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketHistory extends Model
{
    use HasFactory;

    /**
     * Tabel ini hanya memiliki created_at, tidak updated_at.
     */
    public $timestamps = false;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'action',
        'old_value',
        'new_value',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * Tiket yang memiliki history ini.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * User yang melakukan aksi.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
