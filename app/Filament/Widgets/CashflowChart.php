<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class CashflowChart extends ChartWidget
{
    protected static ?string $heading = 'Arus Kas (12 Bulan Terakhir)';
    
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $months = [];
        $incomeData = [];
        $expenseData = [];

        // Set locale to Indonesian for month names
        Carbon::setLocale('id');

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->translatedFormat('F Y');
            $months[] = $monthName;

            $income = Transaction::where('type', 'income')
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount');

            $expense = Transaction::where('type', 'expense')
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount');

            $incomeData[] = (float) $income;
            $expenseData[] = (float) $expense;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Kas Masuk (Pemasukan)',
                    'data' => $incomeData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.05)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Kas Keluar (Pengeluaran)',
                    'data' => $expenseData,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.05)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
