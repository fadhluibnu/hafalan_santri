<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    /** @use HasFactory<\Database\Factories\SantriFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pondok_id',
        'kelas_id',
        'nama',
        'panggilan',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'status_mukim',
        'kondisi',
        'warga_negara',
        'kode_pos',
        'alamat',
        'anak_ke',
        'jumlah_saudara',
        'status_anak',
        'saudara_kandung',
        'saudara_tiri',
        'jarak_pondok',
        'telpon',
        'handphone',
        'email',
        'hobi',
        'foto',
    ];

    /**
     * Relasi ke User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke OrangTua.
     */
    public function orangTuas()
    {
        return $this->hasMany(OrangTua::class);
    }

    /**
     * Relasi ke KesehatanSantri.
     */
    public function kesehatanSantri()
    {
        return $this->hasOne(KesehatanSantri::class);
    }
}
