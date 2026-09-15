<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketAssignment extends Model
{
    use HasFactory;

    /**
     * Tabel ini hanya memiliki created_at, tidak updated_at.
     */
    public $timestamps = false;

    protected $fillable = [
        'ticket_id',
        'technician_id',
        'assigned_by',
        'assigned_at',
        'unassigned_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at'   => 'datetime',
            'unassigned_at' => 'datetime',
            'created_at'    => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Hanya assignment yang masih aktif (belum di-unassign).
     */
    public function scopeActive($query)
    {
        return $query->whereNull('unassigned_at');
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * Tiket yang ditugaskan.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Teknisi yang menerima assignment.
     */
    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    /**
     * Helpdesk/user yang melakukan assignment.
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
