<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TahunAjaranSessionController extends Controller
{
    /**
     * Set selected tahun ajaran in session
     */
    public function setSelected(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
        ]);

        $request->session()->put('selected_tahun_ajaran_id', $validated['tahun_ajaran_id']);

        return back()->with('success', 'Tahun ajaran berhasil dipilih.');
    }
}
