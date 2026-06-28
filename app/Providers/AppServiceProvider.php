<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use App\Models\Transaction;
use App\Models\FundTransfer;
use App\Models\PayrollPeriod;
use App\Observers\TransactionObserver;
use App\Observers\FundTransferObserver;
use App\Observers\PayrollPeriodObserver;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Summarizer;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale('id');
        config(['app.locale' => 'id']);
        Paginator::useTailwind();

        Transaction::observe(TransactionObserver::class);
        FundTransfer::observe(FundTransferObserver::class);
        PayrollPeriod::observe(PayrollPeriodObserver::class);

        if (class_exists(TextColumn::class)) {
            TextColumn::macro('moneyIdr', function () {
                return $this->formatStateUsing(function ($state) {
                    if ($state === null || $state === '') {
                        return null;
                    }
                    if (!is_numeric($state)) {
                        return $state;
                    }
                    return 'Rp ' . number_format((float) $state, 0, ',', '.');
                });
            });
        }

        if (class_exists(Summarizer::class)) {
            Summarizer::macro('moneyIdr', function () {
                return $this->formatStateUsing(function ($state) {
                    if ($state === null || $state === '') {
                        return null;
                    }
                    if (!is_numeric($state)) {
                        return $state;
                    }
                    return 'Rp ' . number_format((float) $state, 0, ',', '.');
                });
            });
        }
    }
}
