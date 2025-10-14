<?php

namespace Database\Seeders;

use App\Models\Santri;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class SantriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pondok = \App\Models\Pondok::first();
        if (! $pondok) {
            // jika belum ada pondok, buat satu sederhana
            $pondok = \App\Models\Pondok::create([
                'nama' => 'Pondok Default',
                'alamat' => 'Alamat Pondok',
            ]);
        }

        $faker = Faker::create();

        for ($i = 1; $i <= 10; $i++) {
            $user = User::create([
                "username" => "santri{$i}",
                "email" => "santri{$i}@example.com",
                "password" => Hash::make("password123"),
                "role" => "santri",
                "status" => 1,
            ]);

            Santri::create([
                "user_id" => $user->id,
                "pondok_id" => $pondok->id,
                "kelas_id" => null,
                "nama" => "Santri {$i}",
                "panggilan" => "Santri",
                "jenis_kelamin" => ($i % 2 === 0) ? "P" : "L",
                "tempat_lahir" => $faker->city,
                "tanggal_lahir" => $faker->date('Y-m-d', '2008-12-31'),
                "status_mukim" => "Mukim",
                "kondisi" => "Sehat",
                "warga_negara" => "Indonesia",
                "kode_pos" => $faker->postcode,
                "alamat" => $faker->address,
                "anak_ke" => $faker->numberBetween(1,4),
                "jumlah_saudara" => $faker->numberBetween(0,6),
                "status_anak" => "Kandung",
                "saudara_kandung" => $faker->numberBetween(0,4),
                "saudara_tiri" => $faker->numberBetween(0,2),
                "jarak_pondok" => $faker->randomFloat(1, 0.5, 50),
                "telpon" => $faker->numerify('0##-#######'),
                "handphone" => $faker->numerify('08#########'),
                "email" => "santri{$i}@example.com",
                "hobi" => $faker->randomElement(['Membaca','Olahraga','Mengaji','Menulis']),
                "foto" => "santri_foto/EcutsyZsaAOmjgknrNCrW4YyZyeOprNr1jr608MO.png",
            ]);
        }
    }
}
