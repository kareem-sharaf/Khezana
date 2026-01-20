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
        Schema::table('approvals', function (Blueprint $table) {
            $table->unique(['approvable_type', 'approvable_id'], 'unique_approvable');
        });

        Schema::table('approvals', function (Blueprint $table) {
            $table->dropForeign(['submitted_by']);
            $table->dropForeign(['reviewed_by']);
        });

        Schema::table('approvals', function (Blueprint $table) {
            $table->foreign('submitted_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            $table->foreign('reviewed_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->dropUnique('unique_approvable');
        });

        Schema::table('approvals', function (Blueprint $table) {
            $table->dropForeign(['submitted_by']);
            $table->dropForeign(['reviewed_by']);
        });

        Schema::table('approvals', function (Blueprint $table) {
            $table->foreign('submitted_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('reviewed_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }
};
