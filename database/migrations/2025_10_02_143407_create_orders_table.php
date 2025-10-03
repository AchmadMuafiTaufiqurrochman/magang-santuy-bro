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

            // Relasi ke teknisi (nullable)
            $table->foreignId('technician_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Detail order
            $table->date('order_date')->default(now());
            $table->date('service_date')->nullable();
            $table->time('time_slot')->nullable();
            $table->string('address')->nullable();

            // Status order (tambahkan 'assigned' supaya sesuai dengan proses update)
            $table->enum('status', ['pending', 'assigned', 'in_progress', 'completed', 'cancelled'])
                  ->default('pending');

            $table->decimal('total_price', 12, 2)->default(0);
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
