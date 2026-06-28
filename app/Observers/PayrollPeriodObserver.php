<?php

namespace App\Observers;

use App\Models\Employee;
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

        $activeEmployees = Employee::where('status', 'active')->get();

        foreach ($activeEmployees as $employee) {
            // Calculate allowances and deductions
            $allowances = $employee->salaryComponents()
                ->where('type', 'allowance')
                ->sum('amount');

            $deductions = $employee->salaryComponents()
                ->where('type', 'deduction')
                ->sum('amount');

            $baseSalary = $employee->base_salary;
            $netSalary = $baseSalary + $allowances - $deductions;

            PayrollDetail::create([
                'payroll_period_id' => $payrollPeriod->id,
                'employee_id' => $employee->id,
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
