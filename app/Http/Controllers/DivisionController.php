<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DivisionController extends Controller implements HasMiddleware
{
    /**
     * Menentukan middleware untuk controller ini (Standar Laravel 11).
     * Ini menggantikan penggunaan $this->middleware() di constructor.
     */
    public static function middleware(): array
    {
        return [
            // Menggunakan gate 'is-superadmin' yang sudah didefinisikan di AppServiceProvider
            new Middleware('can:is-superadmin', only: ['index', 'store', 'destroy']),
        ];
    }

    /**
     * Menampilkan daftar divisi.
     */
    public function index()
    {
        $divisions = Division::all();
        return view('divisions.index', compact('divisions'));
    }

    /**
     * Menyimpan divisi baru ke database.
     */
    public function store(Request $request)
    {
        // Menambahkan validasi untuk field 'initial'
        $request->validate([
            'name' => 'required|string|max:255|unique:divisions,name',
            'initial' => 'required|string|max:10|unique:divisions,initial',
        ]);

        // Menyimpan data name dan initial
        Division::create([
            'name' => $request->name,
            'initial' => strtoupper($request->initial), // Otomatis simpan dalam huruf besar
        ]);

        return redirect()->route('divisions.index')->with('success', 'Divisi baru berhasil ditambahkan.');
    }

    /**
     * Menghapus divisi.
     */
    public function destroy($id)
    {
        $division = Division::findOrFail($id);
        
        // Proteksi: Cek jika divisi masih digunakan oleh user
        if ($division->users()->exists()) {
            return back()->with('error', 'Divisi tidak dapat dihapus karena masih digunakan oleh beberapa pengguna.');
        }

        $division->delete();

        return redirect()->route('divisions.index')->with('success', 'Divisi berhasil dihapus.');
    }
}