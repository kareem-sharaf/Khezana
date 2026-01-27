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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            // معرفات العلاقة
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items')->onDelete('restrict');

            // نوع العملية
            $table->enum('operation_type', ['RENT', 'SALE', 'DONATE'])->nullable();

            // معلومات الإيجار
            $table->date('rent_start_date')->nullable();
            $table->date('rent_end_date')->nullable();
            $table->integer('rent_days')->nullable();

            // الأسعار
            $table->decimal('unit_price', 10, 2);
            $table->decimal('rent_price_per_day', 10, 2)->nullable();
            $table->decimal('deposit_amount', 10, 2)->default(0.00);
            $table->decimal('item_total', 10, 2);

            // حالة العنصر في الطلب
            $table->enum('item_status', [
                'RESERVED',
                'PICKED_UP',
                'DELIVERED',
                'IN_USE',      // للإيجار
                'RETURNED',
                'CANCELLED'
            ])->default('RESERVED')->index();

            $table->timestamps();

            // Indexes
            $table->index('order_id');
            $table->index('item_id');

            // Prevent duplicates
            $table->unique(['order_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
