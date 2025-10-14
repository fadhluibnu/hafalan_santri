<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Guru;
use App\Models\Pondok;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\Hafalan;
use App\Models\QuranSurah;

class HafalanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $guru = Guru::where('user_id', Auth::id())->first();
        $pondokId = $guru->pondok_id ?? null;

        // Ambil data hafalan yang terkait dengan pondok guru
        $hafalan = Hafalan::with(['santri', 'kelas', 'guru', 'dariSurah', 'sampaiSurah'])
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
                    'guru' => $h->guru->nama,
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

        return Inertia::render('Guru/Hafalan/Index', [
            'hafalan' => $hafalan,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil guru yang login untuk mengetahui pondok
        $guru = Guru::where('user_id', Auth::id())->first();
        $pondokId = $guru->pondok_id ?? null;

        // Ambil kelas untuk pondok
        $classes = $pondokId ? Kelas::where('pondok_id', $pondokId)->select('id', 'nama')->orderBy('nama')->get() : collect();

        // Ambil santri pondok dan group by kelas_id (dipakai frontend untuk menampilkan santri berdasarkan kelas terpilih)
        $santris = $pondokId
            ? Santri::where('pondok_id', $pondokId)->select('id', 'nama', 'kelas_id')->orderBy('nama')->get()
            : collect();

        $santrisByClass = $santris->groupBy('kelas_id')->map(function ($group) {
            return $group->map(function ($s) {
                return [
                    'id' => $s->id,
                    'nis' => $s->nis ?? null,
                    'nama' => $s->nama,
                    'kelas_id' => $s->kelas_id,
                ];
            })->values();
        });

        // Ambil daftar guru di pondok agar bisa memilih (biasanya guru sendiri sudah default)
        $gurus = $pondokId ? Guru::where('pondok_id', $pondokId)->select('id', 'nama')->orderBy('nama')->get() : collect();

        // Ambil daftar surah Quran untuk select
        $surahs = QuranSurah::select('id', 'name', 'jumlah_ayat')->orderBy('id')->get();

        return Inertia::render('Guru/Hafalan/Create', [
            'classes' => $classes,
            'santrisByClass' => $santrisByClass,
            'gurus' => $gurus,
            'surahs' => $surahs,
            'currentGuruId' => $guru->id ?? null,
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
                'guru_id' => 'required|integer|exists:gurus,id',
                'tanggal_setor' => 'required|date',
                'juz' => 'required|integer|min:1',
                'dari_surat' => 'required|integer|exists:quran_surahs,id',
                'dari_ayat' => 'required|integer|min:1',
                'sampai_surat' => 'required|integer|exists:quran_surahs,id',
                'sampai_ayat' => 'required|integer|min:1',
                'kategori' => 'required|string|max:100',
                'nilai' => 'required|string|max:5',
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

            $guru = Guru::findOrFail($validated['guru_id']);
            // Pastikan guru yang melakukan action berada di pondok yang sama dengan santri/kelas
            $loggedGuru = Guru::where('user_id', Auth::id())->first();
            $pondokId = $loggedGuru->pondok_id ?? null;

            if ($pondokId && $guru->pondok_id !== $pondokId) {
                abort(403, 'Anda tidak berwenang memilih guru dari pondok lain.');
            }

            $kelas = Kelas::findOrFail($validated['kelas_id']);
            if ($pondokId && $kelas->pondok_id !== $pondokId) {
                abort(403, 'Kelas tidak ditemukan di pondok Anda.');
            }

            $santri = Santri::findOrFail($validated['santri_id']);
            if ($pondokId && $santri->pondok_id !== $pondokId) {
                throw new \Exception('Santri tidak berada di pondok Anda.');
            }
            // Pastikan santri sesuai dengan kelas
            if ((int)$santri->kelas_id !== (int)$kelas->id) {
                throw new \Exception('Santri tidak terdaftar di kelas yang dipilih.');
            }

            // Simpan hafalan
            Hafalan::create([
                'santri_id' => $santri->id,
                'guru_id' => $guru->id,
                'kelas_id' => $kelas->id,
                'tanggal_setor' => $validated['tanggal_setor'],
                'juz' => $validated['juz'],
                'dari_surat' => $validated['dari_surat'],
                'dari_ayat' => $validated['dari_ayat'],
                'sampai_surat' => $validated['sampai_surat'],
                'sampai_ayat' => $validated['sampai_ayat'],
                'kategori' => $validated['kategori'],
                'nilai' => $validated['nilai'],
                'catatan' => $validated['catatan'] ?? null,
            ]);

            DB::commit();
            return redirect()->route('guru.hafalan.index')->with('success', 'Setoran hafalan berhasil disimpan.');
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
