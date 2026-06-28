<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionObserver
{
    private function updateBalance(Transaction $transaction, string $action): void
    {
        DB::transaction(function () use ($transaction, $action) {
            $account = Account::lockForUpdate()->find($transaction->account_id);
            if (!$account) {
                return;
            }

            $amount = $transaction->amount;

            if ($transaction->type === "income") {
                $account->balance = match ($action) {
                    "add" => $account->balance + $amount,
                    "subtract" => $account->balance - $amount,
                    default => $account->balance,
                };
            } else {
                $account->balance = match ($action) {
                    "add" => $account->balance - $amount,
                    "subtract" => $account->balance + $amount,
                    default => $account->balance,
                };
            }

            $account->saveQuietly();
        });
    }

    public function created(Transaction $transaction): void
    {
        $this->updateBalance($transaction, "add");
    }

    public function updated(Transaction $transaction): void
    {
        $original = $transaction->getOriginal();

        $origAccountId = $original["account_id"] ?? null;
        $origAmount = $original["amount"] ?? 0;
        $origType = $original["type"] ?? null;

        $changed = $transaction->isDirty(["account_id", "amount", "type"]);

        if (!$changed) {
            return;
        }

        // Revert the original transaction effect
        $origTransaction = new Transaction([
            "account_id" => $origAccountId,
            "amount" => $origAmount,
            "type" => $origType,
        ]);
        $this->updateBalance($origTransaction, "subtract");

        // Apply the new transaction effect
        $this->updateBalance($transaction, "add");
    }

    public function deleted(Transaction $transaction): void
    {
        $this->updateBalance($transaction, "subtract");
    }
}
