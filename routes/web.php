<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MemoController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/memos', [MemoController::class, 'index'])->name('memos.index');
    Route::get('/memos/create', [MemoController::class, 'create'])->name('memos.create');
    Route::post('/memos', [MemoController::class, 'store'])->name('memos.store');
    Route::post('/memos/{id}/approve', [MemoController::class, 'approve'])->name('memos.approve');
        Route::post('/memos/{id}/reject', [MemoController::class, 'reject'])->name('memos.reject');

    Route::get('/memos/{id}/pdf', [MemoController::class, 'download'])->name('memos.pdf');
});
require __DIR__.'/auth.php';
