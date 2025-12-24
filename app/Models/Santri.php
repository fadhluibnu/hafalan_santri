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
}
