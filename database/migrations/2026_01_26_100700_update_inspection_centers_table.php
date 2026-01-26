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
        Schema::table('inspection_centers', function (Blueprint $table) {
            // إضافة حقول المتجر
            if (!Schema::hasColumn('inspection_centers', 'is_pickup_location')) {
                $table->boolean('is_pickup_location')->default(false)->after('name');
            }

            if (!Schema::hasColumn('inspection_centers', 'pickup_hours')) {
                $table->json('pickup_hours')->nullable()->after('is_pickup_location');
            }

            if (!Schema::hasColumn('inspection_centers', 'contact_phone')) {
                $table->string('contact_phone')->nullable()->after('pickup_hours');
            }

            if (!Schema::hasColumn('inspection_centers', 'whatsapp_number')) {
                $table->string('whatsapp_number')->nullable()->after('contact_phone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inspection_centers', function (Blueprint $table) {
            $table->dropColumn(['is_pickup_location', 'pickup_hours', 'contact_phone', 'whatsapp_number']);
        });
    }
};
