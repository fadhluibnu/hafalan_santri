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
        'tahun_ajaran_id',
        'nama',
        'wali_kelas_id',
        'tingkat',
        'kapasitas',
        'keterangan',
        'status',
    ];

    public function pondok()
    {
        return $this->belongsTo(Pondok::class, 'pondok_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function waliKelas()
    {
        return $this->belongsTo(Ustadz::class, 'wali_kelas_id');
    }

    public function santris()
    {
        return $this->hasMany(Santri::class, 'kelas_id');
    }

    /**
     * Get santri melalui pivot table santri_kelas
     */
    public function santriKelas()
    {
        return $this->hasMany(SantriKelas::class);
    }

    /**
     * Get santri aktif di tahun ajaran aktif
     */
    public function santriAktif()
    {
        return $this->santriKelas()->where('status', 'aktif');
    }

    public function ujians()
    {
        return $this->hasMany(Ujian::class);
    }
}
