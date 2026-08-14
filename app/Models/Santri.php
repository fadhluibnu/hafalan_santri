<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    /** @use HasFactory<\Database\Factories\SantriFactory> */
    use HasFactory;

    protected $fillable = [
        'nis',
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
        'status_santri',
        'tanggal_masuk_pondok',
        'tanggal_lulus',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk_pondok' => 'date',
        'tanggal_lulus' => 'date',
    ];

    /**
     * Generate NIS (Nomor Induk Santri) otomatis.
     * Format: YYYYPPPSSSS
     * - YYYY: Tahun (4 digit)
     * - PPP: Kode Pondok (3 digit)
     * - SSSS: Urutan (4 digit)
     * Contoh: 20240010001
     */
    public static function generateNis($pondokId)
    {
        $year = date('Y');
        $pondokCode = str_pad($pondokId, 3, '0', STR_PAD_LEFT);
        $prefix = $year . $pondokCode;

        // Cari urutan terakhir untuk pondok ini di tahun ini
        $lastNis = self::where('pondok_id', $pondokId)
            ->where('nis', 'like', "{$prefix}%")
            ->orderBy('nis', 'desc')
            ->value('nis');

        if ($lastNis) {
            $lastNumber = (int) substr($lastNis, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Relasi ke OrangTua.
     */
    public function orangTuas()
    {
        return $this->hasMany(OrangTua::class, 'santri_id');
    }

    /**
     * Relasi ke KesehatanSantri.
     */
    public function kesehatanSantri()
    {
        return $this->hasOne(KesehatanSantri::class, 'santri_id');
    }

    public function pondok()
    {
        return $this->belongsTo(Pondok::class);
    }
    
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function jus()
    {
        return $this->hasMany(JuzSantri::class, 'santri_id');
    }

    /**
     * Relasi ke SantriKelas (histori penempatan kelas)
     */
    public function santriKelas()
    {
        return $this->hasMany(SantriKelas::class);
    }

    public function ujianNilais()
    {
        return $this->hasMany(UjianNilai::class);
    }

    /**
     * Get penempatan kelas aktif saat ini
     */
    public function kelasAktif()
    {
        return $this->santriKelas()
            ->where('status', 'aktif')
            ->with('kelas')
            ->orderByDesc('tahun_ajaran_id')
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Scope untuk santri aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status_santri', 'aktif');
    }

    /**
     * Scope untuk alumni
     */
    public function scopeAlumni($query)
    {
        return $query->whereIn('status_santri', ['lulus', 'alumni']);
    }
}
