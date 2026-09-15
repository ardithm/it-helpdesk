<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password'  => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Role Helpers
    // -------------------------------------------------------------------------

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isHelpdesk(): bool
    {
        return $this->role === 'helpdesk';
    }

    public function isTechnician(): bool
    {
        return $this->role === 'technician';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * Department milik user ini.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Tiket yang dibuat oleh user ini.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Assignment tiket kepada teknisi ini.
     */
    public function assignments()
    {
        return $this->hasMany(TicketAssignment::class, 'technician_id');
    }

    /**
     * Komentar yang ditulis oleh user ini.
     */
    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    /**
     * Rating yang diberikan oleh user ini.
     */
    public function ratings()
    {
        return $this->hasMany(TicketRating::class);
    }

    /**
     * Artikel knowledge base yang ditulis oleh user ini.
     */
    public function knowledgeBases()
    {
        return $this->hasMany(KnowledgeBase::class, 'author_id');
    }

    /**
     * Audit log aktivitas user ini.
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
