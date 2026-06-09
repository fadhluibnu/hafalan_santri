<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Ustadz;
use App\Models\Pondok;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\Hafalan;
use App\Models\QuranSurah;
use App\Models\SantriKelas;
use App\Models\SkemaPenilaian;
use App\Models\TahunAjaran;

class HafalanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ustadz = Ustadz::where('user_id', Auth::id())->first();
        $pondokId = $ustadz->pondok_id ?? null;

        // Ambil data hafalan yang terkait dengan pondok ustadz
        $hafalan = Hafalan::with(['santri', 'kelas', 'ustadz', 'dariSurah', 'sampaiSurah'])
            ->whereHas('kelas', function ($query) use ($pondokId) {
                $query->where('pondok_id', $pondokId);
            })
            ->orderBy('tanggal_setor', 'desc')
            ->get()
            ->map(function ($h) {
                return [
                    'id' => $h->id,
                    'santri' => $h->santri->nama,
                    'kelas' => $h->kelas->nama,
                    'ustadz' => $h->ustadz->nama ?? '-',
                    'tanggal_setor' => $h->tanggal_setor,
                    'juz' => $h->juz,
                    'dari_surat' => $h->dariSurah->name,
                    'dari_ayat' => $h->dari_ayat,
                    'sampai_surat' => $h->sampaiSurah->name,
                    'sampai_ayat' => $h->sampai_ayat,
                    'kategori' => $h->kategori,
                    'nilai' => $h->nilai,
                ];
            });

        return Inertia::render('Ustadz/Hafalan/Index', [
            'hafalan' => $hafalan,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil ustadz yang login untuk mengetahui pondok
        $ustadz = Ustadz::where('user_id', Auth::id())->first();
        $pondokId = $ustadz->pondok_id ?? null;
        $activeTahunAjaran = TahunAjaran::where('pondok_id', $pondokId)
            ->where('is_active', true)
            ->first();

        // Ambil kelas untuk pondok
        $classesQuery = $pondokId
            ? Kelas::where('pondok_id', $pondokId)->select('id', 'nama', 'tahun_ajaran_id')->orderBy('nama')
            : Kelas::query()->whereRaw('1=0');
        if ($activeTahunAjaran) {
            $classesQuery->where('tahun_ajaran_id', $activeTahunAjaran->id);
        }
        $classes = $classesQuery->get();

        // Ambil santri berdasarkan penempatan aktif di santri_kelas
        $santriPlacementQuery = SantriKelas::with('santri:id,nis,nama,pondok_id')
            ->where('status', 'aktif')
            ->whereHas('santri', function ($q) use ($pondokId) {
                $q->where('pondok_id', $pondokId);
            });
        if ($activeTahunAjaran) {
            $santriPlacementQuery->where('tahun_ajaran_id', $activeTahunAjaran->id);
        }

        $santrisByClass = $santriPlacementQuery->get()
            ->groupBy('kelas_id')
            ->map(function ($group, $kelasId) {
                return $group->map(function ($placement) use ($kelasId) {
                    $s = $placement->santri;
                    if (!$s) {
                        return null;
                    }

                return [
                        'id' => $s->id,
                        'nis' => $s->nis ?? null,
                        'nama' => $s->nama,
                        'kelas_id' => (int) $kelasId,
                    ];
                })->filter()->values();
            });

        // pastikan kelas tanpa santri tetap memiliki key array kosong supaya frontend aman
        $classes->each(function ($kelas) use (&$santrisByClass) {
            if (!$santrisByClass->has($kelas->id)) {
                $santrisByClass->put($kelas->id, collect());
            }
        });

        $santrisByClass = $santrisByClass->map(function ($items) {
            return $items->values();
        });

        // Ambil daftar ustadz di pondok agar bisa memilih (biasanya ustadz sendiri sudah default)
        $ustadzs = $pondokId ? Ustadz::where('pondok_id', $pondokId)->select('id', 'nama')->orderBy('nama')->get() : collect();

        // Ambil daftar surah Quran untuk select
        $surahs = QuranSurah::select('id', 'name', 'jumlah_ayat')->orderBy('id')->get();

        return Inertia::render('Ustadz/Hafalan/Create', [
            'classes' => $classes,
            'santrisByClass' => $santrisByClass,
            'ustadzs' => $ustadzs,
            'surahs' => $surahs,
            'currentUstadzId' => $ustadz->id ?? null,
            'skemaPenilaian' => $this->getSkemaPenilaianInfo($pondokId),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'kelas_id' => 'required|integer|exists:kelas,id',
                'santri_id' => 'required|integer|exists:santris,id',
                'ustadz_id' => 'required|integer|exists:ustadzs,id',
                'tanggal_setor' => 'required|date',
                'juz' => 'required|integer|min:1',
                'dari_surat' => 'required|integer|exists:quran_surahs,id',
                'dari_ayat' => 'required|integer|min:1',
                'sampai_surat' => 'required|integer|exists:quran_surahs,id',
                'sampai_ayat' => 'required|integer|min:1',
                'kategori' => 'required|string|max:100',
                'nilai' => 'required|string|max:20',
                'catatan' => 'nullable|string',
            ]);

            // Additional validation: Ensure ayat does not exceed jumlah_ayat for selected surah
            $dariSurah = QuranSurah::findOrFail($validated['dari_surat']);
            if ($validated['dari_ayat'] > $dariSurah->jumlah_ayat) {
                throw new \Exception('Dari ayat melebihi jumlah ayat surah yang dipilih.');
            }
            $sampaiSurah = QuranSurah::findOrFail($validated['sampai_surat']);
            if ($validated['sampai_ayat'] > $sampaiSurah->jumlah_ayat) {
                throw new \Exception('Sampai ayat melebihi jumlah ayat surah yang dipilih.');
            }

            $ustadz = Ustadz::findOrFail($validated['ustadz_id']);
            // Pastikan ustadz yang melakukan action berada di pondok yang sama dengan santri/kelas
            $loggedUstadz = Ustadz::where('user_id', Auth::id())->first();
            $pondokId = $loggedUstadz->pondok_id ?? null;

            if ($pondokId && $ustadz->pondok_id !== $pondokId) {
                abort(403, 'Anda tidak berwenang memilih ustadz dari pondok lain.');
            }

            $kelas = Kelas::findOrFail($validated['kelas_id']);
            if ($pondokId && $kelas->pondok_id !== $pondokId) {
                abort(403, 'Kelas tidak ditemukan di pondok Anda.');
            }

            $santri = Santri::findOrFail($validated['santri_id']);
            if ($pondokId && $santri->pondok_id !== $pondokId) {
                throw new \Exception('Santri tidak berada di pondok Anda.');
            }

            $isAssignedToKelas = SantriKelas::query()
                ->where('santri_id', $santri->id)
                ->where('kelas_id', $kelas->id)
                ->where('status', 'aktif')
                ->when($kelas->tahun_ajaran_id, function ($q) use ($kelas) {
                    $q->where('tahun_ajaran_id', $kelas->tahun_ajaran_id);
                })
                ->exists();

            if (!$isAssignedToKelas) {
                throw new \Exception('Santri tidak terdaftar di kelas yang dipilih.');
            }

            $nilai = $this->normalizeNilaiForPondok($pondokId, $validated['nilai']);

            // Simpan hafalan
            Hafalan::create([
                'santri_id' => $santri->id,
                'ustadz_id' => $ustadz->id,
                'kelas_id' => $kelas->id,
                'tanggal_setor' => $validated['tanggal_setor'],
                'juz' => $validated['juz'],
                'dari_surat' => $validated['dari_surat'],
                'dari_ayat' => $validated['dari_ayat'],
                'sampai_surat' => $validated['sampai_surat'],
                'sampai_ayat' => $validated['sampai_ayat'],
                'kategori' => $validated['kategori'],
                'nilai' => $nilai,
                'catatan' => $validated['catatan'] ?? null,
            ]);

            DB::commit();
            return redirect()->route('ustadz.hafalan.index')->with('success', 'Setoran hafalan berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal menyimpan setoran: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $hafalan = Hafalan::with(['santri', 'kelas', 'ustadz', 'dariSurah', 'sampaiSurah'])->findOrFail($id);
        
        return Inertia::render('Ustadz/Hafalan/Show', [
            'hafalan' => $hafalan,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $ustadz = Ustadz::where('user_id', Auth::id())->first();
        $pondokId = $ustadz->pondok_id ?? null;
        $activeTahunAjaran = TahunAjaran::where('pondok_id', $pondokId)
            ->where('is_active', true)
            ->first();

        // Get hafalan data
        $hafalan = Hafalan::with(['santri', 'kelas', 'ustadz', 'dariSurah', 'sampaiSurah'])->findOrFail($id);

        // Verify hafalan belongs to the same pondok
        if ($pondokId && $hafalan->kelas->pondok_id !== $pondokId) {
            abort(403, 'Anda tidak berwenang mengedit data ini.');
        }

        // Ambil kelas untuk pondok
        $classesQuery = $pondokId
            ? Kelas::where('pondok_id', $pondokId)->select('id', 'nama', 'tahun_ajaran_id')->orderBy('nama')
            : Kelas::query()->whereRaw('1=0');
        if ($activeTahunAjaran) {
            $classesQuery->where('tahun_ajaran_id', $activeTahunAjaran->id);
        }
        $classes = $classesQuery->get();

        $santriPlacementQuery = SantriKelas::with('santri:id,nis,nama,pondok_id')
            ->where('status', 'aktif')
            ->whereHas('santri', function ($q) use ($pondokId) {
                $q->where('pondok_id', $pondokId);
            });
        if ($activeTahunAjaran) {
            $santriPlacementQuery->where('tahun_ajaran_id', $activeTahunAjaran->id);
        }

        $santrisByClass = $santriPlacementQuery->get()
            ->groupBy('kelas_id')
            ->map(function ($group, $kelasId) {
                return $group->map(function ($placement) use ($kelasId) {
                    $s = $placement->santri;
                    if (!$s) {
                        return null;
                    }

                return [
                        'id' => $s->id,
                        'nis' => $s->nis ?? null,
                        'nama' => $s->nama,
                        'kelas_id' => (int) $kelasId,
                    ];
                })->filter()->values();
            });

        $classes->each(function ($kelas) use (&$santrisByClass) {
            if (!$santrisByClass->has($kelas->id)) {
                $santrisByClass->put($kelas->id, collect());
            }
        });

        $santrisByClass = $santrisByClass->map(function ($items) {
            return $items->values();
        });

        // Ambil daftar ustadz
        $ustadzs = $pondokId ? Ustadz::where('pondok_id', $pondokId)->select('id', 'nama')->orderBy('nama')->get() : collect();

        // Ambil daftar surah Quran
        $surahs = QuranSurah::select('id', 'name', 'jumlah_ayat')->orderBy('id')->get();

        return Inertia::render('Ustadz/Hafalan/Edit', [
            'hafalan' => [
                'id' => $hafalan->id,
                'kelas_id' => $hafalan->kelas_id,
                'santri_id' => $hafalan->santri_id,
                'ustadz_id' => $hafalan->ustadz_id,
                'tanggal_setor' => $hafalan->tanggal_setor,
                'juz' => $hafalan->juz,
                'dari_surat' => $hafalan->dari_surat,
                'dari_ayat' => $hafalan->dari_ayat,
                'sampai_surat' => $hafalan->sampai_surat,
                'sampai_ayat' => $hafalan->sampai_ayat,
                'kategori' => $hafalan->kategori,
                'nilai' => $hafalan->nilai,
                'catatan' => $hafalan->catatan,
            ],
            'classes' => $classes,
            'santrisByClass' => $santrisByClass,
            'ustadzs' => $ustadzs,
            'surahs' => $surahs,
            'skemaPenilaian' => $this->getSkemaPenilaianInfo($pondokId),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $hafalan = Hafalan::findOrFail($id);

            $validated = $request->validate([
                'kelas_id' => 'required|integer|exists:kelas,id',
                'santri_id' => 'required|integer|exists:santris,id',
                'ustadz_id' => 'required|integer|exists:ustadzs,id',
                'tanggal_setor' => 'required|date',
                'juz' => 'required|integer|min:1',
                'dari_surat' => 'required|integer|exists:quran_surahs,id',
                'dari_ayat' => 'required|integer|min:1',
                'sampai_surat' => 'required|integer|exists:quran_surahs,id',
                'sampai_ayat' => 'required|integer|min:1',
                'kategori' => 'required|string|max:100',
                'nilai' => 'required|string|max:20',
                'catatan' => 'nullable|string',
            ]);

            // Verify access
            $loggedUstadz = Ustadz::where('user_id', Auth::id())->first();
            $pondokId = $loggedUstadz->pondok_id ?? null;

            if ($pondokId && $hafalan->kelas->pondok_id !== $pondokId) {
                abort(403, 'Anda tidak berwenang memperbarui data ini.');
            }

            $kelas = Kelas::findOrFail($validated['kelas_id']);
            $santri = Santri::findOrFail($validated['santri_id']);

            if ($pondokId && $kelas->pondok_id !== $pondokId) {
                throw new \Exception('Kelas tidak ditemukan di pondok Anda.');
            }

            if ($pondokId && $santri->pondok_id !== $pondokId) {
                throw new \Exception('Santri tidak berada di pondok Anda.');
            }

            $isAssignedToKelas = SantriKelas::query()
                ->where('santri_id', $santri->id)
                ->where('kelas_id', $kelas->id)
                ->where('status', 'aktif')
                ->when($kelas->tahun_ajaran_id, function ($q) use ($kelas) {
                    $q->where('tahun_ajaran_id', $kelas->tahun_ajaran_id);
                })
                ->exists();

            if (!$isAssignedToKelas) {
                throw new \Exception('Santri tidak terdaftar di kelas yang dipilih.');
            }

            $nilai = $this->normalizeNilaiForPondok($pondokId, $validated['nilai']);

            $hafalan->update([
                'kelas_id' => $validated['kelas_id'],
                'santri_id' => $validated['santri_id'],
                'ustadz_id' => $validated['ustadz_id'],
                'tanggal_setor' => $validated['tanggal_setor'],
                'juz' => $validated['juz'],
                'dari_surat' => $validated['dari_surat'],
                'dari_ayat' => $validated['dari_ayat'],
                'sampai_surat' => $validated['sampai_surat'],
                'sampai_ayat' => $validated['sampai_ayat'],
                'kategori' => $validated['kategori'],
                'nilai' => $nilai,
                'catatan' => $validated['catatan'] ?? null,
            ]);

            DB::commit();
            return redirect()->route('ustadz.hafalan.index')->with('success', 'Setoran hafalan berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal memperbarui setoran: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $hafalan = Hafalan::findOrFail($id);

            // Verify access
            $loggedUstadz = Ustadz::where('user_id', Auth::id())->first();
            $pondokId = $loggedUstadz->pondok_id ?? null;

            if ($pondokId && $hafalan->kelas->pondok_id !== $pondokId) {
                abort(403, 'Anda tidak berwenang menghapus data ini.');
            }

            $hafalan->delete();

            DB::commit();
            return redirect()->route('ustadz.hafalan.index')->with('success', 'Setoran hafalan berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus setoran: ' . $e->getMessage()]);
        }
    }

    private function getSkemaPenilaianInfo(?int $pondokId): array
    {
        if (!$pondokId) {
            return ['tipe' => 'label', 'items' => []];
        }

        $skema = SkemaPenilaian::activeForPondok($pondokId);
        if (!$skema) {
            return ['tipe' => 'label', 'items' => []];
        }

        $snapshot = $skema->toSnapshot();
        $items = $snapshot['items'] ?? [];

        $mappedItems = collect($items)
            ->map(function (array $item) {
                $singkatan = strtoupper(trim((string) ($item['singkatan'] ?? '')));
                $nama = trim((string) ($item['nama'] ?? ''));

                if ($singkatan === '') {
                    return null;
                }

                return [
                    'value' => $singkatan,
                    'label' => $nama !== '' ? "{$singkatan} - {$nama}" : $singkatan,
                    'batas_bawah' => $item['batas_bawah'] ?? null,
                    'batas_atas' => $item['batas_atas'] ?? null,
                ];
            })
            ->filter()
            ->values()
            ->all();

        return [
            'tipe' => $skema->tipe,
            'items' => $mappedItems,
        ];
    }

    private function normalizeNilaiForPondok(?int $pondokId, mixed $nilaiInput): string
    {
        $skemaInfo = $this->getSkemaPenilaianInfo($pondokId);
        $tipe = $skemaInfo['tipe'];
        
        $nilai = trim((string) $nilaiInput);
        
        if ($tipe === 'numeric') {
            if (!is_numeric($nilai) || $nilai < 0 || $nilai > 100) {
                throw new \Exception('Untuk skema angka murni, nilai harus berupa angka antara 0 dan 100.');
            }
            return (string)(float)$nilai;
        }

        // Kategori / Label
        $nilai = strtoupper($nilai);
        $allowed = collect($skemaInfo['items'])->pluck('value');

        if ($allowed->isEmpty()) {
            throw new \Exception('Skema penilaian aktif untuk pondok ini belum diatur.');
        }

        if (!$allowed->contains($nilai)) {
            throw new \Exception('Nilai kategori tidak ada di skema penilaian pondok.');
        }

        return $nilai;
    }
}
