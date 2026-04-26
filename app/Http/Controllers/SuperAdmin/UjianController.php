<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Concerns\ResolvesPondokScope;
use App\Http\Controllers\Controller;
use App\Models\Ujian;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UjianController extends Controller
{
    use ResolvesPondokScope;

    public function __construct(private readonly ReportService $reportService)
    {
    }

    public function index(Request $request)
    {
        $scope = $this->resolvePondokScope($request);
        $pondokId = $scope['pondok_id'];

        $filters = [
            'pondok_id' => $pondokId,
            'search' => $request->input('search', ''),
            'tahun_ajaran_id' => $request->input('tahun_ajaran_id') ? (int) $request->input('tahun_ajaran_id') : null,
            'kelas_id' => $request->input('kelas_id') ? (int) $request->input('kelas_id') : null,
        ];

        $tahunAjarans = collect();
        $kelasOptions = collect();
        $ujians = null;

        if ($pondokId) {
            $options = $this->reportService->getFilterOptions($pondokId, $filters['tahun_ajaran_id']);
            if (!$filters['tahun_ajaran_id'] && !empty($options['selected_tahun_ajaran_id'])) {
                $filters['tahun_ajaran_id'] = (int) $options['selected_tahun_ajaran_id'];
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

        return Inertia::render('Ujian/Index', [
            'authRole' => 'super_admin',
            'isReadOnly' => true,
            'baseUrl' => '/super-admin',
            'routePrefix' => 'super-admin',
            'filters' => $filters,
            'pondokOptions' => $scope['pondok_options'],
            'requiresPondokSelection' => true,
            'tahunAjarans' => $tahunAjarans,
            'kelasOptions' => $kelasOptions,
            'ujians' => $ujians,
        ]);
    }

    public function show(Request $request, string $id)
    {
        $scope = $this->resolvePondokScope($request);
        $pondokId = $scope['pondok_id'];

        if (!$pondokId) {
            return redirect()
                ->route('super-admin.ujian.index')
                ->with('error', 'Pilih pondok terlebih dahulu.');
        }

        $ujian = $this->reportService->getUjianDetail($pondokId, (int) $id);

        return Inertia::render('Ujian/Show', [
            'authRole' => 'super_admin',
            'isReadOnly' => true,
            'baseUrl' => '/super-admin',
            'routePrefix' => 'super-admin',
            'queryPondokId' => $pondokId,
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
