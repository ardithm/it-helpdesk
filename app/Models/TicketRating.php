<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketRating extends Model
{
    use HasFactory;

    /**
     * Tabel ini hanya memiliki created_at, tidak updated_at.
     */
    public $timestamps = false;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'rating',
        'comment',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'rating'     => 'integer',
            'created_at' => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Kembalikan label teks dari nilai rating.
     */
    public function getRatingLabelAttribute(): string
    {
        return match ($this->rating) {
            1 => 'Sangat Buruk',
            2 => 'Buruk',
            3 => 'Cukup',
            4 => 'Baik',
            5 => 'Sangat Baik',
            default => '-',
        };
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * Tiket yang dinilai.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * User yang memberikan rating.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
