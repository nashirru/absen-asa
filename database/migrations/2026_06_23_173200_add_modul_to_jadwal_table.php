<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal', function (Blueprint $table) {
            $table->string('modul_link')->nullable();
            $table->string('modul_file')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('jadwal', function (Blueprint $table) {
            $table->dropColumn(['modul_link', 'modul_file']);
        });
    }
};
