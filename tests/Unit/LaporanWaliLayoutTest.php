<?php

namespace Tests\Unit;

use App\Support\LaporanWaliLayout as L;
use PHPUnit\Framework\TestCase;

/**
 * Menjaga konstanta geometri template tetap utuh.
 *
 * Angka-angka di sini berasal dari content stream PDF template asli. Bila salah
 * satu berubah tanpa sengaja, seluruh teks pada laporan akan bergeser dan
 * hasilnya tidak lagi sama dengan template. Tes ini menangkapnya tanpa perlu
 * merender PDF.
 */
class LaporanWaliLayoutTest extends TestCase
{
    public function test_baris_tabel_berjumlah_tujuh_dengan_jarak_seragam(): void
    {
        $this->assertCount(L::JUMLAH_BARIS, L::ROW_Y);
        $this->assertSame(7, L::JUMLAH_BARIS);

        for ($i = 1; $i < L::JUMLAH_BARIS; $i++) {
            $jarak = L::ROW_Y[$i - 1] - L::ROW_Y[$i];
            $this->assertEqualsWithDelta(
                22.5,
                $jarak,
                0.001,
                "Jarak baris {$i} seharusnya 22.5pt seperti pada template."
            );
        }
    }

    public function test_konversi_baseline_ke_css_top(): void
    {
        // Nama wali: baseline 647.969pt, font 12pt.
        // top = 841.89 - 647.969 - 12 * 0.86 = 183.601
        $this->assertEqualsWithDelta(
            183.601,
            L::topFor(L::F_NAMA_WALI[1], L::SIZE_IDENTITAS),
            0.001
        );

        // Baris pertama tabel: baseline 458.071pt, font 9pt.
        $this->assertEqualsWithDelta(
            841.89 - 458.071 - 9 * 0.86,
            L::topFor(L::ROW_Y[0], L::SIZE_BARIS),
            0.001
        );
    }

    public function test_ascent_ratio_terkunci(): void
    {
        // Nilai ini hasil pengukuran terhadap template; mengubahnya menggeser
        // SELURUH teks pada laporan.
        $this->assertSame(0.86, L::ASCENT_RATIO);
    }

    public function test_kolom_keterangan_lebih_tinggi_dari_kolom_lain(): void
    {
        foreach (range(0, L::JUMLAH_BARIS - 1) as $i) {
            $this->assertEqualsWithDelta(
                L::rowY($i) + 2.9,
                L::keteranganY($i),
                0.001
            );
        }
    }

    public function test_baris_di_luar_rentang_ditolak(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        L::rowY(L::JUMLAH_BARIS);
    }

    public function test_urutan_kolom_dari_kiri_ke_kanan(): void
    {
        $urut = [
            L::COL_BULAN,
            L::COL_JUZ,
            L::COL_ZIYADAH,
            L::COL_MUROJAAH,
            L::COL_TANGGAL,
            L::COL_PEMBIMBING,
            L::COL_KETERANGAN,
        ];

        $terurut = $urut;
        sort($terurut);
        $this->assertSame($terurut, $urut, 'Posisi x kolom harus urut kiri ke kanan.');
    }

    public function test_teks_pendek_tidak_dipotong(): void
    {
        $this->assertSame('Ustadz Fajar', L::truncate('Ustadz Fajar', 'pembimbing'));
        $this->assertSame('', L::truncate(null, 'keterangan'));
        $this->assertSame('', L::truncate('   ', 'keterangan'));
    }

    public function test_teks_panjang_dipotong_pada_batas_kata(): void
    {
        $panjang = 'Ziyadah 1 juz dan murojaah penuh serta ujian tahfidz semester ganjil';
        $hasil = L::truncate($panjang, 'keterangan');

        $this->assertLessThanOrEqual(L::MAX_CHARS['keterangan'], mb_strlen($hasil));
        $this->assertStringEndsWith('…', $hasil);
        // Tidak berhenti di tengah kata.
        $this->assertStringNotContainsString(' …', $hasil);
    }

    public function test_kata_tunggal_sangat_panjang_tetap_dipotong(): void
    {
        $hasil = L::truncate(str_repeat('A', 80), 'pembimbing');

        $this->assertLessThanOrEqual(L::MAX_CHARS['pembimbing'], mb_strlen($hasil));
        $this->assertStringEndsWith('…', $hasil);
        // Kata tanpa spasi tidak boleh habis menjadi hanya elipsis.
        $this->assertGreaterThan(5, mb_strlen($hasil));
    }

    public function test_kolom_tanpa_batas_dibiarkan_utuh(): void
    {
        $teks = str_repeat('B', 200);
        $this->assertSame($teks, L::truncate($teks, 'kolom_tidak_dikenal'));
    }
}
