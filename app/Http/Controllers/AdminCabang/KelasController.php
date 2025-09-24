<?php

namespace App\Http\Controllers\AdminCabang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Kelas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\AdminCabang;
use App\Models\Guru;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Tampilkan daftar kelas (opsional filter berdasarkan pondok user jika ada)
        $pondokId = Auth::user()->pondok_id ?? null;
        $kelas = $pondokId ? Kelas::where('pondok_id', $pondokId)->get() : Kelas::all();

        return Inertia::render('AdminCabang/Struktur/kelas/Index', [
            'kelas' => $kelas,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil pondok_id dari admin cabang yang login
        $adminCabang = AdminCabang::where('user_id', Auth::id())->first();
        $pondokId = $adminCabang->pondok_id ?? null;

        // Ambil daftar guru untuk pondok tersebut (id + nama)
        $gurus = $pondokId ? Guru::where('pondok_id', $pondokId)->select('id', 'nama')->orderBy('nama')->get() : collect();

        return Inertia::render('AdminCabang/Struktur/kelas/Create', [
            'gurus' => $gurus,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tingkat' => 'nullable|string|max:50',
            'wali_kelas_id' => 'nullable|exists:gurus,id',
            'kapasitas' => 'nullable|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Dapatkan pondok dari admin cabang login
            $adminCabang = AdminCabang::where('user_id', Auth::id())->first();
            $pondokId = $adminCabang->pondok_id ?? null;

            $kelas = Kelas::create([
                'pondok_id' => $pondokId,
                'nama' => $validated['nama'],
                'tingkat' => $validated['tingkat'] ?? null,
                'kapasitas' => $validated['kapasitas'] ?? null,
                'wali_kelas_id' => $validated['wali_kelas_id'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
                'status' => true,
            ]);

            DB::commit();
            return redirect()->route('admin-cabang.struktur.kelas.index')->with('success', 'Kelas berhasil dibuat');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage()])->withInput();
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
