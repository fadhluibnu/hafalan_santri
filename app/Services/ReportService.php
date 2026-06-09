<?php

namespace App\Services;

use App\Models\Hafalan;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\SantriKelas;
use App\Models\TahunAjaran;
use App\Models\Ujian;
use App\Models\UjianNilai;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ReportService
{
    public function getDefaultTahunAjaranId(int $pondokId): ?int
    {
        $active = TahunAjaran::query()
            ->where('pondok_id', $pondokId)
            ->where('is_active', true)
            ->value('id');

        if ($active) {
            return (int) $active;
        }

        $latest = TahunAjaran::query()
            ->where('pondok_id', $pondokId)
            ->orderByDesc('tanggal_mulai')
            ->value('id');

        return $latest ? (int) $latest : null;
    }

    public function getFilterOptions(int $pondokId, ?int $tahunAjaranId = null): array
    {
        $tahunAjarans = TahunAjaran::query()
            ->where('pondok_id', $pondokId)
            ->orderByDesc('tanggal_mulai')
            ->get(['id', 'nama', 'semester', 'is_active', 'tanggal_mulai', 'tanggal_selesai']);

        $selectedTahunAjaranId = $tahunAjaranId ?: $this->getDefaultTahunAjaranId($pondokId);

        $kelasQuery = Kelas::query()
            ->where('pondok_id', $pondokId)
            ->orderBy('nama');

        if ($selectedTahunAjaranId) {
            $kelasQuery->where('tahun_ajaran_id', $selectedTahunAjaranId);
        }

        $kelas = $kelasQuery->get(['id', 'nama', 'tahun_ajaran_id']);

        return [
            'tahun_ajarans' => $tahunAjarans,
            'kelas' => $kelas,
            'selected_tahun_ajaran_id' => $selectedTahunAjaranId,
        ];
    }

    public function getSantriList(
        int $pondokId,
        ?int $tahunAjaranId = null,
        ?int $kelasId = null,
        ?string $search = null
    ): Collection {
        $query = SantriKelas::query()
            ->with([
                'santri:id,nis,nama,pondok_id',
                'kelas:id,nama,tahun_ajaran_id',
                'tahunAjaran:id,nama,semester,tanggal_mulai,tanggal_selesai',
            ])
            ->whereHas('santri', function ($q) use ($pondokId) {
                $q->where('pondok_id', $pondokId);
            });

        if ($tahunAjaranId) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        }

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        if ($search) {
            $query->whereHas('santri', function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('nis', 'like', '%' . $search . '%');
            });
        }

        $placements = $query
            ->orderBy('kelas_id')
            ->orderBy('santri_id')
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id')
            ->get();

        if ($placements->isEmpty()) {
            return collect();
        }

        $santriIds = $placements->pluck('santri_id')->unique()->values();

        $hafalanQuery = Hafalan::query()
            ->whereIn('santri_id', $santriIds)
            ->with(['dariSurah:id,name', 'sampaiSurah:id,name'])
            ->orderByDesc('tanggal_setor')
            ->orderByDesc('id');

        if ($kelasId) {
            $hafalanQuery->where('kelas_id', $kelasId);
        }

        if ($tahunAjaranId) {
            $hafalanQuery->whereHas('kelas', function ($q) use ($tahunAjaranId) {
                $q->where('tahun_ajaran_id', $tahunAjaranId);
            });
        }

        $hafalanByPlacement = $hafalanQuery->get()->groupBy(function (Hafalan $hafalan) {
            return $this->placementKey($hafalan->santri_id, $hafalan->kelas_id);
        });

        return $placements->map(function (SantriKelas $placement) use ($hafalanByPlacement) {
            $latestHafalan = $hafalanByPlacement->get($this->placementKey($placement->santri_id, $placement->kelas_id))?->first();

            $range = '-';
            if ($latestHafalan) {
                $dariSurah = $latestHafalan->dariSurah?->name ?? '-';
                $sampaiSurah = $latestHafalan->sampaiSurah?->name ?? '-';
                $range = sprintf(
                    '%s:%s - %s:%s',
                    $dariSurah,
                    $latestHafalan->dari_ayat,
                    $sampaiSurah,
                    $latestHafalan->sampai_ayat
                );
            }

            return [
                'santri_id' => $placement->santri_id,
                'placement_id' => $placement->id,
                'nis' => $placement->santri?->nis,
                'nama' => $placement->santri?->nama,
                'kelas_id' => $placement->kelas_id,
                'kelas_nama' => $placement->kelas?->nama,
                'tahun_ajaran_id' => $placement->tahun_ajaran_id,
                'tahun_ajaran_nama' => $placement->tahunAjaran?->nama,
                'status_penempatan' => $placement->status,
                'tanggal_masuk' => $placement->tanggal_masuk?->format('Y-m-d'),
                'tanggal_keluar' => $placement->tanggal_keluar?->format('Y-m-d'),
                'periode_penempatan' => $this->formatPeriodePenempatan($placement),
                'hafalan_terakhir' => $range,
                'tanggal_hafalan_terakhir' => $latestHafalan?->tanggal_setor?->format('Y-m-d'),
                'nilai_hafalan_terakhir' => $latestHafalan?->nilai ?? '-',
            ];
        })->values();
    }

    public function getUjianList(
        int $pondokId,
        ?int $tahunAjaranId = null,
        ?int $kelasId = null,
        ?string $search = null,
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = Ujian::query()
            ->with(['kelas:id,nama', 'tahunAjaran:id,nama,semester', 'ustadz:id,nama'])
            ->withCount('nilaiSantri')
            ->where('pondok_id', $pondokId);

        if ($tahunAjaranId) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        }

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                    ->orWhereHas('kelas', function ($q2) use ($search) {
                        $q2->where('nama', 'like', '%' . $search . '%');
                    });
            });
        }

        return $query
            ->orderByDesc('tanggal_ujian')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->through(function (Ujian $ujian) {
                return [
                    'id' => $ujian->id,
                    'nama' => $ujian->nama,
                    'tanggal_ujian' => $ujian->tanggal_ujian?->format('Y-m-d'),
                    'kelas_nama' => $ujian->kelas?->nama,
                    'tahun_ajaran_nama' => $ujian->tahunAjaran?->nama,
                    'ustadz_nama' => $ujian->ustadz?->nama,
                    'status' => $ujian->status,
                    'jumlah_peserta' => $ujian->nilai_santri_count ?? 0,
                ];
            });
    }

    public function getUjianDetail(int $pondokId, int $ujianId): Ujian
    {
        $ujian = Ujian::query()
            ->with([
                'kelas:id,nama',
                'tahunAjaran:id,nama,semester',
                'ustadz:id,nama',
                'nilaiSantri' => function ($q) {
                    $q->with('santri:id,nis,nama');
                },
            ])
            ->where('pondok_id', $pondokId)
            ->findOrFail($ujianId);

        $sorted = $ujian->nilaiSantri->sortBy(function ($nilai) {
            return $nilai->santri?->nama ?? '';
        })->values();

        $ujian->setRelation('nilaiSantri', $sorted);

        return $ujian;
    }

    public function getSantriForPrint(
        int $pondokId,
        string $nis,
        ?int $tahunAjaranId = null,
        ?int $kelasId = null
    ): ?array {
        $santri = Santri::query()
            ->with(['pondok:id,nama', 'orangTuas', 'kesehatanSantri'])
            ->where('pondok_id', $pondokId)
            ->where('nis', $nis)
            ->first();

        if (!$santri) {
            return null;
        }

        $placementQuery = SantriKelas::query()
            ->with(['kelas:id,nama,tahun_ajaran_id', 'tahunAjaran:id,nama,semester,tanggal_mulai,tanggal_selesai'])
            ->where('santri_id', $santri->id);

        if ($tahunAjaranId) {
            $placementQuery->where('tahun_ajaran_id', $tahunAjaranId);
        }

        if ($kelasId) {
            $placementQuery->where('kelas_id', $kelasId);
        }

        $placement = $placementQuery
            ->orderByDesc('tahun_ajaran_id')
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id')
            ->first();

        if (!$placement) {
            return null;
        }

        $hafalanQuery = Hafalan::query()
            ->with(['dariSurah:id,name', 'sampaiSurah:id,name', 'ustadz:id,nama'])
            ->where('santri_id', $santri->id)
            ->orderByDesc('tanggal_setor');

        if ($placement->kelas_id) {
            $hafalanQuery->where('kelas_id', $placement->kelas_id);
        } elseif ($placement->tahun_ajaran_id) {
            $hafalanQuery->whereHas('kelas', function ($q) use ($placement) {
                $q->where('tahun_ajaran_id', $placement->tahun_ajaran_id);
            });
        }

        return [
            'santri' => $santri,
            'placement' => $placement,
            'hafalans' => $hafalanQuery->get(),
        ];
    }

    public function getRaportData(
        int $pondokId,
        string $nis,
        ?int $tahunAjaranId = null,
        ?int $kelasId = null
    ): ?array {
        $printData = $this->getSantriForPrint($pondokId, $nis, $tahunAjaranId, $kelasId);
        if (!$printData) {
            return null;
        }

        /** @var Santri $santri */
        $santri = $printData['santri'];
        /** @var SantriKelas $placement */
        $placement = $printData['placement'];

        $ujianNilaiQuery = UjianNilai::query()
            ->with('ujian')
            ->where('santri_id', $santri->id)
            ->whereHas('ujian', function ($q) use ($pondokId, $placement) {
                $q->where('pondok_id', $pondokId)
                    ->where('tahun_ajaran_id', $placement->tahun_ajaran_id)
                    ->where('kelas_id', $placement->kelas_id);
            });

        $ujianNilais = $ujianNilaiQuery->get()->sortBy(function (UjianNilai $nilai) {
            return $nilai->ujian?->tanggal_ujian;
        })->values();

        return [
            ...$printData,
            'ujian_nilais' => $ujianNilais,
        ];
    }

    private function placementKey(int $santriId, int $kelasId): string
    {
        return $santriId . ':' . $kelasId;
    }

    private function formatPeriodePenempatan(SantriKelas $placement): string
    {
        $masuk = $placement->tanggal_masuk?->format('Y-m-d') ?? '-';
        $keluar = $placement->tanggal_keluar?->format('Y-m-d') ?? 'sekarang';

        return $masuk . ' - ' . $keluar;
    }
}
