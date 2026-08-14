<?php

namespace Database\Seeders;

use App\Models\Pondok;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PondokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pondok::create([
            'nama' => 'Pondok Tahfidz Al-Quran Baitul Hikmah',
            'alamat' => 'Jl. Pesantren No. 1, Bandung',
            'telepon' => '022-1234567',
            'email' => 'baitulhikmah@example.com',
            'website' => 'https://baitulhikmah.or.id',
            'logo' => 'santri_foto\hZgb0NPxgcoLpForDlT77M3VHlm2kp2CEtLqjlVU.jpg',
            'deskripsi' => 'Pondok pesantren tahfidz modern.',
            'tahun_berdiri' => 2010,
        ]);
    }
}
