<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MemoController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Aliskan 'dashboard' ke halaman utama memo agar layout Breeze berfungsi
    Route::get('/dashboard', [MemoController::class, 'index'])->name('dashboard');
    
    // Route Resource untuk Memo
    Route::resource('memos', MemoController::class);
    
    // Route khusus Approval & Reject
    Route::post('/memos/{id}/approve', [MemoController::class, 'approve'])->name('memos.approve');
    Route::post('/memos/{id}/reject', [MemoController::class, 'reject'])->name('memos.reject');
    Route::get('/memos/{id}/pdf', [MemoController::class, 'download'])->name('memos.pdf');
});
require __DIR__.'/auth.php';
