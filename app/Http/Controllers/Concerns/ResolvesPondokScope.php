<?php

namespace App\Http\Controllers\Concerns;

use App\Models\AdminCabang;
use App\Models\Pondok;
use App\Models\Ustadz;
use Illuminate\Http\Request;

trait ResolvesPondokScope
{
    protected function resolvePondokScope(Request $request): array
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Anda belum login.');
        }

        if ($user->role === 'admin_cabang') {
            $adminCabang = AdminCabang::where('user_id', $user->id)->first();
            $pondokId = $adminCabang?->pondok_id;

            return [
                'pondok_id' => $pondokId,
                'pondok_options' => collect(),
                'requires_pondok_selection' => false,
            ];
        }

        if ($user->role === 'ustadz') {
            $ustadz = Ustadz::where('user_id', $user->id)->first();
            $pondokId = $ustadz?->pondok_id;

            return [
                'pondok_id' => $pondokId,
                'pondok_options' => collect(),
                'requires_pondok_selection' => false,
            ];
        }

        if ($user->role === 'super_admin') {
            $pondokOptions = Pondok::query()->select('id', 'nama')->orderBy('nama')->get();
            $pondokId = $request->input('pondok_id') ? (int) $request->input('pondok_id') : null;

            if ($pondokId && !$pondokOptions->contains('id', $pondokId)) {
                abort(404, 'Pondok tidak ditemukan.');
            }

            return [
                'pondok_id' => $pondokId,
                'pondok_options' => $pondokOptions,
                'requires_pondok_selection' => true,
            ];
        }

        abort(403, 'Role pengguna tidak didukung untuk modul ini.');
    }
}
