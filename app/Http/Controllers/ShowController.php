<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password as PasswordRule; // Memberikan alias untuk menghindari konflik

use Illuminate\View\View;

class ShowController extends Controller
{
    public function view()
    {
          $user = Auth::user();
        
        return view('profile/show', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'confirmed', PasswordRule::defaults()],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        // Update Nama
        $user->name = $request->name;

        // Update Password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        /** @var \App\Models\User $user */
        $user->save();

        // Kembali ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'PROFIL BERHASIL DIPERBARUI!');
    }
}
