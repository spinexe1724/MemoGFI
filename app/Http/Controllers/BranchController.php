<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class BranchController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            // Hanya Superadmin yang bisa mengelola master cabang
            new Middleware('can:is-superadmin'),
        ];
    }

    public function index()
    {
        $branches = Branch::all();
        return view('branches.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:branches,name',
            'code' => 'required|string|max:10|unique:branches,code',
        ]);

        Branch::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
        ]);

        return redirect()->route('branches.index')->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);
        
        // Cek jika masih ada user di cabang ini
        if ($branch->users()->exists()) {
            return back()->with('error', 'Cabang tidak bisa dihapus karena masih memiliki pengguna.');
        }

        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'Cabang berhasil dihapus.');
    }
}