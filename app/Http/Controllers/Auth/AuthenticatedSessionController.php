<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Tangani permintaan autentikasi.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Lakukan proses pengecekan email & password standar
        $request->authenticate();

        // 2. Ambil data user yang baru saja mencoba login
        $user = Auth::user();

        /**
         * 3. PROTEKSI: Cek apakah akun masih dalam status 'pending'
         * atau belum memiliki level akses (level 0).
         */
        if ($user->role === 'pending' || $user->level === 0) {
            // Keluarkan user secara paksa
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Kembalikan ke halaman login dengan pesan error khusus
            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda belum aktif. Silakan hubungi Superadmin untuk menetapkan Role dan Divisi Anda.',
            ]);
        }

        // 4. Jika lolos (bukan pending), regenerasi session dan masuk ke dashboard
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Hapus sesi autentikasi (Logout).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}