<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Ustadz;
use App\Models\Ujian;
use App\Services\ReportService;
use App\Services\UjianService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UjianController extends Controller
{
    public function __construct(
        private readonly UjianService $ujianService,
        private readonly ReportService $reportService
    ) {
    }

    public function index(Request $request)
    {
        $ustadz = Ustadz::where('user_id', Auth::id())->firstOrFail();
        $pondokId = (int) $ustadz->pondok_id;

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
            'authRole' => 'ustadz',
            'isReadOnly' => false,
            'baseUrl' => '/ustadz',
            'routePrefix' => 'ustadz',
            'filters' => $filters,
            'tahunAjarans' => $options['tahun_ajarans'],
            'kelasOptions' => $options['kelas'],
            'ujians' => $ujians,
        ]);
    }

    public function create()
    {
        $ustadz = Ustadz::where('user_id', Auth::id())->firstOrFail();
        $pondokId = (int) $ustadz->pondok_id;

        $defaultTahunAjaranId = $this->reportService->getDefaultTahunAjaranId($pondokId);

        $kelasQuery = Kelas::query()
            ->where('pondok_id', $pondokId)
            ->orderBy('nama');

        if ($defaultTahunAjaranId) {
            $kelasQuery->where('tahun_ajaran_id', $defaultTahunAjaranId);
        }

        return Inertia::render('Ujian/Create', [
            'baseUrl' => '/ustadz',
            'routePrefix' => 'ustadz',
            'classes' => $kelasQuery->get(['id', 'nama', 'tahun_ajaran_id']),
        ]);
    }

    public function store(Request $request)
    {
        $ustadz = Ustadz::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kelas_id' => 'required|integer|exists:kelas,id',
            'tanggal_ujian' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $ujian = $this->ujianService->createUjian(
            $ustadz,
            (int) $validated['kelas_id'],
            $validated['nama'],
            $validated['tanggal_ujian'],
            $validated['keterangan'] ?? null
        );

        return redirect()
            ->route('ustadz.ujian.show', $ujian->id)
            ->with('success', 'Ujian berhasil dibuat. Silakan input nilai santri.');
    }

    public function show(string $id)
    {
        $ustadz = Ustadz::where('user_id', Auth::id())->firstOrFail();
        $pondokId = (int) $ustadz->pondok_id;

        $ujian = $this->reportService->getUjianDetail($pondokId, (int) $id);

        return Inertia::render('Ujian/Show', [
            'authRole' => 'ustadz',
            'isReadOnly' => false,
            'baseUrl' => '/ustadz',
            'routePrefix' => 'ustadz',
            'ujian' => $this->transformUjian($ujian),
        ]);
    }

    public function updateNilai(Request $request, string $id)
    {
        $ustadz = Ustadz::where('user_id', Auth::id())->firstOrFail();
        $pondokId = (int) $ustadz->pondok_id;

        $ujian = Ujian::query()
            ->where('pondok_id', $pondokId)
            ->findOrFail((int) $id);

        $validated = $request->validate([
            'nilai' => 'required|array',
            'nilai.*.id' => 'required|integer|exists:ujian_nilais,id',
            'nilai.*.nilai_label' => 'nullable|string|max:20',
            'nilai.*.catatan' => 'nullable|string',
        ]);

        $this->ujianService->updateNilai($ujian, $validated['nilai']);

        return back()->with('success', 'Nilai ujian berhasil disimpan.');
    }

    public function updateStatus(Request $request, string $id)
    {
        $ustadz = Ustadz::where('user_id', Auth::id())->firstOrFail();
        $pondokId = (int) $ustadz->pondok_id;

        $ujian = Ujian::query()
            ->where('pondok_id', $pondokId)
            ->findOrFail((int) $id);

        $validated = $request->validate([
            'status' => 'required|in:draft,selesai',
        ]);

        $this->ujianService->updateStatus($ujian, $validated['status']);

        return back()->with('success', 'Status ujian berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $ustadz = Ustadz::where('user_id', Auth::id())->firstOrFail();
        $pondokId = (int) $ustadz->pondok_id;

        $ujian = Ujian::query()
            ->where('pondok_id', $pondokId)
            ->findOrFail((int) $id);

        $ujian->delete();

        return redirect()
            ->route('ustadz.ujian.index')
            ->with('success', 'Ujian berhasil dihapus.');
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
