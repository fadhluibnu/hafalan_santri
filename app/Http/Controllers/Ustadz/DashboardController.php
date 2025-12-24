<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Ustadz;
use App\Models\Santri;
use App\Models\Hafalan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil ustadz yang login
        $ustadz = Ustadz::where('user_id', Auth::id())->first();
        $pondokId = $ustadz->pondok_id ?? null;

        // Hitung jumlah santri di pondok
        $jumlahSantri = $pondokId ? Santri::where('pondok_id', $pondokId)->count() : 0;

        // Hitung setoran hari ini
        $today = Carbon::today()->toDateString();
        $setoranHariIni = Hafalan::whereDate('tanggal_setor', $today)
            ->whereHas('kelas', function($q) use ($pondokId) {
                $q->where('pondok_id', $pondokId);
            })
            ->count();

        return Inertia::render('Ustadz/Dashboard', [
            'jumlahSantri' => $jumlahSantri,
            'setoranHariIni' => $setoranHariIni,
        ]);
    }
}
