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
                    ->where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
                    ->get();

                if ($santriKelasList->isEmpty()) continue;

                $ustadz = $ustadzs->first();

                $ta = $kelas->tahunAjaran;
                $midDate = $ta ? \Carbon\Carbon::parse($ta->tanggal_mulai)->addMonths(3)->format('Y-m-d') : now()->subDays(30)->format('Y-m-d');
                $endDate = $ta ? \Carbon\Carbon::parse($ta->tanggal_selesai)->subDays(10)->format('Y-m-d') : now()->subDays(2)->format('Y-m-d');

                $ujianTypes = [
                    ['nama' => 'Ujian Tengah Semester (UTS)', 'tanggal' => $midDate],
                    ['nama' => 'Ujian Akhir Semester (UAS)', 'tanggal' => $endDate],
                ];

                foreach ($ujianTypes as $tipe) {
                    $ujian = Ujian::create([
                        'pondok_id' => $pondok->id,
                        'tahun_ajaran_id' => $kelas->tahun_ajaran_id,
                        'kelas_id' => $kelas->id,
                        'ustadz_id' => $ustadz->id,
                        'skema_penilaian_id' => $skema->id,
                        'nama' => $tipe['nama'],
                        'tanggal_ujian' => $tipe['tanggal'],
                        'skema_snapshot' => $snapshot,
                        'status' => 'selesai',
                        'keterangan' => 'Ujian terstruktur statis untuk keperluan seeder',
                    ]);

                    foreach ($santriKelasList as $index => $santriKelas) {
                        $nilaiAngka = null;
                        $nilaiLabel = null;

                        if ($isNumeric) {
                            $nilaiAngka = 80 + (($index + strlen($tipe['nama'])) % 20); // Nilai bervariasi
                        } else {
                            $nilaiLabel = $labelItems[($index + strlen($tipe['nama'])) % count($labelItems)];
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
        }

        echo "Seeder Ujian selesai (Data terstruktur).\n";
    }
}
