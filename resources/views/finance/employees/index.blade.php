@extends('layouts.admin')
@section('title', 'Karyawan')
@section('header', 'Karyawan (Payroll)')
@section('content')
<div class="animate-fade-in-up">
    @if(session('success'))
        <div class="mb-4 p-4 bg-admin-success-tint text-admin-success rounded-admin-md text-sm font-medium">{{ session('success') }}</div>
    @endif
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg">
        <div class="p-5 border-b border-admin-border flex flex-wrap items-center justify-between gap-3">
            <form method="GET" class="flex items-center gap-3">
                <select name="status" onchange="this.form.submit()" class="px-3 py-2 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </form>
            <a href="{{ route('finance.employees.create') }}" class="bg-admin-indigo text-white px-4 py-2 rounded-admin-md text-sm font-medium hover:bg-admin-indigo-deep transition-colors">+ Karyawan Baru</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-admin-canvas text-admin-slate text-xs font-semibold uppercase tracking-wider">
                        <th class="text-left px-5 py-3">Nama</th>
                        <th class="text-left px-5 py-3">Jabatan</th>
                        <th class="text-left px-5 py-3">Departemen</th>
                        <th class="text-right px-5 py-3">Gaji Pokok</th>
                        <th class="text-left px-5 py-3">Tgl Masuk</th>
                        <th class="text-left px-5 py-3">Status</th>
                        <th class="text-center px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-admin-border">
                    @forelse($employees as $employee)
                    <tr class="hover:bg-admin-canvas/50 transition-colors">
                        <td class="px-5 py-3.5 text-admin-ink font-medium">{{ $employee->name }}</td>
                        <td class="px-5 py-3.5 text-admin-slate">{{ $employee->position }}</td>
                        <td class="px-5 py-3.5 text-admin-slate">{{ $employee->department ?? '-' }}</td>
                        <td class="px-5 py-3.5 text-admin-ink text-right font-mono">Rp {{ number_format($employee->base_salary, 0, ',', '.') }}</td>
                        <td class="px-5 py-3.5 text-admin-slate">{{ $employee->join_date->format('d M Y') }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-admin-full text-xs font-medium {{ $employee->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $employee->status == 'active' ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <a href="{{ route('finance.employees.edit', $employee) }}" class="text-admin-indigo hover:text-admin-indigo-deep text-xs font-medium mr-2">Edit</a>
                            @if(auth()->user()->isSuperAdmin())
                            <form method="POST" action="{{ route('finance.employees.destroy', $employee) }}" class="inline" onsubmit="confirmDelete(event, 'Karyawan ini akan dihapus permanen.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-admin-danger hover:text-red-700 text-xs font-medium">Hapus</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-8 text-center text-admin-mist">Belum ada karyawan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-5 border-t border-admin-border">{{ $employees->links() }}</div>
    </div>
</div>
@endsection
