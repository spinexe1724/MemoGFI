<?php

namespace App\Http\Controllers;

use App\Models\User; // PENTING: Pastikan Model User diimpor
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShowController extends Controller
{
    /**
     * Menampilkan halaman profil pengguna.
     */
    public function show(): View
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Memperbarui informasi profil dan kata sandi.
     */
    public function update(Request $request): RedirectResponse
    {
        // Ambil instance model User yang segar dari database
        /** @var \App\Models\User $user */
        $user = User::findOrFail(Auth::id());

        // 1. Validasi Input
        // 'name' diubah menjadi nullable untuk mendukung input 'disabled' di frontend
        $request->validate([
            'name' => ['nullable', 'string', 'max:255'], 
            'password' => ['nullable', 'confirmed', PasswordRule::min(8)], // Minimal 8 karakter
        ], [
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min' => 'Kata sandi minimal harus 8 karakter.',
        ]);

        // 2. Update Informasi Dasar
        // Hanya update nama jika input dikirim (antisipasi input disabled)
        if ($request->filled('name')) {
            $user->name = $request->name;
        }

        // 3. Logika Update Password
        // Password hanya di-hash jika field diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // 4. Simpan perubahan
        $user->save();

        return back()->with('success', 'PROFIL BERHASIL DIPERBARUI!');
    }
}