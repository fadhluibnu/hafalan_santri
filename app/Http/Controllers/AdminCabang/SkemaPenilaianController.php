<?php

namespace App\Http\Controllers\AdminCabang;

use App\Http\Controllers\Controller;
use App\Models\AdminCabang;
use App\Models\SkemaPenilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SkemaPenilaianController extends Controller
{
    public function edit()
    {
        $admin = AdminCabang::where('user_id', Auth::id())->firstOrFail();

        $skema = SkemaPenilaian::query()
            ->with('items')
            ->where('pondok_id', $admin->pondok_id)
            ->first();

        return Inertia::render('AdminCabang/SkemaPenilaian/Edit', [
            'skema' => $skema ? [
                'id' => $skema->id,
                'nama' => $skema->nama,
                'keterangan' => $skema->keterangan,
                'items' => $skema->items->map(function ($item) {
                    $nama = trim((string) ($item->nama ?: $item->label));
                    $singkatan = strtoupper(trim((string) ($item->singkatan ?: $item->label)));

                    return [
                        'id' => $item->id,
                        'nama' => $nama,
                        'singkatan' => $singkatan,
                        'urutan' => (int) $item->urutan,
                    ];
                })->values(),
            ] : null,
        ]);
    }

    public function update(Request $request)
    {
        $admin = AdminCabang::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.nama' => 'required|string|max:100',
            'items.*.singkatan' => 'required|string|max:20',
            'keterangan' => 'nullable|string',
        ]);

        $items = collect($validated['items'])
            ->map(function ($item) {
                $nama = trim((string) ($item['nama'] ?? ''));
                $singkatan = strtoupper(trim((string) ($item['singkatan'] ?? '')));

                return [
                    'nama' => $nama,
                    'singkatan' => $singkatan,
                ];
            })
            ->filter(function ($item) {
                return $item['nama'] !== '' && $item['singkatan'] !== '';
            })
            ->values();

        if ($items->isEmpty()) {
            return back()->withErrors([
                'items' => 'Minimal satu parameter penilaian harus diisi.',
            ])->withInput();
        }

        $duplicateSingkatan = $items->pluck('singkatan')->duplicates()->first();
        if ($duplicateSingkatan) {
            return back()->withErrors([
                'items' => "Singkatan {$duplicateSingkatan} duplikat. Gunakan singkatan yang unik.",
            ])->withInput();
        }

        DB::transaction(function () use ($admin, $validated, $items) {
            $existing = SkemaPenilaian::query()
                ->where('pondok_id', $admin->pondok_id)
                ->first();

            $skema = SkemaPenilaian::query()->updateOrCreate(
                ['pondok_id' => $admin->pondok_id],
                [
                    'nama' => $existing?->nama ?: 'Skema Penilaian Pondok',
                    'is_active' => true,
                    'keterangan' => $validated['keterangan'] ?? null,
                ]
            );

            $skema->items()->delete();

            foreach ($items as $index => $item) {
                $skema->items()->create([
                    'nama' => $item['nama'],
                    'singkatan' => $item['singkatan'],
                    // Tetap isi label untuk kompatibilitas data lama.
                    'label' => $item['singkatan'],
                    'urutan' => $index + 1,
                ]);
            }
        });

        return redirect()
            ->route('admin-cabang.skema-penilaian.edit')
            ->with('success', 'Skema penilaian berhasil disimpan.');
    }
}
