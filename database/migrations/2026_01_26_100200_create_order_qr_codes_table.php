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
        Schema::create('order_qr_codes', function (Blueprint $table) {
            $table->id();

            // معرف الطلب
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');

            // بيانات QR
            $table->string('qr_code')->unique();
            $table->text('qr_data');

            // حالة الكود
            $table->enum('status', ['ACTIVE', 'USED', 'EXPIRED', 'CANCELLED'])->default('ACTIVE')->index();
            $table->dateTime('used_at')->nullable();

            // معلومات الاستخدام
            $table->foreignId('scanned_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('scanned_at')->nullable();

            // صلاحية الكود
            $table->dateTime('expiry_date')->index();

            $table->timestamps();

            // الفهارس
            $table->index('order_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_qr_codes');
    }
};
