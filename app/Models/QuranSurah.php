<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuranSurah extends Model
{
    use HasFactory;

    protected $table = 'quran_surahs';

    protected $fillable = [
        'name',
        'jumlah_ayat',
    ];

    // Relationships (if needed for future use)
    public function dariHafalans()
    {
        return $this->hasMany(Hafalan::class, 'dari_surat');
    }

    public function sampaiHafalans()
    {
        return $this->hasMany(Hafalan::class, 'sampai_surat');
    }
}
