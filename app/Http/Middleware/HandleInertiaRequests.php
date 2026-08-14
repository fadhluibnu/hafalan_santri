<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        $ziggy = new Ziggy($group = null, $request->url());

        // Get user's display name from relationship based on role
        $user = $request->user();
        $displayName = null;
        $pondokId = null;
        $pondokNama = null;
        $tahunAjaranData = [];
        
        if ($user) {
            switch ($user->role) {
                case 'ustadz':
                    $ustadz = \App\Models\Ustadz::where('user_id', $user->id)->with('pondok')->first();
                    $displayName = $ustadz?->nama ?? $user->username;
                    $pondokId = $ustadz?->pondok_id;
                    $pondokNama = $ustadz?->pondok?->nama;
                    break;
                case 'admin_cabang':
                    $adminCabang = \App\Models\AdminCabang::where('user_id', $user->id)->with('pondok')->first();
                    $displayName = $adminCabang?->name ?? $user->username;
                    $pondokId = $adminCabang?->pondok_id;
                    $pondokNama = $adminCabang?->pondok?->nama;
                    break;
                case 'super_admin':
                    $superAdmin = \App\Models\SuperAdmin::where('user_id', $user->id)->first();
                    $displayName = $superAdmin?->name ?? $user->username;
                    break;
                default:
                    $displayName = $user->username;
            }

            // Get tahun ajaran data for admin_cabang and ustadz
            if ($pondokId && in_array($user->role, ['admin_cabang', 'ustadz'])) {
                $tahunAjarans = \App\Models\TahunAjaran::where('pondok_id', $pondokId)
                    ->orderBy('tanggal_mulai', 'desc')
                    ->get(['id', 'nama', 'semester', 'is_active', 'status']);
                
                // Get selected tahun ajaran from session or use active one
                $selectedTahunAjaranId = $request->session()->get('selected_tahun_ajaran_id');
                if (!$selectedTahunAjaranId) {
                    $activeTahunAjaran = $tahunAjarans->firstWhere('is_active', true);
                    $selectedTahunAjaranId = $activeTahunAjaran?->id;
                }

                $tahunAjaranData = [
                    'list' => $tahunAjarans,
                    'selected_id' => $selectedTahunAjaranId,
                ];
            }
        }

        // Build page title: "Pondok Name - App Name" or just "App Name"
        $appName = config('app.name');
        $pageTitle = $pondokNama ? "{$pondokNama} - {$appName}" : $appName;

        return [
            ...parent::share($request),
            'name' => $appName,
            'pageTitle' => $pageTitle,
            'pondok' => $pondokNama ? ['id' => $pondokId, 'nama' => $pondokNama] : null,
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'ziggy' => $ziggy->toArray(),
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
            ],
            'auth' => [
                'user' => $user ? array_merge($user->toArray(), ['name' => $displayName]) : null,
            ],
            'tahunAjaran' => $tahunAjaranData,
        ];
    }
}


