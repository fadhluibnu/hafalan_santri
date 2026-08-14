<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesPondokScope;
use App\Models\Ujian;
use App\Services\LaporanWaliService;
use App\Services\ReportService;
use App\Support\LaporanWaliLayout;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    use ResolvesPondokScope;

    public function __construct(
        private readonly ReportService $reportService,
        private readonly LaporanWaliService $laporanWaliService,
    ) {
    }

    public function daftarSantri(Request $request)
    {
        $roleContext = $this->resolveRoleContext($request);
        $scope = $this->resolvePondokScope($request);
        $pondokId = $scope['pondok_id'];

        $filters = $this->buildCommonFilters($request, $pondokId);

        $tahunAjarans = collect();
        $kelasOptions = collect();
        $rows = collect();

        if ($pondokId) {
            $options = $this->reportService->getFilterOptions($pondokId, $filters['tahun_ajaran_id']);
            if (!$filters['tahun_ajaran_id']) {
                $filters['tahun_ajaran_id'] = $options['selected_tahun_ajaran_id'];
            }

            $tahunAjarans = $options['tahun_ajarans'];
            $kelasOptions = $options['kelas'];
            $rows = $this->reportService->getSantriList(
                $pondokId,
                $filters['tahun_ajaran_id'],
                $filters['kelas_id'],
                $filters['search']
            );
        }

        return Inertia::render('Reports/DaftarSantri', [
            ...$roleContext,
            'filters' => $filters,
            'rows' => $rows,
            'pondokOptions' => $scope['pondok_options'],
            'requiresPondokSelection' => $scope['requires_pondok_selection'],
            'tahunAjarans' => $tahunAjarans,
            'kelasOptions' => $kelasOptions,
        ]);
    }

    public function cetakSantri(Request $request)
    {
        $roleContext = $this->resolveRoleContext($request);
        $scope = $this->resolvePondokScope($request);
        $pondokId = $scope['pondok_id'];

        $filters = $this->buildCommonFilters($request, $pondokId);

        $tahunAjarans = collect();
        $kelasOptions = collect();
        $rows = collect();

        if ($pondokId) {
            $options = $this->reportService->getFilterOptions($pondokId, $filters['tahun_ajaran_id']);
            if (!$filters['tahun_ajaran_id']) {
                $filters['tahun_ajaran_id'] = $options['selected_tahun_ajaran_id'];
            }

            $tahunAjarans = $options['tahun_ajarans'];
            $kelasOptions = $options['kelas'];
            $rows = $this->reportService->getSantriList(
                $pondokId,
                $filters['tahun_ajaran_id'],
                $filters['kelas_id'],
                $filters['search']
            );
        }

        return Inertia::render('Reports/CetakSantri', [
            ...$roleContext,
            'filters' => $filters,
            'rows' => $rows,
            'pondokOptions' => $scope['pondok_options'],
            'requiresPondokSelection' => $scope['requires_pondok_selection'],
            'tahunAjarans' => $tahunAjarans,
            'kelasOptions' => $kelasOptions,
        ]);
    }

    public function cetakSantriPdf(Request $request, string $nis)
    {
        $scope = $this->resolvePondokScope($request);
        $pondokId = $scope['pondok_id'];

        if (!$pondokId) {
            abort(422, 'Pilih pondok terlebih dahulu.');
        }

        $tahunAjaranId = $request->input('tahun_ajaran_id') ? (int) $request->input('tahun_ajaran_id') : null;
        $kelasId = $request->input('kelas_id') ? (int) $request->input('kelas_id') : null;

        $data = $this->reportService->getSantriForPrint($pondokId, $nis, $tahunAjaranId, $kelasId);
        if (!$data) {
            abort(404, 'Data santri pada filter yang dipilih tidak ditemukan.');
        }

        $santri = $data['santri'];
        $placement = $data['placement'];
        $hafalans = $data['hafalans'];

        $ayah = $santri->orangTuas->where('tipe', 'Ayah')->first();
        $ibu = $santri->orangTuas->where('tipe', 'Ibu')->first();
        $wali = $santri->orangTuas->where('tipe', 'Wali')->first();

        $pdf = Pdf::loadView('pdf.santri-filtered', [
            'santri' => $santri,
            'ayah' => $ayah,
            'ibu' => $ibu,
            'wali' => $wali,
            'kesehatan' => $santri->kesehatanSantri,
            'hafalans' => $hafalans,
            'pondokNama' => $santri->pondok?->nama ?? '-',
            'kelasNama' => $placement->kelas?->nama ?? '-',
            'tahunAjaranNama' => $placement->tahunAjaran?->nama ?? '-',
        ])->setPaper('A4', 'portrait');

        return $pdf->download("Santri_{$santri->nama}_{$santri->nis}.pdf");
    }

    public function laporanUjian(Request $request)
    {
        $roleContext = $this->resolveRoleContext($request);
        $scope = $this->resolvePondokScope($request);
        $pondokId = $scope['pondok_id'];

        $filters = $this->buildCommonFilters($request, $pondokId);

        $tahunAjarans = collect();
        $kelasOptions = collect();
        $ujians = null;

        if ($pondokId) {
            $options = $this->reportService->getFilterOptions($pondokId, $filters['tahun_ajaran_id']);
            if (!$filters['tahun_ajaran_id']) {
                $filters['tahun_ajaran_id'] = $options['selected_tahun_ajaran_id'];
            }

            $tahunAjarans = $options['tahun_ajarans'];
            $kelasOptions = $options['kelas'];
            $ujians = $this->reportService->getUjianList(
                $pondokId,
                $filters['tahun_ajaran_id'],
                $filters['kelas_id'],
                $filters['search']
            )->appends($request->only(['pondok_id', 'search', 'tahun_ajaran_id', 'kelas_id']));
        }

        return Inertia::render('Reports/LaporanUjian', [
            ...$roleContext,
            'filters' => $filters,
            'pondokOptions' => $scope['pondok_options'],
            'requiresPondokSelection' => $scope['requires_pondok_selection'],
            'tahunAjarans' => $tahunAjarans,
            'kelasOptions' => $kelasOptions,
            'ujians' => $ujians,
        ]);
    }

    public function laporanUjianDetail(Request $request, string $id)
    {
        $roleContext = $this->resolveRoleContext($request);
        $scope = $this->resolvePondokScope($request);
        $pondokId = $scope['pondok_id'];

        if (!$pondokId) {
            return redirect($roleContext['baseUrl'] . '/laporan/ujian')
                ->with('error', 'Pilih pondok terlebih dahulu.');
        }

        $ujian = $this->reportService->getUjianDetail($pondokId, (int) $id);

        return Inertia::render('Reports/LaporanUjianDetail', [
            ...$roleContext,
            'query' => $request->only(['pondok_id', 'search', 'tahun_ajaran_id', 'kelas_id']),
            'ujian' => $this->transformUjian($ujian),
        ]);
    }

    public function laporanUjianPdf(Request $request, string $id)
    {
        $scope = $this->resolvePondokScope($request);
        $pondokId = $scope['pondok_id'];

        if (!$pondokId) {
            abort(422, 'Pilih pondok terlebih dahulu.');
        }

        $ujian = $this->reportService->getUjianDetail($pondokId, (int) $id);
        $transformed = $this->transformUjian($ujian);

        $pdf = Pdf::loadView('pdf.ujian-detail', [
            'ujian' => $transformed,
        ])->setPaper('A4', 'portrait');

        return $pdf->download('Laporan_Ujian_' . $ujian->nama . '.pdf');
    }

    public function raport(Request $request)
    {
        $roleContext = $this->resolveRoleContext($request);
        $scope = $this->resolvePondokScope($request);
        $pondokId = $scope['pondok_id'];

        $filters = $this->buildCommonFilters($request, $pondokId);

        $tahunAjarans = collect();
        $kelasOptions = collect();
        $rows = collect();

        if ($pondokId) {
            $options = $this->reportService->getFilterOptions($pondokId, $filters['tahun_ajaran_id']);
            if (!$filters['tahun_ajaran_id']) {
                $filters['tahun_ajaran_id'] = $options['selected_tahun_ajaran_id'];
            }

            $tahunAjarans = $options['tahun_ajarans'];
            $kelasOptions = $options['kelas'];
            $rows = $this->reportService->getSantriList(
                $pondokId,
                $filters['tahun_ajaran_id'],
                $filters['kelas_id'],
                $filters['search']
            );
        }

        return Inertia::render('Reports/Raport', [
            ...$roleContext,
            'filters' => $filters,
            'rows' => $rows,
            'pondokOptions' => $scope['pondok_options'],
            'requiresPondokSelection' => $scope['requires_pondok_selection'],
            'tahunAjarans' => $tahunAjarans,
            'kelasOptions' => $kelasOptions,
        ]);
    }
    public function raportPreview(Request $request, string $nis)
    {
        $scope = $this->resolvePondokScope($request);
        $pondokId = $scope['pondok_id'];
        $roleContext = $this->resolveRoleContext($request);

        if (!$pondokId) {
            abort(422, 'Pilih pondok terlebih dahulu.');
        }

        $tahunAjaranId = $request->input('tahun_ajaran_id') ? (int) $request->input('tahun_ajaran_id') : null;
        $kelasId = $request->input('kelas_id') ? (int) $request->input('kelas_id') : null;

        $data = $this->reportService->getRaportData($pondokId, $nis, $tahunAjaranId, $kelasId);
        if (!$data) {
            abort(404, 'Data raport tidak ditemukan untuk filter yang dipilih.');
        }

        return inertia('Reports/RaportPreview', [
            'authRole' => $roleContext['authRole'],
            'baseUrl' => $roleContext['baseUrl'],
            'santri' => $data['santri'],
            'placement' => $data['placement'],
            'hafalans' => $data['hafalans'],
            'ujianNilais' => $data['ujian_nilais'],
            'pondokNama' => $data['santri']->pondok?->nama ?? '-',
            'filters' => $request->only(['tahun_ajaran_id', 'kelas_id']),
        ]);
    }


    public function raportPdf(Request $request, string $nis)
    {
        $scope = $this->resolvePondokScope($request);
        $pondokId = $scope['pondok_id'];

        if (!$pondokId) {
            abort(422, 'Pilih pondok terlebih dahulu.');
        }

        $tahunAjaranId = $request->input('tahun_ajaran_id') ? (int) $request->input('tahun_ajaran_id') : null;
        $kelasId = $request->input('kelas_id') ? (int) $request->input('kelas_id') : null;

        $data = $this->reportService->getRaportData($pondokId, $nis, $tahunAjaranId, $kelasId);
        if (!$data) {
            abort(404, 'Data raport tidak ditemukan untuk filter yang dipilih.');
        }

        $santri = $data['santri'];
        $placement = $data['placement'];
        $hafalans = $data['hafalans'];
        $ujianNilais = $data['ujian_nilais'];

        $pdf = Pdf::loadView('pdf.raport-santri', [
            'santri' => $santri,
            'placement' => $placement,
            'hafalans' => $hafalans,
            'ujianNilais' => $ujianNilais,
            'pondokNama' => $santri->pondok?->nama ?? '-',
            'keterangan' => $request->input('keterangan'),
        ])->setPaper('A4', 'portrait');

        return $pdf->download("Raport_{$santri->nama}_{$santri->nis}.pdf");
    }

    /**
     * Halaman pemilihan santri untuk "Laporan Perkembangan Santri" (wali/orang tua).
     */
    public function laporanWali(Request $request)
    {
        $roleContext = $this->resolveRoleContext($request);
        $this->pastikanBolehAksesLaporanWali($request);

        $scope = $this->resolvePondokScope($request);
        $pondokId = $scope['pondok_id'];

        $filters = $this->buildCommonFilters($request, $pondokId);
        $filters['bulan_akhir'] = $this->normalisasiBulan($request->input('bulan_akhir'));

        $tahunAjarans = collect();
        $kelasOptions = collect();
        $rows = collect();

        if ($pondokId) {
            $options = $this->reportService->getFilterOptions($pondokId, $filters['tahun_ajaran_id']);
            if (!$filters['tahun_ajaran_id']) {
                $filters['tahun_ajaran_id'] = $options['selected_tahun_ajaran_id'];
            }

            $tahunAjarans = $options['tahun_ajarans'];
            $kelasOptions = $options['kelas'];
            $rows = $this->reportService->getSantriList(
                $pondokId,
                $filters['tahun_ajaran_id'],
                $filters['kelas_id'],
                $filters['search']
            );
        }

        return Inertia::render('Reports/LaporanWali', [
            ...$roleContext,
            'filters' => $filters,
            'rows' => $rows,
            'pondokOptions' => $scope['pondok_options'],
            'requiresPondokSelection' => $scope['requires_pondok_selection'],
            'tahunAjarans' => $tahunAjarans,
            'kelasOptions' => $kelasOptions,
            // Periode laporan selalu 7 bulan karena grid tabelnya bagian dari gambar latar.
            'jumlahBulan' => LaporanWaliService::JUMLAH_BULAN,
        ]);
    }

    /**
     * Unduh "Laporan Perkembangan Santri" sebagai PDF.
     */
    public function laporanWaliPdf(Request $request, string $nis)
    {
        $this->pastikanBolehAksesLaporanWali($request);

        $scope = $this->resolvePondokScope($request);
        $pondokId = $scope['pondok_id'];

        if (!$pondokId) {
            abort(422, 'Pilih pondok terlebih dahulu.');
        }

        $bulanAkhir = $this->normalisasiBulan($request->input('bulan_akhir'));

        $data = $this->laporanWaliService->getLaporanData($pondokId, $nis, $bulanAkhir);
        if (!$data) {
            abort(404, 'Data santri tidak ditemukan pada pondok ini.');
        }

        $background = public_path(config('laporan.background'));
        if (!is_file($background)) {
            // Tanpa gambar latar, PDF hanya berisi teks mengambang tanpa kop dan
            // tanpa grid tabel. Lebih baik gagal terang-terangan daripada
            // menghasilkan laporan yang tampak rusak.
            abort(500, 'Gambar latar template laporan tidak ditemukan: ' . config('laporan.background'));
        }

        $pdf = Pdf::loadView('pdf.laporan-wali', [
            'santri' => $data['santri'],
            'wali' => $data['wali'],
            'pondokNama' => $data['pondok_nama'],
            'baris' => $data['baris'],
            'periode' => $data['periode'],
            'footer' => config('laporan.footer'),
            'backgroundPath' => $background,
        ])->setPaper('A4', 'portrait');

        $namaFile = sprintf(
            'Laporan Perkembangan Santri - %s (%s).pdf',
            $data['santri']->nama,
            $data['periode']['bulan_akhir']
        );

        return $pdf->download($namaFile);
    }

    /**
     * Laporan ini hanya untuk super admin dan admin cabang.
     *
     * Route-nya memang tidak didaftarkan pada grup ustadz, tapi pemeriksaan ini
     * tetap ada supaya pembatasannya tidak bergantung pada susunan route saja.
     */
    private function pastikanBolehAksesLaporanWali(Request $request): void
    {
        if (!in_array($request->user()?->role, ['super_admin', 'admin_cabang'], true)) {
            abort(403, 'Role Anda tidak berhak mengakses laporan wali.');
        }
    }

    /**
     * Terima bulan hanya dalam format 'Y-m'; nilai lain diabaikan agar
     * penyusunan periode memakai default (bulan berjalan).
     */
    private function normalisasiBulan($bulan): ?string
    {
        $bulan = is_string($bulan) ? trim($bulan) : '';

        return preg_match('/^\d{4}-\d{2}$/', $bulan) ? $bulan : null;
    }

    private function resolveRoleContext(Request $request): array
    {
        $user = $request->user();

        return match ($user?->role) {
            'ustadz' => [
                'authRole' => 'ustadz',
                'baseUrl' => '/ustadz',
                'routePrefix' => 'ustadz',
            ],
            'admin_cabang' => [
                'authRole' => 'admin_cabang',
                'baseUrl' => '/admin-cabang',
                'routePrefix' => 'admin-cabang',
            ],
            'super_admin' => [
                'authRole' => 'super_admin',
                'baseUrl' => '/super-admin',
                'routePrefix' => 'super-admin',
            ],
            default => abort(403, 'Role tidak didukung.'),
        };
    }

    private function buildCommonFilters(Request $request, ?int $pondokId): array
    {
        return [
            'pondok_id' => $pondokId,
            'search' => $request->input('search', ''),
            'tahun_ajaran_id' => $request->input('tahun_ajaran_id') ? (int) $request->input('tahun_ajaran_id') : null,
            'kelas_id' => $request->input('kelas_id') ? (int) $request->input('kelas_id') : null,
        ];
    }

    private function transformUjian(Ujian $ujian): array
    {
        return [
            'id' => $ujian->id,
            'nama' => $ujian->nama,
            'tanggal_ujian' => $ujian->tanggal_ujian?->format('Y-m-d'),
            'status' => $ujian->status,
            'keterangan' => $ujian->keterangan,
            'kelas' => $ujian->kelas?->nama,
            'tahun_ajaran' => $ujian->tahunAjaran?->nama,
            'ustadz' => $ujian->ustadz?->nama,
            'skema_snapshot' => $ujian->skema_snapshot,
            'nilai' => $ujian->nilaiSantri->map(function ($nilai) {
                return [
                    'id' => $nilai->id,
                    'santri_id' => $nilai->santri_id,
                    'nis' => $nilai->santri?->nis,
                    'nama' => $nilai->santri?->nama,
                    'nilai_angka' => $nilai->nilai_angka,
                    'nilai_label' => $nilai->nilai_label,
                    'catatan' => $nilai->catatan,
                ];
            })->values(),
        ];
    }
}
