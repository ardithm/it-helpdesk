<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetHistory extends Model
{
    use HasFactory;

    /**
     * Tabel ini hanya memiliki created_at, tidak updated_at.
     */
    public $timestamps = false;

    protected $fillable = [
        'asset_id',
        'ticket_id',
        'action',
        'description',
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
     * Aset yang memiliki history ini.
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Tiket yang berkaitan dengan history aset ini (nullable).
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
