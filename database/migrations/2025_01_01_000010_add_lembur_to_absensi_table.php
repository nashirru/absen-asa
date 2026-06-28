<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->boolean('is_lembur')->default(false)->after('catatan');
            $table->time('jam_lembur_mulai')->nullable()->after('is_lembur');
            $table->time('jam_lembur_selesai')->nullable()->after('jam_lembur_mulai');
            $table->decimal('durasi_lembur', 5, 2)->nullable()->after('jam_lembur_selesai');
        });
    }

    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropColumn(['is_lembur', 'jam_lembur_mulai', 'jam_lembur_selesai', 'durasi_lembur']);
        });
    }
};
