<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MemoController;

Route::get('/memos', [MemoController::class, 'index'])->name('memos.index');
Route::get('/memos/create', [MemoController::class, 'create'])->name('memos.create');
Route::post('/memos', [MemoController::class, 'store'])->name('memos.store');
Route::get('/memos/{id}/pdf', [MemoController::class, 'download'])->name('memos.pdf');