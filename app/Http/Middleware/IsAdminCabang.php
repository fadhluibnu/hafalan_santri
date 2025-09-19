<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdminCabang
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->role === 'admin_cabang') {
            return $next($request);
        }

        // For web routes, redirect to login or home page
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You do not have super admin privileges.',
            ], 403);
        }

        return redirect()->route('home')->with('error', 'Anda tidak memiliki akses Admin Cabang.');
    }
}
