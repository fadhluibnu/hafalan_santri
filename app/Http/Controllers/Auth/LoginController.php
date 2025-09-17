<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Inertia\Inertia;

class LoginController extends Controller
{
    // We'll handle middleware via routes instead of controller constructor

    /**
     * Show the application's login form.
     */
    public function showLoginForm()
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'username' => 'required_without:email|string',
                'email' => 'required_without:username|email',
                'password' => 'required|string|min:6',
            ]);

            // Determine if we're using email or username for authentication
            $loginField = $request->has('email') ? 'email' : 'username';
            $loginCredentials = [
                $loginField => $request->input($loginField),
                'password' => $request->password
            ];

            if (Auth::attempt($loginCredentials)) {
                $user = Auth::user();
                $request->session()->regenerate();

                // Redirect based on user role
                switch ($user->role) {
                    case 'super_admin':
                        return redirect()->route('super-admin.dashboard');
                    case 'admin_cabang':
                        return redirect()->route('admin-cabang.dashboard');
                    case 'guru':
                        return redirect()->route('guru.dashboard');
                    case 'santri':
                        return redirect()->route('santri.dashboard');
                    case 'orang_tua':
                        return redirect()->route('orang-tua.dashboard');
                    default:
                        return redirect()->route('home');
                }
            }

            // Authentication failed
            return back()->withErrors([
                'error' => 'The provided credentials do not match our records.',
            ])->withInput();
        } catch (Exception $e) {
            return back()->withErrors([
                'error' => 'An error occurred during login: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Logged out successfully']);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
