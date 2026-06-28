<?php

namespace App\Filament\Pages;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\PayrollPeriod;
use App\Models\PayrollDetail;
use App\Models\Account;
use App\Exports\CashflowExport;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class Reports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationGroup = 'Laporan & Dashboard';

    protected static ?string $navigationLabel = 'Laporan';

    protected static ?string $title = 'Laporan Keuangan';

    protected static string $view = 'filament.pages.reports';

    public string $activeTab = 'cashflow'; // cashflow, category, payroll

    // Cashflow Report Filters
    public ?string $cashflowStartDate = null;
    public ?string $cashflowEndDate = null;
    public ?string $cashflowAccountId = null;
    public ?string $cashflowCategoryId = null;

    // Category Report Filters
    public ?string $categoryStartDate = null;
    public ?string $categoryEndDate = null;

    // Payroll Report Filters
    public ?string $payrollPeriodId = null;

    public function mount(): void
    {
        $this->cashflowStartDate = now()->startOfMonth()->format('Y-m-d');
        $this->cashflowEndDate = now()->endOfMonth()->format('Y-m-d');

        $this->categoryStartDate = now()->startOfMonth()->format('Y-m-d');
        $this->categoryEndDate = now()->endOfMonth()->format('Y-m-d');

        $latestPeriod = PayrollPeriod::orderBy('year', 'desc')->orderBy('month', 'desc')->first();
        if ($latestPeriod) {
            $this->payrollPeriodId = (string) $latestPeriod->id;
        }
    }

    public function getAccountsProperty()
    {
        return Account::orderBy('name', 'asc')->get();
    }

    public function getCategoriesProperty()
    {
        return Category::orderBy('name', 'asc')->get();
    }

    public function getPayrollPeriodsProperty()
    {
        return PayrollPeriod::orderBy('year', 'desc')->orderBy('month', 'desc')->get();
    }

    public function getCashflowTransactions()
    {
        $query = Transaction::query()->with(['category', 'account']);

        if ($this->cashflowStartDate) {
            $query->where('date', '>=', $this->cashflowStartDate);
        }

        if ($this->cashflowEndDate) {
            $query->where('date', '<=', $this->cashflowEndDate);
        }

        if ($this->cashflowAccountId) {
            $query->where('account_id', $this->cashflowAccountId);
        }

        if ($this->cashflowCategoryId) {
            $query->where('category_id', $this->cashflowCategoryId);
        }

        return $query->orderBy('date', 'desc')->get();
    }

    public function getCashflowSummary(): array
    {
        $transactions = $this->getCashflowTransactions();
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        
        $startStr = $this->cashflowStartDate ? date('d/m/Y', strtotime($this->cashflowStartDate)) : '-';
        $endStr = $this->cashflowEndDate ? date('d/m/Y', strtotime($this->cashflowEndDate)) : '-';

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'balance' => $totalIncome - $totalExpense,
            'period' => "{$startStr} s/d {$endStr}"
        ];
    }

    public function getCategoryBreakdown(): array
    {
        $query = Transaction::query();

        if ($this->categoryStartDate) {
            $query->where('date', '>=', $this->categoryStartDate);
        }

        if ($this->categoryEndDate) {
            $query->where('date', '<=', $this->categoryEndDate);
        }

        $incomeBreakdown = (clone $query)->where('type', 'income')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->get();

        $expenseBreakdown = (clone $query)->where('type', 'expense')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->get();

        return [
            'income' => $incomeBreakdown,
            'expense' => $expenseBreakdown,
        ];
    }

    public function getPayrollDetails()
    {
        if (!$this->payrollPeriodId) {
            return collect();
        }

        return PayrollDetail::where('payroll_period_id', $this->payrollPeriodId)
            ->with('employee')
            ->get();
    }

    public function exportExcel()
    {
        $transactions = $this->getCashflowTransactions();
        $summary = $this->getCashflowSummary();
        
        $export = new CashflowExport($transactions, $summary);
        $fileName = 'Laporan_Arus_Kas_' . date('Ymd_His') . '.xlsx';
        
        return Excel::download($export, $fileName);
    }

    public function exportPayrollPdf()
    {
        $details = $this->getPayrollDetails();
        $period = PayrollPeriod::find($this->payrollPeriodId);
        
        if (!$period || $details->isEmpty()) {
            return;
        }

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $periodName = ($months[$period->month] ?? '') . ' ' . $period->year;

        $pdf = Pdf::loadView('reports.payroll-pdf', compact('details', 'periodName'));
        
        $fileName = 'Rekap_Penggajian_' . str_replace(' ', '_', $periodName) . '.pdf';
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName);
    }
}
