<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Menampilkan form registrasi dengan data dropdown dinamis.
     */
    public function create(): View
    {
        $branches = Branch::oldest()->get(); 
        
        // Daftar role yang diizinkan untuk registrasi mandiri
 

        return view('auth.register', compact('branches'));
    }

    /**
     * Menangani proses simpan pendaftaran akun baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'branch' => ['required', 'string', 'exists:branches,code'],
            'phone_number' => ['required', 'string'],
        ]);

      

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'division' => $request->division,
            'branch' => $request->branch,
            'phone_number' => $request->phone_number,
            'role' => 'pending',        // Default role
            'division' => 'Pending',    // Default divisi
            'level' => 0,    
        ]);

        event(new Registered($user));


        return redirect()->route('login')->with('status', 'Pendaftaran berhasil! Akun Anda sedang menunggu verifikasi serta penetapan Role dan Divisi oleh Superadmin');
    }
}