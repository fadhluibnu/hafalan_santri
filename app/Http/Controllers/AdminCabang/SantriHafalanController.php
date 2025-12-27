<?php

namespace App\Http\Controllers\AdminCabang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\Hafalan;
use App\Models\AdminCabang;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class SantriHafalanController extends Controller
{
    /**
     * Show hafalan progress for a santri
     */
    public function index(string $nis)
    {
        $adminCabang = AdminCabang::where('user_id', Auth::id())->first();
        $pondokId = $adminCabang->pondok_id ?? null;

        // Get active tahun ajaran
        $activeTahunAjaran = TahunAjaran::where('pondok_id', $pondokId)
            ->where('is_active', true)
            ->first();

        $santri = Santri::with([
            'pondok',
            'orangTuas',
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
        $hafalans = Hafalan::with(['dariSurah', 'sampaiSurah', 'ustadz'])
            ->where('santri_id', $santri->id)
            ->orderBy('tanggal_setor', 'desc')
            ->get()
            ->map(function ($h) {
                return [
                    'id' => $h->id,
                    'tanggal_setor' => $h->tanggal_setor?->format('Y-m-d'),
                    'juz' => $h->juz,
                    'dari_surat' => $h->dariSurah->nama ?? '-',
                    'dari_ayat' => $h->dari_ayat,
                    'sampai_surat' => $h->sampaiSurah->nama ?? '-',
                    'sampai_ayat' => $h->sampai_ayat,
                    'kategori' => $h->kategori,
                    'nilai' => $h->nilai,
                    'catatan' => $h->catatan,
                    'ustadz' => $h->ustadz->nama ?? '-',
                ];
            });

        return Inertia::render('AdminCabang/Santri/Hafalan', [
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
        $adminCabang = AdminCabang::where('user_id', Auth::id())->first();
        $pondokId = $adminCabang->pondok_id ?? null;

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
        $hafalans = Hafalan::with(['dariSurah', 'sampaiSurah', 'ustadz'])
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

        $pdf = Pdf::loadView('pdf.santri-hafalan', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("Hafalan_Progress_{$santri->nama}_{$santri->nis}.pdf");
    }
}
