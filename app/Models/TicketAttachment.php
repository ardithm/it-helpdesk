<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TicketAttachment extends Model
{
    use HasFactory;

    /**
     * Tabel ini hanya memiliki created_at, tidak updated_at.
     */
    public $timestamps = false;

    protected $fillable = [
        'ticket_id',
        'uploaded_by',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'file_size'  => 'integer',
            'created_at' => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Kembalikan URL publik atau signed URL untuk mengakses file.
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Kembalikan ukuran file dalam format human-readable.
     */
    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }

    /**
     * Cek apakah file adalah gambar.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->file_type, 'image/');
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * Tiket yang memiliki attachment ini.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * User yang mengupload file.
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
