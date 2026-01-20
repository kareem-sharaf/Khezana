<?php

// Khezana Project - Web Routes
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Items routes
    Route::resource('items', \App\Http\Controllers\ItemController::class);
    Route::post('items/{item}/submit-for-approval', [\App\Http\Controllers\ItemController::class, 'submitForApproval'])
        ->name('items.submit-for-approval');
    
    // Requests routes
    Route::resource('requests', \App\Http\Controllers\RequestController::class);
    Route::post('requests/{request}/submit-for-approval', [\App\Http\Controllers\RequestController::class, 'submitForApproval'])
        ->name('requests.submit-for-approval');
    Route::post('requests/{request}/close', [\App\Http\Controllers\RequestController::class, 'close'])
        ->name('requests.close');
});

require __DIR__.'/auth.php';
