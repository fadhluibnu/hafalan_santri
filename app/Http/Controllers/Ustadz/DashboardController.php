<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Ustadz;
use App\Models\Santri;
use App\Models\Hafalan;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\SantriKelas;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil ustadz yang login
        $ustadz = Ustadz::where('user_id', Auth::id())->first();
        $pondokId = $ustadz->pondok_id ?? null;
        $ustadzId = $ustadz->id ?? null;

        // Get active tahun ajaran
        $activeTahunAjaran = TahunAjaran::where('pondok_id', $pondokId)
            ->where('is_active', true)
            ->first();

        // Hitung jumlah santri di pondok
        $jumlahSantri = $pondokId ? Santri::where('pondok_id', $pondokId)->where('status_santri', 'aktif')->count() : 0;

        // Hitung setoran hari ini (oleh ustadz ini)
        $today = Carbon::today()->toDateString();
        $setoranHariIni = Hafalan::where('ustadz_id', $ustadzId)
            ->whereDate('tanggal_setor', $today)
            ->count();

        // Hitung total setoran yang diterima ustadz ini bulan ini
        $setoranBulanIni = Hafalan::where('ustadz_id', $ustadzId)
            ->whereMonth('tanggal_setor', Carbon::now()->month)
            ->whereYear('tanggal_setor', Carbon::now()->year)
            ->count();

        // Hitung total setoran sepanjang waktu oleh ustadz ini
        $totalSetoran = Hafalan::where('ustadz_id', $ustadzId)->count();

        // Statistik nilai setoran ustadz ini
        $nilaiAB = Hafalan::where('ustadz_id', $ustadzId)
            ->whereIn('nilai', ['A', 'B'])
            ->count();
        $nilaiC = Hafalan::where('ustadz_id', $ustadzId)->where('nilai', 'C')->count();
        $nilaiD = Hafalan::where('ustadz_id', $ustadzId)->where('nilai', 'D')->count();

        // Kelas yang diwalikan ustadz ini
        $kelasWali = $activeTahunAjaran ? Kelas::where('wali_kelas_id', $ustadzId)
            ->where('tahun_ajaran_id', $activeTahunAjaran->id)
            ->with('tahunAjaran')
            ->get()
            ->map(function($k) {
                $jumlahSantri = SantriKelas::where('kelas_id', $k->id)
                    ->where('tahun_ajaran_id', $k->tahun_ajaran_id)
                    ->where('status', 'aktif')
                    ->count();
                return [
                    'id' => $k->id,
                    'nama' => $k->nama,
                    'tingkat' => $k->tingkat,
                    'kapasitas' => $k->kapasitas,
                    'jumlah_santri' => $jumlahSantri,
                ];
            }) : collect();

        // Rekap setoran terbaru (10 terakhir oleh ustadz ini)
        $rekapSetoran = Hafalan::with(['santri', 'dariSurah', 'sampaiSurah'])
            ->where('ustadz_id', $ustadzId)
            ->orderBy('tanggal_setor', 'desc')
            ->limit(10)
            ->get()
            ->map(function($h) {
                return [
                    'id' => $h->id,
                    'santri_nama' => $h->santri?->nama ?? '-',
                    'santri_nis' => $h->santri?->nis ?? '-',
                    'juz' => $h->juz,
                    'dari_surah' => $h->dariSurah?->nama ?? '-',
                    'sampai_surah' => $h->sampaiSurah?->nama ?? '-',
                    'kategori' => $h->kategori,
                    'nilai' => $h->nilai,
                    'tanggal' => $h->tanggal_setor?->format('d M Y'),
                ];
            });

        // Statistik per hari (7 hari terakhir)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $count = Hafalan::where('ustadz_id', $ustadzId)
                ->whereDate('tanggal_setor', $date)
                ->count();
            $chartData[] = [
                'tanggal' => $date->format('d M'),
                'jumlah' => $count,
            ];
        }

        return Inertia::render('Ustadz/Dashboard', [
            'ustadz' => [
                'nama' => $ustadz->nama ?? '-',
                'nip' => $ustadz->nip ?? '-',
            ],
            'jumlahSantri' => $jumlahSantri,
            'setoranHariIni' => $setoranHariIni,
            'setoranBulanIni' => $setoranBulanIni,
            'totalSetoran' => $totalSetoran,
            'statistikNilai' => [
                'baik' => $nilaiAB,
                'cukup' => $nilaiC,
                'kurang' => $nilaiD,
            ],
            'kelasWali' => $kelasWali,
            'rekapSetoran' => $rekapSetoran,
            'chartData' => $chartData,
            'tahunAjaranAktif' => $activeTahunAjaran?->nama ?? '-',
        ]);
    }
}
