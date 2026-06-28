<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("payroll_details", function (Blueprint $table) {
            $table->id();
            $table->foreignId("payroll_period_id")->constrained()->cascadeOnDelete();
            $table->foreignId("employee_id")->constrained()->cascadeOnDelete();
            $table->decimal("base_salary", 15, 2);
            $table->decimal("total_allowance", 15, 2)->default(0);
            $table->decimal("total_deduction", 15, 2)->default(0);
            $table->decimal("bonus", 15, 2)->nullable();
            $table->decimal("net_salary", 15, 2);
            $table->timestamp("paid_at")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("payroll_details");
    }
};
