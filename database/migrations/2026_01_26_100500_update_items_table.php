<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // تحديث جدول items
        Schema::table('items', function (Blueprint $table) {
            // إضافة حالة المنتج إذا لم تكن موجودة
            if (!Schema::hasColumn('items', 'item_status')) {
                $table->enum('item_status', [
                    'AVAILABLE',        // متاح
                    'RESERVED',         // محجوز
                    'RENTED',           // مؤجر
                    'SOLD',             // مباع
                    'UNDER_INSPECTION', // تحت الفحص
                    'MAINTENANCE',      // تحت الصيانة
                    'ARCHIVED'          // مؤرشف
                ])->default('AVAILABLE')->after('is_available')->index();
            }

            // إضافة سعر المتجر
            if (!Schema::hasColumn('items', 'store_price')) {
                $table->decimal('store_price', 10, 2)->nullable()->after('price');
            }

            // إضافة تاريخ انتهاء الحجز
            if (!Schema::hasColumn('items', 'reservation_expiry')) {
                $table->dateTime('reservation_expiry')->nullable()->after('item_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['item_status', 'store_price', 'reservation_expiry']);
        });
    }
};
