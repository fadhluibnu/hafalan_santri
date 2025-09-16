<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrangTua extends Model
{
    /** @use HasFactory<\Database\Factories\OrangTuaFactory> */
    use HasFactory;

    protected $fillable = [
        'santri_id',
        'tipe',
        'nama',
        'status',
        'status_hubungan',
        'tempat_lahir',
        'tanggal_lahir',
        'pendidikan',
        'pekerjaan',
        'penghasilan',
        'email',
        'handphone',
        'alamat',
    ];

    /**
     * Relasi ke Santri.
     */
    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }
}
