<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    use HasFactory;

    protected $fillable = [
        'pondok_id',
        'tahun_ajaran_id',
        'kelas_id',
        'ustadz_id',
        'skema_penilaian_id',
        'nama',
        'tanggal_ujian',
        'skema_snapshot',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_ujian' => 'date',
        'skema_snapshot' => 'array',
    ];

    public function pondok()
    {
        return $this->belongsTo(Pondok::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function ustadz()
    {
        return $this->belongsTo(Ustadz::class);
    }

    public function skemaPenilaian()
    {
        return $this->belongsTo(SkemaPenilaian::class);
    }

    public function nilaiSantri()
    {
        return $this->hasMany(UjianNilai::class)->with('santri');
    }
}
