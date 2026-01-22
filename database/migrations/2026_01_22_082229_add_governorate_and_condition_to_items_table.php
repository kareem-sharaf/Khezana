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
        Schema::table('items', function (Blueprint $table) {
            $table->string('governorate', 50)->nullable()->after('description');
            $table->enum('condition', ['new', 'used'])->nullable()->after('governorate');
            
            // Add index for condition (used in filtering)
            // Note: governorate index removed as it's not used in v1 (all items in Damascus)
            $table->index('condition');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex(['condition']);
            $table->dropColumn(['governorate', 'condition']);
        });
    }
};
