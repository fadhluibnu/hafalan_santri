<?php

namespace Database\Seeders;

use App\Models\Pondok;
use App\Models\SkemaPenilaian;
use App\Models\SkemaPenilaianItem;
use Illuminate\Database\Seeder;

class SkemaPenilaianSeeder extends Seeder
{
    public function run(): void
    {
        $pondoks = Pondok::all();

        foreach ($pondoks as $index => $pondok) {
            // Gunakan index ganjil/genap untuk memvariasikan tipe skema
            $tipe = ($index % 2 === 0) ? 'numeric' : 'label';

            $skema = SkemaPenilaian::create([
                'pondok_id' => $pondok->id,
                'nama' => 'Skema ' . ucfirst($tipe) . ' ' . $pondok->nama,
                'tipe' => $tipe,
                'is_active' => true,
            ]);

            if ($tipe === 'label') {
                // Buat item A, B, C
                SkemaPenilaianItem::create([
                    'skema_penilaian_id' => $skema->id,
                    'nama' => 'Sangat Baik',
                    'singkatan' => 'A',
                    'batas_bawah' => 90,
                    'batas_atas' => 100,
                ]);

                SkemaPenilaianItem::create([
                    'skema_penilaian_id' => $skema->id,
                    'nama' => 'Baik',
                    'singkatan' => 'B',
                    'batas_bawah' => 80,
                    'batas_atas' => 89.99,
                ]);

                SkemaPenilaianItem::create([
                    'skema_penilaian_id' => $skema->id,
                    'nama' => 'Cukup',
                    'singkatan' => 'C',
                    'batas_bawah' => 70,
                    'batas_atas' => 79.99,
                ]);
            }
        }

        echo "Seeder SkemaPenilaian selesai (Numeric & Label bervariasi per pondok).\n";
    }
}
