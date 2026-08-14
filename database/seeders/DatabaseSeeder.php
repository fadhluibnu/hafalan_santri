<?php

namespace Database\Seeders;

use App\Models\OrangTua;
use App\Models\SuperAdmin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Master data
            QuranSurahSeeder::class,
            
            // Users & Auth
            SuperAdminSeeder::class,
            
            // Pondok & Admin
            PondokSeeder::class,
            AdminCabangSeeder::class,
            SkemaPenilaianSeeder::class,
            
            // Tahun Ajaran (harus sebelum Kelas)
            TahunAjaranSeeder::class,
            
            // Ustadz (harus sebelum Kelas karena wali kelas)
            UstadzSeeder::class,
            
            // Kelas (harus setelah TahunAjaran dan Ustadz)
            KelasSeeder::class,
            
            // Santri (harus setelah Kelas)
            SantriSeeder::class,
            OrangTuaSeeder::class,
            KesehatanSantriSeeder::class,
            
            // // Penempatan Santri ke Kelas
            SantriKelasSeeder::class,
            
            // Data Hafalan
            HafalanSeeder::class,
            
            // Data Ujian
            UjianSeeder::class,
        ]);

        echo "\n✅ Semua seeder berhasil dijalankan!\n";
        echo "📊 Data yang dibuat:\n";
        echo "   - Tahun Ajaran: 3 tahun per pondok (1 aktif)\n";
        echo "   - Kelas: 8 kelas per pondok\n";
        echo "   - Santri: 50 santri per pondok\n";
        echo "   - Penempatan: 80% santri sudah ditempatkan\n";
        echo "   - Hafalan: 5-15 record per santri\n";
    }
}
