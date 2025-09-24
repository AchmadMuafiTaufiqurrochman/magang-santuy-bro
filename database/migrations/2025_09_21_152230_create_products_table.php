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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id'); // Primary key

            $table->string('name', 150); // Nama produk
            $table->text('description')->nullable(); // Deskripsi produk
            $table->decimal('price', 12, 2); // Harga produk

            $table->unsignedBigInteger('id_package'); // Relasi ke package
            $table->foreign('id_package')->references('id')->on('packages')->onDelete('cascade');

            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
