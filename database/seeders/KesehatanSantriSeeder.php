<?php

namespace Database\Seeders;

use App\Models\KesehatanSantri;
use App\Models\Santri;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class KesehatanSantriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $santris = Santri::all();

        if ($santris->isEmpty()) {
            $this->command->info('Tidak ada santri ditemukan untuk dibuatkan data kesehatan.');
            return;
        }

        foreach ($santris as $santri) {
            // Lewati jika sudah ada record kesehatan untuk santri ini
            if (KesehatanSantri::where('santri_id', $santri->id)->exists()) {
                continue;
            }

            KesehatanSantri::create([
                'santri_id' => $santri->id,
                'golongan_darah' => $faker->randomElement(['A', 'B', 'AB', 'O']),
                'berat_badan' => $faker->randomFloat(1, 30, 80), // kg
                'tinggi_badan' => $faker->randomFloat(1, 120, 190), // cm
                'riwayat_penyakit' => $faker->randomElement([
                    'Tidak ada',
                    'Asma',
                    'Alergi',
                    'Diabetes',
                    'Hipertensi',
                    'Riwayat operasi kecil',
                    'Sakit maag'
                ]),
            ]);
        }

        $this->command->info('Seeder KesehatanSantri selesai.');
    }
}
