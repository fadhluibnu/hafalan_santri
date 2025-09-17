<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated and has super_admin role
        if ($request->user() && $request->user()->role === 'super_admin') {
            return $next($request);
        }

        // For web routes, redirect to login or home page
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You do not have super admin privileges.',
            ], 403);
        }
        
        return redirect()->route('home')->with('error', 'Anda tidak memiliki akses Super Admin.');
    }
}
