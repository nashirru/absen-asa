<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix salary_components FK (column exists but constraint might not)
        try {
            Schema::table('salary_components', function (Blueprint $table) {
                $table->foreign('karyawan_id')->references('id')->on('karyawan')->cascadeOnDelete();
            });
        } catch (\Exception $e) {
            // Constraint may already exist
        }

        // Add karyawan_id to payroll_details if not exists
        if (!Schema::hasColumn('payroll_details', 'karyawan_id')) {
            Schema::table('payroll_details', function (Blueprint $table) {
                $table->foreignId('karyawan_id')->nullable()->constrained('karyawan')->cascadeOnDelete()->after('id');
            });
        }

        // Migrate data (only if employees table still has data)
        if (Schema::hasTable('employees')) {
            $employees = DB::table('employees')->get();
            foreach ($employees as $employee) {
                $karyawan = DB::table('karyawan')
                    ->join('users', 'users.id', '=', 'karyawan.user_id')
                    ->where('users.name', $employee->name)
                    ->select('karyawan.id')
                    ->first();

                if ($karyawan) {
                    DB::table('karyawan')
                        ->where('id', $karyawan->id)
                        ->update([
                            'base_salary' => $employee->base_salary,
                            'join_date' => $employee->join_date,
                            'status' => $employee->status,
                        ]);
                    $karyawanId = $karyawan->id;
                } else {
                    $karyawanId = DB::table('karyawan')->insertGetId([
                        'user_id' => null,
                        'nik' => 'PAY-' . str_pad($employee->id, 4, '0', STR_PAD_LEFT),
                        'jabatan' => $employee->position,
                        'divisi' => $employee->department ?? 'Umum',
                        'alamat' => null,
                        'base_salary' => $employee->base_salary,
                        'join_date' => $employee->join_date,
                        'status' => $employee->status,
                        'created_at' => $employee->created_at ?? now(),
                        'updated_at' => $employee->updated_at ?? now(),
                    ]);
                }

                DB::table('salary_components')
                    ->where('employee_id', $employee->id)
                    ->whereNull('karyawan_id')
                    ->update(['karyawan_id' => $karyawanId]);

                DB::table('payroll_details')
                    ->where('employee_id', $employee->id)
                    ->update(['karyawan_id' => $karyawanId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('salary_components', function (Blueprint $table) {
            $table->dropForeign(['karyawan_id']);
        });

        if (Schema::hasColumn('payroll_details', 'karyawan_id')) {
            Schema::table('payroll_details', function (Blueprint $table) {
                $table->dropForeign(['karyawan_id']);
                $table->dropColumn('karyawan_id');
            });
        }
    }
};
