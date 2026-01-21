<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Division;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Menampilkan daftar pengguna (Hanya untuk Superadmin).
     */
    public function index()
    {
        // Pengecekan keamanan tambahan tingkat controller
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Akses ditolak. Anda bukan Superadmin.');
        }

        $users = User::all();
        return view('users.index', compact('users'));
    }

    /**
     * Menampilkan form untuk membuat pengguna baru.
     */
    public function create()
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403);
        }
        $branches = Branch::all(); // Tambahkan ini
          $divisions = Division::all(); // Ambil dari DB
        return view('users.create', compact('divisions','branches'));
    }

    /**
     * Menyimpan pengguna baru ke database.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:superadmin,gm,direksi,supervisor,admin,ga,bm,manager'],
            'division' => ['nullable'], // Validasi dropdown divisi
                 'level' => ['required_if:role,staff,gm,direksi,superadmin', 'nullable', 'in:2,3'],
                             'branch' => ['required', 'exists:branches,name'], // Validasi cabang

        ]);
        
        $role = $request->role;
        $level = $request->level;
  // LOGIKA AUTO-LEVEL: Jika supervisor, paksa level ke 2 (Akses Divisi)
        if ($role === 'supervisor') {
            $level = 2;
        }
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'division' => $request->division,
                        'branch' => $request->branch,

            'level' => $level ?? 2, // Fallback ke level 2 jika tidak terdefinisi
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan dengan akses Level ' . ($level ?? 2));
    }

    /**
     * Menampilkan form edit pengguna.
     */
    public function edit(User $user)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403);
        }
        return view('users.edit', compact('user'));
    }

    /**
     * Memperbarui data pengguna.
     */
    public function update(Request $request, User $user)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:superadmin,gm,direksi,supervisor,admin,ga,manager,bm'],
            'division' => ['required', 'in:IT,HRD,IC,Remedial'],
                        'level' => ['required_if:role,staff,gm,direksi,superadmin', 'nullable', 'in:2,3'],

        ]);
        $role = $request->role;
        $level = ($role === 'supervisor') ? 2 : $request->level;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        $user->division = $request->division;
        $user->level = $level;

        
        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui.');
    }

    /**
     * Menghapus pengguna dari sistem.
     */
    public function destroy(User $user)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403);
        }

        // Mencegah superadmin menghapus dirinya sendiri
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User telah berhasil dihapus.');
    }
}