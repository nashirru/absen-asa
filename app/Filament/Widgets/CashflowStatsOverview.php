<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class CashflowStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $now = now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $income = Transaction::where("type", "income")
            ->whereYear("date", $currentYear)
            ->whereMonth("date", $currentMonth)
            ->sum("amount");

        $expense = Transaction::where("type", "expense")
            ->whereYear("date", $currentYear)
            ->whereMonth("date", $currentMonth)
            ->sum("amount");

        $totalBalance = Account::sum("balance");

        return [
            Stat::make("Pemasukan Bulan Ini", "Rp " . number_format($income, 0, ",", "."))
                ->description("Total pemasukan " . $now->format("F Y"))
                ->color("success")
                ->icon("heroicon-o-arrow-trending-up"),
            Stat::make("Pengeluaran Bulan Ini", "Rp " . number_format($expense, 0, ",", "."))
                ->description("Total pengeluaran " . $now->format("F Y"))
                ->color("danger")
                ->icon("heroicon-o-arrow-trending-down"),
            Stat::make("Total Saldo", "Rp " . number_format($totalBalance, 0, ",", "."))
                ->description("Saldo gabungan semua akun")
                ->color("info")
                ->icon("heroicon-o-wallet"),
        ];
    }
}
