<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    /** @use HasFactory<\Database\Factories\KelasFactory> */
    use HasFactory;

    protected $fillable = [
        'pondok_id',
        'nama',
        'wali_kelas_id',
        'keterangan',
        'status',
    ];

    public function pondok()
    {
        return $this->belongsTo(Pondok::class, 'pondok_id');
    }

    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }
}
