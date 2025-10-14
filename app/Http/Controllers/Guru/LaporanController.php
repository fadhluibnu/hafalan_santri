<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\Hafalan;
use App\Models\QuranSurah;
use App\Models\Pondok;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $guru = Guru::where('user_id', Auth::id())->first();
        $pondokId = $guru->pondok_id ?? null;
        $pondok = $pondokId ? Pondok::find($pondokId) : null;

        // daftar kelas & guru untuk select di UI
        $classes = $pondokId ? Kelas::where('pondok_id', $pondokId)->select('id', 'nama', 'kapasitas')->orderBy('nama')->get() : collect();
        $gurus = $pondokId ? Guru::where('pondok_id', $pondokId)->select('id', 'nama')->orderBy('nama')->get() : collect();

        // filters (kelas/guru/bulan/tahun/hariAktif)
        $filters = [
            'kelas_id'   => $request->input('kelas_id') ? (int)$request->input('kelas_id') : null,
            'guru_id'    => $request->input('guru_id') ? (int)$request->input('guru_id') : null,
            'bulan'      => $request->input('bulan') ?? date('m'),
            'tahun'      => $request->input('tahun') ?? date('Y'),
            'hariAktif'  => $request->input('hariAktif') ?? 24,
        ];

        // load alquran.json dari public dan build mapping surah->ayat->juz
        $alquranPath = public_path('alquran.json');
        $juzMap = [];
        if (file_exists($alquranPath)) {
            $json = file_get_contents($alquranPath);
            $pages = json_decode($json, true) ?: [];
            foreach ($pages as $p) {
                if (!isset($p['surah'], $p['from_ayat'], $p['to_ayat'], $p['juz'])) continue;
                $surahId = (int)$p['surah'];
                $from = (int)$p['from_ayat'];
                $to = (int)$p['to_ayat'];
                for ($a = $from; $a <= $to; $a++) {
                    $juzMap[$surahId][$a] = (int)$p['juz'];
                }
            }
        }

        // ambil jumlah ayat per surah sebagai fallback/limit
        $quranSurahMax = QuranSurah::pluck('jumlah_ayat', 'id')->map(fn($v) => (int)$v)->toArray();

        $results = [];
        $santris = collect();

        // jika kelas dipilih, ambil santri pada kelas tersebut
        if ($filters['kelas_id']) {
            $santris = Santri::where('pondok_id', $pondokId)
                ->where('kelas_id', $filters['kelas_id'])
                ->select('id', 'nama', 'jenis_kelamin')
                ->orderBy('nama')
                ->get();
        }

        // jika ada santri, ambil hafalan untuk rentang bulan terpilih dan bulan sebelumnya secara grouped per santri
        if ($santris->isNotEmpty()) {
            $bulan = (int)$filters['bulan'];
            $tahun = (int)$filters['tahun'];

            $startCurrent = Carbon::createFromDate($tahun, $bulan, 1)->startOfDay();
            $endCurrent = $startCurrent->copy()->endOfMonth();
            $startPrev = $startCurrent->copy()->subMonth()->startOfMonth();
            $endPrev = $startPrev->copy()->endOfMonth();

            $kelasId = $filters['kelas_id'];

            $hafalansPrev = Hafalan::where('kelas_id', $kelasId)
                ->whereBetween('tanggal_setor', [$startPrev->toDateString(), $endPrev->toDateString()])
                ->get()
                ->groupBy('santri_id');

            $hafalansCurr = Hafalan::where('kelas_id', $kelasId)
                ->whereBetween('tanggal_setor', [$startCurrent->toDateString(), $endCurrent->toDateString()])
                ->get()
                ->groupBy('santri_id');

            $computeFromCollection = function ($collection) use ($juzMap, $quranSurahMax) {
                $juzSet = [];
                foreach ($collection as $h) {
                    $fromSurah = (int)$h->dari_surat;
                    $toSurah = (int)$h->sampai_surat;
                    $fromAyat = (int)$h->dari_ayat;
                    $toAyat = (int)$h->sampai_ayat;

                    for ($s = $fromSurah; $s <= $toSurah; $s++) {
                        $startAy = ($s === $fromSurah) ? $fromAyat : 1;
                        $endAy = ($s === $toSurah) ? $toAyat : ($quranSurahMax[$s] ?? $toAyat);
                        if ($endAy < $startAy) continue;
                        for ($a = $startAy; $a <= $endAy; $a++) {
                            if (isset($juzMap[$s][$a])) {
                                $j = $juzMap[$s][$a];
                                $juzSet[$j] = true;
                            }
                        }
                    }
                }
                return count($juzSet);
            };

            foreach ($santris as $s) {
                $prevColl = $hafalansPrev->has($s->id) ? $hafalansPrev->get($s->id) : collect();
                $currColl = $hafalansCurr->has($s->id) ? $hafalansCurr->get($s->id) : collect();

                $bl = $computeFromCollection($prevColl);
                $bs = $computeFromCollection($currColl);
                $jn = max(0, $bs - $bl);

                $results[] = [
                    'santri_id' => $s->id,
                    'nis'       => $s->nis ?? null,
                    'nama'      => $s->nama,
                    'bl'        => $bl,
                    'bs'        => $bs,
                    'jn'        => $jn,
                ];
            }
        }

        return Inertia::render('Guru/Laporan', [
            'pondok'         => $pondok ? ['id' => $pondok->id, 'nama' => $pondok->nama] : null,
            'classes'        => $classes,
            'gurus'          => $gurus,
            'santris'        => $santris,
            'results'        => $results,
            'initialFilters' => $filters,
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
