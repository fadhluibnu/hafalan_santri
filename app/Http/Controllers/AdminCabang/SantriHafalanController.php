<?php

namespace App\Http\Controllers\AdminCabang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\Hafalan;
use App\Models\AdminCabang;
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

        $santri = Santri::with(['kelas', 'pondok', 'orangTuas'])
            ->where('nis', $nis)
            ->firstOrFail();

        if ($pondokId && $santri->pondok_id !== $pondokId) {
            abort(403, 'Anda tidak berwenang mengakses data ini.');
        }

        // Get all hafalan for this santri
        $hafalans = Hafalan::with(['dariSurah', 'sampaiSurah', 'ustadz'])
            ->where('santri_id', $santri->id)
            ->orderBy('tanggal_setor', 'desc')
            ->get()
            ->map(function ($h) {
                return [
                    'id' => $h->id,
                    'tanggal_setor' => $h->tanggal_setor,
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

        return Inertia::render('AdminCabang/Santri/Hafalan', [
            'santri' => [
                'id' => $santri->id,
                'nis' => $santri->nis,
                'nama' => $santri->nama,
                'kelas' => $santri->kelas?->nama ?? '-',
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

        $santri = Santri::with(['kelas', 'pondok', 'orangTuas', 'kesehatanSantri'])
            ->where('nis', $nis)
            ->firstOrFail();

        if ($pondokId && $santri->pondok_id !== $pondokId) {
            abort(403, 'Anda tidak berwenang mengakses data ini.');
        }

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
            'kelasNama' => $santri->kelas?->nama ?? '-',
        ];

        $pdf = Pdf::loadView('pdf.santri-hafalan', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("Hafalan_Progress_{$santri->nama}_{$santri->nis}.pdf");
    }
}
