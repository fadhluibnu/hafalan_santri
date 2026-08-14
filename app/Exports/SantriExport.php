<?php

namespace App\Exports;

use App\Models\Santri;
use App\Models\AdminCabang;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SantriExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $pondokId;
    protected $activeTahunAjaranId;

    public function __construct()
    {
        $adminCabang = AdminCabang::where('user_id', Auth::id())->first();
        $this->pondokId = $adminCabang ? $adminCabang->pondok_id : null;
        
        // Get active tahun ajaran
        $activeTahunAjaran = TahunAjaran::where('pondok_id', $this->pondokId)
            ->where('is_active', true)
            ->first();
        $this->activeTahunAjaranId = $activeTahunAjaran?->id;
    }

    public function collection()
    {
        return Santri::where('pondok_id', $this->pondokId)
            ->with([
                'orangTuas', 
                'kesehatanSantri',
                'santriKelas' => function($q) {
                    if ($this->activeTahunAjaranId) {
                        $q->where('tahun_ajaran_id', $this->activeTahunAjaranId)
                          ->where('status', 'aktif')
                          ->with('kelas:id,nama');
                    }
                }
            ])
            ->orderBy('nama')
            ->get();
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
            'Kelas',
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

    public function map($santri): array
    {
        // Ambil data orang tua
        $ayah = $santri->orangTuas->where('tipe', 'Ayah')->first();
        $ibu = $santri->orangTuas->where('tipe', 'Ibu')->first();
        $wali = $santri->orangTuas->where('tipe', 'Wali')->first();
        $kesehatan = $santri->kesehatanSantri;

        // Get kelas dari santri_kelas
        $kelasAktif = $santri->santriKelas->first();
        $kelasNama = $kelasAktif ? $kelasAktif->kelas?->nama : '';

        return [
            // Data Santri
            $santri->nis,
            $santri->nama,
            $santri->panggilan,
            $santri->jenis_kelamin,
            $santri->tempat_lahir,
            $santri->tanggal_lahir,
            $santri->status_mukim,
            $santri->kondisi,
            $santri->warga_negara,
            $santri->alamat,
            $santri->kode_pos,
            $santri->anak_ke,
            $santri->jumlah_saudara,
            $santri->status_anak,
            $santri->saudara_kandung,
            $santri->saudara_tiri,
            $santri->jarak_pondok,
            $santri->telpon,
            $santri->handphone,
            $santri->email,
            $santri->hobi,
            $kelasNama,
            // Data Ayah
            $ayah?->nama,
            $ayah?->status,
            $ayah?->status_hubungan,
            $ayah?->tempat_lahir,
            $ayah?->tanggal_lahir,
            $ayah?->pendidikan,
            $ayah?->pekerjaan,
            $ayah?->penghasilan,
            $ayah?->email,
            $ayah?->handphone,
            $ayah?->alamat,
            // Data Ibu
            $ibu?->nama,
            $ibu?->status,
            $ibu?->status_hubungan,
            $ibu?->tempat_lahir,
            $ibu?->tanggal_lahir,
            $ibu?->pendidikan,
            $ibu?->pekerjaan,
            $ibu?->penghasilan,
            $ibu?->email,
            $ibu?->handphone,
            $ibu?->alamat,
            // Data Wali
            $wali?->nama,
            $wali?->status,
            $wali?->status_hubungan,
            $wali?->tempat_lahir,
            $wali?->tanggal_lahir,
            $wali?->pendidikan,
            $wali?->pekerjaan,
            $wali?->penghasilan,
            $wali?->email,
            $wali?->handphone,
            $wali?->alamat,
            // Data Kesehatan
            $kesehatan?->golongan_darah,
            $kesehatan?->berat_badan,
            $kesehatan?->tinggi_badan,
            $kesehatan?->riwayat_penyakit,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
