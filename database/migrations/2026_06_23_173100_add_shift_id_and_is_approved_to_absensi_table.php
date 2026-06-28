<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->boolean('is_approved')->nullable(); // null = pending, true = approved, false = rejected
        });
    }

    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropColumn(['shift_id', 'is_approved']);
        });
    }
};
