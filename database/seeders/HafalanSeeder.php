<?php

namespace Database\Seeders;

use App\Models\Hafalan;
use App\Models\Santri;
use App\Models\Ustadz;
use App\Models\Kelas;
use App\Models\QuranSurah;
use App\Models\Pondok;
use App\Models\SantriKelas;
use App\Models\SkemaPenilaian;
use Illuminate\Database\Seeder;

class HafalanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pondoks = Pondok::all();
        // Mengambil surah-surah Juz 30 awal untuk simulasi terstruktur
        $surahProgress = QuranSurah::whereIn('id', [78, 79, 80, 81, 82])->orderBy('id')->get();

        if ($surahProgress->isEmpty()) {
            echo "QuranSurah belum di-seed. Skip HafalanSeeder.\n";
            return;
        }

        foreach ($pondoks as $pondok) {
            $santris = Santri::where('pondok_id', $pondok->id)
                ->where('status_santri', 'aktif')
                ->take(15) // Batasi 15 santri per pondok agar tidak terlalu lambat/gemuk
                ->get();
            
            $ustadzs = Ustadz::where('pondok_id', $pondok->id)->get();
            
            if ($ustadzs->isEmpty() || $santris->isEmpty()) continue;

            $skema = SkemaPenilaian::activeForPondok($pondok->id);
            $isNumeric = $skema && $skema->tipe === 'numeric';
            $labelItems = [];
            if ($skema && !$isNumeric) {
                $labelItems = $skema->items->pluck('singkatan')->toArray();
            }
            if (empty($labelItems)) {
                $labelItems = ['A', 'B', 'C'];
            }

            foreach ($santris as $santri) {
                $santriKelas = SantriKelas::where('santri_id', $santri->id)->where('status', 'aktif')->first();
                if (!$santriKelas) continue;
                
                $kelas = Kelas::find($santriKelas->kelas_id);
                if (!$kelas) continue;
                
                $ustadz = $ustadzs->first();
                
                // Setoran berurutan
                foreach ($surahProgress as $index => $surah) {
                    $hariMundur = (5 - $index) * 3; // 15, 12, 9, 6, 3 hari yang lalu
                    $tanggal = now()->subDays($hariMundur)->format('Y-m-d');
                    
                    // Jika numeric, berikan nilai 85, 87, 89, 91, 93
                    // Jika label, berikan label secara rotasi, umumnya A atau B
                    $nilai = $isNumeric 
                        ? (85 + ($index * 2)) 
                        : $labelItems[$index % count($labelItems)];
                    
                    Hafalan::create([
                        'santri_id' => $santri->id,
                        'ustadz_id' => $ustadz->id,
                        'kelas_id' => $kelas->id,
                        'tanggal_setor' => $tanggal,
                        'juz' => 30,
                        'dari_surat' => $surah->id,
                        'dari_ayat' => 1,
                        'sampai_surat' => $surah->id,
                        'sampai_ayat' => $surah->jumlah_ayat ?? 10,
                        'kategori' => 'Ziyadah',
                        'nilai' => $nilai,
                        'catatan' => 'Lancar dan baik',
                    ]);
                }
            }
        }

        echo "Seeder Hafalan selesai (Data terstruktur Juz 30).\n";
    }
}
