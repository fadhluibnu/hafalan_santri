<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\Santri;
use App\Models\SantriKelas;
use Illuminate\Support\Carbon;

class SantriPlacementService
{
    public function assignToClass(Santri $santri, Kelas $kelas, string $previousStatus = 'pindah', mixed $date = null): SantriKelas
    {
        if (!$kelas->tahun_ajaran_id) {
            throw new \InvalidArgumentException('Kelas belum terhubung ke tahun ajaran.');
        }

        $effectiveDate = $this->dateString($date);

        SantriKelas::query()
            ->where('santri_id', $santri->id)
            ->where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
            ->where('status', 'aktif')
            ->where('kelas_id', '!=', $kelas->id)
            ->update([
                'status' => $previousStatus,
                'tanggal_keluar' => $effectiveDate,
            ]);

        $activeSameClass = SantriKelas::query()
            ->where('santri_id', $santri->id)
            ->where('kelas_id', $kelas->id)
            ->where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
            ->where('status', 'aktif')
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id')
            ->get();

        if ($activeSameClass->isNotEmpty()) {
            $activeSameClass->skip(1)->each(function (SantriKelas $duplicate) use ($previousStatus, $effectiveDate) {
                $duplicate->update([
                    'status' => $previousStatus,
                    'tanggal_keluar' => $effectiveDate,
                ]);
            });

            $this->syncLegacyKelas($santri, $kelas->id);

            return $activeSameClass->first();
        }

        $placement = SantriKelas::create([
            'santri_id' => $santri->id,
            'kelas_id' => $kelas->id,
            'tahun_ajaran_id' => $kelas->tahun_ajaran_id,
            'status' => 'aktif',
            'tanggal_masuk' => $effectiveDate,
        ]);

        $this->syncLegacyKelas($santri, $kelas->id);

        return $placement;
    }

    public function closeActivePlacement(SantriKelas $placement, string $status = 'pindah', mixed $date = null): void
    {
        if ($placement->status !== 'aktif') {
            return;
        }

        $placement->update([
            'status' => $status,
            'tanggal_keluar' => $this->dateString($date),
        ]);

        $this->syncLegacyAfterClose($placement);
    }

    public function syncClassRoster(Kelas $kelas, array $santriIds): void
    {
        $incoming = collect($santriIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        $currentPlacements = SantriKelas::query()
            ->where('kelas_id', $kelas->id)
            ->where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
            ->where('status', 'aktif')
            ->get();

        $currentPlacements
            ->whereNotIn('santri_id', $incoming->all())
            ->each(fn (SantriKelas $placement) => $this->closeActivePlacement($placement));

        if ($incoming->isEmpty()) {
            return;
        }

        $santris = Santri::query()
            ->whereIn('id', $incoming->all())
            ->get()
            ->keyBy('id');

        foreach ($incoming as $santriId) {
            $santri = $santris->get($santriId);
            if ($santri) {
                $this->assignToClass($santri, $kelas);
            }
        }
    }

    private function syncLegacyKelas(Santri $santri, ?int $kelasId): void
    {
        if ((int) $santri->kelas_id === (int) $kelasId) {
            return;
        }

        $santri->forceFill(['kelas_id' => $kelasId])->save();
    }

    private function syncLegacyAfterClose(SantriKelas $placement): void
    {
        $santri = $placement->santri ?: Santri::query()->find($placement->santri_id);
        if (!$santri || (int) $santri->kelas_id !== (int) $placement->kelas_id) {
            return;
        }

        $nextActivePlacement = SantriKelas::query()
            ->where('santri_id', $placement->santri_id)
            ->where('status', 'aktif')
            ->orderByDesc('tahun_ajaran_id')
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id')
            ->first();

        $this->syncLegacyKelas($santri, $nextActivePlacement?->kelas_id);
    }

    private function dateString(mixed $date): string
    {
        return $date ? Carbon::parse($date)->toDateString() : now()->toDateString();
    }
}
