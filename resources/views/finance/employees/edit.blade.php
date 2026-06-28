@extends('layouts.admin')
@section('title', 'Edit Karyawan')
@section('header', 'Edit Karyawan')
@section('content')
<div class="max-w-xl mx-auto animate-fade-in-up">
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-6">
        <form method="POST" action="{{ route('finance.employees.update', $employee) }}" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="text-xs font-semibold text-admin-slate">Nama Karyawan</label>
                <input type="text" name="name" value="{{ old('name', $employee->name) }}" required maxlength="255"
                       class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-admin-slate">Jabatan</label>
                    <input type="text" name="position" value="{{ old('position', $employee->position) }}" required maxlength="255"
                           class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                </div>
                <div>
                    <label class="text-xs font-semibold text-admin-slate">Departemen</label>
                    <input type="text" name="department" value="{{ old('department', $employee->department) }}" maxlength="255"
                           class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-admin-slate">Gaji Pokok</label>
                    <input type="number" step="0.01" name="base_salary" value="{{ old('base_salary', $employee->base_salary) }}" required min="0"
                           class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                </div>
                <div>
                    <label class="text-xs font-semibold text-admin-slate">Tanggal Bergabung</label>
                    <input type="date" name="join_date" value="{{ old('join_date', $employee->join_date->format('Y-m-d')) }}" required
                           class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                </div>
            </div>
            <div>
                <label class="text-xs font-semibold text-admin-slate">Status</label>
                <select name="status" required
                        class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-admin-indigo text-white px-6 py-2.5 rounded-admin-md text-sm font-medium hover:bg-admin-indigo-deep transition-colors">Simpan</button>
                <a href="{{ route('finance.employees.index') }}" class="text-admin-slate hover:text-admin-ink text-sm font-medium transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
