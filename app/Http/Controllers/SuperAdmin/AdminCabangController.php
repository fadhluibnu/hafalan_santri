<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AdminCabang;
use App\Models\Pondok;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AdminCabangController extends Controller
{
    /**
     * Display a listing of the admin cabang.
     */
    public function index()
    {
        $adminCabangs = AdminCabang::with('pondok', 'user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // return $adminCabangs;

        return Inertia::render('SuperAdmin/AdminCabang/Index', [
            'adminCabangs' => $adminCabangs,
        ]);
    }

    /**
     * Show the form for creating a new admin cabang.
     */
    public function create()
    {
        $pondoks = Pondok::select('id', 'nama')->get();

        return Inertia::render('SuperAdmin/AdminCabang/Create', [
            'pondoks' => $pondoks,
        ]);
    }

    /**
     * Store a newly created admin cabang in storage.
     */
    public function store(Request $request)
    {

        DB::beginTransaction();

        try {

            $validate = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:15',
                'jabatan' => 'required|string|max:50',
                'pondok_id' => 'required|exists:pondoks,id',
                'email' => 'required|email|unique:users,email',
                'username' => 'required|string|max:255|unique:users,username',
            ]);

            // return $validate;
            // Create user account first
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'admin_cabang',
                'status' => 1,
            ]);

            if (!$user) {
                throw new \Exception('Gagal membuat user.');
            }

            // Then create admin cabang with the user ID
            $adminCabang = AdminCabang::create([
                'user_id' => $user->id,
                'pondok_id' => $request->pondok_id,
                'name' => $request->name,
                'phone' => $request->phone,
                'jabatan' => $request->jabatan,
            ]);

            if (!$adminCabang) {
                throw new \Exception('Gagal membuat admin cabang.');
            }

            DB::commit();

            return redirect()->route('super-admin.admin-cabang.index')
                ->with('success', 'Admin Cabang berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified admin cabang.
     */
    public function show(AdminCabang $adminCabang)
    {
        $adminCabang->load('user', 'pondok');

        return Inertia::render('SuperAdmin/AdminCabang/Show', [
            'adminCabang' => $adminCabang,
        ]);
    }

    /**
     * Show the form for editing the specified admin cabang.
     */
    public function edit(AdminCabang $adminCabang)
    {
        $adminCabang->load('user', 'pondok');
        $pondoks = Pondok::select('id', 'nama')->get();

        return Inertia::render('SuperAdmin/AdminCabang/Edit', [
            'adminCabang' => $adminCabang,
            'pondoks' => $pondoks,
        ]);
    }

    /**
     * Update the specified admin cabang in storage.
     */
    public function update(Request $request, AdminCabang $adminCabang)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'jabatan' => 'required|string|max:50',
            'pondok_id' => 'required|exists:pondoks,id',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($adminCabang->user_id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($adminCabang->user_id)],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update user data
        $user = User::findOrFail($adminCabang->user_id);
        $userData = [
            'username' => $request->username,
            'email' => $request->email,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        // Update admin cabang data
        $adminCabang->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'jabatan' => $request->jabatan,
            'pondok_id' => $request->pondok_id,
        ]);

        return redirect()->route('super-admin.admin-cabang.index')
            ->with('success', 'Admin Cabang berhasil diperbarui.');
    }

    /**
     * Remove the specified admin cabang from storage.
     */
    public function destroy(AdminCabang $adminCabang)
    {
        $userId = $adminCabang->user_id;

        // Delete admin cabang first
        $adminCabang->delete();

        // Then delete the associated user
        User::find($userId)->delete();

        return redirect()->route('super-admin.admin-cabang.index')
            ->with('success', 'Admin Cabang berhasil dihapus.');
    }
}
