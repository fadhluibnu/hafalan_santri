<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminCabang extends Model
{
    /** @use HasFactory<\Database\Factories\AdminCabangFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pondok_id',
        'name',
        'phone',
        'jabatan',
    ];

    /**
     * Relasi ke User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Relasi ke Pondok.
     */
    public function pondok()
    {
        return $this->belongsTo(Pondok::class);
    }
}
