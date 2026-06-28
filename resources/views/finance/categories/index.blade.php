@extends('layouts.admin')
@section('title', 'Kategori')
@section('header', 'Kategori')
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
                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Pemasukan</option>
                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                </select>
            </form>
            <a href="{{ route('finance.categories.create') }}" class="btn btn-primary bg-admin-indigo text-white px-4 py-2 rounded-admin-md text-sm font-medium hover:bg-admin-indigo-deep transition-colors">
                + Kategori Baru
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-admin-canvas text-admin-slate text-xs font-semibold uppercase tracking-wider">
                        <th class="text-left px-5 py-3">Warna</th>
                        <th class="text-left px-5 py-3">Nama Kategori</th>
                        <th class="text-left px-5 py-3">Tipe</th>
                        <th class="text-center px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-admin-border">
                    @forelse($categories as $category)
                    <tr class="hover:bg-admin-canvas/50 transition-colors">
                        <td class="px-5 py-3.5">
                            <span class="inline-block w-5 h-5 rounded-admin-full border border-admin-border" style="background-color: {{ $category->color }}"></span>
                        </td>
                        <td class="px-5 py-3.5 text-admin-ink font-medium">{{ $category->name }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-admin-full text-xs font-medium {{ $category->type == 'income' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $category->type == 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <a href="{{ route('finance.categories.edit', $category) }}" class="text-admin-indigo hover:text-admin-indigo-deep text-xs font-medium mr-2">Edit</a>
                            <form method="POST" action="{{ route('finance.categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-admin-danger hover:text-red-700 text-xs font-medium">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-admin-mist">Belum ada kategori.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-5 border-t border-admin-border">{{ $categories->links() }}</div>
    </div>
</div>
@endsection
