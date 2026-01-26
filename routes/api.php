<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\StorePickupController;

Route::middleware('auth:sanctum')->group(function () {
    /**
     * Routes الطلبات
     */
    Route::prefix('orders')->controller(OrderController::class)->group(function () {
        // إنشاء طلب جديد
        Route::post('/', 'create');

        // الحصول على تفاصيل طلب
        Route::get('{id}', 'getDetails');

        // إلغاء طلب
        Route::post('{id}/cancel', 'cancel');
    });

    // قائمة طلبات المستخدم
    Route::get('/user/orders', [OrderController::class, 'listUserOrders']);

    /**
     * Routes الاستلام من المتجر
     */
    Route::prefix('store')->controller(StorePickupController::class)->group(function () {
        // التحقق من QR
        Route::post('/verify-qr', 'verifyQr');

        // إكمال الاستلام
        Route::post('/complete-pickup', 'completePickup');

        // قائمة طلبات الاستلام
        Route::get('/pickup-orders', 'getPendingPickups');

        // إحصائيات المتجر
        Route::get('/statistics', 'getStatistics');
    });
});
