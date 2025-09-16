<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JuzSantri extends Model
{
    /** @use HasFactory<\Database\Factories\JuzSantriFactory> */
    use HasFactory;
    protected $fillable = [
        'santri_id',
        'no_juz',
        'status',
        'tanggal_sah',
    ];

    public function santris()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }
}
