<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make employee_id nullable in salary_components (may have been dropped by 181000 migration)
        if (Schema::hasColumn('salary_components', 'employee_id')) {
            Schema::table('salary_components', function (Blueprint $table) {
                $table->foreignId('employee_id')->nullable()->change();
            });
        }

        // Make employee_id nullable in payroll_details (may have been dropped by 181000 migration)
        if (Schema::hasColumn('payroll_details', 'employee_id')) {
            Schema::table('payroll_details', function (Blueprint $table) {
                $table->foreignId('employee_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('salary_components', 'employee_id')) {
            Schema::table('salary_components', function (Blueprint $table) {
                $table->foreignId('employee_id')->nullable(false)->change();
            });
        }

        if (Schema::hasColumn('payroll_details', 'employee_id')) {
            Schema::table('payroll_details', function (Blueprint $table) {
                $table->foreignId('employee_id')->nullable(false)->change();
            });
        }
    }
};
