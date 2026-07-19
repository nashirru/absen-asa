<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $query = Account::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $accounts = $query->latest()->paginate(10);

        return view('finance.accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('finance.accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:cash,bank',
            'balance' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        Account::create($validated);

        return redirect()->route('finance.accounts.index')
            ->with('success', 'Akun berhasil dibuat.');
    }

    public function edit(Account $account)
    {
        return view('finance.accounts.edit', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:cash,bank',
            'balance' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $account->update($validated);

        return redirect()->route('finance.accounts.index')
            ->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(Account $account)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat menghapus akun rekening.');
        }

        $account->delete();

        return redirect()->route('finance.accounts.index')
            ->with('success', 'Akun berhasil dihapus.');
    }
}
