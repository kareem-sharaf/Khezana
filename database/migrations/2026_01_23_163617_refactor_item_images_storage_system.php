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
        Schema::table('item_images', function (Blueprint $table) {
            // Remove base64 data column
            $table->dropColumn('data');
            
            // Add disk column for storage flexibility (local, s3, etc.)
            $table->string('disk')->default('public')->after('path');
            
            // Update path to be nullable (for flexibility)
            $table->string('path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_images', function (Blueprint $table) {
            $table->longText('data')->nullable();
            $table->dropColumn('disk');
            $table->string('path')->nullable(false)->change();
        });
    }
};
