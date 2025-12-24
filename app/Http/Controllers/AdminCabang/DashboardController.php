<?php

namespace App\Http\Controllers\AdminCabang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminCabang;
use App\Models\Santri;
use App\Models\Ustadz;
use App\Models\JuzSantri; // pastikan nama model juz sesuai (sesuaikan jika berbeda)

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil pondok yang terkait dengan admin cabang yang sedang login
        $adminCabang = AdminCabang::where('user_id', Auth::id())->first();
        $pondokId = $adminCabang->pondok_id ?? null;

        // Hitung jumlah santri / ustadz untuk pondok tersebut
        $jumlahSantri = $pondokId ? Santri::where('pondok_id', $pondokId)->count() : Santri::count();
        $jumlahUstadz = $pondokId ? Ustadz::where('pondok_id', $pondokId)->count() : Ustadz::count();

        // Hitung total juz sah (gabungkan ke santri pondok jika diperlukan)
        $totalJuzSah = 0;
        if (class_exists(JuzSantri::class)) {
            $q = JuzSantri::where('status', 'sah');
            if ($pondokId) {
                $q->whereHas('santris', function ($qq) use ($pondokId) {
                    $qq->where('pondok_id', $pondokId);
                });
            }
            $totalJuzSah = $q->count();
        }

        // Ambil rekap hafalan terbaru (limit 10) dengan info santri
        $rekap = [];
        if (class_exists(JuzSantri::class)) {
            $recent = JuzSantri::with('santris')
                ->when($pondokId, function ($q) use ($pondokId) {
                    $q->whereHas('santris', function ($qq) use ($pondokId) {
                        $qq->where('pondok_id', $pondokId);
                    });
                })
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $rekap = $recent->map(function ($r) {
                return [
                    'id' => $r->id,
                    'nama' => $r->santris?->nama ?? null,
                    'juz' => $r->nama ?? $r->juz ?? null,
                    'status' => $r->status ?? null,
                    'created_at' => $r->created_at?->toDateTimeString(),
                ];
            })->toArray();
        }

        return Inertia::render('AdminCabang/Dashboard', [
            'jumlahSantri' => $jumlahSantri,
            'jumlahUstadz' => $jumlahUstadz,
            'totalJuzSah' => $totalJuzSah,
            'rekapData' => $rekap,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
