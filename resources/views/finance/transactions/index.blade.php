@extends('layouts.admin')
@section('title', 'Transaksi')
@section('header', 'Transaksi')
@section('content')
<div class="animate-fade-in-up">
    @if(session('success'))
        <div class="mb-4 p-4 bg-admin-success-tint text-admin-success rounded-admin-md text-sm font-medium">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-admin-danger-tint text-admin-danger rounded-admin-md text-sm font-medium">{{ session('error') }}</div>
    @endif

    <div class="bg-admin-surface border border-admin-border rounded-admin-lg">
        <div class="p-5 border-b border-admin-border flex flex-wrap items-center justify-between gap-3">
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <select name="type" onchange="this.form.submit()" class="px-3 py-2 bg-admin-canvas rounded-admin-md border border-admin-border text-sm focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <option value="">Semua Tipe</option>
                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Pemasukan</option>
                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                </select>
                <select name="account_id" onchange="this.form.submit()" class="px-3 py-2 bg-admin-canvas rounded-admin-md border border-admin-border text-sm focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <option value="">Semua Akun</option>
                    @foreach($accounts as $id => $name)
                        <option value="{{ $id }}" {{ request('account_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="Dari"
                       class="px-3 py-2 bg-admin-canvas rounded-admin-md border border-admin-border text-sm focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                <input type="date" name="date_until" value="{{ request('date_until') }}" placeholder="Sampai"
                       class="px-3 py-2 bg-admin-canvas rounded-admin-md border border-admin-border text-sm focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                <button type="submit" class="px-3 py-2 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-slate hover:text-admin-ink">Filter</button>
                <a href="{{ route('finance.transactions.index') }}" class="px-3 py-2 text-xs text-admin-slate hover:text-admin-ink">Reset</a>
            </form>
            <a href="{{ route('finance.transactions.create') }}" class="bg-admin-indigo text-white px-4 py-2 rounded-admin-md text-sm font-medium hover:bg-admin-indigo-deep transition-colors">+ Transaksi Baru</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-admin-canvas text-admin-slate text-xs font-semibold uppercase tracking-wider">
                        <th class="text-left px-5 py-3">Tanggal</th>
                        <th class="text-left px-5 py-3">Tipe</th>
                        <th class="text-left px-5 py-3">Akun</th>
                        <th class="text-left px-5 py-3">Kategori</th>
                        <th class="text-right px-5 py-3">Jumlah</th>
                        <th class="text-left px-5 py-3">Keterangan</th>
                        <th class="text-center px-5 py-3">Bukti Transaksi</th>
                        <th class="text-center px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-admin-border">
                    @forelse($transactions as $t)
                    <tr class="hover:bg-admin-canvas/50 transition-colors">
                        <td class="px-5 py-3.5 text-admin-ink">{{ $t->date->format('d/m/Y') }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-admin-full text-xs font-medium {{ $t->type == 'income' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $t->type == 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-admin-slate">{{ $t->account->name }}</td>
                        <td class="px-5 py-3.5 text-admin-slate">
                            <div>{{ $t->category->name ?? 'Tanpa Kategori' }}</div>
                            @if($t->jenis_pengeluaran && count($t->jenis_pengeluaran) > 0)
                                <div class="mt-1 flex flex-wrap gap-1">
                                    @foreach($t->jenis_pengeluaran as $sub)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-admin-indigo-tint text-[10px] font-semibold text-admin-indigo">
                                            {{ $sub }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-admin-ink text-right font-mono font-semibold">Rp {{ number_format($t->amount, 0, ',', '.') }}</td>
                        <td class="px-5 py-3.5 text-admin-mist max-w-[200px] truncate">{{ $t->description ?? '-' }}</td>
                        <td class="px-5 py-3.5 text-center">
                            @if($t->attachment)
                                <a href="{{ Storage::url($t->attachment) }}" target="_blank"
                                   class="inline-flex items-center gap-1 px-2 py-1 rounded-admin-md text-xs font-medium bg-admin-indigo-tint text-admin-indigo hover:bg-admin-indigo/20 transition-colors">
                                    <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>
                                    Lihat
                                </a>
                            @else
                                <span class="text-xs text-admin-mist">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if(!$t->ref_payroll_id)
                                <a href="{{ route('finance.transactions.edit', $t) }}" class="text-admin-indigo hover:text-admin-indigo-deep text-xs font-medium mr-2">Edit</a>
                                @if(auth()->user()->isSuperAdmin())
                                <form method="POST" action="{{ route('finance.transactions.destroy', $t) }}" class="inline" onsubmit="confirmDelete(event, 'Transaksi ini akan dihapus permanen.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-admin-danger hover:text-red-700 text-xs font-medium">Hapus</button>
                                </form>
                                @endif
                            @else
                                <span class="text-admin-mist text-xs italic">Payroll</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-5 py-8 text-center text-admin-mist">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-5 border-t border-admin-border">{{ $transactions->links() }}</div>
    </div>
</div>
@endsection
