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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama layanan, misalnya "Cuci AC"
            $table->text('description')->nullable(); // Deskripsi layanan
            $table->decimal('price', 10, 2); // Harga layanan
            $table->enum('status', ['active', 'inactive'])->default('active'); // Status layanan
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
