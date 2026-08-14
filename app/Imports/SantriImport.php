<?php

namespace App\Imports;

use App\Models\Santri;
use App\Models\OrangTua;
use App\Models\KesehatanSantri;
use App\Models\AdminCabang;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Services\SantriPlacementService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SantriImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $pondokId;
    protected $activeTahunAjaranId;
    protected SantriPlacementService $placements;

    public function __construct()
    {
        $this->placements = app(SantriPlacementService::class);

        $adminCabang = AdminCabang::where('user_id', Auth::id())->first();
        $this->pondokId = $adminCabang ? $adminCabang->pondok_id : null;
        
        // Get active tahun ajaran
        $activeTahunAjaran = TahunAjaran::where('pondok_id', $this->pondokId)
            ->where('is_active', true)
            ->first();
        $this->activeTahunAjaranId = $activeTahunAjaran?->id;
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
                // Cek apakah santri dengan NIS sudah ada (update) atau baru (create)
                $existingSantri = null;
                if (!empty($row['nis'])) {
                    $existingSantri = Santri::where('nis', $row['nis'])
                        ->where('pondok_id', $this->pondokId)
                        ->first();
                }

                if ($existingSantri) {
                    // UPDATE existing santri
                    $santri = $existingSantri;
                    $santri->update([
                        'nama' => $row['nama'],
                        'panggilan' => $row['panggilan'] ?? $santri->panggilan,
                        'jenis_kelamin' => $row['jenis_kelamin'] ?? $santri->jenis_kelamin,
                        'tempat_lahir' => $row['tempat_lahir'] ?? $santri->tempat_lahir,
                        'tanggal_lahir' => $row['tanggal_lahir'] ?? $santri->tanggal_lahir,
                        'status_mukim' => $row['status_mukim'] ?? $santri->status_mukim,
                        'kondisi' => $row['kondisi'] ?? $santri->kondisi,
                        'warga_negara' => $row['warga_negara'] ?? $santri->warga_negara,
                        'alamat' => $row['alamat'] ?? $santri->alamat,
                        'kode_pos' => $row['kode_pos'] ?? $santri->kode_pos,
                        'anak_ke' => $row['anak_ke'] ?? $santri->anak_ke,
                        'jumlah_saudara' => $row['jumlah_saudara'] ?? $santri->jumlah_saudara,
                        'status_anak' => $row['status_anak'] ?? $santri->status_anak,
                        'saudara_kandung' => $row['saudara_kandung'] ?? $santri->saudara_kandung,
                        'saudara_tiri' => $row['saudara_tiri'] ?? $santri->saudara_tiri,
                        'jarak_pondok' => $row['jarak_pondok_km'] ?? $santri->jarak_pondok,
                        'telpon' => $row['telepon'] ?? $santri->telpon,
                        'handphone' => $row['handphone'] ?? $santri->handphone,
                        'email' => $row['email'] ?? $santri->email,
                        'hobi' => $row['hobi'] ?? $santri->hobi,
                    ]);
                } else {
                    // CREATE new santri
                    $nis = $row['nis'] ?? null;
                    if (empty($nis)) {
                        $nis = Santri::generateNis($this->pondokId);
                    }

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
                        'foto' => 'santri_foto/default.png',
                        'status_santri' => 'aktif',
                    ]);
                }

                // Handle Kelas assignment (jika ada nama kelas di Excel)
                if (!empty($row['kelas']) && $this->activeTahunAjaranId) {
                    $kelas = Kelas::where('pondok_id', $this->pondokId)
                        ->where('tahun_ajaran_id', $this->activeTahunAjaranId)
                        ->where('nama', $row['kelas'])
                        ->first();

                    if ($kelas) {
                        $this->placements->assignToClass($santri, $kelas);
                    }
                }

                // Update atau buat data Ayah
                if (!empty($row['ayah_nama'])) {
                    OrangTua::updateOrCreate(
                        ['santri_id' => $santri->id, 'tipe' => 'Ayah'],
                        [
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
                        ]
                    );
                }

                // Update atau buat data Ibu
                if (!empty($row['ibu_nama'])) {
                    OrangTua::updateOrCreate(
                        ['santri_id' => $santri->id, 'tipe' => 'Ibu'],
                        [
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
                        ]
                    );
                }

                // Update atau buat data Wali
                if (!empty($row['wali_nama'])) {
                    OrangTua::updateOrCreate(
                        ['santri_id' => $santri->id, 'tipe' => 'Wali'],
                        [
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
                        ]
                    );
                }

                // Update atau buat data Kesehatan
                if (!empty($row['golongan_darah'])) {
                    KesehatanSantri::updateOrCreate(
                        ['santri_id' => $santri->id],
                        [
                            'golongan_darah' => $row['golongan_darah'],
                            'berat_badan' => $row['berat_badan'] ?? null,
                            'tinggi_badan' => $row['tinggi_badan'] ?? null,
                            'riwayat_penyakit' => $row['riwayat_penyakit'] ?? null,
                        ]
                    );
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
