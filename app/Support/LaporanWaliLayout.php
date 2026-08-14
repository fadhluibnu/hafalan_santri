<?php

namespace App\Support;

/**
 * Geometri "Laporan Perkembangan Santri" (template wali/orang tua).
 *
 * Semua angka di sini diekstrak langsung dari content stream PDF template asli,
 * bukan hasil perkiraan. Satuan poin (pt), mengikuti sistem koordinat PDF:
 * origin di kiri-BAWAH halaman, dan y menunjuk ke BASELINE teks.
 *
 * Kelas ini sengaja dipisah dari Blade supaya angka geometri punya satu sumber
 * kebenaran dan bisa diuji unit tanpa merender PDF.
 */
final class LaporanWaliLayout
{
    /** Tinggi halaman A4 dalam poin. */
    public const PAGE_HEIGHT = 841.89;

    /** Lebar halaman A4 dalam poin. */
    public const PAGE_WIDTH = 595.28;

    /**
     * Penempatan gambar latar, persis seperti pada template asli:
     * `525.000 0 0 712.500 34.016 95.374 cm /I1 Do`
     */
    public const BG_WIDTH = 525.0;
    public const BG_HEIGHT = 712.5;
    public const BG_LEFT = 34.016;

    /**
     * Rasio ascent efektif dompdf untuk font Times pada `line-height: 1`.
     *
     * Angka ini hasil pengukuran, bukan tebakan: pada percobaan, 0.78 membuat
     * seluruh teks 1.92pt terlalu rendah dan 0.94 membuat 1.92pt terlalu tinggi,
     * konsisten untuk ukuran 6pt sampai 12pt. Titik tengahnya, 0.86, menghasilkan
     * pergeseran maksimum 0.96pt terhadap template asli.
     *
     * Mengubah nilai ini akan menggeser SEMUA teks. Jangan diubah tanpa
     * menjalankan ulang tes regresi geometri.
     */
    public const ASCENT_RATIO = 0.86;

    /** Font size tiap kelompok teks (pt). */
    public const SIZE_IDENTITAS = 12.0;
    public const SIZE_BARIS = 9.0;
    public const SIZE_KETERANGAN = 6.8;
    public const SIZE_FOOTER = 6.8;
    public const SIZE_FOOTER_TELP = 6.0;

    /** Jumlah baris pada tabel bulanan. Terpaku 7 karena grid-nya bagian dari gambar latar. */
    public const JUMLAH_BARIS = 7;

    /**
     * Field identitas: [x, baseline_y].
     */
    public const F_NAMA_WALI = [356.516, 647.969];
    public const F_HP_WALI = [326.516, 631.469];
    public const F_NAMA_SANTRI = [191.516, 537.719];
    public const F_NAMA_PONDOK = [191.516, 511.469];

    /**
     * Posisi x tiap kolom tabel bulanan, urut kiri ke kanan.
     */
    public const COL_BULAN = 101.516;
    public const COL_JUZ = 176.516;
    public const COL_ZIYADAH = 214.016;
    public const COL_MUROJAAH = 244.016;
    public const COL_TANGGAL = 281.516;
    public const COL_PEMBIMBING = 351.300;
    public const COL_KETERANGAN = 424.000;

    /**
     * Baseline y untuk 7 baris tabel. Jaraknya tepat 22.5pt.
     */
    public const ROW_Y = [458.071, 435.571, 413.071, 390.571, 368.071, 345.571, 323.071];

    /**
     * Pada template asli, kolom Keterangan digambar 2.9pt lebih tinggi
     * dari kolom lain di baris yang sama.
     */
    public const KETERANGAN_OFFSET_Y = 2.9;

    /** Footer statis: [x, baseline_y]. */
    public const F_FOOTER_TELP = [424.8, 150.0];
    public const F_FOOTER_L1 = [94.0, 142.9];
    public const F_FOOTER_L2 = [94.0, 134.9];
    public const F_FOOTER_L3 = [94.0, 126.8];

    /**
     * Batas jumlah karakter per kolom, supaya teks panjang tidak menabrak
     * kolom sebelahnya. Diturunkan dari lebar kolom pada gambar latar dibagi
     * lebar rata-rata karakter Times pada ukuran font kolom tersebut.
     */
    public const MAX_CHARS = [
        'pembimbing' => 20,
        'keterangan' => 42,
        'nama_wali' => 34,
        'nama_santri' => 46,
        'nama_pondok' => 46,
    ];

    /**
     * Konversi baseline PDF (origin kiri-bawah) menjadi CSS `top`
     * (origin kiri-atas, mengukur tepi atas kotak teks).
     *
     * Wajib dipakai bersama `line-height: 1` pada elemen teksnya; tanpa itu
     * dompdf menambah leading dan hasilnya bergeser.
     */
    public static function topFor(float $baselineY, float $fontSize): float
    {
        return self::PAGE_HEIGHT - $baselineY - $fontSize * self::ASCENT_RATIO;
    }

    /**
     * Ambil baseline y untuk baris ke-$index (0-based).
     */
    public static function rowY(int $index): float
    {
        if ($index < 0 || $index >= self::JUMLAH_BARIS) {
            throw new \InvalidArgumentException(
                "Baris tabel hanya 0.." . (self::JUMLAH_BARIS - 1) . ", diminta: {$index}"
            );
        }

        return self::ROW_Y[$index];
    }

    /**
     * Baseline y kolom Keterangan untuk baris ke-$index.
     */
    public static function keteranganY(int $index): float
    {
        return self::rowY($index) + self::KETERANGAN_OFFSET_Y;
    }

    /**
     * Potong teks agar tidak melebihi lebar kolom. Memotong pada batas kata
     * bila memungkinkan, supaya hasilnya tetap terbaca.
     */
    public static function truncate(?string $text, string $kolom): string
    {
        $text = trim((string) $text);
        if ($text === '') {
            return '';
        }

        $max = self::MAX_CHARS[$kolom] ?? null;
        if ($max === null || mb_strlen($text) <= $max) {
            return $text;
        }

        $cut = mb_substr($text, 0, $max - 1);
        $spasi = mb_strrpos($cut, ' ');

        // Potong di batas kata hanya bila tidak membuang lebih dari 40% teks,
        // supaya kata tunggal yang panjang tidak hilang seluruhnya.
        if ($spasi !== false && $spasi >= (int) (($max - 1) * 0.6)) {
            $cut = mb_substr($cut, 0, $spasi);
        }

        return rtrim($cut) . '…';
    }
}
