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
    
    // Offers routes
    Route::get('requests/{request}/offers/create', [\App\Http\Controllers\OfferController::class, 'create'])
        ->name('offers.create');
    Route::post('requests/{request}/offers', [\App\Http\Controllers\OfferController::class, 'store'])
        ->name('offers.store');
    Route::get('offers/{offer}/edit', [\App\Http\Controllers\OfferController::class, 'edit'])
        ->name('offers.edit');
    Route::put('offers/{offer}', [\App\Http\Controllers\OfferController::class, 'update'])
        ->name('offers.update');
    Route::post('offers/{offer}/cancel', [\App\Http\Controllers\OfferController::class, 'cancel'])
        ->name('offers.cancel');
    Route::post('offers/{offer}/accept', [\App\Http\Controllers\OfferController::class, 'accept'])
        ->name('offers.accept');
    Route::post('offers/{offer}/reject', [\App\Http\Controllers\OfferController::class, 'reject'])
        ->name('offers.reject');
});

require __DIR__.'/auth.php';
