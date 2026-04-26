<?php

namespace App\Http\Controllers\AdminCabang;

use App\Http\Controllers\Controller;
use App\Models\AdminCabang;
use App\Models\Ujian;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UjianController extends Controller
{
    public function __construct(private readonly ReportService $reportService)
    {
    }

    public function index(Request $request)
    {
        $adminCabang = AdminCabang::where('user_id', Auth::id())->firstOrFail();
        $pondokId = (int) $adminCabang->pondok_id;

        $filters = [
            'search' => $request->input('search', ''),
            'tahun_ajaran_id' => $request->input('tahun_ajaran_id') ? (int) $request->input('tahun_ajaran_id') : null,
            'kelas_id' => $request->input('kelas_id') ? (int) $request->input('kelas_id') : null,
        ];

        $options = $this->reportService->getFilterOptions($pondokId, $filters['tahun_ajaran_id']);
        if (!$filters['tahun_ajaran_id'] && !empty($options['selected_tahun_ajaran_id'])) {
            $filters['tahun_ajaran_id'] = (int) $options['selected_tahun_ajaran_id'];
        }

        $ujians = $this->reportService->getUjianList(
            $pondokId,
            $filters['tahun_ajaran_id'],
            $filters['kelas_id'],
            $filters['search']
        )->appends($request->only(['search', 'tahun_ajaran_id', 'kelas_id']));

        return Inertia::render('Ujian/Index', [
            'authRole' => 'admin_cabang',
            'isReadOnly' => true,
            'baseUrl' => '/admin-cabang',
            'routePrefix' => 'admin-cabang',
            'filters' => $filters,
            'tahunAjarans' => $options['tahun_ajarans'],
            'kelasOptions' => $options['kelas'],
            'ujians' => $ujians,
        ]);
    }

    public function show(string $id)
    {
        $adminCabang = AdminCabang::where('user_id', Auth::id())->firstOrFail();
        $pondokId = (int) $adminCabang->pondok_id;

        $ujian = $this->reportService->getUjianDetail($pondokId, (int) $id);

        return Inertia::render('Ujian/Show', [
            'authRole' => 'admin_cabang',
            'isReadOnly' => true,
            'baseUrl' => '/admin-cabang',
            'routePrefix' => 'admin-cabang',
            'ujian' => $this->transformUjian($ujian),
        ]);
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
