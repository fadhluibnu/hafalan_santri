<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkemaPenilaianItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'skema_penilaian_id',
        'nama',
        'singkatan',
        'label',
        'urutan',
        'batas_bawah',
        'batas_atas',
    ];

    public function skemaPenilaian()
    {
        return $this->belongsTo(SkemaPenilaian::class);
    }
}
