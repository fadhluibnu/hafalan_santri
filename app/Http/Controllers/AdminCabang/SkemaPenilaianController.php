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
                'tipe' => $skema->tipe,
                'keterangan' => $skema->keterangan,
                'items' => $skema->items->map(function ($item) {
                    $nama = trim((string) ($item->nama ?: $item->label));
                    $singkatan = strtoupper(trim((string) ($item->singkatan ?: $item->label)));

                    return [
                        'id' => $item->id,
                        'nama' => $nama,
                        'singkatan' => $singkatan,
                        'urutan' => (int) $item->urutan,
                        'batas_bawah' => $item->batas_bawah !== null ? (float) $item->batas_bawah : null,
                        'batas_atas' => $item->batas_atas !== null ? (float) $item->batas_atas : null,
                    ];
                })->values(),
            ] : null,
        ]);
    }

    public function update(Request $request)
    {
        $admin = AdminCabang::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'tipe' => 'required|in:label,numeric',
            'items' => 'required_if:tipe,label|array',
            'items.*.nama' => 'required_if:tipe,label|string|max:100',
            'items.*.singkatan' => 'required_if:tipe,label|string|max:20',
            'items.*.batas_bawah' => 'nullable|numeric',
            'items.*.batas_atas' => 'nullable|numeric',
            'keterangan' => 'nullable|string',
        ]);

        $items = collect($validated['items'] ?? [])
            ->map(function ($item) {
                $nama = trim((string) ($item['nama'] ?? ''));
                $singkatan = strtoupper(trim((string) ($item['singkatan'] ?? '')));

                return [
                    'nama' => $nama,
                    'singkatan' => $singkatan,
                    'batas_bawah' => isset($item['batas_bawah']) && $item['batas_bawah'] !== '' ? (float) $item['batas_bawah'] : null,
                    'batas_atas' => isset($item['batas_atas']) && $item['batas_atas'] !== '' ? (float) $item['batas_atas'] : null,
                ];
            })
            ->filter(function ($item) {
                return $item['nama'] !== '' && $item['singkatan'] !== '';
            })
            ->values();

        if ($validated['tipe'] === 'label' && $items->isEmpty()) {
            return back()->withErrors([
                'items' => 'Minimal satu parameter penilaian harus diisi untuk tipe kategori.',
            ])->withInput();
        }

        $duplicateSingkatan = $items->pluck('singkatan')->duplicates()->first();
        if ($duplicateSingkatan) {
            return back()->withErrors([
                'items' => "Singkatan {$duplicateSingkatan} duplikat. Gunakan singkatan yang unik.",
            ])->withInput();
        }

        // Validate batas_bawah and batas_atas logic if tipe is label
        if ($validated['tipe'] === 'label') {
            foreach ($items as $index => $item) {
                if ($item['batas_bawah'] !== null && $item['batas_atas'] !== null && $item['batas_bawah'] >= $item['batas_atas']) {
                    return back()->withErrors([
                        'items' => "Batas Bawah pada item {$item['nama']} tidak boleh lebih besar atau sama dengan Batas Atas.",
                    ])->withInput();
                }
            }
        }

        DB::transaction(function () use ($admin, $validated, $items) {
            $existing = SkemaPenilaian::query()
                ->where('pondok_id', $admin->pondok_id)
                ->first();

            $skema = SkemaPenilaian::query()->updateOrCreate(
                ['pondok_id' => $admin->pondok_id],
                [
                    'nama' => $existing?->nama ?: 'Skema Penilaian Pondok',
                    'tipe' => $validated['tipe'],
                    'is_active' => true,
                    'keterangan' => $validated['keterangan'] ?? null,
                ]
            );

            $skema->items()->delete();

            if ($validated['tipe'] === 'label') {
                foreach ($items as $index => $item) {
                    $skema->items()->create([
                        'nama' => $item['nama'],
                        'singkatan' => $item['singkatan'],
                        // Tetap isi label untuk kompatibilitas data lama.
                        'label' => $item['singkatan'],
                        'urutan' => $index + 1,
                        'batas_bawah' => $item['batas_bawah'],
                        'batas_atas' => $item['batas_atas'],
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin-cabang.skema-penilaian.edit')
            ->with('success', 'Skema penilaian berhasil disimpan.');
    }
}
