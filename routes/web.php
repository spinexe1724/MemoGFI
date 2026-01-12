<?php

use App\Http\Controllers\MemoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DivisionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini Anda dapat mendefinisikan rute untuk aplikasi Anda. 
| Semua rute di bawah ini dilindungi oleh middleware 'auth' agar hanya 
| pengguna yang sudah login yang dapat mengaksesnya.
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    // --- Dashboard & Memo Management ---
    // Dashboard menggunakan method index dari MemoController
    Route::get('/dashboard', [MemoController::class, 'index'])->name('dashboard');
     Route::get('/memos/logs', [MemoController::class, 'allLogs'])
        ->name('memos.logs')
        ->middleware('can:is-superadmin');
    // Resource route untuk Memos (Index, Create, Store, Edit, Update, Show, Destroy)
    Route::resource('memos', MemoController::class);
    
    // Rute Khusus Persetujuan & Penolakan (GM & Direksi)
    Route::post('/memos/{id}/approve', [MemoController::class, 'approve'])->name('memos.approve');
    Route::post('/memos/{id}/reject', [MemoController::class, 'reject'])->name('memos.reject');
    
    // Rute Generate PDF
    Route::get('/memos/{id}/pdf', [MemoController::class, 'download'])->name('memos.pdf');


    // --- User Management (Khusus Superadmin) ---
    // Proteksi menggunakan Gate 'is-superadmin' yang didefinisikan di AppServiceProvider
   Route::middleware(['can:is-superadmin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('divisions', DivisionController::class)->only(['index', 'store', 'destroy']);
    });


    // --- Profile Management (Bawaan Laravel Breeze) ---
    // Rute ini wajib ada agar layout navigasi tidak error
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/divisions/create', function () {return view('divisions.create');})->name('divisions.create');
});

// Memuat rute autentikasi bawaan Breeze (Login, Register, dsb)
require __DIR__.'/auth.php';