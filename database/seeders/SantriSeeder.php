<?php

namespace Database\Seeders;

use App\Models\Santri;
use App\Models\Pondok;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class SantriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pondoks = Pondok::all();
        $faker = Faker::create('id_ID');

        $namaLakiLaki = [
            'Ahmad', 'Muhammad', 'Abdullah', 'Umar', 'Ali', 'Hasan', 'Husein', 'Ibrahim',
            'Yusuf', 'Ismail', 'Musa', 'Isa', 'Zakaria', 'Yahya', 'Sulaiman', 'Daud',
            'Amir', 'Faris', 'Hamza', 'Bilal', 'Khalid', 'Salman', 'Zaid', 'Thariq',
            'Rizki', 'Fajar', 'Ramadhan', 'Rafi', 'Hafiz', 'Hakim', 'Malik', 'Naufal',
            'Raffi', 'Arif', 'Farhan', 'Ihsan', 'Ikhsan', 'Fadli', 'Fauzan', 'Fikri'
        ];

        $namaPerempuan = [
            'Aisyah', 'Fatimah', 'Khadijah', 'Maryam', 'Zainab', 'Ruqayyah', 'Hafshah',
            'Safiyyah', 'Aminah', 'Halimah', 'Asma', 'Raihana', 'Salsabila', 'Naura',
            'Zahra', 'Nabila', 'Rahma', 'Salma', 'Hana', 'Nadia', 'Layla', 'Yasmin',
            'Syifa', 'Alya', 'Dina', 'Rania', 'Siti', 'Nur', 'Putri', 'Dewi'
        ];

        $hobi = ['Membaca', 'Olahraga', 'Mengaji', 'Menulis', 'Menggambar', 'Berenang', 'Memanah', 'Berkuda'];

        foreach ($pondoks as $pondok) {
            // Generate 50 santri per pondok
            for ($i = 1; $i <= 50; $i++) {
                $jenisKelamin = $faker->randomElement(['L', 'P']);
                
                if ($jenisKelamin === 'L') {
                    $nama = $faker->randomElement($namaLakiLaki) . ' ' . $faker->randomElement(['bin', '']) . ' ' . $faker->lastName;
                } else {
                    $nama = $faker->randomElement($namaPerempuan) . ' ' . $faker->randomElement(['binti', '']) . ' ' . $faker->lastName;
                }

                $nis = Santri::generateNis($pondok->id);
                $tahunMasuk = $faker->randomElement([2021, 2022, 2023, 2024]);
                
                Santri::create([
                    'nis' => $nis,
                    'pondok_id' => $pondok->id,
                    'kelas_id' => null,
                    'nama' => trim(str_replace('  ', ' ', $nama)),
                    'panggilan' => explode(' ', $nama)[0],
                    'jenis_kelamin' => $jenisKelamin,
                    'tempat_lahir' => $faker->city,
                    'tanggal_lahir' => $faker->dateTimeBetween('2006-01-01', '2012-12-31')->format('Y-m-d'),
                    'status_mukim' => $faker->randomElement(['Mukim', 'Non-mukim']),
                    'kondisi' => $faker->randomElement(['Sehat', 'Sehat', 'Sehat', 'Dalam Perawatan']),
                    'warga_negara' => 'Indonesia',
                    'kode_pos' => $faker->postcode,
                    'alamat' => $faker->address,
                    'anak_ke' => $faker->numberBetween(1, 5),
                    'jumlah_saudara' => $faker->numberBetween(1, 6),
                    'status_anak' => $faker->randomElement(['Kandung', 'Kandung', 'Kandung', 'Yatim', 'Piatu', 'Angkat']),
                    'saudara_kandung' => $faker->numberBetween(0, 4),
                    'saudara_tiri' => $faker->numberBetween(0, 1),
                    'jarak_pondok' => $faker->randomFloat(1, 0.5, 100),
                    'telpon' => $faker->numerify('0##-########'),
                    'handphone' => $faker->numerify('08##########'),
                    'email' => strtolower(str_replace(' ', '', explode(' ', $nama)[0])) . $i . '@example.com',
                    'hobi' => $faker->randomElement($hobi),
                    'foto' => 'santri_foto/default.png',
                    'status_santri' => $faker->randomElement(['aktif', 'aktif', 'aktif', 'aktif', 'cuti']),
                    'tanggal_masuk_pondok' => "{$tahunMasuk}-07-" . $faker->numberBetween(1, 28),
                    'tanggal_lulus' => null,
                ]);
            }
        }

        echo "Seeder Santri selesai (50 santri per pondok).\n";
    }
}
