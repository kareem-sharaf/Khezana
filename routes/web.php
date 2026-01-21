<?php

// Khezana Project - Web Routes
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public Routes (No Authentication Required) - Must be before auth routes
Route::middleware(['cache.headers:300', 'throttle:60,1'])->group(function () {
    Route::get('/items', [\App\Http\Controllers\Public\ItemController::class, 'index'])->name('public.items.index');
    Route::get('/items/{id}/{slug?}', [\App\Http\Controllers\Public\ItemController::class, 'show'])->name('public.items.show');

    Route::get('/request-clothing', [\App\Http\Controllers\Public\RequestController::class, 'createInfo'])->name('public.requests.create-info');
    Route::get('/requests', [\App\Http\Controllers\Public\RequestController::class, 'index'])->name('public.requests.index');
    Route::get('/requests/{id}/{slug?}', [\App\Http\Controllers\Public\RequestController::class, 'show'])->name('public.requests.show');
});

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    // Redirect dashboard to home for simplicity
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Items routes (User authenticated routes) - Using 'my-items' prefix to avoid conflict
    Route::prefix('my-items')->name('items.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ItemController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\ItemController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\ItemController::class, 'store'])->name('store');
        Route::get('/{item}', [\App\Http\Controllers\ItemController::class, 'show'])->name('show');
        Route::get('/{item}/edit', [\App\Http\Controllers\ItemController::class, 'edit'])->name('edit');
        Route::put('/{item}', [\App\Http\Controllers\ItemController::class, 'update'])->name('update');
        Route::delete('/{item}', [\App\Http\Controllers\ItemController::class, 'destroy'])->name('destroy');
        Route::post('/{item}/submit-for-approval', [\App\Http\Controllers\ItemController::class, 'submitForApproval'])
            ->name('submit-for-approval');
    });

    // Requests routes (User authenticated routes) - Using 'my-requests' prefix to avoid conflict
    Route::prefix('my-requests')->name('requests.')->group(function () {
        Route::get('/', [\App\Http\Controllers\RequestController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\RequestController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\RequestController::class, 'store'])->name('store');
        Route::get('/{request}', [\App\Http\Controllers\RequestController::class, 'show'])->name('show');
        Route::get('/{request}/edit', [\App\Http\Controllers\RequestController::class, 'edit'])->name('edit');
        Route::put('/{request}', [\App\Http\Controllers\RequestController::class, 'update'])->name('update');
        Route::delete('/{request}', [\App\Http\Controllers\RequestController::class, 'destroy'])->name('destroy');
        Route::post('/{request}/submit-for-approval', [\App\Http\Controllers\RequestController::class, 'submitForApproval'])
            ->name('submit-for-approval');
        Route::post('/{request}/close', [\App\Http\Controllers\RequestController::class, 'close'])
            ->name('close');
    });

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

// Public Actions (Require Authentication with Redirect)
Route::middleware('auth.redirect')->group(function () {
    Route::post('/items/{item}/contact', [\App\Http\Controllers\Public\ItemController::class, 'contact'])
        ->name('public.items.contact');

    Route::post('/items/{item}/favorite', [\App\Http\Controllers\FavoriteController::class, 'toggle'])
        ->name('public.items.favorite');

    Route::post('/items/{item}/report', [\App\Http\Controllers\Public\ItemController::class, 'report'])
        ->name('public.items.report');

    Route::post('/requests/{request}/offer', [\App\Http\Controllers\Public\RequestController::class, 'submitOffer'])
        ->name('public.requests.offer');

    Route::post('/requests/{request}/contact', [\App\Http\Controllers\Public\RequestController::class, 'contact'])
        ->name('public.requests.contact');

    Route::post('/requests/{request}/report', [\App\Http\Controllers\Public\RequestController::class, 'report'])
        ->name('public.requests.report');
});

require __DIR__ . '/auth.php';
