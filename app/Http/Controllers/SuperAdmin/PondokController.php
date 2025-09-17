<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Pondok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PondokController extends Controller
{
    /**
     * Display a listing of the pondok.
     */
    public function index()
    {
        $pondoks = Pondok::with(['gurus', 'santris'])->orderBy('created_at', 'desc')->paginate(10);
        return Inertia::render('SuperAdmin/Pondok/Index', [
            'pondoks' => $pondoks,
        ]);
    }

    /**
     * Show the form for creating a new pondok.
     */
    public function create()
    {
        return Inertia::render('SuperAdmin/Pondok/Create');
    }

    /**
     * Store a newly created pondok in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'telepon' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:pondoks,email',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'deskripsi' => 'nullable|string',
            'tahun_berdiri' => 'nullable|integer|min:1900|max:' . date('Y'),
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('pondok-logos', 'public');
            $validated['logo'] = $path;
        }

        $pondok = Pondok::create($validated);

        return redirect()->route('super-admin.pondok.index')
            ->with('success', 'Pondok berhasil ditambahkan.');
    }

    /**
     * Display the specified pondok.
     */
    public function show($pondok)
    {
        $pondok = Pondok::with(['kelas', 'gurus', 'santris'])->findOrFail($pondok);
        return Inertia::render('SuperAdmin/Pondok/Show', [
            'pondok' => $pondok,
        ]);
    }

    /**
     * Show the form for editing the specified pondok.
     */
    public function edit(Pondok $pondok)
    {
        return Inertia::render('SuperAdmin/Pondok/Edit', [
            'pondok' => $pondok,
        ]);
    }

    /**
     * Update the specified pondok in storage.
     */
    public function update(Request $request, Pondok $pondok)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'telepon' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:pondoks,email,' . $pondok->id,
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'deskripsi' => 'nullable|string',
            'tahun_berdiri' => 'nullable|integer|min:1900|max:' . date('Y'),
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($pondok->logo && Storage::disk('public')->exists($pondok->logo)) {
                Storage::disk('public')->delete($pondok->logo);
            }
            
            $path = $request->file('logo')->store('pondok-logos', 'public');
            $validated['logo'] = $path;
        }

        $pondok->update($validated);

        return redirect()->route('super-admin.pondok.index')
            ->with('success', 'Pondok berhasil diperbarui.');
    }

    /**
     * Remove the specified pondok from storage.
     */
    public function destroy(Pondok $pondok)
    {
        // Check if there are related admin cabangs
        if ($pondok->adminCabangs()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus pondok yang masih memiliki Admin Cabang.');
        }
        
        // Delete logo if exists
        if ($pondok->logo && Storage::disk('public')->exists($pondok->logo)) {
            Storage::disk('public')->delete($pondok->logo);
        }
        
        $pondok->delete();

        return redirect()->route('super-admin.pondok.index')
            ->with('success', 'Pondok berhasil dihapus.');
    }
}
