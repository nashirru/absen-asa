<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make employee_id nullable in salary_components
        Schema::table('salary_components', function (Blueprint $table) {
            $table->foreignId('employee_id')->nullable()->change();
        });

        // Make employee_id nullable in payroll_details
        Schema::table('payroll_details', function (Blueprint $table) {
            $table->foreignId('employee_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('salary_components', function (Blueprint $table) {
            $table->foreignId('employee_id')->nullable(false)->change();
        });

        Schema::table('payroll_details', function (Blueprint $table) {
            $table->foreignId('employee_id')->nullable(false)->change();
        });
    }
};
