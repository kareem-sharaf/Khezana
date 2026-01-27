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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // معرف الطلب الفريد
            $table->string('order_number')->unique();

            // معلومات العميل
            $table->foreignId('customer_id')->constrained('users')->onDelete('restrict');

            // طريقة الاستلام
            $table->enum('channel', ['DELIVERY', 'IN_STORE_PICKUP'])->default('DELIVERY');

            // حالة الطلب
            $table->enum('status', [
                'CREATED',           // تم الإنشاء
                'PENDING_PAYMENT',   // في انتظار الدفع
                'CONFIRMED',         // مؤكد
                'PROCESSING',        // قيد المعالجة
                'READY_FOR_PICKUP',  // جاهز للاستلام
                'CUSTOMER_ARRIVED',  // وصل العميل للمتجر
                'COMPLETED',         // مكتمل
                'CANCELLED',         // ملغي
                'EXPIRED'            // منتهي الصلاحية
            ])->default('CREATED')->index();

            // معلومات الأسعار
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->decimal('delivery_fee', 10, 2)->default(0.00);

            // معلومات التوصيل (للطلبات المرسلة)
            $table->text('delivery_address')->nullable();
            $table->string('delivery_city')->nullable();
            $table->text('delivery_notes')->nullable();

            // معلومات الاستلام من المتجر
            $table->foreignId('pickup_store_id')->nullable()->constrained('inspection_centers')->onDelete('set null');
            $table->string('pickup_code')->nullable()->unique();
            $table->dateTime('pickup_expiry')->nullable()->index();

            // معلومات الدفع
            $table->enum('payment_method', ['CASH_ON_DELIVERY', 'CASH_IN_STORE', 'ONLINE'])->default('CASH_ON_DELIVERY');
            $table->enum('payment_status', ['PENDING', 'PAID', 'PARTIALLY_PAID', 'REFUNDED'])->default('PENDING');

            // تواريخ مهمة
            $table->date('pickup_scheduled_date')->nullable();
            $table->dateTime('pickup_actual_date')->nullable();

            // ملاحظات عامة
            $table->text('notes')->nullable();

            $table->timestamps();

            // Additional indexes
            $table->index('customer_id');
            $table->index('pickup_code');
            $table->index('order_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
