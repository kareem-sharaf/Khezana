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
        Schema::create('order_tracking', function (Blueprint $table) {
            $table->id();

            // معرف الطلب
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');

            // معلومات التغيير
            $table->string('old_status')->nullable();
            $table->string('new_status');

            // نوع الحدث
            $table->enum('event_type', [
                'STATUS_CHANGE',
                'PAYMENT_UPDATE',
                'PICKUP_SCHEDULED',
                'PICKUP_COMPLETED',
                'DELIVERY_UPDATE',
                'CANCELLATION',
                'EXPIRATION',
                'QR_SCANNED'
            ]);

            // ملاحظات والممثل
            $table->text('notes')->nullable();
            $table->foreignId('actor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('actor_type', ['CUSTOMER', 'VENDOR', 'STAFF', 'SYSTEM'])->default('SYSTEM');

            $table->timestamps();

            // الفهارس
            $table->index('order_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_tracking');
    }
};
