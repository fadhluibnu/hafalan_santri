<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);
            
            if (Auth::attempt($credentials)) {
                // Jika berhasil, regenerasi session untuk keamanan
                // $request->session()->regenerate();
                $token = $request->user()->createToken($request->email);

                return response()->json([
                    'message' => 'Login successful',
                    'token' => $token->plainTextToken
                ], 200);
            }

            return response()->json([
                'message' => 'Invalid credentials',
                'token' => null
            ], 401);

        } catch (Exception $e) {
            
            return response()->json([
                'message' => 'An error occurred during login',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
