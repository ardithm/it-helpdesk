<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_code',
        'category',
        'brand',
        'model',
        'serial_number',
        'user_id',
        'department_id',
        'purchase_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
        ];
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Aset dengan status aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * User pemilik/pengguna aset ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Department tempat aset ini terdaftar.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Tiket yang berkaitan dengan aset ini.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Histori penanganan/perubahan aset ini.
     */
    public function histories()
    {
        return $this->hasMany(AssetHistory::class);
    }
}
