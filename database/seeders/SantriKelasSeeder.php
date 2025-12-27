<?php

namespace Database\Seeders;

use App\Models\SantriKelas;
use App\Models\Santri;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Pondok;
use Illuminate\Database\Seeder;

class SantriKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pondoks = Pondok::all();
        $totalPlaced = 0;

        foreach ($pondoks as $pondok) {
            echo "Processing pondok: {$pondok->nama} (ID: {$pondok->id})\n";

            // Get active tahun ajaran
            $tahunAjaranAktif = TahunAjaran::where('pondok_id', $pondok->id)
                ->where('is_active', true)
                ->first();

            if (!$tahunAjaranAktif) {
                echo "  - No active tahun ajaran found, skipping...\n";
                continue;
            }
            echo "  - Tahun Ajaran Aktif: {$tahunAjaranAktif->nama} (ID: {$tahunAjaranAktif->id})\n";

            // Get all kelas for this tahun ajaran
            $kelasList = Kelas::where('pondok_id', $pondok->id)
                ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
                ->get();

            if ($kelasList->isEmpty()) {
                echo "  - No kelas found for this tahun ajaran, skipping...\n";
                continue;
            }
            echo "  - Found {$kelasList->count()} kelas\n";

            // Get all santri for this pondok
            $santris = Santri::where('pondok_id', $pondok->id)
                ->where('status_santri', 'aktif')
                ->get();

            echo "  - Found {$santris->count()} active santri\n";

            if ($santris->isEmpty()) {
                echo "  - No santri to place, skipping...\n";
                continue;
            }

            // Distribute 80% of santri to classes (20% belum ditempatkan)
            $santriCount = $santris->count();
            $placeCount = (int)($santriCount * 0.8);
            $santriToPlace = $santris->take($placeCount);
            
            echo "  - Will place {$placeCount} santri (80% of {$santriCount})\n";
            
            $kelasIndex = 0;
            $countPerKelas = [];
            $placedInThisPondok = 0;

            foreach ($santriToPlace as $santri) {
                $kelas = $kelasList[$kelasIndex];
                
                // Track count per kelas to respect kapasitas
                if (!isset($countPerKelas[$kelas->id])) {
                    $countPerKelas[$kelas->id] = 0;
                }

                // If kelas is full, move to next kelas
                if ($countPerKelas[$kelas->id] >= $kelas->kapasitas) {
                    $kelasIndex = ($kelasIndex + 1) % $kelasList->count();
                    $kelas = $kelasList[$kelasIndex];
                    if (!isset($countPerKelas[$kelas->id])) {
                        $countPerKelas[$kelas->id] = 0;
                    }
                }

                SantriKelas::create([
                    'santri_id' => $santri->id,
                    'kelas_id' => $kelas->id,
                    'tahun_ajaran_id' => $tahunAjaranAktif->id,
                    'tanggal_masuk' => $tahunAjaranAktif->tanggal_mulai,
                    'tanggal_keluar' => null,
                    'status' => 'aktif',
                    'keterangan' => null,
                ]);

                $countPerKelas[$kelas->id]++;
                $placedInThisPondok++;
                $kelasIndex = ($kelasIndex + 1) % $kelasList->count();
            }

            echo "  - Placed {$placedInThisPondok} santri successfully\n";
            $totalPlaced += $placedInThisPondok;
        }

        echo "\n✅ Seeder SantriKelas selesai. Total {$totalPlaced} santri ditempatkan ke kelas.\n";
        echo "Verifikasi: Total SantriKelas records = " . SantriKelas::count() . "\n";
    }
}
