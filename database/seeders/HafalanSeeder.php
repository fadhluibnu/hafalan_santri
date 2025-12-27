<?php

namespace Database\Seeders;

use App\Models\Hafalan;
use App\Models\Santri;
use App\Models\Ustadz;
use App\Models\Kelas;
use App\Models\QuranSurah;
use App\Models\Pondok;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class HafalanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $pondoks = Pondok::all();
        $kategoriHafalan = ['Sabaq', 'Sabqi', 'Manzil'];
        $surahs = QuranSurah::orderBy('id')->get();

        if ($surahs->isEmpty()) {
            echo "QuranSurah belum di-seed. Skip HafalanSeeder.\n";
            return;
        }

        foreach ($pondoks as $pondok) {
            $santris = Santri::where('pondok_id', $pondok->id)
                ->where('status_santri', 'aktif')
                ->get();
            
            // Get ustadzs (stored in gurus table)
            $ustadzs = Ustadz::where('pondok_id', $pondok->id)->get();
            
            // Get kelas
            $kelasList = Kelas::where('pondok_id', $pondok->id)->get();

            if ($ustadzs->isEmpty() || $kelasList->isEmpty()) continue;

            foreach ($santris as $santri) {
                // Generate 5-15 hafalan records per santri
                $jumlahHafalan = $faker->numberBetween(5, 15);

                for ($i = 0; $i < $jumlahHafalan; $i++) {
                    $kategori = $faker->randomElement($kategoriHafalan);
                    $ustadz = $ustadzs->random();
                    $kelas = $kelasList->random();
                    
                    // Pick random surah
                    $surahIndex = $faker->numberBetween(0, $surahs->count() - 1);
                    $dariSurah = $surahs[$surahIndex];
                    
                    // Sampai surah bisa sama atau surah berikutnya
                    $sampaiSurahIndex = $faker->randomElement([$surahIndex, min($surahIndex + 1, $surahs->count() - 1)]);
                    $sampaiSurah = $surahs[$sampaiSurahIndex];

                    $dariAyat = $faker->numberBetween(1, min(20, $dariSurah->jumlah_ayat ?? 20));
                    
                    if ($dariSurah->id === $sampaiSurah->id) {
                        $sampaiAyat = $faker->numberBetween($dariAyat, min($dariAyat + 10, $sampaiSurah->jumlah_ayat ?? 30));
                    } else {
                        $sampaiAyat = $faker->numberBetween(1, min(10, $sampaiSurah->jumlah_ayat ?? 10));
                    }

                    // Generate random date in last 6 months
                    $tanggalSetor = $faker->dateTimeBetween('-6 months', 'now');
                    
                    // Random juz 1-30
                    $juz = $faker->numberBetween(1, 30);

                    Hafalan::create([
                        'santri_id' => $santri->id,
                        'ustadz_id' => $ustadz->id,
                        'kelas_id' => $kelas->id,
                        'tanggal_setor' => $tanggalSetor->format('Y-m-d'),
                        'juz' => $juz,
                        'dari_surat' => $dariSurah->id,
                        'dari_ayat' => $dariAyat,
                        'sampai_surat' => $sampaiSurah->id,
                        'sampai_ayat' => $sampaiAyat,
                        'kategori' => $kategori,
                        'nilai' => $faker->randomElement(['A', 'B', 'C', 'D']),
                        'catatan' => $faker->randomElement([
                            null,
                            'Perlu perbaikan tajwid',
                            'Sudah lancar',
                            'Makhraj perlu diperbaiki',
                            'Sangat baik',
                            'Perlu muroja\'ah',
                            'Hafalan kuat',
                        ]),
                    ]);
                }
            }
        }

        echo "Seeder Hafalan selesai (5-15 hafalan per santri).\n";
    }
}
