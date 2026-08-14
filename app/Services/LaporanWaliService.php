<?php

namespace App\Services;

use App\Models\Hafalan;
use App\Models\OrangTua;
use App\Models\Santri;
use App\Models\Ujian;
use App\Models\UjianNilai;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Menyusun data "Laporan Perkembangan Santri" untuk wali/orang tua.
 *
 * Laporan selalu berisi tepat 7 baris bulanan karena grid tabelnya merupakan
 * bagian dari gambar latar template (lihat App\Support\LaporanWaliLayout).
 * Bulan tanpa aktivitas tetap muncul sebagai baris kosong, bukan dilewati.
 */
class LaporanWaliService
{
    /** Jumlah baris bulanan pada template. */
    public const JUMLAH_BULAN = 7;

    /**
     * Urutan prioritas penerima laporan.
     *
     * Nilai `tipe` pada tabel `orang_tuas` tersimpan sebagai 'Ayah', 'Ibu',
     * 'Wali' (huruf besar di awal), jadi pembandingan dilakukan case-insensitive
     * agar tidak bergantung pada ejaan penulisan.
     */
    private const PRIORITAS_TIPE = ['wali', 'ayah', 'ibu'];

    /**
     * Ejaan `kategori` yang dianggap ziyadah.
     *
     * Form Guru menyimpan 'ziadah' sedangkan form Ustadz menyimpan 'Ziyadah',
     * dan kolomnya string bebas tanpa enum. Keduanya diterima di sini supaya
     * hitungan laporan tidak bocor karena beda ejaan di sisi input.
     */
    private const EJAAN_ZIYADAH = ['ziyadah', 'ziadah'];

    /** Ejaan `kategori` yang dianggap murojaah. */
    private const EJAAN_MUROJAAH = ['murojaah', 'muroja\'ah', 'murajaah'];

    private const NAMA_BULAN = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * Kumpulkan seluruh data laporan.
     *
     * @param  string  $bulanAkhir  Bulan terakhir periode, format 'Y-m'.
     *                              Periode mencakup 7 bulan mundur dari bulan ini.
     * @return array{santri: Santri, wali: array, pondok_nama: string, periode: array, baris: array}|null
     *         null bila santri tidak ada atau bukan milik pondok tersebut.
     */
    public function getLaporanData(int $pondokId, string $nis, ?string $bulanAkhir = null): ?array
    {
        $santri = Santri::query()
            ->with(['pondok:id,nama', 'orangTuas'])
            ->where('pondok_id', $pondokId)
            ->where('nis', $nis)
            ->first();

        if (!$santri) {
            return null;
        }

        $bulanList = $this->buildBulanList($bulanAkhir);
        $awal = $bulanList->first()['mulai'];
        $akhir = $bulanList->last()['selesai'];

        $hafalanPerBulan = $this->agregasiHafalan($santri->id, $awal, $akhir);
        $ujianPerBulan = $this->agregasiUjian($santri->id, $pondokId, $awal, $akhir);
        $juzSebelumnya = $this->juzTertinggiSebelum($santri->id, $awal);

        return [
            'santri' => $santri,
            'wali' => $this->pilihWali($santri),
            'pondok_nama' => $santri->pondok?->nama ?? '-',
            'periode' => [
                'mulai' => $awal->format('Y-m-d'),
                'selesai' => $akhir->format('Y-m-d'),
                'bulan_akhir' => $bulanList->last()['key'],
            ],
            'baris' => $this->susunBaris($bulanList, $hafalanPerBulan, $ujianPerBulan, $juzSebelumnya),
        ];
    }

    /**
     * Bangun daftar 7 bulan, urut dari paling lama ke paling baru.
     *
     * Periode boleh melintasi batas tahun maupun batas semester — memang harus,
     * karena satu tahun ajaran di sistem hanya mencakup 6 bulan sedangkan
     * template membutuhkan 7 baris.
     */
    public function buildBulanList(?string $bulanAkhir = null): Collection
    {
        $akhir = $this->parseBulan($bulanAkhir);

        return collect(range(self::JUMLAH_BULAN - 1, 0))
            ->map(function (int $mundur) use ($akhir) {
                $bulan = $akhir->subMonths($mundur);

                return [
                    'key' => $bulan->format('Y-m'),
                    'label' => self::NAMA_BULAN[(int) $bulan->format('n')],
                    'mulai' => $bulan->startOfMonth(),
                    'selesai' => $bulan->endOfMonth(),
                ];
            })
            ->values();
    }

    /**
     * Pilih penerima laporan: Wali, lalu Ayah, lalu Ibu.
     *
     * @return array{nama: string, handphone: string, tipe: string|null}
     */
    public function pilihWali(Santri $santri): array
    {
        $kandidat = null;

        foreach (self::PRIORITAS_TIPE as $tipe) {
            $kandidat = $santri->orangTuas
                ->first(fn (OrangTua $ot) => mb_strtolower(trim((string) $ot->tipe)) === $tipe);

            if ($kandidat) {
                break;
            }
        }

        if (!$kandidat) {
            return ['nama' => '-', 'handphone' => '-', 'tipe' => null];
        }

        return [
            'nama' => $this->formatNamaWali($kandidat),
            'handphone' => trim((string) $kandidat->handphone) !== '' ? $kandidat->handphone : '-',
            'tipe' => $kandidat->tipe,
        ];
    }

    /**
     * Tambahkan prefiks "Alm."/"Almh." bila orang tua sudah meninggal.
     *
     * "Almh." dipakai untuk Ibu, "Alm." untuk Ayah maupun Wali. Prefiks tidak
     * ditambahkan dua kali bila nama tersimpan sudah memuatnya.
     */
    private function formatNamaWali(OrangTua $ot): string
    {
        $nama = trim((string) $ot->nama);
        if ($nama === '') {
            return '-';
        }

        if (mb_strtolower(trim((string) $ot->status)) !== 'almarhum') {
            return $nama;
        }

        if (preg_match('/^alm\.?h?\.?\s/i', $nama)) {
            return $nama;
        }

        $prefiks = mb_strtolower(trim((string) $ot->tipe)) === 'ibu' ? 'Almh.' : 'Alm.';

        return $prefiks . ' ' . $nama;
    }

    /**
     * Agregasi hafalan per bulan dalam SATU query.
     *
     * Sengaja tidak melakukan query di dalam loop 7 bulan, agar jumlah query
     * tetap konstan (tidak N+1) berapa pun panjang periodenya.
     *
     * @return Collection<string, array> dikunci 'Y-m'
     */
    private function agregasiHafalan(int $santriId, CarbonImmutable $awal, CarbonImmutable $akhir): Collection
    {
        $rows = Hafalan::query()
            ->with('ustadz:id,nama')
            ->where('santri_id', $santriId)
            ->whereBetween('tanggal_setor', [$awal->toDateString(), $akhir->toDateString()])
            ->orderBy('tanggal_setor')
            ->get(['id', 'santri_id', 'ustadz_id', 'tanggal_setor', 'juz', 'kategori', 'catatan']);

        return $rows
            ->groupBy(fn (Hafalan $h) => CarbonImmutable::parse($h->tanggal_setor)->format('Y-m'))
            ->map(function (Collection $items) {
                return [
                    'juz_maks' => (int) $items->max('juz'),
                    'ziyadah' => $items->filter(fn ($h) => $this->isKategori($h->kategori, self::EJAAN_ZIYADAH))->count(),
                    'murojaah' => $items->filter(fn ($h) => $this->isKategori($h->kategori, self::EJAAN_MUROJAAH))->count(),
                    'tanggal_terakhir' => CarbonImmutable::parse($items->max('tanggal_setor')),
                    'ustadz_terbanyak' => $this->ustadzTerbanyak($items),
                    'catatan' => $this->catatanTerakhir($items),
                ];
            });
    }

    /**
     * Agregasi ujian per bulan dalam SATU query.
     *
     * Ujian hanya muncul 1-2 kali per semester, jadi tidak dipakai sebagai
     * tulang punggung baris — hanya memperkaya kolom Keterangan dan Pembimbing
     * pada bulan yang memang ada ujiannya.
     *
     * @return Collection<string, array> dikunci 'Y-m'
     */
    private function agregasiUjian(int $santriId, int $pondokId, CarbonImmutable $awal, CarbonImmutable $akhir): Collection
    {
        $rows = UjianNilai::query()
            ->where('santri_id', $santriId)
            ->whereHas('ujian', function ($q) use ($pondokId, $awal, $akhir) {
                $q->where('pondok_id', $pondokId)
                    ->whereBetween('tanggal_ujian', [$awal->toDateString(), $akhir->toDateString()]);
            })
            ->with(['ujian:id,nama,tanggal_ujian,ustadz_id', 'ujian.ustadz:id,nama'])
            ->get();

        return $rows
            ->filter(fn (UjianNilai $n) => $n->ujian !== null)
            ->groupBy(fn (UjianNilai $n) => CarbonImmutable::parse($n->ujian->tanggal_ujian)->format('Y-m'))
            ->map(function (Collection $items) {
                /** @var Ujian $terakhir */
                $terakhir = $items->sortByDesc(fn ($n) => $n->ujian->tanggal_ujian)->first()->ujian;

                return [
                    'nama_ujian' => $items
                        ->map(fn ($n) => trim((string) $n->ujian->nama))
                        ->filter()
                        ->unique()
                        ->values()
                        ->all(),
                    'ustadz_penguji' => $terakhir->ustadz?->nama,
                ];
            });
    }

    /**
     * Juz tertinggi yang pernah dicapai SEBELUM periode laporan dimulai.
     *
     * Diperlukan supaya kolom juz bersifat kumulatif: bulan tanpa setoran tetap
     * menampilkan capaian terakhir, bukan turun menjadi kosong.
     */
    private function juzTertinggiSebelum(int $santriId, CarbonImmutable $awal): int
    {
        return (int) Hafalan::query()
            ->where('santri_id', $santriId)
            ->where('tanggal_setor', '<', $awal->toDateString())
            ->max('juz');
    }

    /**
     * Gabungkan agregasi hafalan dan ujian menjadi 7 baris siap render.
     */
    private function susunBaris(
        Collection $bulanList,
        Collection $hafalanPerBulan,
        Collection $ujianPerBulan,
        int $juzAwal
    ): array {
        $juzBerjalan = $juzAwal;
        $baris = [];

        foreach ($bulanList as $bulan) {
            $h = $hafalanPerBulan->get($bulan['key']);
            $u = $ujianPerBulan->get($bulan['key']);

            if ($h && $h['juz_maks'] > $juzBerjalan) {
                $juzBerjalan = $h['juz_maks'];
            }

            $adaAktivitas = $h !== null || $u !== null;

            $baris[] = [
                'bulan' => $bulan['label'],
                'bulan_key' => $bulan['key'],
                // Juz hanya ditampilkan bila sudah ada capaian, supaya baris
                // sebelum santri mulai menyetor tidak menampilkan angka 0.
                'juz' => $juzBerjalan > 0 && $adaAktivitas ? (string) $juzBerjalan : '',
                'ziyadah' => $h && $h['ziyadah'] > 0 ? (string) $h['ziyadah'] : '',
                'murojaah' => $h && $h['murojaah'] > 0 ? (string) $h['murojaah'] : '',
                'tanggal' => $h ? $h['tanggal_terakhir']->format('d-m-Y') : '',
                'pembimbing' => $this->pilihPembimbing($h, $u),
                'keterangan' => $this->susunKeterangan($h, $u),
            ];
        }

        return $baris;
    }

    /**
     * Pembimbing: ustadz penguji diprioritaskan pada bulan yang ada ujiannya,
     * sisanya memakai ustadz dengan setoran terbanyak bulan itu.
     */
    private function pilihPembimbing(?array $hafalan, ?array $ujian): string
    {
        if ($ujian && !empty($ujian['ustadz_penguji'])) {
            return (string) $ujian['ustadz_penguji'];
        }

        return (string) ($hafalan['ustadz_terbanyak'] ?? '');
    }

    /**
     * Keterangan: catatan hafalan, ditambah nama ujian bila bulan itu ada ujian.
     */
    private function susunKeterangan(?array $hafalan, ?array $ujian): string
    {
        $bagian = [];

        $catatan = trim((string) ($hafalan['catatan'] ?? ''));
        if ($catatan !== '') {
            $bagian[] = $catatan;
        }

        foreach ($ujian['nama_ujian'] ?? [] as $nama) {
            $bagian[] = $nama;
        }

        return implode(', ', $bagian);
    }

    /**
     * Ustadz dengan jumlah setoran terbanyak pada bulan tersebut.
     * Bila seri, yang menyetor paling akhir dipilih agar hasilnya deterministik.
     */
    private function ustadzTerbanyak(Collection $items): string
    {
        $terpilih = $items
            ->filter(fn (Hafalan $h) => $h->ustadz !== null)
            ->groupBy('ustadz_id')
            ->sortByDesc(fn (Collection $g) => [$g->count(), $g->max('tanggal_setor')])
            ->first();

        return $terpilih ? (string) $terpilih->first()->ustadz->nama : '';
    }

    /**
     * Catatan dari setoran terakhir yang punya catatan pada bulan tersebut.
     */
    private function catatanTerakhir(Collection $items): string
    {
        $catatan = $items
            ->sortByDesc('tanggal_setor')
            ->map(fn (Hafalan $h) => trim((string) $h->catatan))
            ->filter()
            ->first();

        return (string) ($catatan ?? '');
    }

    /**
     * Cocokkan kategori terhadap daftar ejaan yang diterima, case-insensitive.
     */
    private function isKategori(?string $kategori, array $ejaan): bool
    {
        return in_array(mb_strtolower(trim((string) $kategori)), $ejaan, true);
    }

    /**
     * Parse 'Y-m' menjadi awal bulan. Input tidak valid jatuh ke bulan ini,
     * supaya URL yang dirusak tangan tidak melempar exception.
     */
    private function parseBulan(?string $bulan): CarbonImmutable
    {
        if (is_string($bulan) && preg_match('/^\d{4}-\d{2}$/', $bulan)) {
            try {
                return CarbonImmutable::createFromFormat('Y-m-d', $bulan . '-01')->startOfMonth();
            } catch (\Throwable) {
                // jatuh ke default di bawah
            }
        }

        return CarbonImmutable::now()->startOfMonth();
    }
}
