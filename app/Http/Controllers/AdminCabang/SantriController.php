<?php

namespace App\Http\Controllers\AdminCabang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\OrangTua;
use App\Models\KesehatanSantri;
use App\Models\User;
use App\Models\AdminCabang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class SantriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil data penting saja
        $santris = Santri::with('kelas:id,nama') // pastikan relasi 'kelas' ada di model
            ->select('id', 'nama', 'kelas_id')
            ->get()
            ->map(function ($santri) {
                return [
                    'id' => $santri->id,
                    'nama' => $santri->nama,
                    'kelas' => $santri->kelas ? $santri->kelas->nama : '-',
                    'totalJuz' => 0, // Placeholder, ganti jika ada field/relasi total juz sah
                ];
            });

        return Inertia::render('AdminCabang/Santri/Index', [
            'santris' => $santris,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('AdminCabang/Santri/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validasi request
            $validated = $request->validate([
                // User
                'username' => 'required|string|max:50|unique:users,username',
                'email' => 'nullable|email|max:100|unique:users,email',
                'password' => 'required|string|min:8',

                // Santri
                'nama' => 'required|string|max:255',
                'panggilan' => 'nullable|string|max:255',
                'jenis_kelamin' => 'required|in:L,P',
                'tempat_lahir' => 'required|string|max:100',
                'tanggal_lahir' => 'required|date',
                'status_mukim' => 'required|string|max:50',
                'kondisi' => 'required|string|max:100',
                'warga_negara' => 'required|string|max:100',
                'kode_pos' => 'nullable|string|max:20',
                'alamat' => 'required|string',
                'anak_ke' => 'required|integer|min:1',
                'jumlah_saudara' => 'required|integer|min:0',
                'status_anak' => 'required|string|max:50',
                'saudara_kandung' => 'nullable|integer|min:0',
                'saudara_tiri' => 'nullable|integer|min:0',
                'jarak_pondok' => 'nullable|numeric',
                'telpon' => 'nullable|string|max:20',
                'handphone' => 'nullable|string|max:20',
                'hobi' => 'nullable|string|max:100',
                'kelas_id' => 'nullable|exists:kelas,id',
                'foto' => 'nullable|image|max:2048',

                // Ayah
                'ayah_nama' => 'required|string|max:255',
                'ayah_status' => 'required|string|max:20',
                'ayah_status_hubungan' => 'required|string|max:20',
                'ayah_tempat_lahir' => 'nullable|string|max:100',
                'ayah_tanggal_lahir' => 'nullable|date',
                'ayah_pendidikan' => 'nullable|string|max:50',
                'ayah_pekerjaan' => 'nullable|string|max:50',
                'ayah_penghasilan' => 'nullable|string|max:50',
                'ayah_email' => 'nullable|email|max:100',
                'ayah_handphone' => 'nullable|string|max:20',
                'ayah_alamat' => 'nullable|string',

                // Ibu
                'ibu_nama' => 'required|string|max:255',
                'ibu_status' => 'required|string|max:20',
                'ibu_status_hubungan' => 'required|string|max:20',
                'ibu_tempat_lahir' => 'nullable|string|max:100',
                'ibu_tanggal_lahir' => 'nullable|date',
                'ibu_pendidikan' => 'nullable|string|max:50',
                'ibu_pekerjaan' => 'nullable|string|max:50',
                'ibu_penghasilan' => 'nullable|string|max:50',
                'ibu_email' => 'nullable|email|max:100',
                'ibu_handphone' => 'nullable|string|max:20',
                'ibu_alamat' => 'nullable|string',

                // Wali (opsional)
                'wali_nama' => 'nullable|string|max:255',
                'wali_status' => 'nullable|string|max:20',
                'wali_status_hubungan' => 'nullable|string|max:20',
                'wali_tempat_lahir' => 'nullable|string|max:100',
                'wali_tanggal_lahir' => 'nullable|date',
                'wali_pendidikan' => 'nullable|string|max:50',
                'wali_pekerjaan' => 'nullable|string|max:50',
                'wali_penghasilan' => 'nullable|string|max:50',
                'wali_email' => 'nullable|email|max:100',
                'wali_handphone' => 'nullable|string|max:20',
                'wali_alamat' => 'nullable|string',

                // Kesehatan
                'golongan_darah' => 'required|string|max:5',
                'berat_badan' => 'nullable|numeric',
                'tinggi_badan' => 'nullable|numeric',
                'riwayat_penyakit' => 'nullable|string',
            ]);

            // Dapatkan pondok_id dari admin cabang yang sedang login
            $adminCabang = AdminCabang::where('user_id', Auth::id())->first();
            if (!$adminCabang) {
                throw new \Exception('Data admin cabang tidak ditemukan.');
            }

            // Buat user baru untuk santri
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'santri',
                'status' => true,
            ]);
            if (!$user) {
                throw new \Exception('Gagal membuat user santri.');
            }

            // Upload foto jika ada
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('santri_foto', 'public');
            }

            // Buat data santri
            $santri = Santri::create([
                'user_id' => $user->id,
                'pondok_id' => $adminCabang->pondok_id,
                'kelas_id' => $request->kelas_id,
                'nama' => $request->nama,
                'panggilan' => $request->panggilan,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'status_mukim' => $request->status_mukim,
                'kondisi' => $request->kondisi,
                'warga_negara' => $request->warga_negara,
                'kode_pos' => $request->kode_pos,
                'alamat' => $request->alamat,
                'anak_ke' => $request->anak_ke,
                'jumlah_saudara' => $request->jumlah_saudara,
                'status_anak' => $request->status_anak,
                'saudara_kandung' => $request->saudara_kandung ?? 0,
                'saudara_tiri' => $request->saudara_tiri ?? 0,
                'jarak_pondok' => $request->jarak_pondok,
                'telpon' => $request->telpon,
                'handphone' => $request->handphone,
                'email' => $request->email,
                'hobi' => $request->hobi,
                'foto' => $fotoPath ?? '',
            ]);
            if (!$santri) {
                throw new \Exception('Gagal membuat data santri.');
            }

            // Simpan data orang tua Ayah
            OrangTua::create([
                'santri_id' => $santri->id,
                'tipe' => 'Ayah',
                'nama' => $request->ayah_nama,
                'status' => $request->ayah_status,
                'status_hubungan' => $request->ayah_status_hubungan,
                'tempat_lahir' => $request->ayah_tempat_lahir,
                'tanggal_lahir' => $request->ayah_tanggal_lahir,
                'pendidikan' => $request->ayah_pendidikan,
                'pekerjaan' => $request->ayah_pekerjaan,
                'penghasilan' => $request->ayah_penghasilan,
                'email' => $request->ayah_email,
                'handphone' => $request->ayah_handphone,
                'alamat' => $request->ayah_alamat,
            ]);
            // Simpan data orang tua Ibu
            OrangTua::create([
                'santri_id' => $santri->id,
                'tipe' => 'Ibu',
                'nama' => $request->ibu_nama,
                'status' => $request->ibu_status,
                'status_hubungan' => $request->ibu_status_hubungan,
                'tempat_lahir' => $request->ibu_tempat_lahir,
                'tanggal_lahir' => $request->ibu_tanggal_lahir,
                'pendidikan' => $request->ibu_pendidikan,
                'pekerjaan' => $request->ibu_pekerjaan,
                'penghasilan' => $request->ibu_penghasilan,
                'email' => $request->ibu_email,
                'handphone' => $request->ibu_handphone,
                'alamat' => $request->ibu_alamat,
            ]);
            // Simpan data wali jika ada nama wali
            if ($request->wali_nama) {
                OrangTua::create([
                    'santri_id' => $santri->id,
                    'tipe' => 'Wali',
                    'nama' => $request->wali_nama,
                    'status' => $request->wali_status,
                    'status_hubungan' => $request->wali_status_hubungan,
                    'tempat_lahir' => $request->wali_tempat_lahir,
                    'tanggal_lahir' => $request->wali_tanggal_lahir,
                    'pendidikan' => $request->wali_pendidikan,
                    'pekerjaan' => $request->wali_pekerjaan,
                    'penghasilan' => $request->wali_penghasilan,
                    'email' => $request->wali_email,
                    'handphone' => $request->wali_handphone,
                    'alamat' => $request->wali_alamat,
                ]);
            }

            // Simpan data kesehatan santri
            KesehatanSantri::create([
                'santri_id' => $santri->id,
                'golongan_darah' => $request->golongan_darah,
                'berat_badan' => $request->berat_badan ?? null,
                'tinggi_badan' => $request->tinggi_badan ?? null,
                'riwayat_penyakit' => $request->riwayat_penyakit ?? null,
            ]);

            DB::commit();
            return redirect()->route('admin-cabang.santri.index')
                ->with('success', 'Data santri berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
