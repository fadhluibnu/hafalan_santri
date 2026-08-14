<?php

namespace App\Http\Controllers\AdminCabang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminCabang;
use App\Models\Santri;
use App\Models\Ustadz;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\SantriKelas;
use App\Models\Hafalan;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil pondok yang terkait dengan admin cabang yang sedang login
        $adminCabang = AdminCabang::where('user_id', Auth::id())->first();
        $pondokId = $adminCabang->pondok_id ?? null;

        // Get selected tahun ajaran from session
        $selectedTahunAjaranId = session('selected_tahun_ajaran_id');
        if (!$selectedTahunAjaranId && $pondokId) {
            $activeTahunAjaran = TahunAjaran::where('pondok_id', $pondokId)->where('is_active', true)->first();
            $selectedTahunAjaranId = $activeTahunAjaran?->id;
        }

        // Hitung jumlah santri / ustadz untuk pondok tersebut
        $jumlahSantri = $pondokId ? Santri::where('pondok_id', $pondokId)->count() : 0;
        $jumlahUstadz = $pondokId ? Ustadz::where('pondok_id', $pondokId)->count() : 0;
        
        // Hitung jumlah kelas di tahun ajaran terpilih
        $jumlahKelas = 0;
        if ($pondokId && $selectedTahunAjaranId) {
            $jumlahKelas = Kelas::where('pondok_id', $pondokId)
                ->where('tahun_ajaran_id', $selectedTahunAjaranId)
                ->count();
        }

        // Hitung santri yang sudah ditempatkan ke kelas
        $santriSudahDitempatkan = 0;
        $santriBelumDitempatkan = 0;
        if ($pondokId && $selectedTahunAjaranId) {
            $santriSudahDitempatkan = SantriKelas::where('tahun_ajaran_id', $selectedTahunAjaranId)
                ->where('status', 'aktif')
                ->whereHas('santri', function($q) use ($pondokId) {
                    $q->where('pondok_id', $pondokId);
                })
                ->count();
            $santriBelumDitempatkan = $jumlahSantri - $santriSudahDitempatkan;
        }

        // Hitung jumlah tahun ajaran
        $jumlahTahunAjaran = $pondokId ? TahunAjaran::where('pondok_id', $pondokId)->count() : 0;
        
        // Get tahun ajaran aktif name
        $tahunAjaranAktif = $pondokId ? TahunAjaran::where('pondok_id', $pondokId)->where('is_active', true)->first() : null;

        // Ambil rekap hafalan terbaru (limit 10)
        $rekapHafalan = [];
        if ($pondokId) {
            $rekapHafalan = Hafalan::with(['santri', 'ustadz', 'dariSurah', 'sampaiSurah'])
                ->whereHas('santri', function($q) use ($pondokId) {
                    $q->where('pondok_id', $pondokId);
                })
                ->orderBy('tanggal_setor', 'desc')
                ->limit(10)
                ->get()
                ->map(function($h) {
                    return [
                        'id' => $h->id,
                        'santri_nama' => $h->santri?->nama ?? '-',
                        'santri_nis' => $h->santri?->nis ?? '-',
                        'jenis' => $h->jenis_hafalan ?? '-',
                        'dari_surah' => $h->dariSurah?->nama ?? '-',
                        'sampai_surah' => $h->sampaiSurah?->nama ?? '-',
                        'dari_ayat' => $h->dari_ayat,
                        'sampai_ayat' => $h->sampai_ayat,
                        'nilai' => $h->nilai,
                        'tanggal' => $h->tanggal_setor?->format('d M Y'),
                        'ustadz' => $h->ustadz?->nama ?? '-',
                    ];
                })
                ->toArray();
        }

        // Statistik per kelas (top 5 kelas dengan santri terbanyak)
        $statistikKelas = [];
        if ($pondokId && $selectedTahunAjaranId) {
            $statistikKelas = Kelas::where('pondok_id', $pondokId)
                ->where('tahun_ajaran_id', $selectedTahunAjaranId)
                ->withCount(['santriKelas as santri_count' => function($q) {
                    $q->where('status', 'aktif');
                }])
                ->orderBy('santri_count', 'desc')
                ->limit(5)
                ->get()
                ->map(function($k) {
                    return [
                        'id' => $k->id,
                        'nama' => $k->nama,
                        'tingkat' => $k->tingkat,
                        'kapasitas' => $k->kapasitas ?? 0,
                        'terisi' => $k->santri_count ?? 0,
                    ];
                })
                ->toArray();
        }

        return Inertia::render('AdminCabang/Dashboard', [
            'jumlahSantri' => $jumlahSantri,
            'jumlahUstadz' => $jumlahUstadz,
            'jumlahKelas' => $jumlahKelas,
            'jumlahTahunAjaran' => $jumlahTahunAjaran,
            'santriSudahDitempatkan' => $santriSudahDitempatkan,
            'santriBelumDitempatkan' => $santriBelumDitempatkan < 0 ? 0 : $santriBelumDitempatkan,
            'tahunAjaranAktif' => $tahunAjaranAktif?->nama ?? '-',
            'rekapHafalan' => $rekapHafalan,
            'statistikKelas' => $statistikKelas,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
