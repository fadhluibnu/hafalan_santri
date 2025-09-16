<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KesehatanSantri extends Model
{
    /** @use HasFactory<\Database\Factories\KesehatanSantriFactory> */
    use HasFactory;

    protected $fillable = [
        'santri_id',
        'golongan_darah',
        'berat_badan',
        'tinggi_badan',
        'riwayat_penyakit',
    ];

    /**
     * Relasi ke Santri.
     */
    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }
}
