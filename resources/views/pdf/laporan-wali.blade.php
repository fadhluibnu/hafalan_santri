{{--
    Laporan Perkembangan Santri (wali/orang tua).

    View ini SENGAJA tidak meng-extends `pdf.layout`. Layout tersebut memakai
    `padding: 10px`, font DejaVu Sans, dan tema hijau — ketiganya bertabrakan
    dengan laporan ini yang memerlukan `@page { margin: 0 }`, font Times, dan
    penempatan absolut berbasis koordinat.

    Seluruh desain (logo, kop, garis grid tabel, label kolom) berada di dalam
    gambar latar, karena template aslinya memang satu gambar flatten. Yang
    digambar di sini hanya teks dinamisnya, pada koordinat dari
    App\Support\LaporanWaliLayout.
--}}
@php
    use App\Support\LaporanWaliLayout as L;

    /**
     * Render satu potong teks pada koordinat baseline PDF.
     * `line-height: 1` wajib, karena LaporanWaliLayout::topFor() mengasumsikannya.
     */
    $teks = function (float $x, float $baselineY, float $size, ?string $isi, bool $bold = false) {
        $isi = trim((string) $isi);
        if ($isi === '') {
            return '';
        }

        return sprintf(
            '<div class="t%s" style="left:%.3fpt;top:%.3fpt;font-size:%.2fpt;">%s</div>',
            $bold ? ' b' : '',
            $x,
            L::topFor($baselineY, $size),
            $size,
            e($isi)
        );
    };
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Perkembangan Santri - {{ $santri->nama }}</title>
    <style>
        @page {
            margin: 0;
        }

        html, body {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Times', 'Times New Roman', serif;
            color: #000;
            position: relative;
            width: {{ L::PAGE_WIDTH }}pt;
            height: {{ L::PAGE_HEIGHT }}pt;
        }

        /* Gambar latar ditempatkan persis seperti pada template asli:
           525 x 712.5 pt, dan karena tinggi halaman 841.89pt maka
           top = 841.89 - 95.374 - 712.5 = 34.016pt. */
        img.bg {
            position: absolute;
            left: {{ L::BG_LEFT }}pt;
            top: {{ L::BG_LEFT }}pt;
            width: {{ L::BG_WIDTH }}pt;
            height: {{ L::BG_HEIGHT }}pt;
        }

        .t {
            position: absolute;
            line-height: 1;
            white-space: nowrap;
        }

        .t.b {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <img class="bg" src="{{ $backgroundPath }}" alt="Template laporan perkembangan santri">

    {{-- Identitas wali dan santri --}}
    {!! $teks(L::F_NAMA_WALI[0], L::F_NAMA_WALI[1], L::SIZE_IDENTITAS, L::truncate($wali['nama'], 'nama_wali')) !!}
    {!! $teks(L::F_HP_WALI[0], L::F_HP_WALI[1], L::SIZE_IDENTITAS, $wali['handphone']) !!}
    {!! $teks(L::F_NAMA_SANTRI[0], L::F_NAMA_SANTRI[1], L::SIZE_IDENTITAS, L::truncate($santri->nama, 'nama_santri')) !!}
    {!! $teks(L::F_NAMA_PONDOK[0], L::F_NAMA_PONDOK[1], L::SIZE_IDENTITAS, L::truncate($pondokNama, 'nama_pondok')) !!}

    {{-- Tabel bulanan: tepat 7 baris, mengikuti grid pada gambar latar --}}
    @foreach($baris as $index => $row)
        @continue($index >= L::JUMLAH_BARIS)
        @php $y = L::rowY($index); @endphp

        {!! $teks(L::COL_BULAN, $y, L::SIZE_BARIS, $row['bulan']) !!}
        {!! $teks(L::COL_JUZ, $y, L::SIZE_BARIS, $row['juz']) !!}
        {!! $teks(L::COL_ZIYADAH, $y, L::SIZE_BARIS, $row['ziyadah']) !!}
        {!! $teks(L::COL_MUROJAAH, $y, L::SIZE_BARIS, $row['murojaah']) !!}
        {!! $teks(L::COL_TANGGAL, $y, L::SIZE_BARIS, $row['tanggal']) !!}
        {!! $teks(L::COL_PEMBIMBING, $y, L::SIZE_BARIS, L::truncate($row['pembimbing'], 'pembimbing')) !!}
        {!! $teks(L::COL_KETERANGAN, L::keteranganY($index), L::SIZE_KETERANGAN, L::truncate($row['keterangan'], 'keterangan')) !!}
    @endforeach

    {{-- Footer: identitas kantor PPPA Jateng (statis, dari config) --}}
    {!! $teks(L::F_FOOTER_TELP[0], L::F_FOOTER_TELP[1], L::SIZE_FOOTER_TELP, $footer['telp'], true) !!}
    {!! $teks(L::F_FOOTER_L1[0], L::F_FOOTER_L1[1], L::SIZE_FOOTER, $footer['nama']) !!}
    {!! $teks(L::F_FOOTER_L2[0], L::F_FOOTER_L2[1], L::SIZE_FOOTER, $footer['alamat_1']) !!}
    {!! $teks(L::F_FOOTER_L3[0], L::F_FOOTER_L3[1], L::SIZE_FOOTER, $footer['alamat_2']) !!}
</body>
</html>
