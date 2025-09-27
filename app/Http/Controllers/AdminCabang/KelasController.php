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
use App\Models\Santri;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil pondok_id dari admin cabang yang login
        $adminCabang = AdminCabang::where('user_id', Auth::id())->first();
        $pondokId = $adminCabang->pondok_id ?? null;

        $q = $request->input('q');

        $query = Kelas::with('waliKelas')->withCount('santris');

        if ($pondokId) {
            $query->where('pondok_id', $pondokId);
        }

        if ($q) {
            $query->where(function ($qry) use ($q) {
                $qry->where('nama', 'like', "%{$q}%")
                    ->orWhere('tingkat', 'like', "%{$q}%")
                    ->orWhereHas('waliKelas', function ($q2) use ($q) {
                        $q2->where('nama', 'like', "%{$q}%");
                    });
            });
        }

        $perPage = 10;
        $kelas = $query->orderBy('nama')->paginate($perPage)->appends($request->only('q'));

        // Transform item supaya frontend mudah akses properti yang dibutuhkan
        $kelasTransformed = $kelas->through(function ($k) {
            return [
                'id' => $k->id,
                'nama' => $k->nama,
                'tingkat' => $k->tingkat,
                'wali_kelas' => $k->waliKelas?->nama,
                'wali_kelas_id' => $k->wali_kelas_id,
                'kapasitas' => $k->kapasitas,
                'terisi' => $k->santris_count ?? 0,
            ];
        });

        return Inertia::render('AdminCabang/Struktur/kelas/Index', [
            'kelas' => $kelasTransformed,
            'filters' => $request->only('q'),
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
        // Ambil kelas beserta wali dan santris (dengan jus terbaru)
        $kelas = Kelas::with([
            'waliKelas',
            'santris.jus' => function ($q) {
                $q->orderBy('created_at', 'desc');
            },
        ])->findOrFail($id);

        // Pastikan kelas milik pondok admin cabang yang login
        $adminCabang = AdminCabang::where('user_id', Auth::id())->first();
        $pondokId = $adminCabang->pondok_id ?? null;
        if ($pondokId && $kelas->pondok_id !== $pondokId) {
            abort(403, 'Anda tidak berwenang mengakses kelas ini.');
        }

        // Transform santri supaya frontend menerima field yang diharapkan
        $santriList = $kelas->santris->map(function ($s) {
            $latestJuz = $s->jus->first(); // karena sudah di-order desc
            $juzLabel = null;
            if ($latestJuz) {
                // coba ambil properti yang umum (sesuaikan jika berbeda)
                $juzLabel = $latestJuz->nama ?? $latestJuz->juz ?? null;
            }
            return [
                'id' => $s->id,
                'nis' => $s->nis ?? null,
                'nama' => $s->nama,
                'jenis_kelamin' => $s->jenis_kelamin,
                'juzTerakhir' => $juzLabel,
            ];
        })->values();

        return Inertia::render('AdminCabang/Struktur/kelas/Show', [
            'kelas' => [
                'id' => $kelas->id,
                'nama' => $kelas->nama,
                'tingkat' => $kelas->tingkat,
                'waliKelas' => $kelas->waliKelas?->nama,
                'kapasitas' => $kelas->kapasitas,
                'keterangan' => $kelas->keterangan,
            ],
            'santri' => $santriList,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Ambil kelas beserta relasi wali
        $kelas = Kelas::with('waliKelas')->findOrFail($id);

        // Pastikan kelas milik pondok admin cabang yang login
        $adminCabang = AdminCabang::where('user_id', Auth::id())->first();
        $pondokId = $adminCabang->pondok_id ?? null;
        if ($pondokId && $kelas->pondok_id !== $pondokId) {
            abort(403, 'Anda tidak berwenang mengakses kelas ini.');
        }

        // Ambil daftar guru untuk pondok tersebut (id + nama)
        $gurus = $pondokId ? Guru::where('pondok_id', $pondokId)->select('id', 'nama')->orderBy('nama')->get() : collect();

        // Kirim ke Inertia
        return Inertia::render('AdminCabang/Struktur/kelas/Edit', [
            'kelas' => [
                'id' => $kelas->id,
                'nama' => $kelas->nama,
                'tingkat' => $kelas->tingkat,
                'wali_kelas_id' => $kelas->wali_kelas_id,
                'kapasitas' => $kelas->kapasitas,
                'keterangan' => $kelas->keterangan,
            ],
            'gurus' => $gurus,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
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
            $kelas = Kelas::findOrFail($id);

            // Pastikan kelas milik pondok admin cabang yang login
            $adminCabang = AdminCabang::where('user_id', Auth::id())->first();
            $pondokId = $adminCabang->pondok_id ?? null;
            if ($pondokId && $kelas->pondok_id !== $pondokId) {
                abort(403, 'Anda tidak berwenang memperbarui kelas ini.');
            }

            $kelas->update([
                'nama' => $validated['nama'],
                'tingkat' => $validated['tingkat'] ?? null,
                'kapasitas' => $validated['kapasitas'] ?? null,
                'wali_kelas_id' => $validated['wali_kelas_id'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            DB::commit();
            return redirect()->route('admin-cabang.struktur.kelas.index')->with('success', 'Kelas berhasil diperbarui');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $kelas = Kelas::withCount('santris')->findOrFail($id);

            // Pastikan admin cabang memiliki pondok yang sama
            $adminCabang = AdminCabang::where('user_id', Auth::id())->first();
            if (!$adminCabang) {
                abort(403, 'Data admin cabang tidak ditemukan.');
            }
            if ($kelas->pondok_id !== $adminCabang->pondok_id) {
                abort(403, 'Anda tidak berwenang menghapus kelas ini.');
            }

            // Cek dependensi: jika ada santri di kelas, tolak penghapusan
            if (($kelas->santris_count ?? 0) > 0) {
                return redirect()->back()->withErrors(['error' => 'Kelas memiliki santri. Pindahkan atau hapus santri terlebih dahulu sebelum menghapus kelas.']);
            }

            // Jika perlu hapus relasi lain yang aman di sini (misal jadwal), tambahkan pengecekan sebelum menghapus

            $kelas->delete();

            DB::commit();
            return redirect()->route('admin-cabang.struktur.kelas.index')->with('success', 'Kelas berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus: ' . $e->getMessage()]);
        }
    }

    /**
     * Show manage santri page for a class.
     */
    public function manageSantri(Request $request, string $id)
    {
        $kelas = Kelas::findOrFail($id);

        // Pastikan kelas milik pondok admin cabang yang login
        $adminCabang = AdminCabang::where('user_id', Auth::id())->first();
        $pondokId = $adminCabang->pondok_id ?? null;
        if ($pondokId && $kelas->pondok_id !== $pondokId) {
            abort(403, 'Anda tidak berwenang mengakses halaman ini.');
        }

        // Ambil semua santri di pondok tersebut (minimal fields) dan latest juz (jika ada)
        $santris = $pondokId
            ? Santri::with(['jus' => function ($q) { $q->orderBy('created_at', 'desc'); }])
                ->where('pondok_id', $pondokId)
                ->select('id', 'nama', 'jenis_kelamin', 'kelas_id')
                ->orderBy('nama')
                ->get()
            : collect();

        $assignedIds = $santris->filter(fn($s) => $s->kelas_id == $kelas->id)->pluck('id')->toArray();

        // Transform santri untuk frontend (ambil latest juz label jika ada)
        $santrisTransformed = $santris->map(function ($s) {
            $latestJuz = $s->jus->first();
            return [
                'id' => $s->id,
                'nis' => $s->nis ?? null,
                'nama' => $s->nama,
                'jenis_kelamin' => $s->jenis_kelamin,
                'kelas_id' => $s->kelas_id,
                'juzTerakhir' => $latestJuz?->nama ?? $latestJuz?->juz ?? null,
            ];
        })->values();

        return Inertia::render('AdminCabang/Struktur/kelas/ManageSantri', [
            'kelas' => [
                'id' => $kelas->id,
                'nama' => $kelas->nama,
                'tingkat' => $kelas->tingkat,
            ],
            'santris' => $santrisTransformed,
            'assignedIds' => $assignedIds,
        ]);
    }

    /**
     * Store santri assignments to a class.
     */
    public function storeSantri(Request $request, string $id)
    {
        $validated = $request->validate([
            'santri_ids' => 'nullable|array',
            'santri_ids.*' => 'integer|exists:santris,id',
        ]);

        DB::beginTransaction();
        try {
            $kelas = Kelas::findOrFail($id);

            // Pastikan kelas milik pondok admin cabang yang login
            $adminCabang = AdminCabang::where('user_id', Auth::id())->first();
            if (!$adminCabang) {
                abort(403, 'Data admin cabang tidak ditemukan.');
            }
            $pondokId = $adminCabang->pondok_id ?? null;
            if ($pondokId && $kelas->pondok_id !== $pondokId) {
                abort(403, 'Anda tidak berwenang mengubah penempatan santri untuk kelas ini.');
            }

            $incoming = collect($validated['santri_ids'] ?? [])->map(fn($v) => (int)$v)->unique()->values()->all();

            // Pastikan semua incoming santri memang milik pondok yang sama
            if (!empty($incoming)) {
                $validCount = Santri::whereIn('id', $incoming)->where('pondok_id', $pondokId)->count();
                if ($validCount !== count($incoming)) {
                    throw new \Exception('Beberapa santri tidak ditemukan di pondok Anda atau tidak valid.');
                }
            }

            // Lepaskan santri yang sebelumnya pada kelas ini tapi tidak ada di incoming
            Santri::where('kelas_id', $kelas->id)
                ->whereNotIn('id', $incoming)
                ->update(['kelas_id' => null]);

            // Set kelas_id untuk incoming santri
            if (!empty($incoming)) {
                Santri::whereIn('id', $incoming)
                    ->where('pondok_id', $pondokId)
                    ->update(['kelas_id' => $kelas->id]);
            }

            DB::commit();
            return redirect()->route('admin-cabang.struktur.kelas.show', $kelas->id)->with('success', 'Penempatan santri berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal menyimpan penempatan santri: ' . $e->getMessage()])->withInput();
        }
    }
}
