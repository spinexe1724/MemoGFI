<?php

use App\Http\Controllers\MemoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\BranchController;
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
            Route::post('/memos/{id}/publish', [MemoController::class, 'publish'])->name('memos.publish');
Route::get('/memo/approvals', [MemoController::class, 'pendingApprovals'])->name('memos.approvals');
        Route::get('/memos/my-memos', [MemoController::class, 'myMemos'])->name('memos.my_memos');
        Route::get('/memos/rejectedMemos', [MemoController::class, 'rejectedMemos'])->name('memos.rejectedMemos');
    Route::get('/memos/drafts', [MemoController::class, 'drafts'])->name('memos.drafts');
         Route::post('/memos/{id}/approve', [MemoController::class, 'approve'])->name('memos.approve');
    Route::post('/memos/{id}/reject', [MemoController::class, 'reject'])->name('memos.reject');
    Route::post('/memos/upload-image', [MemoController::class, 'uploadImage'])->name('memos.upload');
    Route::get('/memos/my-own', [App\Http\Controllers\MemoController::class, 'myOwnMemos'])->name('memos.own');
    Route::delete('/memos/{id}', [MemoController::class, 'destroy'])->name('memos.destroy');

// Rute untuk menghapus LAMPIRAN (Gunakan URL yang berbeda agar tidak tertukar)
Route::delete('/attachments/{id}', [MemoController::class, 'destroyAttachment'])->name('memos.attachment.destroy');
      Route::get('/attachments/{id}/download', [MemoController::class, 'downloadAttachment'])->name('memos.attachment.download');
Route::delete('/attachments/{id}', [MemoController::class, 'destroyAttachment'])->name('memos.attachment.destroy');
    // Rute Generate PDF
    Route::get('/memos/{id}/pdf', [MemoController::class, 'download'])->name('memos.pdf');

    // Resource route untuk Memos (Index, Create, Store, Edit, Update, Show, Destroy)
    Route::resource('memos', MemoController::class);
    
    
    
    // Rute Khusus Persetujuan & Penolakan (GM & Direksi)
   

    // --- User Management (Khusus Superadmin) ---
    // Proteksi menggunakan Gate 'is-superadmin' yang didefinisikan di AppServiceProvider
   Route::middleware(['can:is-superadmin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('divisions', DivisionController::class)->only(['index', 'store', 'destroy']);
                Route::resource('branches', BranchController::class);
                Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
Route::delete('/users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.force_delete');

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