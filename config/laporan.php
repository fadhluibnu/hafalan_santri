<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Identitas Kantor pada Footer Laporan
    |--------------------------------------------------------------------------
    |
    | Footer template laporan memuat identitas kantor LAZNAS, BUKAN alamat
    | pondok tempat santri belajar. Karena itu nilainya statis di sini dan
    | tidak diambil dari tabel `pondoks`.
    |
    */

    'footer' => [
        'nama' => env('LAPORAN_FOOTER_NAMA', 'KANTOR PPPA JATENG'),
        'alamat_1' => env('LAPORAN_FOOTER_ALAMAT_1', 'Jl Gedung Batu Utara V No.7,kelurahan Ngemplak'),
        'alamat_2' => env('LAPORAN_FOOTER_ALAMAT_2', 'Simongan,Kecamatan Semarang Barat'),
        'telp' => env('LAPORAN_FOOTER_TELP', '085165810466 / 024-76435007'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Gambar Latar Laporan Wali
    |--------------------------------------------------------------------------
    |
    | Seluruh desain template (logo, kop, garis grid tabel, label kolom) berada
    | di dalam satu berkas gambar, karena template asli yang diberikan memang
    | sudah ter-flatten menjadi gambar. Path relatif terhadap public_path().
    |
    */

    'background' => env('LAPORAN_WALI_BACKGROUND', 'img/laporan-wali-bg.jpg'),

];
