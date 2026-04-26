<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkemaPenilaian extends Model
{
    use HasFactory;

    protected $fillable = [
        'pondok_id',
        'nama',
        'is_active',
        'keterangan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function pondok()
    {
        return $this->belongsTo(Pondok::class);
    }

    public function items()
    {
        return $this->hasMany(SkemaPenilaianItem::class)->orderBy('urutan');
    }

    public function ujians()
    {
        return $this->hasMany(Ujian::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function toSnapshot(): array
    {
        $items = $this->items
            ->map(function (SkemaPenilaianItem $item) {
                $nama = trim((string) ($item->nama ?: $item->label));
                $singkatan = strtoupper(trim((string) ($item->singkatan ?: $item->label)));

                if ($nama === '' || $singkatan === '') {
                    return null;
                }

                return [
                    'nama' => $nama,
                    'singkatan' => $singkatan,
                    'urutan' => (int) $item->urutan,
                ];
            })
            ->filter()
            ->values()
            ->all();

        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'items' => $items,
        ];
    }

    public static function activeForPondok(int $pondokId): ?self
    {
        return self::with('items')
            ->where('pondok_id', $pondokId)
            ->active()
            ->first();
    }
}
