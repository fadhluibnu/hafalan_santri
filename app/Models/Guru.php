<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    /** @use HasFactory<\Database\Factories\GuruFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pondok_id',
        'nip',
        'nama',
        'gelar_awal',
        'gelar_akhir',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'status_menikah',
        'alamat',
        'no_identitas',
        'no_telpon',
        'no_handphone',
        'email',
        'tanggal_kerja',
        'non_aktif',
        'keterangan',
    ];

    /**
     * Relasi ke User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
