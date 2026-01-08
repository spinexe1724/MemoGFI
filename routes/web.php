<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MemoController;
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Aliskan 'dashboard' ke halaman utama memo agar layout Breeze berfungsi
    Route::get('/dashboard', [MemoController::class, 'index'])->name('dashboard');
      // Superadmin Only: User Management
    Route::middleware(['can:is-superadmin'])->group(function () {
        Route::resource('users', UserController::class);
    });
    // Route Resource untuk Memo
    Route::resource('memos', MemoController::class);
    
    // Route khusus Approval & Reject
    Route::post('/memos/{id}/approve', [MemoController::class, 'approve'])->name('memos.approve');
    Route::post('/memos/{id}/reject', [MemoController::class, 'reject'])->name('memos.reject');
    Route::get('/memos/{id}/pdf', [MemoController::class, 'download'])->name('memos.pdf');

     // Profile Routes (Wajib ada untuk Navigation Layout Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
require __DIR__.'/auth.php';
