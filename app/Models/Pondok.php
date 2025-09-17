<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pondok extends Model
{
    /** @use HasFactory<\Database\Factories\PondokFactory> */
    use HasFactory;

    protected $fillable = [
        'nama',
        'alamat',
        'telepon',
        'email',
        'website',
        'logo',
        'deskripsi',
        'tahun_berdiri'
    ];
    
    /**
     * Get the admin cabangs for the pondok.
     */
    public function adminCabangs(): HasMany
    {
        return $this->hasMany(AdminCabang::class);
    }

    public function gurus(): HasMany
    {
        return $this->hasMany(Guru::class);
    }

    public function santris(): HasMany
    {
        return $this->hasMany(Santri::class);
    }

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class);
    }
}
