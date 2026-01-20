<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('items')) {
            Schema::table('items', function (Blueprint $table) {
                if (!$this->hasIndex('items', 'availability_status')) {
                    $table->index('availability_status', 'idx_items_availability_status');
                }
            });
        }

        if (Schema::hasTable('requests')) {
            Schema::table('requests', function (Blueprint $table) {
                if (!$this->hasIndex('requests', 'status')) {
                    $table->index('status', 'idx_requests_status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('items')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropIndex('idx_items_availability_status');
            });
        }

        if (Schema::hasTable('requests')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->dropIndex('idx_requests_status');
            });
        }
    }

    private function hasIndex(string $table, string $column): bool
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Column_name = ?", [$column]);
            return !empty($indexes);
        } catch (\Exception $e) {
            return false;
        }
    }
};
