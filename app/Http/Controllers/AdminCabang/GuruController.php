<?php

namespace App\Http\Controllers\AdminCabang;

use App\Http\Controllers\Controller;
use App\Models\AdminCabang;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class GuruController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Dapatkan id pondok dari admin cabang yang sedang login
            $adminCabang = AdminCabang::where('user_id', Auth::id())->first();

            if (!$adminCabang) {
                return redirect()->route('admin-cabang.dashboard')
                    ->withErrors(['error' => 'Data admin cabang tidak ditemukan.']);
            }

            // Ambil data guru berdasarkan pondok_id
            $gurus = Guru::where('pondok_id', $adminCabang->pondok_id)
                ->select([
                    'id',
                    'nama',
                    'nip',
                    'jenis_kelamin',
                    'no_handphone',
                    'email',
                    'tanggal_kerja',
                    'non_aktif'
                ])
                ->get();

            return Inertia::render('AdminCabang/Guru/Index', [
                'gurus' => $gurus,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin-cabang.dashboard')
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('AdminCabang/Guru/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        DB::beginTransaction();

        try {

            $request->validate([
                'nama' => 'required|string|max:255',
                'nip' => 'nullable|string|max:50',
                'gelar_awal' => 'nullable|string|max:50',
                'gelar_akhir' => 'nullable|string|max:50',
                'tempat_lahir' => 'nullable|string|max:100',
                'tanggal_lahir' => 'nullable|date',
                'jenis_kelamin' => 'required|in:L,P',
                'status_menikah' => 'nullable|boolean',
                'alamat' => 'nullable|string',
                'no_identitas' => 'nullable|string|max:50',
                'no_telpon' => 'nullable|string|max:15',
                'no_handphone' => 'nullable|string|max:15',
                'email' => 'nullable|email|max:100',
                'tanggal_kerja' => 'nullable|date',
                'non_aktif' => 'boolean',
                'keterangan' => 'nullable|string',
                'username' => 'required|string|max:50|unique:users,username',
                'password' => 'required|string|min:8',
            ]);
            // Dapatkan id pondok dari admin cabang yang sedang login
            $adminCabang = AdminCabang::where('user_id', Auth::id())->first();

            if (!$adminCabang) {
                throw new \Exception('Data admin cabang tidak ditemukan.');
            }

            // Buat user baru untuk guru
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'guru',
                'status' => true,
            ]);

            if (!$user) {
                throw new \Exception('Gagal membuat user.');
            }

            // Set data guru dengan pondok_id dari admin cabang dan user_id dari user yang baru dibuat
            $guruData = $request->except(['username', 'password']);
            $guruData['pondok_id'] = $adminCabang->pondok_id;
            $guruData['user_id'] = $user->id;

            $guru = Guru::create($guruData);

            if (!$guru) {
                throw new \Exception('Gagal membuat data guru.');
            }

            DB::commit();

            return redirect()->route('admin-cabang.guru.index')
                ->with('success', 'Data guru berhasil ditambahkan.');
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
        try {
            // Dapatkan id pondok dari admin cabang yang sedang login
            $adminCabang = AdminCabang::where('user_id', Auth::id())->first();

            if (!$adminCabang) {
                return redirect()->route('admin-cabang.dashboard')
                    ->withErrors(['error' => 'Data admin cabang tidak ditemukan.']);
            }

            // Ambil data guru berdasarkan id dan pondok_id
            $guru = Guru::where('id', $id)
                ->where('pondok_id', $adminCabang->pondok_id)
                ->first();

            if (!$guru) {
                return redirect()->route('admin-cabang.guru.index')
                    ->withErrors(['error' => 'Data guru tidak ditemukan.']);
            }

            return Inertia::render('AdminCabang/Guru/Show', [
                'guru' => $guru,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin-cabang.guru.index')
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            // Dapatkan id pondok dari admin cabang yang sedang login
            $adminCabang = AdminCabang::where('user_id', Auth::id())->first();

            if (!$adminCabang) {
                return redirect()->route('admin-cabang.dashboard')
                    ->withErrors(['error' => 'Data admin cabang tidak ditemukan.']);
            }

            // Ambil data guru berdasarkan id dan pondok_id
            $guru = Guru::where('id', $id)
                ->where('pondok_id', $adminCabang->pondok_id)
                ->first();

            if (!$guru) {
                return redirect()->route('admin-cabang.guru.index')
                    ->withErrors(['error' => 'Data guru tidak ditemukan.']);
            }

            $user = User::find($guru->user_id);
            if (!$user) {
                throw new \Exception('User untuk guru ini tidak ditemukan.');
            }

            return Inertia::render('AdminCabang/Guru/Edit', [
                'guru' => $guru,
                'user' => [
                    'username' => $user->username,
                    'email' => $user->email,
                ],
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin-cabang.guru.index')
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            // Dapatkan id pondok dari admin cabang yang sedang login
            $adminCabang = AdminCabang::where('user_id', Auth::id())->first();

            if (!$adminCabang) {
                return redirect()->back()->withErrors(['error' => 'Data admin cabang tidak ditemukan.']);
            }

            // Ambil data guru berdasarkan id dan pondok_id
            $guru = Guru::where('id', $id)
                ->where('pondok_id', $adminCabang->pondok_id)
                ->first();

            if (!$guru) {
                return redirect()->route('admin-cabang.guru.index')
                    ->withErrors(['error' => 'Data guru tidak ditemukan.']);
            }

            // Ambil user id dari guru
            $userId = $guru->user_id;

            // Validasi input, gunakan sometimes agar hanya field yang dikirim yang divalidasi
            $request->validate([
                'nama' => 'sometimes|required|string|max:255',
                'nip' => 'sometimes|nullable|string|max:50',
                'gelar_awal' => 'sometimes|nullable|string|max:50',
                'gelar_akhir' => 'sometimes|nullable|string|max:50',
                'tempat_lahir' => 'sometimes|nullable|string|max:100',
                'tanggal_lahir' => 'sometimes|nullable|date',
                'jenis_kelamin' => 'sometimes|required|in:L,P',
                'status_menikah' => 'sometimes|nullable|boolean',
                'alamat' => 'sometimes|nullable|string',
                'no_identitas' => 'sometimes|nullable|string|max:50',
                'no_telpon' => 'sometimes|nullable|string|max:15',
                'no_handphone' => 'sometimes|nullable|string|max:15',
                'email' => 'sometimes|nullable|email|max:100',
                'tanggal_kerja' => 'sometimes|nullable|date',
                'non_aktif' => 'sometimes|boolean',
                'keterangan' => 'sometimes|nullable|string',
                // Perbaiki validasi unique username dan email berdasarkan user id
                'username' => 'sometimes|required|string|max:50|unique:users,username,' . $userId,
                'password' => 'sometimes|nullable|string|min:8',
            ]);

            // Update data user jika ada perubahan username, email, atau password
            if ($guru->user_id) {
                $user = User::find($guru->user_id);
                if ($user) {
                    $userData = [];
                    if ($request->has('username')) {
                        $userData['username'] = $request->username;
                    }
                    if ($request->has('email')) {
                        $userData['email'] = $request->email;
                    }
                    if ($request->filled('password')) {
                        $userData['password'] = Hash::make($request->password);
                    }
                    if (!empty($userData)) {
                        $user->update($userData);
                    }
                }
            }

            // Update data guru, kecualikan username & password
            $guruData = $request->except(['username', 'password']);
            $guru->update($guruData);

            DB::commit();

            return redirect()->route('admin-cabang.guru.index')
                ->with('success', 'Data guru berhasil diperbarui.');
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
        try {
            DB::beginTransaction();

            // Dapatkan id pondok dari admin cabang yang sedang login
            $adminCabang = AdminCabang::where('user_id', Auth::id())->first();

            if (!$adminCabang) {
                return redirect()->back()->withErrors(['error' => 'Data admin cabang tidak ditemukan.']);
            }

            // Ambil data guru berdasarkan id dan pondok_id
            $guru = Guru::where('id', $id)
                ->where('pondok_id', $adminCabang->pondok_id)
                ->first();

            if (!$guru) {
                return redirect()->route('admin-cabang.guru.index')
                    ->withErrors(['error' => 'Data guru tidak ditemukan.']);
            }

            // Hapus guru
            $guru->delete();

            DB::commit();

            return redirect()->route('admin-cabang.guru.index')
                ->with('success', 'Data guru berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
