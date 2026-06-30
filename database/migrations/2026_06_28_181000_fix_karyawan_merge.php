<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ============================================================
        // 1. Add karyawan_id columns (if not already present)
        // ============================================================
        if (!Schema::hasColumn('salary_components', 'karyawan_id')) {
            Schema::table('salary_components', function (Blueprint $table) {
                $table->foreignId('karyawan_id')->nullable()->constrained('karyawan')->cascadeOnDelete()->after('id');
                $table->index('karyawan_id');
            });
        }

        if (!Schema::hasColumn('payroll_details', 'karyawan_id')) {
            Schema::table('payroll_details', function (Blueprint $table) {
                $table->foreignId('karyawan_id')->nullable()->constrained('karyawan')->cascadeOnDelete()->after('id');
                $table->index('karyawan_id');
            });
        }

        // ============================================================
        // 2. Migrate data from employees to karyawan
        // ============================================================
        if (Schema::hasTable('employees') && DB::table('employees')->count() > 0) {
            $employees = DB::table('employees')->get();
            foreach ($employees as $employee) {
                $karyawan = DB::table('karyawan')
                    ->join('users', 'users.id', '=', 'karyawan.user_id')
                    ->where('users.name', $employee->name)
                    ->select('karyawan.id')
                    ->first();

                if ($karyawan) {
                    // Update existing karyawan with employee data
                    DB::table('karyawan')
                        ->where('id', $karyawan->id)
                        ->update([
                            'base_salary' => $employee->base_salary,
                            'join_date' => $employee->join_date,
                            'status' => $employee->status,
                        ]);
                    $karyawanId = $karyawan->id;
                } else {
                    // Skip employees without a matching user account
                    // to avoid creating orphaned karyawan records with user_id = null
                    continue;
                }

                // Link salary_components to the karyawan record
                DB::table('salary_components')
                    ->where('employee_id', $employee->id)
                    ->whereNull('karyawan_id')
                    ->update(['karyawan_id' => $karyawanId]);

                // Link payroll_details to the karyawan record
                DB::table('payroll_details')
                    ->where('employee_id', $employee->id)
                    ->update(['karyawan_id' => $karyawanId]);
            }
        }

        // ============================================================
        // 3. Clean up employee_id column from salary_components
        // ============================================================
        if (Schema::hasColumn('salary_components', 'employee_id')) {
            try {
                Schema::table('salary_components', function (Blueprint $table) {
                    $table->dropForeign(['employee_id']);
                });
            } catch (\Exception $e) {
                // FK constraint may not exist
            }

            Schema::table('salary_components', function (Blueprint $table) {
                $table->dropColumn('employee_id');
            });
        }

        // ============================================================
        // 4. Clean up employee_id column from payroll_details
        // ============================================================
        if (Schema::hasColumn('payroll_details', 'employee_id')) {
            try {
                Schema::table('payroll_details', function (Blueprint $table) {
                    $table->dropForeign(['employee_id']);
                });
            } catch (\Exception $e) {
                // FK constraint may not exist
            }

            Schema::table('payroll_details', function (Blueprint $table) {
                $table->dropColumn('employee_id');
            });
        }
    }

    public function down(): void
    {
        // Restore employee_id to salary_components
        if (!Schema::hasColumn('salary_components', 'employee_id')) {
            Schema::table('salary_components', function (Blueprint $table) {
                $table->foreignId('employee_id')->nullable()->constrained('employees')->cascadeOnDelete()->after('id');
            });
        }

        // Restore employee_id to payroll_details
        if (!Schema::hasColumn('payroll_details', 'employee_id')) {
            Schema::table('payroll_details', function (Blueprint $table) {
                $table->foreignId('employee_id')->nullable()->constrained('employees')->cascadeOnDelete()->after('id');
            });
        }

        // Drop karyawan_id FK and column from salary_components
        if (Schema::hasColumn('salary_components', 'karyawan_id')) {
            Schema::table('salary_components', function (Blueprint $table) {
                $table->dropForeign(['karyawan_id']);
                $table->dropColumn('karyawan_id');
            });
        }

        // Drop karyawan_id FK and column from payroll_details
        if (Schema::hasColumn('payroll_details', 'karyawan_id')) {
            Schema::table('payroll_details', function (Blueprint $table) {
                $table->dropForeign(['karyawan_id']);
                $table->dropColumn('karyawan_id');
            });
        }
    }
};
