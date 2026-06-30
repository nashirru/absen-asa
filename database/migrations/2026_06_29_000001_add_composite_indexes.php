<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Absensi indexes
        Schema::table('absensi', function (Blueprint $table) {
            if (!Schema::hasIndex('absensi', 'idx_absensi_user_tanggal')) {
                $table->index(['user_id', 'tanggal'], 'idx_absensi_user_tanggal');
            }
            if (!Schema::hasIndex('absensi', 'idx_absensi_tanggal_status')) {
                $table->index(['tanggal', 'status'], 'idx_absensi_tanggal_status');
            }
            if (!Schema::hasIndex('absensi', 'idx_absensi_shift_id')) {
                $table->index(['shift_id'], 'idx_absensi_shift_id');
            }
            if (!Schema::hasIndex('absensi', 'idx_absensi_is_anomaly')) {
                $table->index(['is_anomaly'], 'idx_absensi_is_anomaly');
            }
        });

        // Transactions indexes
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasIndex('transactions', 'idx_transactions_ref_payroll_id')) {
                $table->index(['ref_payroll_id'], 'idx_transactions_ref_payroll_id');
            }
            if (!Schema::hasIndex('transactions', 'idx_transactions_date_type')) {
                $table->index(['date', 'type'], 'idx_transactions_date_type');
            }
            if (!Schema::hasIndex('transactions', 'idx_transactions_account_date')) {
                $table->index(['account_id', 'date'], 'idx_transactions_account_date');
            }
        });

        // Fund transfers indexes (column name is 'date', not 'transfer_date')
        Schema::table('fund_transfers', function (Blueprint $table) {
            if (!Schema::hasIndex('fund_transfers', 'idx_transfers_from_date')) {
                $table->index(['from_account_id', 'date'], 'idx_transfers_from_date');
            }
            if (!Schema::hasIndex('fund_transfers', 'idx_transfers_to_date')) {
                $table->index(['to_account_id', 'date'], 'idx_transfers_to_date');
            }
        });

        // Payroll periods index
        Schema::table('payroll_periods', function (Blueprint $table) {
            if (!Schema::hasIndex('payroll_periods', 'idx_payroll_periods_year_month_status')) {
                $table->index(['year', 'month', 'status'], 'idx_payroll_periods_year_month_status');
            }
        });

        // Users device_uuid index
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasIndex('users', 'idx_users_device_uuid')) {
                $table->index(['device_uuid'], 'idx_users_device_uuid');
            }
        });

        // CHECK constraint for absensi status (MySQL only)
        $driver = DB::connection()->getDriverName();
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
