@extends('layouts.admin')
@section('title', 'Edit Komponen Gaji')
@section('header', 'Edit Komponen Gaji')
@section('content')
<div class="max-w-xl mx-auto animate-fade-in-up">
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-6">
        <form method="POST" action="{{ route('finance.salary-components.update', $salaryComponent) }}" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="text-xs font-semibold text-admin-slate">Karyawan</label>
                <select name="karyawan_id" required
                        class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    @foreach($karyawans as $id => $name)
                        <option value="{{ $id }}" {{ old('karyawan_id', $salaryComponent->karyawan_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-admin-slate">Nama Komponen</label>
                <input type="text" name="name" value="{{ old('name', $salaryComponent->name) }}" required maxlength="255"
                       class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-admin-slate">Tipe</label>
                    <select name="type" required
                            class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                        <option value="allowance" {{ old('type', $salaryComponent->type) == 'allowance' ? 'selected' : '' }}>Tunjangan</option>
                        <option value="deduction" {{ old('type', $salaryComponent->type) == 'deduction' ? 'selected' : '' }}>Potongan</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-admin-slate">Jumlah</label>
                    <input type="number" step="0.01" name="amount" value="{{ old('amount', $salaryComponent->amount) }}" required min="0"
                           class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                </div>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-admin-indigo text-white px-6 py-2.5 rounded-admin-md text-sm font-medium hover:bg-admin-indigo-deep transition-colors">Simpan</button>
                <a href="{{ route('finance.salary-components.index') }}" class="text-admin-slate hover:text-admin-ink text-sm font-medium transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
