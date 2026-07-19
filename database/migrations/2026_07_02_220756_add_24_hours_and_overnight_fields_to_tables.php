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
        Schema::table('shifts', function (Blueprint $table) {
            $table->boolean('is_24_hours')->default(false)->after('batas_terlambat');
        });

        Schema::table('absensi', function (Blueprint $table) {
            $table->date('tanggal_keluar')->nullable()->after('tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn('is_24_hours');
        });

        Schema::table('absensi', function (Blueprint $table) {
            $table->dropColumn('tanggal_keluar');
        });
    }
};
