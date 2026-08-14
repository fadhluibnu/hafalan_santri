<?php

namespace App\Http\Controllers\AdminCabang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\OrangTua;
use App\Models\KesehatanSantri;
use App\Models\AdminCabang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Exports\SantriExport;
use App\Exports\SantriTemplateExport;
use App\Imports\SantriImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\SantriKelas;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use App\Services\SantriPlacementService;

class SantriController extends Controller
{
    public function __construct(private readonly SantriPlacementService $placements)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $adminCabang = AdminCabang::where('user_id', Auth::id())->with('pondok')->first();
        $pondokId = $adminCabang->pondok->id;

        // Get active tahun ajaran
        $activeTahunAjaran = TahunAjaran::where('pondok_id', $pondokId)
            ->where('is_active', true)
            ->first();

        // Build query
        $query = Santri::where('pondok_id', $pondokId)
            ->with([
                'jus' => function($q) {
                    $q->where('status', 'sah');
                },
                'santriKelas' => function($q) use ($activeTahunAjaran) {
                    if ($activeTahunAjaran) {
                        $q->where('tahun_ajaran_id', $activeTahunAjaran->id)
                          ->where('status', 'aktif')
                          ->with('kelas:id,nama');
                    }
                }
            ])
            ->select('id', 'nis', 'nama', 'foto');

        // Apply search filter
        $search = $request->input('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $santris = $query->orderBy('nama')->paginate(10)->appends($request->only('search'));

        // Transform untuk menambahkan kelas dari santri_kelas
        $santris->getCollection()->transform(function ($santri) {
            $kelasAktif = $santri->santriKelas->first();
            $santri->kelas = $kelasAktif ? $kelasAktif->kelas : null;
            unset($santri->santriKelas); // Hapus untuk menghindari nested data
            return $santri;
        });

        return Inertia::render('AdminCabang/Santri/Index', [
            'santris' => $santris,
            'filters' => $request->only('search'),
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

            // Upload foto jika ada
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('santri_foto', 'public');
            }

            // Generate NIS menggunakan method di model
            $nis = Santri::generateNis($adminCabang->pondok_id);

            // Buat data santri
            $santri = Santri::create([
                'nis' => $nis,
                'pondok_id' => $adminCabang->pondok_id,
                'kelas_id' => null,
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

            if ($request->filled('kelas_id')) {
                $kelas = Kelas::query()->findOrFail($request->kelas_id);
                if ((int) $kelas->pondok_id !== (int) $adminCabang->pondok_id) {
                    throw new \Exception('Kelas tidak ditemukan di pondok Anda.');
                }

                $this->placements->assignToClass($santri, $kelas);
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
     * @param string $nis NIS Santri
     */
    public function show(string $nis)
    {
        // Ambil pondok_id admin cabang
        $adminCabang = AdminCabang::where('user_id', Auth::id())->first();
        $pondokId = $adminCabang->pondok_id ?? null;

        // Get active tahun ajaran
        $activeTahunAjaran = TahunAjaran::where('pondok_id', $pondokId)
            ->where('is_active', true)
            ->first();

        // Ambil data santri berdasarkan NIS beserta relasi
        $santri = Santri::with([
            'orangTuas',
            'kesehatanSantri',
            'pondok:id,nama',
            'santriKelas' => function($q) use ($activeTahunAjaran) {
                if ($activeTahunAjaran) {
                    $q->where('tahun_ajaran_id', $activeTahunAjaran->id)
                      ->where('status', 'aktif')
                      ->with('kelas:id,nama');
                }
            }
        ])->where('nis', $nis)->firstOrFail();

        // Get kelas aktif dari santri_kelas
        $kelasAktif = $santri->santriKelas->first();
        $kelasNama = $kelasAktif ? $kelasAktif->kelas?->nama : null;
        $kelasId = $kelasAktif ? $kelasAktif->kelas_id : null;

        // Format data orang tua agar mudah diakses di frontend
        $ayah = $santri->orangTuas->where('tipe', 'Ayah')->first();
        $ibu = $santri->orangTuas->where('tipe', 'Ibu')->first();
        $wali = $santri->orangTuas->where('tipe', 'Wali')->first();

        return Inertia::render('AdminCabang/Santri/Show', [
            'santri' => [
                'id' => $santri->id,
                'nis' => $santri->nis,
                'nama' => $santri->nama,
                'panggilan' => $santri->panggilan,
                'jenis_kelamin' => $santri->jenis_kelamin,
                'tempat_lahir' => $santri->tempat_lahir,
                'tanggal_lahir' => $santri->tanggal_lahir,
                'status_mukim' => $santri->status_mukim,
                'kondisi' => $santri->kondisi,
                'warga_negara' => $santri->warga_negara,
                'kode_pos' => $santri->kode_pos,
                'alamat' => $santri->alamat,
                'anak_ke' => $santri->anak_ke,
                'jumlah_saudara' => $santri->jumlah_saudara,
                'status_anak' => $santri->status_anak,
                'saudara_kandung' => $santri->saudara_kandung,
                'saudara_tiri' => $santri->saudara_tiri,
                'jarak_pondok' => $santri->jarak_pondok,
                'telpon' => $santri->telpon,
                'handphone' => $santri->handphone,
                'email' => $santri->email,
                'hobi' => $santri->hobi,
                'foto' => $santri->foto,
                'pondok_id' => $santri->pondok_id,
                'kelas_id' => $kelasId,
                'kelas_nama' => $kelasNama,
                'pondok_nama' => $santri->pondok ? $santri->pondok->nama : null,
                // Orang tua
                'ayah' => $ayah,
                'ibu' => $ibu,
                'wali' => $wali,
                // Kesehatan
                'kesehatan' => $santri->kesehatanSantri,
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param string $nis NIS Santri
     */
    public function edit(string $nis)
    {
        // Ambil data santri berdasarkan NIS beserta relasi orang tua dan kesehatan
        $santri = Santri::with([
            'orangTuas',
            'kesehatanSantri',
            'kelas:id,nama',
            'pondok:id,nama'
        ])->where('nis', $nis)->firstOrFail();

        // Format data orang tua agar mudah diakses di frontend
        $ayah = $santri->orangTuas->where('tipe', 'Ayah')->first();
        $ibu = $santri->orangTuas->where('tipe', 'Ibu')->first();
        $wali = $santri->orangTuas->where('tipe', 'Wali')->first();

        return Inertia::render('AdminCabang/Santri/Edit', [
            'santri' => [
                'id' => $santri->id,
                'nis' => $santri->nis,
                'pondok_id' => $santri->pondok_id,
                'kelas_id' => $santri->kelas_id,
                'nama' => $santri->nama,
                'panggilan' => $santri->panggilan,
                'jenis_kelamin' => $santri->jenis_kelamin,
                'tempat_lahir' => $santri->tempat_lahir,
                'tanggal_lahir' => $santri->tanggal_lahir?->format('Y-m-d'),
                'status_mukim' => $santri->status_mukim,
                'kondisi' => $santri->kondisi,
                'warga_negara' => $santri->warga_negara,
                'kode_pos' => $santri->kode_pos,
                'alamat' => $santri->alamat,
                'anak_ke' => $santri->anak_ke,
                'jumlah_saudara' => $santri->jumlah_saudara,
                'status_anak' => $santri->status_anak,
                'saudara_kandung' => $santri->saudara_kandung,
                'saudara_tiri' => $santri->saudara_tiri,
                'jarak_pondok' => $santri->jarak_pondok,
                'telpon' => $santri->telpon,
                'handphone' => $santri->handphone,
                'email' => $santri->email,
                'hobi' => $santri->hobi,
                'foto' => $santri->foto,
                // Orang tua Ayah
                'ayah_nama' => $ayah?->nama,
                'ayah_status' => $ayah?->status,
                'ayah_status_hubungan' => $ayah?->status_hubungan,
                'ayah_tempat_lahir' => $ayah?->tempat_lahir,
                'ayah_tanggal_lahir' => $ayah?->tanggal_lahir?->format('Y-m-d'),
                'ayah_pendidikan' => $ayah?->pendidikan,
                'ayah_pekerjaan' => $ayah?->pekerjaan,
                'ayah_penghasilan' => $ayah?->penghasilan,
                'ayah_email' => $ayah?->email,
                'ayah_handphone' => $ayah?->handphone,
                'ayah_alamat' => $ayah?->alamat,
                // Orang tua Ibu
                'ibu_nama' => $ibu?->nama,
                'ibu_status' => $ibu?->status,
                'ibu_status_hubungan' => $ibu?->status_hubungan,
                'ibu_tempat_lahir' => $ibu?->tempat_lahir,
                'ibu_tanggal_lahir' => $ibu?->tanggal_lahir?->format('Y-m-d'),
                'ibu_pendidikan' => $ibu?->pendidikan,
                'ibu_pekerjaan' => $ibu?->pekerjaan,
                'ibu_penghasilan' => $ibu?->penghasilan,
                'ibu_email' => $ibu?->email,
                'ibu_handphone' => $ibu?->handphone,
                'ibu_alamat' => $ibu?->alamat,
                // Wali
                'wali_nama' => $wali?->nama,
                'wali_status' => $wali?->status,
                'wali_status_hubungan' => $wali?->status_hubungan,
                'wali_tempat_lahir' => $wali?->tempat_lahir,
                'wali_tanggal_lahir' => $wali?->tanggal_lahir?->format('Y-m-d'),
                'wali_pendidikan' => $wali?->pendidikan,
                'wali_pekerjaan' => $wali?->pekerjaan,
                'wali_penghasilan' => $wali?->penghasilan,
                'wali_email' => $wali?->email,
                'wali_handphone' => $wali?->handphone,
                'wali_alamat' => $wali?->alamat,
                // Kesehatan
                'golongan_darah' => $santri->kesehatanSantri?->golongan_darah,
                'berat_badan' => $santri->kesehatanSantri?->berat_badan,
                'tinggi_badan' => $santri->kesehatanSantri?->tinggi_badan,
                'riwayat_penyakit' => $santri->kesehatanSantri?->riwayat_penyakit,
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $nis)
    {
        DB::beginTransaction();
        try {
            // Ambil data santri berdasarkan NIS
            $santri = Santri::with(['orangTuas', 'kesehatanSantri'])->where('nis', $nis)->firstOrFail();

            // Validasi request
            $validated = $request->validate([
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

            // Upload foto jika ada
            $fotoPath = $santri->foto;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('santri_foto', 'public');
            }

            // Update data santri
            $santri->update([
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

            if ($request->filled('kelas_id')) {
                $kelas = Kelas::query()->findOrFail($request->kelas_id);
                if ((int) $kelas->pondok_id !== (int) $santri->pondok_id) {
                    throw new \Exception('Kelas tidak ditemukan di pondok santri.');
                }

                $this->placements->assignToClass($santri, $kelas);
            }

            // Update atau create data orang tua Ayah
            $ayah = $santri->orangTuas->where('tipe', 'Ayah')->first();
            if ($ayah) {
                $ayah->update([
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
            } else {
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
            }

            // Update atau create data orang tua Ibu
            $ibu = $santri->orangTuas->where('tipe', 'Ibu')->first();
            if ($ibu) {
                $ibu->update([
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
            } else {
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
            }

            // Update atau create data wali jika ada nama wali
            $wali = $santri->orangTuas->where('tipe', 'Wali')->first();
            if ($request->wali_nama) {
                if ($wali) {
                    $wali->update([
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
                } else {
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
            } else if ($wali) {
                // Jika nama wali kosong, hapus data wali
                $wali->delete();
            }

            // Update atau create data kesehatan santri
            if ($santri->kesehatanSantri) {
                $santri->kesehatanSantri->update([
                    'golongan_darah' => $request->golongan_darah,
                    'berat_badan' => $request->berat_badan ?? null,
                    'tinggi_badan' => $request->tinggi_badan ?? null,
                    'riwayat_penyakit' => $request->riwayat_penyakit ?? null,
                ]);
            } else {
                KesehatanSantri::create([
                    'santri_id' => $santri->id,
                    'golongan_darah' => $request->golongan_darah,
                    'berat_badan' => $request->berat_badan ?? null,
                    'tinggi_badan' => $request->tinggi_badan ?? null,
                    'riwayat_penyakit' => $request->riwayat_penyakit ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('admin-cabang.santri.show', $santri->nis)
                ->with('success', 'Data santri berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $santri = Santri::findOrFail($id);

            // Hapus data relasi terlebih dahulu
            $santri->orangTuas()->delete();
            $santri->kesehatanSantri()->delete();
            
            // Hapus santri
            $santri->delete();

            DB::commit();

            return redirect()->route('admin-cabang.santri.index')
                ->with('success', 'Data santri berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menghapus santri: ' . $e->getMessage()]);
        }
    }

    /**
     * Export santri to Excel.
     */
    public function export()
    {
        return Excel::download(new SantriExport, 'data_santri_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Download Excel template for import.
     */
    public function downloadTemplate()
    {
        return Excel::download(new SantriTemplateExport, 'template_import_santri.xlsx');
    }

    /**
     * Import santri from Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:10240',
        ]);

        try {
            Excel::import(new SantriImport, $request->file('file'));
            return redirect()->route('admin-cabang.santri.index')
                ->with('success', 'Data santri berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal import: ' . $e->getMessage()]);
        }
    }
}
