<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UjianNilai extends Model
{
    use HasFactory;

    protected $fillable = [
        'ujian_id',
        'santri_id',
        'nilai_angka',
        'nilai_label',
        'catatan',
    ];

    protected $casts = [
        'nilai_angka' => 'float',
    ];

    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }
}
