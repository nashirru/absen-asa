<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("absensi", function (Blueprint $table) {
            $table->enum("shift", ["pagi", "siang"])->default("pagi")->after("status");
        });
    }

    public function down(): void
    {
        Schema::table("absensi", function (Blueprint $table) {
            $table->dropColumn("shift");
        });
    }
};
