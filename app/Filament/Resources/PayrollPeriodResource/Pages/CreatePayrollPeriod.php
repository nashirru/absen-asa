<?php

namespace App\Filament\Resources\PayrollPeriodResource\Pages;

use App\Filament\Resources\PayrollPeriodResource;
use App\Models\Employee;
use App\Models\PayrollDetail;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreatePayrollPeriod extends CreateRecord
{
    protected static string $resource = PayrollPeriodResource::class;

    protected function afterCreate(): void
    {
        $period = $this->record;

        DB::transaction(function () use ($period): void {
            $activeEmployees = Employee::where("status", "active")->get();

            foreach ($activeEmployees as $employee) {
                $components = $employee->salaryComponents;

                $totalAllowance = $components
                    ->where("type", "allowance")
                    ->sum("amount");

                $totalDeduction = $components
                    ->where("type", "deduction")
                    ->sum("amount");

                $baseSalary = $employee->base_salary;
                $netSalary = $baseSalary + $totalAllowance - $totalDeduction;

                PayrollDetail::create([
                    "payroll_period_id" => $period->id,
                    "employee_id" => $employee->id,
                    "base_salary" => $baseSalary,
                    "total_allowance" => $totalAllowance,
                    "total_deduction" => $totalDeduction,
                    "bonus" => 0,
                    "net_salary" => max($netSalary, 0),
                ]);
            }
        });
    }
}