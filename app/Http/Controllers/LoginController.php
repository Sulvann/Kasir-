<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            /** @var \App\Models\User $user */
            $user = Auth::user();

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'redirect_url' => $user->role === 'admin' ? '/admin/dashboard' : '/cashier'
            ]);
        }

        return response()->json([
            'message' => 'Email atau Kata Sandi yang Anda masukkan tidak cocok.',
        ], 401);
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out']);
        // Or if using form submit: return redirect('/login');
    }
}
