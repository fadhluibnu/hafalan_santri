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

        // Options sesuai dengan frontend
        $statusOrtu = ['Hidup', 'Almarhum'];
        $statusHubungan = ['Kandung', 'Tiri', 'Angkat'];
        $pendidikanOptions = ['Tidak Sekolah', 'SD/Sederajat', 'SMP/Sederajat', 'SMA/Sederajat', 'D1', 'D2', 'D3', 'D4/S1', 'S2', 'S3'];

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
                'status' => $faker->randomElement($statusOrtu),
                'status_hubungan' => $faker->randomElement($statusHubungan),
                'tempat_lahir' => $faker->city,
                'tanggal_lahir' => $faker->dateTimeBetween('-70 years', '-30 years')->format('Y-m-d'),
                'pendidikan' => $faker->randomElement($pendidikanOptions),
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
                'status' => $faker->randomElement($statusOrtu),
                'status_hubungan' => $faker->randomElement($statusHubungan),
                'tempat_lahir' => $faker->city,
                'tanggal_lahir' => $faker->dateTimeBetween('-65 years', '-25 years')->format('Y-m-d'),
                'pendidikan' => $faker->randomElement($pendidikanOptions),
                'pekerjaan' => $faker->randomElement(['Ibu Rumah Tangga','Guru','Pedagang','Karyawan Swasta','Wiraswasta']),
                'penghasilan' => (string) $faker->numberBetween(0, 10000000),
                'email' => "ibu.santri{$santri->id}@example.com",
                'handphone' => $faker->numerify('08#########'),
                'alamat' => $faker->address,
            ]);
        }
    }
}
