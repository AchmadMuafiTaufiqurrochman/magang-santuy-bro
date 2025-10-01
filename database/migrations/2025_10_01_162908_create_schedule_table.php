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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();

            // Relasi ke orders (jadwal untuk order tertentu)
            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->cascadeOnDelete();

            // Relasi ke users (khusus teknisi)
            $table->foreignId('technician_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Detail jadwal
            $table->date('scheduled_date');
            $table->time('scheduled_time');

            // Status jadwal
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])
                  ->default('pending');

            // Catatan opsional
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
