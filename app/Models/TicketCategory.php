<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Hanya kategori yang aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * Tiket yang berada dalam kategori ini.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'category_id');
    }

    /**
     * Artikel Knowledge Base yang termasuk kategori ini.
     */
    public function knowledgeBases()
    {
        return $this->hasMany(KnowledgeBase::class, 'category_id');
    }
}
