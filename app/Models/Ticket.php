<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'category_id',
        'asset_id',
        'type',
        'title',
        'description',
        'priority',
        'status',
        'sla_policy_id',
        'response_deadline',
        'resolution_deadline',
        'first_responded_at',
        'resolved_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'response_deadline'  => 'datetime',
            'resolution_deadline' => 'datetime',
            'first_responded_at' => 'datetime',
            'resolved_at'        => 'datetime',
            'closed_at'          => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Constants — Status & Priority
    // -------------------------------------------------------------------------

    const STATUS_OPEN        = 'open';
    const STATUS_ASSIGNED    = 'assigned';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_WAITING     = 'waiting';
    const STATUS_RESOLVED    = 'resolved';
    const STATUS_CLOSED      = 'closed';

    const PRIORITY_LOW      = 'low';
    const PRIORITY_MEDIUM   = 'medium';
    const PRIORITY_HIGH     = 'high';
    const PRIORITY_CRITICAL = 'critical';

    const TYPE_INCIDENT        = 'incident';
    const TYPE_SERVICE_REQUEST = 'service_request';

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', self::STATUS_ASSIGNED);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeWaiting($query)
    {
        return $query->where('status', self::STATUS_WAITING);
    }

    public function scopeResolved($query)
    {
        return $query->where('status', self::STATUS_RESOLVED);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_CLOSED]);
    }

    /**
     * Tiket yang SLA-nya sudah atau hampir breach.
     */
    public function scopeNearDeadline($query, int $minutesBefore = 60)
    {
        return $query->whereNotIn('status', [self::STATUS_RESOLVED, self::STATUS_CLOSED])
            ->where('resolution_deadline', '<=', now()->addMinutes($minutesBefore))
            ->where('resolution_deadline', '>', now());
    }

    public function scopeSlaBreached($query)
    {
        return $query->whereNotIn('status', [self::STATUS_RESOLVED, self::STATUS_CLOSED])
            ->where('resolution_deadline', '<', now());
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Kembalikan kondisi SLA: on_track | near_deadline | breached.
     */
    public function getSlaStatusAttribute(): string
    {
        if (in_array($this->status, [self::STATUS_RESOLVED, self::STATUS_CLOSED])) {
            return 'completed';
        }

        if (! $this->resolution_deadline) {
            return 'no_sla';
        }

        if ($this->resolution_deadline->isPast()) {
            return 'breached';
        }

        if (now()->diffInMinutes($this->resolution_deadline, false) <= 60) {
            return 'near_deadline';
        }

        return 'on_track';
    }

    /**
     * Generate nomor tiket unik: IT-{YYYY}-{sequence 5 digit}.
     */
    public static function generateTicketNumber(): string
    {
        $year  = now()->format('Y');
        $prefix = "IT-{$year}-";

        $last = static::where('ticket_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('ticket_number');

        $sequence = $last
            ? (int) substr($last, strlen($prefix)) + 1
            : 1;

        return $prefix . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * User yang membuat tiket ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Kategori tiket.
     */
    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    /**
     * Aset IT yang berkaitan.
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * SLA Policy yang diterapkan.
     */
    public function slaPolicy()
    {
        return $this->belongsTo(SlaPolicy::class, 'sla_policy_id');
    }

    /**
     * Riwayat penugasan tiket kepada teknisi.
     */
    public function assignments()
    {
        return $this->hasMany(TicketAssignment::class);
    }

    /**
     * Assignment aktif (belum di-unassign).
     */
    public function activeAssignment()
    {
        return $this->hasOne(TicketAssignment::class)->whereNull('unassigned_at');
    }

    /**
     * Teknisi yang sedang menangani tiket (via assignment aktif).
     */
    public function technician()
    {
        return $this->hasOneThrough(
            User::class,
            TicketAssignment::class,
            'ticket_id',
            'id',
            'id',
            'technician_id'
        )->whereNull('ticket_assignments.unassigned_at');
    }

    /**
     * Semua komentar pada tiket.
     */
    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    /**
     * Komentar publik (bukan internal).
     */
    public function publicComments()
    {
        return $this->hasMany(TicketComment::class)->where('is_internal', false);
    }

    /**
     * File attachment tiket.
     */
    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    /**
     * Histori perubahan status/aktivitas tiket.
     */
    public function histories()
    {
        return $this->hasMany(TicketHistory::class);
    }

    /**
     * Data diagnosis dan resolusi tiket.
     */
    public function resolution()
    {
        return $this->hasOne(TicketResolution::class);
    }

    /**
     * Rating pelayanan dari user.
     */
    public function rating()
    {
        return $this->hasOne(TicketRating::class);
    }
}
