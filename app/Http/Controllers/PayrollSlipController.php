<?php

namespace App\Http\Controllers;

use App\Models\PayrollDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PayrollSlipController extends Controller
{
    public function show(PayrollDetail $payrollDetail, Request $request)
    {
        $payrollDetail->load(['karyawan.user', 'karyawan.salaryComponents', 'payrollPeriod']);

        $allowances = $payrollDetail->karyawan?->salaryComponents->where('type', 'allowance') ?? collect();
        $deductions = $payrollDetail->karyawan?->salaryComponents->where('type', 'deduction') ?? collect();

        $employeeName = $payrollDetail->karyawan?->user?->name ?? 'PAY-' . $payrollDetail->karyawan?->nik;

        if ($request->query('format') === 'pdf') {
            $pdf = Pdf::loadView('payroll.slip-pdf', compact('payrollDetail', 'allowances', 'deductions', 'employeeName'));
            $filename = 'Slip_Gaji_' . $employeeName . '_' . $payrollDetail->payrollPeriod->month . '_' . $payrollDetail->payrollPeriod->year . '.pdf';
            return $pdf->download($filename);
        }

        return view('payroll.slip', compact('payrollDetail', 'allowances', 'deductions', 'employeeName'));
    }
}