<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SantriTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        // Contoh data untuk template
        return [
            [
                // Data Santri
                '', // NIS (kosong, akan auto-generate)
                'Nama Santri',
                'Panggilan',
                'L', // L atau P
                'Jakarta',
                '2010-01-01',
                'Mukim', // Mukim atau Non-mukim
                'Sehat',
                'Indonesia',
                'Jl. Contoh No. 123',
                '12345',
                '1',
                '2',
                'Kandung', // Kandung, Yatim, Piatu, Yatim Piatu, Angkat
                '1',
                '0',
                '5.5',
                '021-1234567',
                '081234567890',
                'email@example.com',
                'Membaca',
                // Data Ayah
                'Nama Ayah',
                'Hidup', // Hidup atau Almarhum
                'Kandung', // Kandung, Tiri, Angkat
                'Jakarta',
                '1980-01-01',
                'S1', // Pendidikan
                'Wiraswasta',
                '5.000.000',
                'ayah@example.com',
                '081234567891',
                'Jl. Ayah No. 1',
                // Data Ibu
                'Nama Ibu',
                'Hidup',
                'Kandung',
                'Jakarta',
                '1982-01-01',
                'S1',
                'Ibu Rumah Tangga',
                '',
                'ibu@example.com',
                '081234567892',
                'Jl. Ibu No. 2',
                // Data Wali (opsional)
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                // Data Kesehatan
                'A', // Golongan Darah: A, B, AB, O
                '45', // Berat Badan
                '150', // Tinggi Badan
                '', // Riwayat Penyakit
            ],
        ];
    }

    public function headings(): array
    {
        return [
            // Data Santri
            'NIS',
            'Nama',
            'Panggilan',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Status Mukim',
            'Kondisi',
            'Warga Negara',
            'Alamat',
            'Kode Pos',
            'Anak Ke',
            'Jumlah Saudara',
            'Status Anak',
            'Saudara Kandung',
            'Saudara Tiri',
            'Jarak Pondok (km)',
            'Telepon',
            'Handphone',
            'Email',
            'Hobi',
            // Data Ayah
            'Ayah Nama',
            'Ayah Status',
            'Ayah Status Hubungan',
            'Ayah Tempat Lahir',
            'Ayah Tanggal Lahir',
            'Ayah Pendidikan',
            'Ayah Pekerjaan',
            'Ayah Penghasilan',
            'Ayah Email',
            'Ayah Handphone',
            'Ayah Alamat',
            // Data Ibu
            'Ibu Nama',
            'Ibu Status',
            'Ibu Status Hubungan',
            'Ibu Tempat Lahir',
            'Ibu Tanggal Lahir',
            'Ibu Pendidikan',
            'Ibu Pekerjaan',
            'Ibu Penghasilan',
            'Ibu Email',
            'Ibu Handphone',
            'Ibu Alamat',
            // Data Wali
            'Wali Nama',
            'Wali Status',
            'Wali Status Hubungan',
            'Wali Tempat Lahir',
            'Wali Tanggal Lahir',
            'Wali Pendidikan',
            'Wali Pekerjaan',
            'Wali Penghasilan',
            'Wali Email',
            'Wali Handphone',
            'Wali Alamat',
            // Data Kesehatan
            'Golongan Darah',
            'Berat Badan',
            'Tinggi Badan',
            'Riwayat Penyakit',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

