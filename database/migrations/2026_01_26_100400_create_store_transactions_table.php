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
        Schema::create('store_transactions', function (Blueprint $table) {
            $table->id();

            // معرفات العلاقة
            $table->foreignId('order_id')->constrained('orders')->onDelete('restrict');
            $table->foreignId('store_id')->constrained('inspection_centers')->onDelete('restrict');
            $table->foreignId('staff_user_id')->constrained('users')->onDelete('restrict');

            // نوع المعاملة
            $table->enum('transaction_type', [
                'PICKUP_SCAN',      // مسح QR للاستلام
                'PAYMENT_RECEIVED', // استلام دفعة
                'RETURN_PROCESSED', // معالجة الإرجاع
                'CANCELLATION'      // إلغاء
            ]);

            // معلومات المبلغ
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('payment_method')->nullable();

            // ملاحظات
            $table->text('notes')->nullable();

            $table->timestamps();

            // الفهارس
            $table->index('order_id');
            $table->index('store_id');
            $table->index('staff_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_transactions');
    }
};
