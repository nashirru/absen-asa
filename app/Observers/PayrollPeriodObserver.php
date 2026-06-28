<?php

namespace App\Observers;

use App\Models\Karyawan;
use App\Models\PayrollDetail;
use App\Models\PayrollPeriod;

class PayrollPeriodObserver
{
    public function created(PayrollPeriod $payrollPeriod): void
    {
        // Only auto-generate if it's draft and details don't exist yet
        if ($payrollPeriod->status !== 'draft') {
            return;
        }

        $activeKaryawans = Karyawan::where('status', 'active')->get();

        foreach ($activeKaryawans as $karyawan) {
            // Calculate allowances and deductions
            $allowances = $karyawan->salaryComponents()
                ->where('type', 'allowance')
                ->sum('amount');

            $deductions = $karyawan->salaryComponents()
                ->where('type', 'deduction')
                ->sum('amount');

            $baseSalary = $karyawan->base_salary;
            $netSalary = $baseSalary + $allowances - $deductions;

            PayrollDetail::create([
                'payroll_period_id' => $payrollPeriod->id,
                'karyawan_id' => $karyawan->id,
                'base_salary' => $baseSalary,
                'total_allowance' => $allowances,
                'total_deduction' => $deductions,
                'bonus' => 0,
                'net_salary' => $netSalary,
            ]);
        }
    }

    public function deleted(PayrollPeriod $payrollPeriod): void
    {
        \App\Models\Transaction::where('ref_payroll_id', $payrollPeriod->id)->get()->each->delete();
    }
}
