<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Phase 3.3: WebP support – optional path for WebP variant */
    public function up(): void
    {
        Schema::table('item_images', function (Blueprint $table) {
            $table->string('path_webp')->nullable()->after('path');
        });
    }

    public function down(): void
    {
        Schema::table('item_images', function (Blueprint $table) {
            $table->dropColumn('path_webp');
        });
    }
};
