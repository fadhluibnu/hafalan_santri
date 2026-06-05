<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SantriKelas extends Model
{
    use HasFactory;

    protected $table = 'santri_kelas';

    protected $fillable = [
        'santri_id',
        'kelas_id',
        'tahun_ajaran_id',
        'status',
        'tanggal_masuk',
        'tanggal_keluar',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
    ];

    /**
     * Relasi ke Santri
     */
    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    /**
     * Relasi ke Kelas
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Relasi ke TahunAjaran
     */
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    /**
     * Scope untuk status aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope untuk tahun ajaran tertentu
     */
    public function scopeForTahunAjaran($query, $tahunAjaranId)
    {
        return $query->where('tahun_ajaran_id', $tahunAjaranId);
    }

    /**
     * Get penempatan aktif untuk santri
     */
    public static function getCurrentForSantri($santriId)
    {
        return self::where('santri_id', $santriId)
            ->where('status', 'aktif')
            ->with(['kelas', 'tahunAjaran'])
            ->orderByDesc('tahun_ajaran_id')
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id')
            ->first();
    }
}
