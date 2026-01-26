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
        Schema::create('offer_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('request_id')->constrained()->onDelete('cascade');
            $table->enum('channel', ['whatsapp', 'telegram'])->index();
            
            // Optional: store offer summary data
            $table->string('operation_type')->nullable(); // sell, rent, donate
            $table->decimal('price', 10, 2)->nullable();
            
            $table->timestamps();
            
            // Indexes for dashboard queries
            $table->index(['request_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_attempts');
    }
};
