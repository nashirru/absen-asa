@extends('layouts.admin')
@section('title', 'Akun Rekening')
@section('header', 'Akun Rekening')
@section('content')
<div class="animate-fade-in-up">
    @if(session('success'))
        <div class="mb-4 p-4 bg-admin-success-tint text-admin-success rounded-admin-md text-sm font-medium">{{ session('success') }}</div>
    @endif

    <div class="bg-admin-surface border border-admin-border rounded-admin-lg">
        <div class="p-5 border-b border-admin-border flex flex-wrap items-center justify-between gap-3">
            <form method="GET" class="flex items-center gap-3">
                <select name="type" onchange="this.form.submit()" class="px-3 py-2 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <option value="">Semua Tipe</option>
                    <option value="cash" {{ request('type') == 'cash' ? 'selected' : '' }}>Kas</option>
                    <option value="bank" {{ request('type') == 'bank' ? 'selected' : '' }}>Bank</option>
                </select>
            </form>
            <a href="{{ route('finance.accounts.create') }}" class="btn btn-primary bg-admin-indigo text-white px-4 py-2 rounded-admin-md text-sm font-medium hover:bg-admin-indigo-deep transition-colors">
                + Akun Baru
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-admin-canvas text-admin-slate text-xs font-semibold uppercase tracking-wider">
                        <th class="text-left px-5 py-3">Nama Akun</th>
                        <th class="text-left px-5 py-3">Tipe</th>
                        <th class="text-right px-5 py-3">Saldo</th>
                        <th class="text-center px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-admin-border">
                    @forelse($accounts as $account)
                    <tr class="hover:bg-admin-canvas/50 transition-colors">
                        <td class="px-5 py-3.5 text-admin-ink font-medium">{{ $account->name }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-admin-full text-xs font-medium {{ $account->type == 'cash' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $account->type == 'cash' ? 'Kas' : 'Bank' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-admin-ink text-right font-mono font-semibold">Rp {{ number_format($account->balance, 0, ',', '.') }}</td>
                        <td class="px-5 py-3.5 text-center">
                            <a href="{{ route('finance.accounts.edit', $account) }}" class="text-admin-indigo hover:text-admin-indigo-deep text-xs font-medium mr-2">Edit</a>
                            <form method="POST" action="{{ route('finance.accounts.destroy', $account) }}" class="inline" onsubmit="return confirm('Hapus akun ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-admin-danger hover:text-red-700 text-xs font-medium">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-admin-mist">Belum ada akun.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-5 border-t border-admin-border">{{ $accounts->links() }}</div>
    </div>
</div>
@endsection
