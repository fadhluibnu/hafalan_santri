<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use App\Models\Pondok;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pondoks = Pondok::all();

        foreach ($pondoks as $pondok) {
            // Tahun ajaran 2023/2024 Ganjil
            TahunAjaran::create([
                'pondok_id' => $pondok->id,
                'nama' => '2023/2024',
                'semester' => 'ganjil',
                'tanggal_mulai' => '2023-07-01',
                'tanggal_selesai' => '2023-12-31',
                'keterangan' => 'Tahun ajaran 2023/2024 Semester Ganjil',
                'is_active' => false,
                'status' => 'selesai',
            ]);

            // Tahun ajaran 2023/2024 Genap
            TahunAjaran::create([
                'pondok_id' => $pondok->id,
                'nama' => '2023/2024',
                'semester' => 'genap',
                'tanggal_mulai' => '2024-01-01',
                'tanggal_selesai' => '2024-06-30',
                'keterangan' => 'Tahun ajaran 2023/2024 Semester Genap',
                'is_active' => false,
                'status' => 'selesai',
            ]);

            // Tahun ajaran 2024/2025 Ganjil (Aktif)
            TahunAjaran::create([
                'pondok_id' => $pondok->id,
                'nama' => '2024/2025',
                'semester' => 'ganjil',
                'tanggal_mulai' => '2024-07-01',
                'tanggal_selesai' => '2024-12-31',
                'keterangan' => 'Tahun ajaran 2024/2025 Semester Ganjil (Berjalan)',
                'is_active' => true,
                'status' => 'aktif',
            ]);

            // Tahun ajaran 2024/2025 Genap
            TahunAjaran::create([
                'pondok_id' => $pondok->id,
                'nama' => '2024/2025',
                'semester' => 'genap',
                'tanggal_mulai' => '2025-01-01',
                'tanggal_selesai' => '2025-06-30',
                'keterangan' => 'Tahun ajaran 2024/2025 Semester Genap',
                'is_active' => false,
                'status' => 'selesai',
            ]);
        }
    }
}
