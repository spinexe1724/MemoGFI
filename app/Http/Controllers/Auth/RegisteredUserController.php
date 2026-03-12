<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Division;
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
        $divisions = Division::orderBy('name', 'asc')->get();
        $branches = Branch::oldest()->get(); 
        
        // Daftar role yang diizinkan untuk registrasi mandiri
        $roles = [
            'admin' => 'Admin Cabang',
            'supervisor' => 'Supervisor',
            'manager' => 'Manager',
            'direksi' => 'Direksi',
            'bm' => 'Branch Manager'        ];

        return view('auth.register', compact('divisions', 'branches', 'roles'));
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
            'role' => ['required', 'string', 'in:staff,admin,supervisor,manager,bm,ga'],
            'division' => ['required', 'string'],
            'branch' => ['required', 'string', 'exists:branches,code'],
        ]);

        // Logika Level Otomatis:
        // Role tertentu dipaksa ke Level 2 (Akses Divisi)
        $level = 2;
        if (in_array($request->role, ['manager', 'gm', 'direksi'])) {
            $level = 3; // Akses Global jika dibutuhkan
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'division' => $request->division,
            'branch' => $request->branch,
            'level' => $level,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}