<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ReportPrintController extends Controller
{
    public function show(Request $request)
    {
        $reportType = $request->query('reportType', 'cashflow');
        $filterType = $request->query('filterType', 'monthly');
        $startDate = $request->query('startDate');
        $endDate = $request->query('endDate');
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        $title = '';
        $data = [];

        if ($reportType === 'cashflow') {
            $query = Transaction::query();
            if ($filterType === 'daily') {
                $query->whereBetween('date', [$startDate, $endDate]);
                $items = $query->select(
                    'date',
                    DB::raw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income"),
                    DB::raw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense")
                )
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();
                $title = "Laporan Arus Kas Harian ({$startDate} s/d {$endDate})";
            } elseif ($filterType === 'monthly') {
                $query->whereYear('date', $year);
                $items = $query->select(
                    DB::raw("MONTH(date) as month"),
                    DB::raw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income"),
                    DB::raw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense")
                )
                ->groupBy(DB::raw("MONTH(date)"))
                ->orderBy('month', 'asc')
                ->get();
                $title = "Laporan Arus Kas Bulanan Tahun {$year}";
            } else {
                $items = $query->select(
                    DB::raw("YEAR(date) as year"),
                    DB::raw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income"),
                    DB::raw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense")
                )
                ->groupBy(DB::raw("YEAR(date)"))
                ->orderBy('year', 'asc')
                ->get();
                $title = "Laporan Arus Kas Tahunan";
            }
            $data = ['items' => $items, 'filterType' => $filterType];
        } elseif ($reportType === 'category') {
            $query = Transaction::query();
            if ($filterType === 'daily') {
                $query->whereBetween('date', [$startDate, $endDate]);
                $title = "Laporan Kategori Periode {$startDate} s/d {$endDate}";
            } elseif ($filterType === 'monthly') {
                $query->whereYear('date', $year)->whereMonth('date', $month);
                $months = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                $monthName = $months[$month] ?? '';
                $title = "Laporan Kategori Periode {$monthName} {$year}";
            } else {
                $query->whereYear('date', $year);
                $title = "Laporan Kategori Tahun {$year}";
            }

            $incomeQuery = clone $query;
            $income = $incomeQuery->where('type', 'income')
                ->select('category_id', DB::raw('SUM(amount) as total'))
                ->groupBy('category_id')
                ->with('category')
                ->get();

            $expenseQuery = clone $query;
            $expense = $expenseQuery->where('type', 'expense')
                ->select('category_id', DB::raw('SUM(amount) as total'))
                ->groupBy('category_id')
                ->with('category')
                ->get();

            $data = ['income' => $income, 'expense' => $expense];
        } else {
            $payrollPeriods = PayrollPeriod::with(['payrollDetails'])
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();

            $items = [];
            foreach ($payrollPeriods as $period) {
                $transaction = Transaction::where('ref_payroll_id', $period->id)
                    ->with('account')
                    ->first();

                $items[] = [
                    'period' => $period,
                    'total_employees' => $period->payrollDetails->count(),
                    'total_net_salary' => $period->payrollDetails->sum('net_salary'),
                    'account_name' => $transaction?->account?->name ?? '-',
                    'paid_at' => $transaction?->created_at ?? null,
                ];
            }
            $title = "Laporan Penggajian Karyawan";
            $data = ['items' => $items];
        }

        return view('reports.print', compact('reportType', 'title', 'data'));
    }
}
