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
        
        if ($user) {
            switch ($user->role) {
                case 'ustadz':
                    $ustadz = \App\Models\Ustadz::where('user_id', $user->id)->first();
                    $displayName = $ustadz?->nama ?? $user->username;
                    break;
                case 'admin_cabang':
                    $adminCabang = \App\Models\AdminCabang::where('user_id', $user->id)->first();
                    $displayName = $adminCabang?->name ?? $user->username;
                    break;
                case 'super_admin':
                    $superAdmin = \App\Models\SuperAdmin::where('user_id', $user->id)->first();
                    $displayName = $superAdmin?->name ?? $user->username;
                    break;
                default:
                    $displayName = $user->username;
            }
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'ziggy' => $ziggy->toArray(),
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
            ],
            'auth' => [
                'user' => $user ? array_merge($user->toArray(), ['name' => $displayName]) : null,
            ],
        ];
    }
}
