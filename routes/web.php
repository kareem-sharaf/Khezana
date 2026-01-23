<?php

// Khezana Project - Web Routes
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Serve storage files (fallback route if .htaccess doesn't work)
// This route only serves files from storage/app/public directory
Route::get('/storage/{path}', function (string $path) {
    // Security: Prevent directory traversal
    $path = str_replace('..', '', $path);
    $path = ltrim($path, '/');
    
    $filePath = storage_path('app/public/' . $path);
    $storagePath = storage_path('app/public');
    
    // Security: Ensure file is within storage/app/public directory
    $realFilePath = realpath($filePath);
    $realStoragePath = realpath($storagePath);
    
    if (!$realFilePath || !$realStoragePath || !str_starts_with($realFilePath, $realStoragePath)) {
        abort(404);
    }
    
    if (!file_exists($realFilePath) || !is_file($realFilePath)) {
        abort(404);
    }
    
    $mimeType = mime_content_type($realFilePath) ?: 'application/octet-stream';
    $fileSize = filesize($realFilePath);
    
    return response()->file($realFilePath, [
        'Content-Type' => $mimeType,
        'Content-Length' => $fileSize,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('storage.serve');

// Public Routes (No Authentication Required) - Must be before auth routes
Route::middleware(['cache.headers:300', 'throttle:60,1'])->group(function () {
    Route::get('/items', [\App\Http\Controllers\Public\ItemController::class, 'index'])->name('public.items.index');
    Route::get('/items/{id}/{slug?}', [\App\Http\Controllers\Public\ItemController::class, 'show'])->name('public.items.show');

    Route::get('/request-clothing', [\App\Http\Controllers\Public\RequestController::class, 'createInfo'])->name('public.requests.create-info');
    Route::get('/requests', [\App\Http\Controllers\Public\RequestController::class, 'index'])->name('public.requests.index');
    Route::get('/requests/{id}/{slug?}', [\App\Http\Controllers\Public\RequestController::class, 'show'])->name('public.requests.show');
});

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Static Pages
Route::get('/terms', [\App\Http\Controllers\PageController::class, 'terms'])->name('pages.terms');
Route::get('/privacy', [\App\Http\Controllers\PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/how-it-works', [\App\Http\Controllers\PageController::class, 'howItWorks'])->name('pages.how-it-works');
Route::get('/fees', [\App\Http\Controllers\PageController::class, 'fees'])->name('pages.fees');

Route::get('/dashboard', function () {
    // Redirect dashboard to home for simplicity
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/password', [ProfileController::class, 'showPasswordForm'])->name('profile.password');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
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
        Route::delete('/{item}/force', [\App\Http\Controllers\ItemController::class, 'forceDestroy'])->name('force-destroy');
        Route::post('/{item}/restore', [\App\Http\Controllers\ItemController::class, 'restore'])->name('restore');
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
        Route::delete('/{request}/force', [\App\Http\Controllers\RequestController::class, 'forceDestroy'])->name('force-destroy');
        Route::post('/{request}/restore', [\App\Http\Controllers\RequestController::class, 'restore'])->name('restore');
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
