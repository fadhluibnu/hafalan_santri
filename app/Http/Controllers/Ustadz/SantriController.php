<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Ustadz;
use App\Models\Santri;
use App\Models\Hafalan;
use App\Models\TahunAjaran;

class SantriController extends Controller
{
    /**
     * Display list of santri for ustadz
     */
    public function index(Request $request)
    {
        $ustadz = Ustadz::where('user_id', Auth::id())->first();
        $pondokId = $ustadz->pondok_id ?? null;

        // Get active tahun ajaran
        $activeTahunAjaran = TahunAjaran::where('pondok_id', $pondokId)
            ->where('is_active', true)
            ->first();

        $q = $request->input('q');

        $query = Santri::with([
                'jus',
                'santriKelas' => function($q) use ($activeTahunAjaran) {
                    if ($activeTahunAjaran) {
                        $q->where('tahun_ajaran_id', $activeTahunAjaran->id)
                          ->where('status', 'aktif')
                          ->with('kelas:id,nama');
                    }
                }
            ])
            ->where('pondok_id', $pondokId)
            ->where('status_santri', 'aktif');

        if ($q) {
            $query->where(function ($qry) use ($q) {
                $qry->where('nama', 'like', "%{$q}%")
                    ->orWhere('nis', 'like', "%{$q}%");
            });
        }

        $santris = $query->orderBy('nama')->paginate(15)->appends($request->only('q'));

        // Transform untuk menambahkan kelas dari santri_kelas
        $santris->getCollection()->transform(function ($santri) {
            $kelasAktif = $santri->santriKelas->first();
            $santri->kelas = $kelasAktif ? $kelasAktif->kelas : null;
            unset($santri->santriKelas);
            return $santri;
        });

        return Inertia::render('Ustadz/Santri/Index', [
            'santris' => $santris,
            'filters' => $request->only('q'),
        ]);
    }

    /**
     * Show santri detail
     */
    public function show(string $nis)
    {
        $ustadz = Ustadz::where('user_id', Auth::id())->first();
        $pondokId = $ustadz->pondok_id ?? null;

        // Get active tahun ajaran
        $activeTahunAjaran = TahunAjaran::where('pondok_id', $pondokId)
            ->where('is_active', true)
            ->first();

        $santri = Santri::with([
                'pondok',
                'orangTuas',
                'kesehatanSantri',
                'santriKelas' => function($q) use ($activeTahunAjaran) {
                    if ($activeTahunAjaran) {
                        $q->where('tahun_ajaran_id', $activeTahunAjaran->id)
                          ->where('status', 'aktif')
                          ->with('kelas:id,nama');
                    }
                }
            ])
            ->where('nis', $nis)
            ->firstOrFail();

        if ($pondokId && $santri->pondok_id !== $pondokId) {
            abort(403, 'Anda tidak berwenang mengakses data ini.');
        }

        // Get kelas aktif dari santri_kelas
        $kelasAktif = $santri->santriKelas->first();
        $kelasNama = $kelasAktif ? $kelasAktif->kelas?->nama : null;

        // Organize orang tua data
        $ayah = $santri->orangTuas->where('tipe', 'Ayah')->first();
        $ibu = $santri->orangTuas->where('tipe', 'Ibu')->first();
        $wali = $santri->orangTuas->where('tipe', 'Wali')->first();

        return Inertia::render('Ustadz/Santri/Show', [
            'santri' => [
                'id' => $santri->id,
                'nis' => $santri->nis,
                'nama' => $santri->nama,
                'panggilan' => $santri->panggilan,
                'jenis_kelamin' => $santri->jenis_kelamin,
                'tempat_lahir' => $santri->tempat_lahir,
                'tanggal_lahir' => $santri->tanggal_lahir,
                'alamat' => $santri->alamat,
                'status_mukim' => $santri->status_mukim,
                'kelas' => $kelasNama,
                'pondok' => $santri->pondok?->nama,
                'ayah' => $ayah,
                'ibu' => $ibu,
                'wali' => $wali,
                'kesehatan' => $santri->kesehatanSantri,
            ],
        ]);
    }

    /**
     * Show hafalan progress for a santri
     */
    public function hafalan(string $nis)
    {
        $ustadz = Ustadz::where('user_id', Auth::id())->first();
        $pondokId = $ustadz->pondok_id ?? null;

        // Get active tahun ajaran
        $activeTahunAjaran = TahunAjaran::where('pondok_id', $pondokId)
            ->where('is_active', true)
            ->first();

        $santri = Santri::with([
                'pondok',
                'santriKelas' => function($q) use ($activeTahunAjaran) {
                    if ($activeTahunAjaran) {
                        $q->where('tahun_ajaran_id', $activeTahunAjaran->id)
                          ->where('status', 'aktif')
                          ->with('kelas:id,nama');
                    }
                }
            ])
            ->where('nis', $nis)
            ->firstOrFail();

        if ($pondokId && $santri->pondok_id !== $pondokId) {
            abort(403, 'Anda tidak berwenang mengakses data ini.');
        }

        // Get kelas aktif dari santri_kelas
        $kelasAktif = $santri->santriKelas->first();
        $kelasNama = $kelasAktif ? $kelasAktif->kelas?->nama : '-';

        // Get all hafalan for this santri
        $hafalans = Hafalan::with(['dariSurah', 'sampaiSurah', 'ustadz', 'kelas:id,nama'])
            ->where('santri_id', $santri->id)
            ->orderBy('tanggal_setor', 'desc')
            ->get()
            ->map(function ($h) {
                return [
                    'id' => $h->id,
                    'kelas' => $h->kelas?->nama ?? '-',
                    'tanggal_setor' => $h->tanggal_setor?->format('Y-m-d'),
                    'juz' => $h->juz,
                    'dari_surat' => $h->dariSurah->name ?? '-',
                    'dari_ayat' => $h->dari_ayat,
                    'sampai_surat' => $h->sampaiSurah->name ?? '-',
                    'sampai_ayat' => $h->sampai_ayat,
                    'kategori' => $h->kategori,
                    'nilai' => $h->nilai,
                    'catatan' => $h->catatan,
                    'ustadz' => $h->ustadz->nama ?? '-',
                ];
            });

        return Inertia::render('Ustadz/Santri/Hafalan', [
            'santri' => [
                'id' => $santri->id,
                'nis' => $santri->nis,
                'nama' => $santri->nama,
                'kelas' => $kelasNama,
                'pondok' => $santri->pondok?->nama ?? '-',
            ],
            'hafalans' => $hafalans,
        ]);
    }

    /**
     * Export hafalan progress to PDF
     */
    public function exportPdf(string $nis)
    {
        $ustadz = Ustadz::where('user_id', Auth::id())->first();
        $pondokId = $ustadz->pondok_id ?? null;

        // Get active tahun ajaran
        $activeTahunAjaran = TahunAjaran::where('pondok_id', $pondokId)
            ->where('is_active', true)
            ->first();

        $santri = Santri::with([
                'pondok',
                'orangTuas',
                'kesehatanSantri',
                'santriKelas' => function($q) use ($activeTahunAjaran) {
                    if ($activeTahunAjaran) {
                        $q->where('tahun_ajaran_id', $activeTahunAjaran->id)
                          ->where('status', 'aktif')
                          ->with('kelas:id,nama');
                    }
                }
            ])
            ->where('nis', $nis)
            ->firstOrFail();

        if ($pondokId && $santri->pondok_id !== $pondokId) {
            abort(403, 'Anda tidak berwenang mengakses data ini.');
        }

        // Get kelas aktif dari santri_kelas
        $kelasAktif = $santri->santriKelas->first();
        $kelasNama = $kelasAktif ? $kelasAktif->kelas?->nama : '-';

        // Get all hafalan for this santri
        $hafalans = Hafalan::with(['dariSurah', 'sampaiSurah', 'ustadz', 'kelas:id,nama'])
            ->where('santri_id', $santri->id)
            ->orderBy('tanggal_setor', 'desc')
            ->get();

        // Organize orang tua data
        $ayah = $santri->orangTuas->where('tipe', 'Ayah')->first();
        $ibu = $santri->orangTuas->where('tipe', 'Ibu')->first();

        $data = [
            'santri' => $santri,
            'ayah' => $ayah,
            'ibu' => $ibu,
            'kesehatan' => $santri->kesehatanSantri,
            'hafalans' => $hafalans,
            'pondokNama' => $santri->pondok?->nama ?? '-',
            'kelasNama' => $kelasNama,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.santri-hafalan', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("Hafalan_Progress_{$santri->nama}_{$santri->nis}.pdf");
    }
}
