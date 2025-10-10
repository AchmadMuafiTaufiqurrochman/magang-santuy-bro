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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // relasi ke orders
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            // metode pembayaran
            $table->enum('payment_method', ['cash', 'bank_transfer', 'ewallet'])
                ->default('cash');

            // status transaksi
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])
                ->default('pending');

            // jumlah pembayaran
            $table->decimal('amount', 12, 2)->default(0);

            // kode unik pembayaran (opsional)
            $table->string('reference')->nullable();

            // tanggal pembayaran
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
