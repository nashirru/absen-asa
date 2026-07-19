<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        // Helper: try creating index, catch duplicate errors (MySQL hasIndex bug)
        $createIndex = function ($table, $columns, $indexName) use ($driver) {
            try {
                if ($driver === 'mysql') {
                    // MySQL: just try and catch duplicate
                    Schema::table($table, function (Blueprint $t) use ($columns, $indexName) {
                        $t->index($columns, $indexName);
                    });
                } else {
                    // SQLite etc: hasIndex works fine
                    if (!Schema::hasIndex($table, $indexName)) {
                        Schema::table($table, function (Blueprint $t) use ($columns, $indexName) {
                            $t->index($columns, $indexName);
                        });
                    }
                }
            } catch (\Exception $e) {
                // 1061 = Duplicate key name, 42000 = syntax error from duplicate
                // Ignore — index already exists
            }
        };

        // Absensi indexes
        $createIndex('absensi', ['user_id', 'tanggal'], 'idx_absensi_user_tanggal');
        $createIndex('absensi', ['tanggal', 'status'], 'idx_absensi_tanggal_status');
        $createIndex('absensi', ['shift_id'], 'idx_absensi_shift_id');
        $createIndex('absensi', ['is_anomaly'], 'idx_absensi_is_anomaly');

        // Transactions indexes
        $createIndex('transactions', ['ref_payroll_id'], 'idx_transactions_ref_payroll_id');
        $createIndex('transactions', ['date', 'type'], 'idx_transactions_date_type');
        $createIndex('transactions', ['account_id', 'date'], 'idx_transactions_account_date');

        // Fund transfers indexes
        $createIndex('fund_transfers', ['from_account_id', 'date'], 'idx_transfers_from_date');
        $createIndex('fund_transfers', ['to_account_id', 'date'], 'idx_transfers_to_date');

        // Payroll periods index
        $createIndex('payroll_periods', ['year', 'month', 'status'], 'idx_payroll_periods_year_month_status');

        // Users device_uuid index
        $createIndex('users', ['device_uuid'], 'idx_users_device_uuid');

        // CHECK constraint for absensi status (MySQL only)
        if ($driver === 'mysql') {
            try {
                DB::statement("ALTER TABLE absensi ADD CONSTRAINT chk_absensi_status CHECK (status IN ('hadir', 'terlambat', 'izin', 'sakit', 'alpha'))");
            } catch (\Exception $e) {
                // Constraint may already exist — ignore duplicate
            }
        }
    }

    public function down(): void
    {
        $indexes = [
            'absensi' => ['idx_absensi_user_tanggal', 'idx_absensi_tanggal_status', 'idx_absensi_shift_id', 'idx_absensi_is_anomaly'],
            'transactions' => ['idx_transactions_ref_payroll_id', 'idx_transactions_date_type', 'idx_transactions_account_date'],
            'fund_transfers' => ['idx_transfers_from_date', 'idx_transfers_to_date'],
            'payroll_periods' => ['idx_payroll_periods_year_month_status'],
            'users' => ['idx_users_device_uuid'],
        ];

        foreach ($indexes as $table => $names) {
            Schema::table($table, function (Blueprint $table) use ($names) {
                foreach ($names as $index) {
                    if (Schema::hasIndex($table->getTable(), $index)) {
                        $table->dropIndex($index);
                    }
                }
            });
        }

        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            try {
                DB::statement("ALTER TABLE absensi DROP CHECK chk_absensi_status");
            } catch (\Exception $e) {
                // Constraint may not exist
            }
        }
    }
};
