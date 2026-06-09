<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Pondok;
use App\Models\SantriKelas;
use App\Models\SkemaPenilaian;
use App\Models\Ujian;
use App\Models\UjianNilai;
use App\Models\Ustadz;
use Illuminate\Database\Seeder;

class UjianSeeder extends Seeder
{
    public function run(): void
    {
        $pondoks = Pondok::all();

        foreach ($pondoks as $pondok) {
            $kelasList = Kelas::where('pondok_id', $pondok->id)->get();
            $ustadzs = Ustadz::where('pondok_id', $pondok->id)->get();

            if ($ustadzs->isEmpty() || $kelasList->isEmpty()) continue;

            $skema = SkemaPenilaian::activeForPondok($pondok->id);
            if (!$skema) continue;

            $isNumeric = $skema->tipe === 'numeric';
            $snapshot = $skema->toSnapshot();
            
            $labelItems = [];
            if (!$isNumeric) {
                $labelItems = $skema->items->pluck('singkatan')->toArray();
            }
            if (empty($labelItems)) {
                $labelItems = ['A', 'B', 'C'];
            }

            foreach ($kelasList as $kelas) {
                $santriKelasList = SantriKelas::where('kelas_id', $kelas->id)
                    ->where('status', 'aktif')
                    ->where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
                    ->get();

                if ($santriKelasList->isEmpty()) continue;

                $ustadz = $ustadzs->first();

                $ujian = Ujian::create([
                    'pondok_id' => $pondok->id,
                    'tahun_ajaran_id' => $kelas->tahun_ajaran_id,
                    'kelas_id' => $kelas->id,
                    'ustadz_id' => $ustadz->id,
                    'skema_penilaian_id' => $skema->id,
                    'nama' => 'Ujian Hafalan Juz 30',
                    'tanggal_ujian' => now()->subDays(2)->format('Y-m-d'),
                    'skema_snapshot' => $snapshot,
                    'status' => 'selesai',
                    'keterangan' => 'Ujian terstruktur statis untuk keperluan seeder',
                ]);

                foreach ($santriKelasList as $index => $santriKelas) {
                    $nilaiAngka = null;
                    $nilaiLabel = null;

                    if ($isNumeric) {
                        $nilaiAngka = 80 + ($index % 20); // Nilai berkisar 80-99
                    } else {
                        $nilaiLabel = $labelItems[$index % count($labelItems)];
                    }

                    UjianNilai::create([
                        'ujian_id' => $ujian->id,
                        'santri_id' => $santriKelas->santri_id,
                        'nilai_angka' => $nilaiAngka,
                        'nilai_label' => $nilaiLabel,
                        'catatan' => 'Baik',
                    ]);
                }
            }
        }

        echo "Seeder Ujian selesai (Data terstruktur).\n";
    }
}
