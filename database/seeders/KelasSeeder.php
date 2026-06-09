<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Pondok;
use App\Models\Ustadz;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pondoks = Pondok::all();

        $kelasData = [
            ['nama' => 'Kelas Tahfidz 1A', 'tingkat' => 'Juz 30', 'kapasitas' => 25],
            ['nama' => 'Kelas Tahfidz 1B', 'tingkat' => 'Juz 30', 'kapasitas' => 25],
            ['nama' => 'Kelas Tahfidz 2A', 'tingkat' => 'Juz 29', 'kapasitas' => 20],
            ['nama' => 'Kelas Tahfidz 2B', 'tingkat' => 'Juz 29', 'kapasitas' => 20],
            ['nama' => 'Kelas Tahfidz 3A', 'tingkat' => 'Juz 28', 'kapasitas' => 20],
            ['nama' => 'Kelas Mutqin A', 'tingkat' => 'Juz 1-10', 'kapasitas' => 15],
            ['nama' => 'Kelas Mutqin B', 'tingkat' => 'Juz 11-20', 'kapasitas' => 15],
            ['nama' => 'Kelas Mutqin C', 'tingkat' => 'Juz 21-30', 'kapasitas' => 15],
        ];

        foreach ($pondoks as $pondok) {
            // Get all tahun ajaran for this pondok
            $tahunAjarans = TahunAjaran::where('pondok_id', $pondok->id)->get();

            // Get ustadzs for wali kelas
            $ustadzs = Ustadz::where('pondok_id', $pondok->id)->get();

            foreach ($tahunAjarans as $tahunAjaran) {
                foreach ($kelasData as $index => $data) {
                    $waliKelas = $ustadzs->count() > 0 ? $ustadzs[$index % $ustadzs->count()] : null;

                    Kelas::create([
                        'pondok_id' => $pondok->id,
                        'tahun_ajaran_id' => $tahunAjaran->id,
                        'nama' => $data['nama'],
                        'tingkat' => $data['tingkat'],
                        'kapasitas' => $data['kapasitas'],
                        'wali_kelas_id' => $waliKelas?->id,
                        'keterangan' => "Kelas {$data['tingkat']} tahun ajaran {$tahunAjaran->nama} ({$tahunAjaran->semester})",
                        'status' => true,
                    ]);
                }
            }
        }

        echo "Seeder Kelas selesai.\n";
    }
}
