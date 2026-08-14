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
                // Get ALL placements for this santri
                $placements = SantriKelas::where('santri_id', $santri->id)->with('tahunAjaran')->get();
                if ($placements->isEmpty()) continue;
                
                $ustadz = $ustadzs->first();
                
                foreach ($placements as $placement) {
                    $kelas = Kelas::find($placement->kelas_id);
                    if (!$kelas) continue;

                    $ta = $placement->tahunAjaran;
                    if (!$ta || !$ta->tanggal_mulai || !$ta->tanggal_selesai) continue;

                    $start = \Carbon\Carbon::parse($ta->tanggal_mulai)->startOfMonth();
                    $end = \Carbon\Carbon::parse($ta->tanggal_selesai)->endOfMonth();

                    $currentMonth = $start->copy();
                    
                    // Generate 2 hafalans per month in the semester
                    while ($currentMonth <= $end) {
                        for ($i = 0; $i < 2; $i++) {
                            // Random day in the month
                            $randomDay = rand(1, $currentMonth->daysInMonth);
                            $tanggal = $currentMonth->copy()->addDays($randomDay - 1)->format('Y-m-d');

                            // Random surah from the progress
                            $surah = $surahProgress->random();
                            
                            $nilai = $isNumeric 
                                ? rand(80, 95)
                                : $labelItems[array_rand($labelItems)];
                            
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
                        $currentMonth->addMonth();
                    }
                }
            }
        }

        echo "Seeder Hafalan selesai (Data terstruktur Juz 30).\n";
    }
}
