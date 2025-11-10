<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // ===================== START: Show Form Login =====================
    public function showLoginForm()
    {
        // --- START: Cek jika user sudah login ---
        if (Auth::check()) {
            return redirect()->route('home');
        }
        // --- END: Cek login ---

        return view('auth.login');
    }
    // ===================== END: Show Form Login =====================



    // ===================== START: Proses Login =====================
    public function login(Request $request)
    {
        // --- START: Validasi input ---
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }
        // --- END: Validasi input ---


        // --- START: Ambil kredensial dan attempt login ---
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // Regenerasi sesi untuk mencegah fiksasi
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }
        // --- END: Attempt ---


        // --- START: Gagal login (kirim pesan error) ---
        return back()
            ->withErrors([
                'email' => 'Email atau password yang Anda masukkan salah.',
            ])
            ->withInput();
        // --- END: Gagal login ---
    }
    // ===================== END: Proses Login =====================



    // ===================== START: Logout =====================
    public function logout(Request $request)
    {
        // --- START: Clear auth + session ---
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // --- END: Clear auth + session ---

        return redirect('/');
    }
    // ===================== END: Logout =====================
}
