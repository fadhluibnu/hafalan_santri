<?php

namespace App\Http\Controllers\AdminCabang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Kelas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\AdminCabang;
use App\Models\Ustadz;
use App\Models\TahunAjaran;
use App\Models\Santri;
use App\Models\SantriKelas;

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

        // Get selected tahun ajaran from session
        $selectedTahunAjaranId = $request->session()->get('selected_tahun_ajaran_id');
        
        // If no tahun ajaran selected, try to get the active one
        if (!$selectedTahunAjaranId && $pondokId) {
            $activeTahunAjaran = TahunAjaran::where('pondok_id', $pondokId)->where('is_active', true)->first();
            $selectedTahunAjaranId = $activeTahunAjaran?->id;
        }

        $q = $request->input('q');

        $query = Kelas::with('waliKelas', 'tahunAjaran')
            ->withCount(['santriKelas as santris_count' => function($q) {
                $q->where('status', 'aktif');
            }]);

        if ($pondokId) {
            $query->where('pondok_id', $pondokId);
        }

        // Filter by selected tahun ajaran
        if ($selectedTahunAjaranId) {
            $query->where('tahun_ajaran_id', $selectedTahunAjaranId);
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
                'tahun_ajaran' => $k->tahunAjaran?->nama,
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

        // Ambil daftar ustadz untuk pondok tersebut (id + nama)
        $ustadzs = $pondokId ? Ustadz::where('pondok_id', $pondokId)->select('id', 'nama')->orderBy('nama')->get() : collect();

        // Ambil daftar tahun ajaran aktif
        $tahunAjarans = $pondokId ? TahunAjaran::where('pondok_id', $pondokId)->where('status', 'aktif')->orderBy('tanggal_mulai', 'desc')->get() : collect();
        $activeTahunAjaran = TahunAjaran::getActiveForPondok($pondokId);

        return Inertia::render('AdminCabang/Struktur/kelas/Create', [
            'ustadzs' => $ustadzs,
            'tahunAjarans' => $tahunAjarans,
            'activeTahunAjaranId' => $activeTahunAjaran?->id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'tingkat' => 'nullable|string|max:50',
            'wali_kelas_id' => 'nullable|exists:ustadzs,id',
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
                'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
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
        // Ambil kelas beserta wali dan santri dari pivot santri_kelas
        $kelas = Kelas::with([
            'waliKelas',
            'tahunAjaran',
            'santriKelas' => function ($q) {
                $q->where('status', 'aktif')->with(['santri.jus' => function ($q2) {
                    $q2->orderBy('created_at', 'desc');
                }]);
            },
        ])->findOrFail($id);

        // Pastikan kelas milik pondok admin cabang yang login
        $adminCabang = AdminCabang::where('user_id', Auth::id())->first();
        $pondokId = $adminCabang->pondok_id ?? null;
        if ($pondokId && $kelas->pondok_id !== $pondokId) {
            abort(403, 'Anda tidak berwenang mengakses kelas ini.');
        }

        // Transform santri dari pivot santri_kelas
        $santriList = $kelas->santriKelas->map(function ($sk) {
            $s = $sk->santri;
            if (!$s) return null;
            
            $latestJuz = $s->jus->first(); // karena sudah di-order desc
            $juzLabel = null;
            if ($latestJuz) {
                $juzLabel = $latestJuz->nama ?? $latestJuz->juz ?? null;
            }
            return [
                'id' => $s->id,
                'nis' => $s->nis ?? null,
                'nama' => $s->nama,
                'jenis_kelamin' => $s->jenis_kelamin,
                'juzTerakhir' => $juzLabel,
            ];
        })->filter()->values();

        return Inertia::render('AdminCabang/Struktur/kelas/Show', [
            'kelas' => [
                'id' => $kelas->id,
                'nama' => $kelas->nama,
                'tingkat' => $kelas->tingkat,
                'tahun_ajaran' => $kelas->tahunAjaran?->nama,
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

        // Ambil daftar ustadz untuk pondok tersebut (id + nama)
        $ustadzs = $pondokId ? Ustadz::where('pondok_id', $pondokId)->select('id', 'nama')->orderBy('nama')->get() : collect();

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
            'ustadzs' => $ustadzs,
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
            'wali_kelas_id' => 'nullable|exists:ustadzs,id',
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
    public function manageSantri(string $id)
    {
        $kelas = Kelas::with('tahunAjaran')->findOrFail($id);

        // Pastikan kelas milik pondok admin cabang yang login
        $adminCabang = AdminCabang::where('user_id', Auth::id())->first();
        $pondokId = $adminCabang->pondok_id ?? null;
        if ($pondokId && $kelas->pondok_id !== $pondokId) {
            abort(403, 'Anda tidak berwenang mengakses halaman ini.');
        }

        $tahunAjaranId = $kelas->tahun_ajaran_id;

        // Ambil santri yang sudah ditempatkan di kelas ini (dari tabel santri_kelas)
        $assignedSantriIds = SantriKelas::where('kelas_id', $kelas->id)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->where('status', 'aktif')
            ->pluck('santri_id')
            ->toArray();

        $assignedSantris = $pondokId
            ? Santri::with(['jus' => function ($q) { $q->orderBy('created_at', 'desc'); }])
                ->where('pondok_id', $pondokId)
                ->whereIn('id', $assignedSantriIds)
                ->select('id', 'nis', 'nama', 'jenis_kelamin')
                ->orderBy('nama')
                ->get()
            : collect();

        // Ambil santri yang belum ditempatkan di kelas manapun pada tahun ajaran ini
        $allPlacedSantriIds = SantriKelas::where('tahun_ajaran_id', $tahunAjaranId)
            ->where('status', 'aktif')
            ->pluck('santri_id')
            ->toArray();

        $availableSantris = $pondokId
            ? Santri::with(['jus' => function ($q) { $q->orderBy('created_at', 'desc'); }])
                ->where('pondok_id', $pondokId)
                ->where('status_santri', 'aktif')
                ->whereNotIn('id', $allPlacedSantriIds)
                ->select('id', 'nis', 'nama', 'jenis_kelamin')
                ->orderBy('nama')
                ->get()
            : collect();

        // Transform untuk frontend (ambil latest juz label jika ada)
        $transform = function ($collection) {
            return $collection->map(function ($s) {
                $latestJuz = $s->jus->first();
                return [
                    'id' => $s->id,
                    'nis' => $s->nis ?? null,
                    'nama' => $s->nama,
                    'jenis_kelamin' => $s->jenis_kelamin,
                    'juzTerakhir' => $latestJuz?->nama ?? $latestJuz?->juz ?? null,
                ];
            })->values();
        };

        $assignedTransformed = $transform($assignedSantris);
        $availableTransformed = $transform($availableSantris);

        // Ambil daftar id santri yang memang sudah ditempatkan pada kelas ini
        $assignedIds = $assignedTransformed->pluck('id')->toArray();

        return Inertia::render('AdminCabang/Struktur/kelas/ManageSantri', [
            'kelas' => [
                'id' => $kelas->id,
                'nama' => $kelas->nama,
                'tingkat' => $kelas->tingkat,
                'tahun_ajaran' => $kelas->tahunAjaran?->nama,
                'kapasitas' => $kelas->kapasitas,
            ],
            'assignedSantris' => $assignedTransformed,
            'availableSantris' => $availableTransformed,
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

            $tahunAjaranId = $kelas->tahun_ajaran_id;
            $incoming = collect($validated['santri_ids'] ?? [])->map(fn($v) => (int)$v)->unique()->values()->all();

            // Pastikan semua incoming santri memang milik pondok yang sama
            if (!empty($incoming)) {
                $validCount = Santri::query()->whereIn('id', $incoming)->where('pondok_id', $pondokId)->count();
                if ($validCount !== count($incoming)) {
                    throw new \Exception('Beberapa santri tidak ditemukan di pondok Anda atau tidak valid.');
                }

                // Pastikan tidak melebihi kapasitas kelas (jika kapasitas di-set)
                if (!is_null($kelas->kapasitas) && count($incoming) > $kelas->kapasitas) {
                    throw new \Exception('Jumlah santri melebihi kapasitas kelas (' . $kelas->kapasitas . ').');
                }
            }

            // Hapus penempatan santri yang sebelumnya di kelas ini tapi tidak ada di incoming
            SantriKelas::where('kelas_id', $kelas->id)
                ->where('tahun_ajaran_id', $tahunAjaranId)
                ->whereNotIn('santri_id', $incoming)
                ->delete();

            // Tambahkan santri baru ke kelas ini
            foreach ($incoming as $santriId) {
                // Cek apakah santri sudah ada di kelas ini
                $existing = SantriKelas::where('santri_id', $santriId)
                    ->where('kelas_id', $kelas->id)
                    ->where('tahun_ajaran_id', $tahunAjaranId)
                    ->first();

                if (!$existing) {
                    // Hapus penempatan lama di kelas lain (jika ada) untuk tahun ajaran ini
                    SantriKelas::where('santri_id', $santriId)
                        ->where('tahun_ajaran_id', $tahunAjaranId)
                        ->delete();

                    // Buat penempatan baru
                    SantriKelas::create([
                        'santri_id' => $santriId,
                        'kelas_id' => $kelas->id,
                        'tahun_ajaran_id' => $tahunAjaranId,
                        'tanggal_masuk' => now(),
                        'status' => 'aktif',
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin-cabang.struktur.kelas.show', $kelas->id)->with('success', 'Penempatan santri berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal menyimpan penempatan santri: ' . $e->getMessage()])->withInput();
        }
    }
}
