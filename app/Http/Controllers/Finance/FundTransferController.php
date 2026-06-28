<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\FundTransfer;
use Illuminate\Http\Request;

class FundTransferController extends Controller
{
    public function index(Request $request)
    {
        $query = FundTransfer::with(['fromAccount', 'toAccount']);

        if ($request->filled('from_account_id')) {
            $query->where('from_account_id', $request->from_account_id);
        }

        if ($request->filled('to_account_id')) {
            $query->where('to_account_id', $request->to_account_id);
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_until')) {
            $query->where('date', '<=', $request->date_until);
        }

        $transfers = $query->latest('date')->paginate(15);
        $accounts = Account::pluck('name', 'id');

        return view('finance.fund-transfers.index', compact('transfers', 'accounts'));
    }

    public function create()
    {
        $accounts = Account::pluck('name', 'id');
        return view('finance.fund-transfers.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_account_id' => 'required|exists:accounts,id|different:to_account_id',
            'to_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        // Check balance
        $fromAccount = Account::findOrFail($validated['from_account_id']);
        if ($fromAccount->balance < $validated['amount']) {
            return back()->withInput()
                ->with('error', 'Saldo akun asal tidak mencukupi. Saldo tersedia: Rp ' . number_format($fromAccount->balance, 0, ',', '.'));
        }

        FundTransfer::create($validated);

        return redirect()->route('finance.fund-transfers.index')
            ->with('success', 'Transfer dana berhasil.');
    }

    public function edit(FundTransfer $fundTransfer)
    {
        $accounts = Account::pluck('name', 'id');
        return view('finance.fund-transfers.edit', compact('fundTransfer', 'accounts'));
    }

    public function update(Request $request, FundTransfer $fundTransfer)
    {
        $validated = $request->validate([
            'from_account_id' => 'required|exists:accounts,id|different:to_account_id',
            'to_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        // Check balance of new from_account (accounting for old amount being reverted)
        $fromAccount = Account::findOrFail($validated['from_account_id']);
        $oldAmount = $fundTransfer->amount;

        // If changing from_account, the old from_account already got its balance back via observer
        // Check if the new from_account has enough balance
        $availableBalance = $fromAccount->balance;
        if ($fromAccount->id === $fundTransfer->from_account_id) {
            // Same from_account: old amount was reverted, so available = current + old amount
            $availableBalance = $fromAccount->balance + $oldAmount;
        }

        if ($availableBalance < $validated['amount']) {
            return back()->withInput()
                ->with('error', 'Saldo akun asal tidak mencukupi. Saldo tersedia: Rp ' . number_format($availableBalance, 0, ',', '.'));
        }

        $fundTransfer->update($validated);

        return redirect()->route('finance.fund-transfers.index')
            ->with('success', 'Transfer dana berhasil diperbarui.');
    }

    public function destroy(FundTransfer $fundTransfer)
    {
        $fundTransfer->delete();

        return redirect()->route('finance.fund-transfers.index')
            ->with('success', 'Transfer dana berhasil dihapus.');
    }
}
