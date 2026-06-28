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
    }
}
