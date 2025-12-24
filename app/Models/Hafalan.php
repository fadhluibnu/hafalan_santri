<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hafalan extends Model
{
    /** @use HasFactory<\Database\Factories\HafalanFactory> */
    use HasFactory;
    
    protected $fillable = [
        'santri_id',
        'ustadz_id',
        'kelas_id',
        'tanggal_setor',
        'juz',
        'dari_surat',
        'dari_ayat',
        'sampai_surat',
        'sampai_ayat',
        'kategori',
        'nilai',
        'catatan',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    public function ustadz()
    {
        return $this->belongsTo(Ustadz::class, 'ustadz_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function dariSurah()
    {
        return $this->belongsTo(QuranSurah::class, 'dari_surat');
    }   

    public function sampaiSurah()
    {
        return $this->belongsTo(QuranSurah::class, 'sampai_surat');
    }

}

