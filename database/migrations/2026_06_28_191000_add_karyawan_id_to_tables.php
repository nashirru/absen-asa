<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('salary_components', 'karyawan_id')) {
            Schema::table('salary_components', function (Blueprint $table) {
                $table->foreignId('karyawan_id')->nullable()->constrained('karyawan')->cascadeOnDelete()->after('id');
            });
        }

        if (!Schema::hasColumn('payroll_details', 'karyawan_id')) {
            Schema::table('payroll_details', function (Blueprint $table) {
                $table->foreignId('karyawan_id')->nullable()->constrained('karyawan')->cascadeOnDelete()->after('id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('salary_components', function (Blueprint $table) {
            if (Schema::hasColumn('salary_components', 'karyawan_id')) {
                $table->dropForeign(['karyawan_id']);
                $table->dropColumn('karyawan_id');
            }
        });

        Schema::table('payroll_details', function (Blueprint $table) {
            if (Schema::hasColumn('payroll_details', 'karyawan_id')) {
                $table->dropForeign(['karyawan_id']);
                $table->dropColumn('karyawan_id');
            }
        });
    }
};
