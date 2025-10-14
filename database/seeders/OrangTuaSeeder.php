<?php

namespace Database\Seeders;

use App\Models\OrangTua;
use App\Models\Santri;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class OrangTuaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (Santri::all() as $santri) {
            // skip if parents already created for this santri
            if (OrangTua::where('santri_id', $santri->id)->exists()) {
                continue;
            }

            // Ayah
            OrangTua::create([
                'santri_id' => $santri->id,
                'tipe' => 'Ayah',
                'nama' => $faker->name('male'),
                'status' => $faker->randomElement(['Hidup', 'Meninggal']),
                'status_hubungan' => 'Kandung',
                'tempat_lahir' => $faker->city,
                'tanggal_lahir' => $faker->dateTimeBetween('-70 years', '-30 years')->format('Y-m-d'),
                'pendidikan' => $faker->randomElement(['SD','SMP','SMA','D3','S1','S2']),
                'pekerjaan' => $faker->randomElement(['Petani','Pedagang','Guru','Karyawan Swasta','Wiraswasta','PNS']),
                'penghasilan' => (string) $faker->numberBetween(500000, 15000000),
                'email' => "ayah.santri{$santri->id}@example.com",
                'handphone' => $faker->numerify('08#########'),
                'alamat' => $faker->address,
            ]);

            // Ibu
            OrangTua::create([
                'santri_id' => $santri->id,
                'tipe' => 'Ibu',
                'nama' => $faker->name('female'),
                'status' => $faker->randomElement(['Hidup', 'Meninggal']),
                'status_hubungan' => 'Kandung',
                'tempat_lahir' => $faker->city,
                'tanggal_lahir' => $faker->dateTimeBetween('-65 years', '-25 years')->format('Y-m-d'),
                'pendidikan' => $faker->randomElement(['SD','SMP','SMA','D3','S1']),
                'pekerjaan' => $faker->randomElement(['Ibu Rumah Tangga','Guru','Pedagang','Karyawan Swasta','Wiraswasta']),
                'penghasilan' => (string) $faker->numberBetween(0, 10000000),
                'email' => "ibu.santri{$santri->id}@example.com",
                'handphone' => $faker->numerify('08#########'),
                'alamat' => $faker->address,
            ]);
        }
    }
}
