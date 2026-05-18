<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('pages.auth.signin', ['title' => 'Sign In']);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt([...$credentials, 'status' => 'Aktif'])) {
            $user = Auth::user();
            $user->updateQuietly(['last_seen_at' => now()]);
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        $user = \App\Models\User::where('email', $request->email)->first();
        if ($user && $user->status !== 'Aktif') {
            return back()->withErrors([
                'email' => 'Akun Anda dinonaktifkan. Silakan hubungi Super Admin.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('signin');
    }
}
