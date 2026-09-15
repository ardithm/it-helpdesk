<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlaPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'priority',
        'response_time_minutes',
        'resolution_time_minutes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'response_time_minutes'  => 'integer',
            'resolution_time_minutes' => 'integer',
            'is_active'              => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Hanya SLA policy yang aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Cari policy berdasarkan priority.
     */
    public function scopeForPriority($query, string $priority)
    {
        return $query->where('priority', $priority)->where('is_active', true);
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * Tiket yang menggunakan SLA policy ini.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'sla_policy_id');
    }
}
