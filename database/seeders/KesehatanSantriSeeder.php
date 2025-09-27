<?php

namespace Database\Seeders;

use App\Models\KesehatanSantri;
use App\Models\Santri;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KesehatanSantriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $santri = Santri::first();

        KesehatanSantri::create([
            'santri_id' => $santri->id,
            'golongan_darah' => 'O',
            'berat_badan' => 45.5,
            'tinggi_badan' => 155.0,
            'riwayat_penyakit' => 'Tidak ada',
        ]);
    }
}
