<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;

class DashboardPdfController extends Controller
{
    public function export(Request $request)
    {
        $now = now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // 1. Financial stats summary of current month
        $income = Transaction::where('type', 'income')
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonth)
            ->sum('amount');

        $expense = Transaction::where('type', 'expense')
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonth)
            ->sum('amount');

        $totalBalance = Account::sum('balance');
        $netMonthly = $income - $expense;

        // 2. Account list and balances
        $accounts = Account::orderBy('name')->get();

        // 3. Cashflow last 12 months (tabular representation of chart)
        $monthsData = [];
        Carbon::setLocale('id');
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->translatedFormat('F Y');

            $in = Transaction::where('type', 'income')
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount');

            $out = Transaction::where('type', 'expense')
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount');

            $monthsData[] = [
                'month' => $monthName,
                'income' => $in,
                'expense' => $out,
                'net' => $in - $out
            ];
        }

        // 4. Latest 5 transactions
        $latestTransactions = Transaction::with(['category', 'account'])
            ->latest('date')
            ->latest('id')
            ->limit(5)
            ->get();

        // Compile all data
        $data = [
            'printed_at' => $now->format('d/m/Y H:i'),
            'month_label' => $now->translatedFormat('F Y'),
            'summary' => [
                'income' => $income,
                'expense' => $expense,
                'net' => $netMonthly,
                'total_balance' => $totalBalance,
            ],
            'accounts' => $accounts,
            'cashflow_trend' => $monthsData,
            'latest_transactions' => $latestTransactions,
        ];

        $pdf = Pdf::loadView('dashboard-pdf', $data);
        $filename = 'Laporan_Dashboard_' . date('Ymd_His') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }
}
