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
     * Dilengkapi fitur filter untuk melihat user yang dinonaktifkan.
     */
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Akses ditolak. Anda bukan Superadmin.');
        }

        $query = User::query();

        // Fitur: Lihat user yang sudah di-soft-delete (arsip)
        if ($request->has('show_deleted')) {
            $query->onlyTrashed();
        }

        $users = $query->latest()->paginate(100);
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
        
        $branches = Branch::all();
        $divisions = Division::all();
        
        return view('users.create', compact('divisions', 'branches'));
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
            'division' => ['required'], 
            'level' => ['required_if:role,staff,gm,direksi,superadmin', 'nullable', 'in:2,3'],
            'branch' => ['required', 'exists:branches,code'],
        ]);
        
        $role = $request->role;
        $level = $request->level;

        // LOGIKA AUTO-LEVEL: Jika supervisor, paksa level ke 2 (Akses Divisi)
        if (in_array($role, ['supervisor', 'admin', 'bm'])) {
            $level = 2;
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'division' => $request->division,
            'branch' => $request->branch,
            'level' => $level ?? 2,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit pengguna.
     */
    public function edit(User $user)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403);
        }

        $branches = Branch::all();
        $divisions = Division::all();

        return view('users.edit', compact('user', 'divisions', 'branches'));
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
            'division' => ['required'],
            'level' => ['required_if:role,staff,gm,direksi,superadmin', 'nullable', 'in:2,3'],
            'branch' => ['required', 'exists:branches,code'],
        ]);

        $role = $request->role;
        // Paksa level 2 untuk role cabang/divisi
        $level = in_array($role, ['supervisor', 'admin', 'bm']) ? 2 : $request->level;

        $user->fill([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'division' => $request->division,
            'branch' => $request->branch,
            'level' => $level,
        ]);

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
     * Menghapus pengguna (Soft Delete).
     */
    public function destroy(User $user)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403);
        }

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User telah dinonaktifkan (Arsip).');
    }

    /**
     * Mengaktifkan kembali user yang dihapus.
     */
    public function restore($id)
    {
        if (Auth::user()->role !== 'superadmin') abort(403);

        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('users.index')->with('success', 'User berhasil diaktifkan kembali.');
    }

    /**
     * Menghapus user secara permanen.
     */
    public function forceDelete($id)
    {
        if (Auth::user()->role !== 'superadmin') abort(403);

        $user = User::withTrashed()->findOrFail($id);
        
        // Cek integritas data bisa dilakukan di sini jika perlu
        $user->forceDelete();

        return redirect()->route('users.index')->with('success', 'User dihapus secara permanen dari sistem.');
    }
}