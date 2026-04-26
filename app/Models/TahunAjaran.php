<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajarans';

    protected $fillable = [
        'pondok_id',
        'nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke Pondok
     */
    public function pondok()
    {
        return $this->belongsTo(Pondok::class);
    }

    /**
     * Relasi ke Kelas
     */
    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }

    /**
     * Relasi ke SantriKelas (histori penempatan)
     */
    public function santriKelas()
    {
        return $this->hasMany(SantriKelas::class);
    }

    public function ujians()
    {
        return $this->hasMany(Ujian::class);
    }

    /**
     * Scope untuk tahun ajaran aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk pondok tertentu
     */
    public function scopeForPondok($query, $pondokId)
    {
        return $query->where('pondok_id', $pondokId);
    }

    /**
     * Set tahun ajaran ini sebagai aktif (dan nonaktifkan yang lain di pondok yang sama)
     */
    public function setAsActive()
    {
        // Nonaktifkan semua tahun ajaran di pondok yang sama
        self::where('pondok_id', $this->pondok_id)
            ->where('id', '!=', $this->id)
            ->update(['is_active' => false]);

        // Aktifkan tahun ajaran ini
        $this->update(['is_active' => true]);
    }

    /**
     * Get tahun ajaran aktif untuk pondok tertentu
     */
    public static function getActiveForPondok($pondokId)
    {
        return self::where('pondok_id', $pondokId)
            ->where('is_active', true)
            ->first();
    }
}
