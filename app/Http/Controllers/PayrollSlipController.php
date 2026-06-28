<?php

namespace App\Http\Controllers;

use App\Models\PayrollDetail;
use App\Models\Employee;
use App\Models\PayrollPeriod;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PayrollSlipController extends Controller
{
    public function show(PayrollDetail $payrollDetail, Request $request)
    {
        $payrollDetail->load(['employee.salaryComponents', 'payrollPeriod']);

        $allowances = $payrollDetail->employee->salaryComponents->where('type', 'allowance');
        $deductions = $payrollDetail->employee->salaryComponents->where('type', 'deduction');

        if ($request->query('format') === 'pdf') {
            $pdf = Pdf::loadView('payroll.slip-pdf', compact('payrollDetail', 'allowances', 'deductions'));
            $filename = 'Slip_Gaji_' . $payrollDetail->employee->name . '_' . $payrollDetail->payrollPeriod->month . '_' . $payrollDetail->payrollPeriod->year . '.pdf';
            return $pdf->download($filename);
        }

        return view('payroll.slip', compact('payrollDetail', 'allowances', 'deductions'));
    }
}