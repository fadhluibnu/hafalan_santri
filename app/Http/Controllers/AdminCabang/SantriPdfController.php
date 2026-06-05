<?php

namespace App\Http\Controllers\AdminCabang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\AdminCabang;
use App\Models\Hafalan;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class SantriPdfController extends Controller
{
    /**
     * Generate PDF for a santri
     */
    public function generatePdf(string $nis)
    {
        // Get admin cabang pondok
        $adminCabang = AdminCabang::where('user_id', Auth::id())->first();
        $pondokId = $adminCabang->pondok_id ?? null;

        // Get active tahun ajaran
        $activeTahunAjaran = TahunAjaran::where('pondok_id', $pondokId)
            ->where('is_active', true)
            ->first();

        // Get santri with relations
        $santri = Santri::with([
            'orangTuas',
            'kesehatanSantri',
            'pondok:id,nama',
            'santriKelas' => function($q) use ($activeTahunAjaran) {
                if ($activeTahunAjaran) {
                    $q->where('tahun_ajaran_id', $activeTahunAjaran->id)
                      ->where('status', 'aktif')
                      ->with('kelas:id,nama');
                }
            }
        ])->where('nis', $nis)->firstOrFail();

        // Ensure santri belongs to admin's pondok
        if ($pondokId && $santri->pondok_id !== $pondokId) {
            abort(403, 'Anda tidak berwenang mengakses data santri ini.');
        }

        // Get kelas aktif dari santri_kelas
        $kelasAktif = $santri->santriKelas->first();
        $kelasNama = $kelasAktif ? $kelasAktif->kelas?->nama : '-';

        // Get hafalan/setoran data
        $hafalans = Hafalan::with(['dariSurah', 'sampaiSurah', 'ustadz', 'kelas:id,nama'])
            ->where('santri_id', $santri->id)
            ->orderBy('tanggal_setor', 'desc')
            ->limit(20)
            ->get();

        // Organize orang tua data
        $ayah = $santri->orangTuas->where('tipe', 'Ayah')->first();
        $ibu = $santri->orangTuas->where('tipe', 'Ibu')->first();
        $wali = $santri->orangTuas->where('tipe', 'Wali')->first();

        $data = [
            'santri' => $santri,
            'ayah' => $ayah,
            'ibu' => $ibu,
            'wali' => $wali,
            'kesehatan' => $santri->kesehatanSantri,
            'hafalans' => $hafalans,
            'pondokNama' => $santri->pondok?->nama ?? '-',
            'kelasNama' => $kelasNama,
        ];

        $pdf = Pdf::loadView('pdf.santri', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("Santri_{$santri->nama}_{$santri->nis}.pdf");
    }
}
