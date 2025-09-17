<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AdminCabang;
use App\Models\Guru;
use App\Models\Pondok;
use App\Models\Santri;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pondok = Pondok::count();
        $adminCabang = AdminCabang::count();
        $guru = Guru::count();
        $santri = Santri::count();

        $summary = [
            'pondokCount' => $pondok,
            'adminCabangCount' => $adminCabang,
            'guruCount' => $guru,
            'santriCount' => $santri,
        ];
        return Inertia::render('SuperAdmin/Dashboard', [
            'summary' => $summary
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
