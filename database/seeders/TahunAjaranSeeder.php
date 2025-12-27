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
            // Tahun ajaran sebelumnya (sudah selesai)
            TahunAjaran::create([
                'pondok_id' => $pondok->id,
                'nama' => '2022/2023',
                'tanggal_mulai' => '2022-07-01',
                'tanggal_selesai' => '2023-06-30',
                'keterangan' => 'Tahun ajaran 2022/2023',
                'is_active' => false,
                'status' => 'selesai',
            ]);

            TahunAjaran::create([
                'pondok_id' => $pondok->id,
                'nama' => '2023/2024',
                'tanggal_mulai' => '2023-07-01',
                'tanggal_selesai' => '2024-06-30',
                'keterangan' => 'Tahun ajaran 2023/2024',
                'is_active' => false,
                'status' => 'selesai',
            ]);

            // Tahun ajaran aktif
            TahunAjaran::create([
                'pondok_id' => $pondok->id,
                'nama' => '2024/2025',
                'tanggal_mulai' => '2024-07-01',
                'tanggal_selesai' => '2025-06-30',
                'keterangan' => 'Tahun ajaran 2024/2025 - Tahun ajaran berjalan',
                'is_active' => true,
                'status' => 'aktif',
            ]);
        }
    }
}
