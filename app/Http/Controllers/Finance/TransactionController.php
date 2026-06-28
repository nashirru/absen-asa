<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['account', 'category']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_until')) {
            $query->where('date', '<=', $request->date_until);
        }

        $transactions = $query->latest('date')->paginate(15);
        $accounts = Account::pluck('name', 'id');
        $categories = Category::pluck('name', 'id');

        return view('finance.transactions.index', compact('transactions', 'accounts', 'categories'));
    }

    public function create()
    {
        $accounts = Account::pluck('name', 'id');
        $categories = Category::pluck('name', 'id');
        return view('finance.transactions.create', compact('accounts', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('transactions', 'public');
        }

        // ref_payroll_id not set here — only system sets it
        Transaction::create($validated);

        return redirect()->route('finance.transactions.index')
            ->with('success', 'Transaksi berhasil dibuat.');
    }

    public function edit(Transaction $transaction)
    {
        // Prevent editing payroll-linked transactions
        if ($transaction->ref_payroll_id) {
            return redirect()->route('finance.transactions.index')
                ->with('error', 'Transaksi penggajian tidak dapat diedit.');
        }

        $accounts = Account::pluck('name', 'id');
        $categories = Category::pluck('name', 'id');
        return view('finance.transactions.edit', compact('transaction', 'accounts', 'categories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->ref_payroll_id) {
            return redirect()->route('finance.transactions.index')
                ->with('error', 'Transaksi penggajian tidak dapat diedit.');
        }

        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('attachment')) {
            // Delete old attachment
            if ($transaction->attachment) {
                Storage::disk('public')->delete($transaction->attachment);
            }
            $validated['attachment'] = $request->file('attachment')->store('transactions', 'public');
        }

        $transaction->update($validated);

        return redirect()->route('finance.transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->ref_payroll_id) {
            return redirect()->route('finance.transactions.index')
                ->with('error', 'Transaksi penggajian tidak dapat dihapus.');
        }

        if ($transaction->attachment) {
            Storage::disk('public')->delete($transaction->attachment);
        }

        $transaction->delete();

        return redirect()->route('finance.transactions.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }

    public function getCategoriesByType(Request $request)
    {
        $categories = Category::where('type', $request->type)->pluck('name', 'id');
        return response()->json($categories);
    }
}
