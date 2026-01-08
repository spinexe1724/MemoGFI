<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index() {
        if (Auth::user()->role !== 'superadmin') abort(403);
        $users = User::where('id', '!=', Auth::id())->get();
        return view('users.index', compact('users'));
    }
public function show(){

}
    public function create() {
        if (Auth::user()->role !== 'superadmin') abort(403);
        return view('users.create');
    }

    public function store(Request $request) {
        if (Auth::user()->role !== 'superadmin') abort(403);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:gm,staff',
             'division' => 'required|string|max:100', // Validasi divisi
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'division' => $request->division,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }
     public function destroy($id) {
        if (Auth::user()->role !== 'superadmin') abort(403);
        User::findOrFail($id)->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
