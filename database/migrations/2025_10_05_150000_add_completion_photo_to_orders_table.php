<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('completion_photo')->nullable()->after('note');
            $table->text('completion_notes')->nullable()->after('completion_photo');
            $table->timestamp('completed_at')->nullable()->after('completion_notes');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['completion_photo', 'completion_notes', 'completed_at']);
        });
    }
};