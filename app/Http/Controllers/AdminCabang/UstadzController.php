<?php

namespace App\Http\Controllers\AdminCabang;

use App\Http\Controllers\Controller;
use App\Models\AdminCabang;
use App\Models\Ustadz;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UstadzController extends Controller
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

            // Ambil data ustadz berdasarkan pondok_id
            $ustadzs = Ustadz::where('pondok_id', $adminCabang->pondok_id)
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
                ->orderBy('nama')
                ->paginate(10);

            return Inertia::render('AdminCabang/Ustadz/Index', [
                'ustadzs' => $ustadzs,
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
        return Inertia::render('AdminCabang/Ustadz/Create');
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
                'nip' => 'required|string|max:50|unique:ustadzs,nip',
                'gelar_awal' => 'nullable|string|max:50',
                'gelar_akhir' => 'nullable|string|max:50',
                'tempat_lahir' => 'required|string|max:100',
                'tanggal_lahir' => 'required|date',
                'jenis_kelamin' => 'required|in:L,P',
                'status_menikah' => 'required|boolean',
                'alamat' => 'required|string',
                'no_identitas' => 'required|string|max:50',
                'no_telpon' => 'required|string|max:15',
                'no_handphone' => 'required|string|max:15',
                'email' => 'required|email|max:100',
                'tanggal_kerja' => 'required|date',
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

            // Buat user baru untuk ustadz
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'ustadz',
                'status' => true,
            ]);

            if (!$user) {
                throw new \Exception('Gagal membuat user.');
            }

            // Set data ustadz dengan pondok_id dari admin cabang dan user_id dari user yang baru dibuat
            $ustadzData = $request->except(['username', 'password']);
            $ustadzData['pondok_id'] = $adminCabang->pondok_id;
            $ustadzData['user_id'] = $user->id;

            $ustadz = Ustadz::create($ustadzData);

            if (!$ustadz) {
                throw new \Exception('Gagal membuat data ustadz.');
            }

            DB::commit();

            return redirect()->route('admin-cabang.ustadz.index')
                ->with('success', 'Data ustadz berhasil ditambahkan.');
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

            // Ambil data ustadz berdasarkan id dan pondok_id
            $ustadz = Ustadz::where('id', $id)
                ->where('pondok_id', $adminCabang->pondok_id)
                ->first();

            if (!$ustadz) {
                return redirect()->route('admin-cabang.ustadz.index')
                    ->withErrors(['error' => 'Data ustadz tidak ditemukan.']);
            }

            return Inertia::render('AdminCabang/Ustadz/Show', [
                'ustadz' => $ustadz,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin-cabang.ustadz.index')
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

            // Ambil data ustadz berdasarkan id dan pondok_id
            $ustadz = Ustadz::where('id', $id)
                ->where('pondok_id', $adminCabang->pondok_id)
                ->first();

            if (!$ustadz) {
                return redirect()->route('admin-cabang.ustadz.index')
                    ->withErrors(['error' => 'Data ustadz tidak ditemukan.']);
            }

            $user = User::find($ustadz->user_id);
            if (!$user) {
                throw new \Exception('User untuk ustadz ini tidak ditemukan.');
            }

            return Inertia::render('AdminCabang/Ustadz/Edit', [
                'ustadz' => $ustadz,
                'user' => [
                    'username' => $user->username,
                    'email' => $user->email,
                ],
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin-cabang.ustadz.index')
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

            // Ambil data ustadz berdasarkan id dan pondok_id
            $ustadz = Ustadz::where('id', $id)
                ->where('pondok_id', $adminCabang->pondok_id)
                ->first();

            if (!$ustadz) {
                return redirect()->route('admin-cabang.ustadz.index')
                    ->withErrors(['error' => 'Data ustadz tidak ditemukan.']);
            }

            // Ambil user id dari ustadz
            $userId = $ustadz->user_id;

            $request->validate([
                'nama' => 'required|string|max:255',
                'nip' => 'required|string|max:50|unique:ustadzs,nip,' . $ustadz->id,
                'gelar_awal' => 'nullable|string|max:50',
                'gelar_akhir' => 'nullable|string|max:50',
                'tempat_lahir' => 'required|string|max:100',
                'tanggal_lahir' => 'required|date',
                'jenis_kelamin' => 'required|in:L,P',
                'status_menikah' => 'boolean',
                'alamat' => 'required|string',
                'no_identitas' => 'required|string|max:50',
                'no_telpon' => 'required|string|max:15',
                'no_handphone' => 'required|string|max:15',
                'email' => 'required|email|max:100',
                'tanggal_kerja' => 'required|date',
                'non_aktif' => 'boolean',
                'keterangan' => 'nullable|string',
                'username' => 'required|string|max:50|unique:users,username,' . $userId,
                'password' => 'nullable|string|min:8',
            ]);

            // Update data user jika ada perubahan username, email, atau password
            if ($ustadz->user_id) {
                $user = User::find($ustadz->user_id);
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

            // Update data ustadz, kecualikan username & password
            $ustadzData = $request->except(['username', 'password']);
            $ustadz->update($ustadzData);

            DB::commit();

            return redirect()->route('admin-cabang.ustadz.index')
                ->with('success', 'Data ustadz berhasil diperbarui.');
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

            // Ambil data ustadz berdasarkan id dan pondok_id
            $ustadz = Ustadz::where('id', $id)
                ->where('pondok_id', $adminCabang->pondok_id)
                ->first();

            if (!$ustadz) {
                return redirect()->route('admin-cabang.ustadz.index')
                    ->withErrors(['error' => 'Data ustadz tidak ditemukan.']);
            }

            // Hapus ustadz
            $ustadz->delete();

            DB::commit();

            return redirect()->route('admin-cabang.ustadz.index')
                ->with('success', 'Data ustadz berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
