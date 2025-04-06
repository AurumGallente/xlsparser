<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;



Route::get('/dashboard', function () {
    return redirect()->route('upload.form');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/table', [TableController::class, 'index'])->name('table.index');

Route::middleware('auth')->group(function () {
    Route::get('/', [UploadController::class, 'index'])->name('upload.form');
    Route::post('/upload', [UploadController::class, 'upload'])->name('xls.upload');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
