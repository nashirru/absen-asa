<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\FundTransfer;
use Illuminate\Support\Facades\DB;

class FundTransferObserver
{
    public function created(FundTransfer $fundTransfer): void
    {
        $this->adjustBalances($fundTransfer->from_account_id, $fundTransfer->to_account_id, $fundTransfer->amount);
    }

    public function updated(FundTransfer $fundTransfer): void
    {
        $original = $fundTransfer->getOriginal();

        $oldFrom = $original['from_account_id'] ?? null;
        $oldTo = $original['to_account_id'] ?? null;
        $oldAmount = $original['amount'] ?? 0;

        // Revert old transaction balances
        if ($oldFrom && $oldTo) {
            $this->adjustBalances($oldFrom, $oldTo, -$oldAmount);
        }

        // Apply new transaction balances
        $this->adjustBalances($fundTransfer->from_account_id, $fundTransfer->to_account_id, $fundTransfer->amount);
    }

    public function deleted(FundTransfer $fundTransfer): void
    {
        // Revert transfer (add back to from_account, deduct from to_account)
        $this->adjustBalances($fundTransfer->from_account_id, $fundTransfer->to_account_id, -$fundTransfer->amount);
    }

    /**
     * Adjust account balances.
     * Subtracts amount from source account, adds to destination account.
     */
    private function adjustBalances(int $fromAccountId, int $toAccountId, float $amount): void
    {
        DB::transaction(function () use ($fromAccountId, $toAccountId, $amount) {
            $fromAccount = Account::lockForUpdate()->find($fromAccountId);
            if ($fromAccount) {
                $fromAccount->balance -= $amount;
                $fromAccount->saveQuietly();
            }

            $toAccount = Account::lockForUpdate()->find($toAccountId);
            if ($toAccount) {
                $toAccount->balance += $amount;
                $toAccount->saveQuietly();
            }
        });
    }
}
