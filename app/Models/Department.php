<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * User yang tergabung dalam department ini.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Aset IT yang terdaftar pada department ini.
     */
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
