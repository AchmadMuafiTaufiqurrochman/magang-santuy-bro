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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Relasi ke user (customer)
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Relasi ke service, product, package
            $table->foreignId('service_id')
                ->constrained('services')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->foreignId('package_id')
                ->constrained('packages')
                ->cascadeOnDelete();

            // Relasi ke teknisi (nullable, bisa null kalau belum ditugaskan)
            $table->foreignId('technician_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Detail order
            $table->date('order_date'); // hanya tanggal
            $table->time('time_slot')->nullable(); // slot waktu
            $table->string('address')->nullable();

            // Status order
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])
                ->default('pending');

            // Total harga
            $table->decimal('total_price', 12, 2)->default(0);

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
        Schema::dropIfExists('orders');
    }
};
