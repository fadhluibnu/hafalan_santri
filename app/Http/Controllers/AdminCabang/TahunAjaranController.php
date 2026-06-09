<?php

namespace App\Http\Controllers\AdminCabang;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use App\Models\AdminCabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class TahunAjaranController extends Controller
{
    /**
     * Get pondok_id for current admin cabang
     */
    private function getPondokId()
    {
        $adminCabang = AdminCabang::where('user_id', Auth::id())->first();
        return $adminCabang->pondok_id ?? null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pondokId = $this->getPondokId();

        $tahunAjarans = TahunAjaran::where('pondok_id', $pondokId)
            ->orderBy('tanggal_mulai', 'desc')
            ->get()
            ->map(function ($ta) {
                return [
                    'id' => $ta->id,
                    'nama' => $ta->nama,
                    'semester' => $ta->semester,
                    'tanggal_mulai' => $ta->tanggal_mulai->format('Y-m-d'),
                    'tanggal_selesai' => $ta->tanggal_selesai->format('Y-m-d'),
                    'is_active' => $ta->is_active,
                    'status' => $ta->status,
                    'keterangan' => $ta->keterangan,
                ];
            });

        return Inertia::render('AdminCabang/TahunAjaran/Index', [
            'tahunAjarans' => $tahunAjarans,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('AdminCabang/TahunAjaran/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $pondokId = $this->getPondokId();

        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'semester' => 'required|in:ganjil,genap',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Jika set aktif, nonaktifkan yang lain
            if ($request->boolean('is_active')) {
                TahunAjaran::where('pondok_id', $pondokId)->update(['is_active' => false]);
            }

            TahunAjaran::create([
                'pondok_id' => $pondokId,
                'nama' => $validated['nama'],
                'semester' => $validated['semester'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'keterangan' => $validated['keterangan'] ?? null,
                'is_active' => $request->boolean('is_active'),
                'status' => 'aktif',
            ]);

            DB::commit();
            return redirect()->route('admin-cabang.tahun-ajaran.index')
                ->with('success', 'Tahun Ajaran berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Gagal membuat tahun ajaran: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pondokId = $this->getPondokId();
        $tahunAjaran = TahunAjaran::where('pondok_id', $pondokId)->findOrFail($id);

        return Inertia::render('AdminCabang/TahunAjaran/Edit', [
            'tahunAjaran' => [
                'id' => $tahunAjaran->id,
                'nama' => $tahunAjaran->getRawOriginal('nama'),
                'semester' => $tahunAjaran->semester,
                'tanggal_mulai' => $tahunAjaran->tanggal_mulai->format('Y-m-d'),
                'tanggal_selesai' => $tahunAjaran->tanggal_selesai->format('Y-m-d'),
                'is_active' => $tahunAjaran->is_active,
                'status' => $tahunAjaran->status,
                'keterangan' => $tahunAjaran->keterangan,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pondokId = $this->getPondokId();
        $tahunAjaran = TahunAjaran::where('pondok_id', $pondokId)->findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'semester' => 'required|in:ganjil,genap',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Jika set aktif, nonaktifkan yang lain
            if ($request->boolean('is_active')) {
                TahunAjaran::where('pondok_id', $pondokId)
                    ->where('id', '!=', $id)
                    ->update(['is_active' => false]);
            }

            $tahunAjaran->update([
                'nama' => $validated['nama'],
                'semester' => $validated['semester'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'keterangan' => $validated['keterangan'] ?? null,
                'is_active' => $request->boolean('is_active'),
            ]);

            DB::commit();
            return redirect()->route('admin-cabang.tahun-ajaran.index')
                ->with('success', 'Tahun Ajaran berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Gagal memperbarui tahun ajaran: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pondokId = $this->getPondokId();
        $tahunAjaran = TahunAjaran::where('pondok_id', $pondokId)->findOrFail($id);

        DB::beginTransaction();
        try {
            // Cek apakah ada kelas yang terkait
            if ($tahunAjaran->kelas()->count() > 0) {
                throw new \Exception('Tidak dapat menghapus tahun ajaran yang memiliki kelas.');
            }

            $tahunAjaran->delete();

            DB::commit();
            return redirect()->route('admin-cabang.tahun-ajaran.index')
                ->with('success', 'Tahun Ajaran berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Set tahun ajaran as active
     */
    public function setActive(string $id)
    {
        $pondokId = $this->getPondokId();
        $tahunAjaran = TahunAjaran::where('pondok_id', $pondokId)->findOrFail($id);

        DB::beginTransaction();
        try {
            $tahunAjaran->setAsActive();

            DB::commit();
            return redirect()->route('admin-cabang.tahun-ajaran.index')
                ->with('success', 'Tahun Ajaran berhasil diaktifkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Gagal mengaktifkan tahun ajaran: ' . $e->getMessage()]);
        }
    }

    /**
     * Close tahun ajaran (tutup tahun ajaran)
     */
    public function close(string $id)
    {
        $pondokId = $this->getPondokId();
        $tahunAjaran = TahunAjaran::where('pondok_id', $pondokId)->findOrFail($id);

        DB::beginTransaction();
        try {
            $tahunAjaran->update([
                'status' => 'selesai',
                'is_active' => false,
            ]);

            DB::commit();
            return redirect()->route('admin-cabang.tahun-ajaran.index')
                ->with('success', 'Tahun Ajaran berhasil ditutup.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menutup tahun ajaran: ' . $e->getMessage()]);
        }
    }
}
