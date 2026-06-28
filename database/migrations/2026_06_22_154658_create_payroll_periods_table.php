<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("payroll_periods", function (Blueprint $table) {
            $table->id();
            $table->integer("month");
            $table->year("year");
            $table->enum("status", ["draft", "processed", "paid"])->default("draft");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("payroll_periods");
    }
};
