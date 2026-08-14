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

            // Get all tahun ajaran for this pondok
            $tahunAjarans = TahunAjaran::where('pondok_id', $pondok->id)->get();

            if ($tahunAjarans->isEmpty()) {
                echo "  - No tahun ajaran found, skipping...\n";
                continue;
            }

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
            
            echo "  - Will place {$placeCount} santri (80% of {$santriCount}) across {$tahunAjarans->count()} semesters\n";

            foreach ($tahunAjarans as $ta) {
                echo "    > Placing for {$ta->nama} ({$ta->semester})\n";
                // Get all kelas for this tahun ajaran
                $kelasList = Kelas::where('pondok_id', $pondok->id)
                    ->where('tahun_ajaran_id', $ta->id)
                    ->get();

                if ($kelasList->isEmpty()) {
                    continue;
                }
                
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
                        'tahun_ajaran_id' => $ta->id,
                        'tanggal_masuk' => $ta->tanggal_mulai,
                        'tanggal_keluar' => $ta->status === 'selesai' ? $ta->tanggal_selesai : null,
                        'status' => $ta->status === 'selesai' ? 'lulus' : 'aktif',
                        'keterangan' => null,
                    ]);

                    $countPerKelas[$kelas->id]++;
                    $placedInThisPondok++;
                    $kelasIndex = ($kelasIndex + 1) % $kelasList->count();
                }
                $totalPlaced += $placedInThisPondok;
            }
        }

        echo "\n✅ Seeder SantriKelas selesai. Total {$totalPlaced} santri ditempatkan ke kelas.\n";
        echo "Verifikasi: Total SantriKelas records = " . SantriKelas::count() . "\n";
    }
}
