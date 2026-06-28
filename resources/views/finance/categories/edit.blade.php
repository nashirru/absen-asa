@extends('layouts.admin')
@section('title', 'Edit Kategori')
@section('header', 'Edit Kategori')
@section('content')
<div class="max-w-xl mx-auto animate-fade-in-up">
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-6">
        <form method="POST" action="{{ route('finance.categories.update', $category) }}" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="text-xs font-semibold text-admin-slate">Nama Kategori</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required maxlength="255"
                       class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
            </div>
            <div>
                <label class="text-xs font-semibold text-admin-slate">Tipe</label>
                <select name="type" required
                        class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <option value="income" {{ old('type', $category->type) == 'income' ? 'selected' : '' }}>Pemasukan</option>
                    <option value="expense" {{ old('type', $category->type) == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-admin-slate">Warna</label>
                <input type="color" name="color" value="{{ old('color', $category->color) }}" required
                       class="w-12 h-10 p-1 rounded-admin-md border border-admin-border cursor-pointer">
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-admin-indigo text-white px-6 py-2.5 rounded-admin-md text-sm font-medium hover:bg-admin-indigo-deep transition-colors">Simpan</button>
                <a href="{{ route('finance.categories.index') }}" class="text-admin-slate hover:text-admin-ink text-sm font-medium transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
