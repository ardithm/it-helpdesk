<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'comment',
        'is_internal',
    ];

    protected function casts(): array
    {
        return [
            'is_internal' => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Hanya komentar yang dapat dilihat publik (non-internal).
     */
    public function scopePublic($query)
    {
        return $query->where('is_internal', false);
    }

    /**
     * Hanya komentar internal (khusus tim IT).
     */
    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * Tiket yang memiliki komentar ini.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * User yang menulis komentar.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
