@extends('layouts.admin')
@section('title', 'Transfer Dana')
@section('header', 'Transfer Dana')
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
                <select name="from_account_id" onchange="this.form.submit()" class="px-3 py-2 bg-admin-canvas rounded-admin-md border border-admin-border text-sm focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <option value="">Semua Dari Akun</option>
                    @foreach($accounts as $id => $name)
                        <option value="{{ $id }}" {{ request('from_account_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-2 bg-admin-canvas rounded-admin-md border border-admin-border text-sm">
                <input type="date" name="date_until" value="{{ request('date_until') }}" class="px-3 py-2 bg-admin-canvas rounded-admin-md border border-admin-border text-sm">
                <button type="submit" class="px-3 py-2 bg-admin-canvas rounded-admin-md border border-admin-border text-sm">Filter</button>
                <a href="{{ route('finance.fund-transfers.index') }}" class="px-3 py-2 text-xs text-admin-slate">Reset</a>
            </form>
            <a href="{{ route('finance.fund-transfers.create') }}" class="bg-admin-indigo text-white px-4 py-2 rounded-admin-md text-sm font-medium hover:bg-admin-indigo-deep transition-colors">+ Transfer Baru</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-admin-canvas text-admin-slate text-xs font-semibold uppercase tracking-wider">
                        <th class="text-left px-5 py-3">Tanggal</th>
                        <th class="text-left px-5 py-3">Dari Akun</th>
                        <th class="text-left px-5 py-3">Ke Akun</th>
                        <th class="text-right px-5 py-3">Jumlah</th>
                        <th class="text-left px-5 py-3">Catatan</th>
                        <th class="text-center px-5 py-3">Lampiran</th>
                        <th class="text-center px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-admin-border">
                    @forelse($transfers as $tf)
                    <tr class="hover:bg-admin-canvas/50 transition-colors">
                        <td class="px-5 py-3.5 text-admin-ink">{{ $tf->date->format('d/m/Y') }}</td>
                        <td class="px-5 py-3.5 text-admin-slate">{{ $tf->fromAccount->name }}</td>
                        <td class="px-5 py-3.5 text-admin-slate">{{ $tf->toAccount->name }}</td>
                        <td class="px-5 py-3.5 text-admin-ink text-right font-mono font-semibold">Rp {{ number_format($tf->amount, 0, ',', '.') }}</td>
                        <td class="px-5 py-3.5 text-admin-mist max-w-[200px] truncate">{{ $tf->note ?? '-' }}</td>
                        <td class="px-5 py-3.5 text-center">
                            @if($tf->attachment)
                                <a href="{{ Storage::url($tf->attachment) }}" target="_blank"
                                   class="inline-flex items-center gap-1 px-2 py-1 rounded-admin-md text-xs font-medium bg-admin-indigo-tint text-admin-indigo hover:bg-admin-indigo/20 transition-colors">
                                    <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>
                                    Lihat
                                </a>
                            @else
                                <span class="text-xs text-admin-mist">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <a href="{{ route('finance.fund-transfers.edit', $tf) }}" class="text-admin-indigo hover:text-admin-indigo-deep text-xs font-medium mr-2">Edit</a>
                            @if(auth()->user()->isSuperAdmin())
                            <form method="POST" action="{{ route('finance.fund-transfers.destroy', $tf) }}" class="inline" onsubmit="confirmDelete(event, 'Hapus transfer ini? Saldo akun akan dikembalikan.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-admin-danger hover:text-red-700 text-xs font-medium">Hapus</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-8 text-center text-admin-mist">Belum ada transfer dana.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-5 border-t border-admin-border">{{ $transfers->links() }}</div>
    </div>
</div>
@endsection
