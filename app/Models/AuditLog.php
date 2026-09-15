<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    /**
     * Tabel ini hanya memiliki created_at, tidak updated_at.
     */
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'ip_address',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Helpers — Static Factory
    // -------------------------------------------------------------------------

    /**
     * Catat aktivitas penting ke audit log.
     *
     * @param  string       $action          Jenis aksi, mis. 'ticket.status_changed'
     * @param  Model|null   $auditable       Entitas yang berubah
     * @param  array|null   $oldValues       Nilai sebelum perubahan
     * @param  array|null   $newValues       Nilai setelah perubahan
     * @param  int|null     $userId          ID user pelaku (null jika sistem)
     */
    public static function record(
        string $action,
        ?Model $auditable = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $userId = null
    ): static {
        return static::create([
            'user_id'        => $userId ?? auth()->id(),
            'action'         => $action,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id'   => $auditable?->getKey(),
            'old_values'     => $oldValues,
            'new_values'     => $newValues,
            'ip_address'     => request()->ip() ?? '0.0.0.0',
            'created_at'     => now(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * User yang melakukan aktivitas.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Entitas polymorphic yang di-audit (mis. Ticket, User, Asset).
     */
    public function auditable()
    {
        return $this->morphTo();
    }
}
