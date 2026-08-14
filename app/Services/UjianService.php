<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\SantriKelas;
use App\Models\SkemaPenilaian;
use App\Models\Ujian;
use App\Models\UjianNilai;
use App\Models\Ustadz;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UjianService
{
    public function createUjian(
        Ustadz $ustadz,
        int $kelasId,
        string $nama,
        string $tanggalUjian,
        ?string $keterangan = null
    ): Ujian {
        $pondokId = (int) $ustadz->pondok_id;

        $kelas = Kelas::query()->findOrFail($kelasId);
        if ((int) $kelas->pondok_id !== $pondokId) {
            throw ValidationException::withMessages([
                'kelas_id' => 'Kelas tidak berada di pondok Anda.',
            ]);
        }

        if (!$kelas->tahun_ajaran_id) {
            throw ValidationException::withMessages([
                'kelas_id' => 'Kelas belum terhubung ke tahun ajaran.',
            ]);
        }

        $skema = SkemaPenilaian::activeForPondok($pondokId);
        if (!$skema) {
            throw ValidationException::withMessages([
                'skema' => 'Skema penilaian aktif untuk pondok ini belum diatur.',
            ]);
        }

        $snapshot = $skema->toSnapshot();
        if (empty($snapshot['items'])) {
            throw ValidationException::withMessages([
                'skema' => 'Skema penilaian belum memiliki parameter nilai.',
            ]);
        }

        return DB::transaction(function () use ($ustadz, $kelas, $skema, $snapshot, $nama, $tanggalUjian, $keterangan, $pondokId) {
            $ujian = Ujian::create([
                'pondok_id' => $pondokId,
                'tahun_ajaran_id' => $kelas->tahun_ajaran_id,
                'kelas_id' => $kelas->id,
                'ustadz_id' => $ustadz->id,
                'skema_penilaian_id' => $skema->id,
                'nama' => $nama,
                'tanggal_ujian' => $tanggalUjian,
                'skema_snapshot' => $snapshot,
                'status' => 'draft',
                'keterangan' => $keterangan,
            ]);

            $peserta = SantriKelas::query()
                ->where('kelas_id', $kelas->id)
                ->where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
                ->where('status', 'aktif')
                ->pluck('santri_id');

            foreach ($peserta as $santriId) {
                UjianNilai::create([
                    'ujian_id' => $ujian->id,
                    'santri_id' => $santriId,
                ]);
            }

            return $ujian;
        });
    }

    public function updateNilai(Ujian $ujian, array $items): void
    {
        $snapshot = $ujian->skema_snapshot ?: [];
        $tipe = $snapshot['tipe'] ?? 'label';

        DB::transaction(function () use ($ujian, $items, $snapshot, $tipe) {
            foreach ($items as $item) {
                $nilaiId = isset($item['id']) ? (int) $item['id'] : 0;
                if ($nilaiId <= 0) {
                    continue;
                }

                /** @var UjianNilai|null $nilai */
                $nilai = UjianNilai::query()
                    ->where('ujian_id', $ujian->id)
                    ->find($nilaiId);

                if (!$nilai) {
                    continue;
                }

                $updateData = [
                    'catatan' => isset($item['catatan']) && $item['catatan'] !== '' ? $item['catatan'] : null,
                ];

                if ($tipe === 'numeric') {
                    $nilaiAngka = isset($item['nilai_angka']) ? trim((string)$item['nilai_angka']) : '';
                    if ($nilaiAngka !== '') {
                        if (!is_numeric($nilaiAngka) || $nilaiAngka < 0 || $nilaiAngka > 100) {
                            throw ValidationException::withMessages([
                                'nilai' => 'Untuk skema angka murni, nilai harus berupa angka antara 0 dan 100.',
                            ]);
                        }
                        $updateData['nilai_angka'] = (string)(float)$nilaiAngka;
                        $updateData['nilai_label'] = null;
                    } else {
                        $updateData['nilai_angka'] = null;
                        $updateData['nilai_label'] = null;
                    }
                } else {
                    $updateData['nilai_label'] = $this->normalizeNilai($snapshot, $item['nilai_label'] ?? null);
                    $updateData['nilai_angka'] = null;
                }

                $nilai->update($updateData);
            }
        });
    }

    public function updateStatus(Ujian $ujian, string $status): void
    {
        if (!in_array($status, ['draft', 'selesai'], true)) {
            throw ValidationException::withMessages([
                'status' => 'Status ujian tidak valid.',
            ]);
        }

        $ujian->update(['status' => $status]);
    }

    private function normalizeNilai(array $snapshot, mixed $nilaiLabelInput): ?string
    {
        $label = strtoupper(is_string($nilaiLabelInput) ? trim($nilaiLabelInput) : '');
        if ($label === '') {
            return null;
        }

        $allowed = collect($snapshot['items'] ?? [])
            ->map(function ($item) {
                return strtoupper(trim((string) ($item['singkatan'] ?? '')));
            })
            ->filter()
            ->values()
            ->all();

        if (empty($allowed)) {
            $allowed = collect($snapshot['labels'] ?? [])
                ->map(function ($labelItem) {
                    return strtoupper(trim((string) $labelItem));
                })
                ->filter()
                ->values()
                ->all();
        }

        if (!in_array($label, $allowed, true)) {
            throw ValidationException::withMessages([
                'nilai' => 'Nilai kategori tidak ada di skema penilaian pondok.',
            ]);
        }

        return $label;
    }
}
