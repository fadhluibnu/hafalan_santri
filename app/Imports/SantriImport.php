<?php

namespace App\Imports;

use App\Models\Santri;
use App\Models\OrangTua;
use App\Models\KesehatanSantri;
use App\Models\AdminCabang;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SantriImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $pondokId;

    public function __construct()
    {
        $adminCabang = AdminCabang::where('user_id', Auth::id())->first();
        $this->pondokId = $adminCabang ? $adminCabang->pondok_id : null;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip jika nama kosong
            if (empty($row['nama'])) {
                continue;
            }

            DB::beginTransaction();
            try {
                // Generate NIS jika tidak ada
                $nis = $row['nis'] ?? null;
                if (empty($nis)) {
                    $nis = Santri::generateNis($this->pondokId);
                }

                // Buat Santri
                $santri = Santri::create([
                    'nis' => $nis,
                    'pondok_id' => $this->pondokId,
                    'nama' => $row['nama'],
                    'panggilan' => $row['panggilan'] ?? null,
                    'jenis_kelamin' => $row['jenis_kelamin'] ?? 'L',
                    'tempat_lahir' => $row['tempat_lahir'] ?? '',
                    'tanggal_lahir' => $row['tanggal_lahir'] ?? date('Y-m-d'),
                    'status_mukim' => $row['status_mukim'] ?? 'Mukim',
                    'kondisi' => $row['kondisi'] ?? 'Sehat',
                    'warga_negara' => $row['warga_negara'] ?? 'Indonesia',
                    'alamat' => $row['alamat'] ?? '',
                    'kode_pos' => $row['kode_pos'] ?? null,
                    'anak_ke' => $row['anak_ke'] ?? 1,
                    'jumlah_saudara' => $row['jumlah_saudara'] ?? 0,
                    'status_anak' => $row['status_anak'] ?? 'Kandung',
                    'saudara_kandung' => $row['saudara_kandung'] ?? 0,
                    'saudara_tiri' => $row['saudara_tiri'] ?? 0,
                    'jarak_pondok' => $row['jarak_pondok_km'] ?? null,
                    'telpon' => $row['telepon'] ?? null,
                    'handphone' => $row['handphone'] ?? null,
                    'email' => $row['email'] ?? null,
                    'hobi' => $row['hobi'] ?? null,
                    'foto' => '',
                ]);

                // Buat data Ayah jika ada
                if (!empty($row['ayah_nama'])) {
                    OrangTua::create([
                        'santri_id' => $santri->id,
                        'tipe' => 'Ayah',
                        'nama' => $row['ayah_nama'],
                        'status' => $row['ayah_status'] ?? 'Hidup',
                        'status_hubungan' => $row['ayah_status_hubungan'] ?? 'Kandung',
                        'tempat_lahir' => $row['ayah_tempat_lahir'] ?? null,
                        'tanggal_lahir' => $row['ayah_tanggal_lahir'] ?? null,
                        'pendidikan' => $row['ayah_pendidikan'] ?? null,
                        'pekerjaan' => $row['ayah_pekerjaan'] ?? null,
                        'penghasilan' => $row['ayah_penghasilan'] ?? null,
                        'email' => $row['ayah_email'] ?? null,
                        'handphone' => $row['ayah_handphone'] ?? null,
                        'alamat' => $row['ayah_alamat'] ?? null,
                    ]);
                }

                // Buat data Ibu jika ada
                if (!empty($row['ibu_nama'])) {
                    OrangTua::create([
                        'santri_id' => $santri->id,
                        'tipe' => 'Ibu',
                        'nama' => $row['ibu_nama'],
                        'status' => $row['ibu_status'] ?? 'Hidup',
                        'status_hubungan' => $row['ibu_status_hubungan'] ?? 'Kandung',
                        'tempat_lahir' => $row['ibu_tempat_lahir'] ?? null,
                        'tanggal_lahir' => $row['ibu_tanggal_lahir'] ?? null,
                        'pendidikan' => $row['ibu_pendidikan'] ?? null,
                        'pekerjaan' => $row['ibu_pekerjaan'] ?? null,
                        'penghasilan' => $row['ibu_penghasilan'] ?? null,
                        'email' => $row['ibu_email'] ?? null,
                        'handphone' => $row['ibu_handphone'] ?? null,
                        'alamat' => $row['ibu_alamat'] ?? null,
                    ]);
                }

                // Buat data Wali jika ada
                if (!empty($row['wali_nama'])) {
                    OrangTua::create([
                        'santri_id' => $santri->id,
                        'tipe' => 'Wali',
                        'nama' => $row['wali_nama'],
                        'status' => $row['wali_status'] ?? 'Hidup',
                        'status_hubungan' => $row['wali_status_hubungan'] ?? null,
                        'tempat_lahir' => $row['wali_tempat_lahir'] ?? null,
                        'tanggal_lahir' => $row['wali_tanggal_lahir'] ?? null,
                        'pendidikan' => $row['wali_pendidikan'] ?? null,
                        'pekerjaan' => $row['wali_pekerjaan'] ?? null,
                        'penghasilan' => $row['wali_penghasilan'] ?? null,
                        'email' => $row['wali_email'] ?? null,
                        'handphone' => $row['wali_handphone'] ?? null,
                        'alamat' => $row['wali_alamat'] ?? null,
                    ]);
                }

                // Buat data Kesehatan jika ada golongan darah
                if (!empty($row['golongan_darah'])) {
                    KesehatanSantri::create([
                        'santri_id' => $santri->id,
                        'golongan_darah' => $row['golongan_darah'],
                        'berat_badan' => $row['berat_badan'] ?? null,
                        'tinggi_badan' => $row['tinggi_badan'] ?? null,
                        'riwayat_penyakit' => $row['riwayat_penyakit'] ?? null,
                    ]);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'nullable|in:L,P',
        ];
    }
}

