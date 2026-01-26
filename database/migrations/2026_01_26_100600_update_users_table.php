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
        Schema::table('users', function (Blueprint $table) {
            // إضافة نوع المستخدم إذا لم يكن موجوداً
            if (!Schema::hasColumn('users', 'user_type')) {
                $table->enum('user_type', [
                    'CUSTOMER',      // عميل
                    'SELLER',        // بائع
                    'STORE_STAFF',   // موظف متجر
                    'INSPECTOR',     // فاحص
                    'COURIER',       // مندوب توصيل
                    'ADMIN'          // مسؤول
                ])->default('CUSTOMER')->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('user_type');
        });
    }
};
