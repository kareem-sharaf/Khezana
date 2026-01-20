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
            $table->enum('availability_status', ['available', 'unavailable'])
                ->default('available')
                ->after('is_available');
        });

        \Illuminate\Support\Facades\DB::table('items')
            ->where('is_available', true)
            ->update(['availability_status' => 'available']);
            
        \Illuminate\Support\Facades\DB::table('items')
            ->where('is_available', false)
            ->update(['availability_status' => 'unavailable']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('availability_status');
        });
    }
};
