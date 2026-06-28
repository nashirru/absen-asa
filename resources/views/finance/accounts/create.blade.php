@extends('layouts.admin')
@section('title', 'Buat Akun')
@section('header', 'Buat Akun')
@section('content')
<div class="max-w-xl mx-auto animate-fade-in-up">
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-6">
        <form method="POST" action="{{ route('finance.accounts.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="text-xs font-semibold text-admin-slate">Nama Akun</label>
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="255"
                       class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                @error('name') <p class="text-xs text-admin-danger mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs font-semibold text-admin-slate">Tipe</label>
                <select name="type" required
                        class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <option value="cash" {{ old('type') == 'cash' ? 'selected' : '' }}>Kas</option>
                    <option value="bank" {{ old('type') == 'bank' ? 'selected' : '' }}>Bank</option>
                </select>
                @error('type') <p class="text-xs text-admin-danger mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs font-semibold text-admin-slate">Saldo Awal</label>
                <input type="number" step="0.01" name="balance" value="{{ old('balance', '0') }}" required min="0"
                       class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                @error('balance') <p class="text-xs text-admin-danger mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs font-semibold text-admin-slate">Keterangan</label>
                <textarea name="description" rows="3"
                          class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">{{ old('description') }}</textarea>
                @error('description') <p class="text-xs text-admin-danger mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-admin-indigo text-white px-6 py-2.5 rounded-admin-md text-sm font-medium hover:bg-admin-indigo-deep transition-colors">Simpan</button>
                <a href="{{ route('finance.accounts.index') }}" class="text-admin-slate hover:text-admin-ink text-sm font-medium transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
