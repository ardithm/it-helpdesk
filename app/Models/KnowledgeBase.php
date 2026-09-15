<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'author_id',
        'title',
        'content',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Hanya artikel yang sudah dipublikasikan.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Cari artikel berdasarkan kata kunci di judul atau konten.
     */
    public function scopeSearch($query, string $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
              ->orWhere('content', 'like', "%{$keyword}%");
        });
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * Kategori tiket yang terkait dengan artikel ini.
     */
    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    /**
     * User yang menulis artikel ini.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
