@extends('layouts.admin')
@section('title', 'Komponen Gaji')
@section('header', 'Komponen Gaji')
@section('content')
<div class="animate-fade-in-up">
    @if(session('success'))
        <div class="mb-4 p-4 bg-admin-success-tint text-admin-success rounded-admin-md text-sm font-medium">{{ session('success') }}</div>
    @endif

    {{-- Info banner --}}
    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 text-blue-700 rounded-admin-md text-sm flex items-center gap-2">
        <i data-lucide="info" class="w-4 h-4 shrink-0"></i>
        <span>Komponen gaji juga bisa diatur langsung dari halaman
        <a href="{{ route('karyawan.index') }}" class="underline font-semibold">Data Karyawan</a>
        — edit karyawan lalu atur tunjangan & potongan di bagian "Komponen Gaji".</span>
    </div>

    <div class="bg-admin-surface border border-admin-border rounded-admin-lg">
        <div class="p-5 border-b border-admin-border flex flex-wrap items-center justify-between gap-3">
            <form method="GET" class="flex items-center gap-3">
                <select name="type" onchange="this.form.submit()" class="px-3 py-2 bg-admin-canvas rounded-admin-md border border-admin-border text-sm focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <option value="">Semua Tipe</option>
                    <option value="allowance" {{ request('type') == 'allowance' ? 'selected' : '' }}>Tunjangan</option>
                    <option value="deduction" {{ request('type') == 'deduction' ? 'selected' : '' }}>Potongan</option>
                </select>
                <select name="karyawan_id" onchange="this.form.submit()" class="px-3 py-2 bg-admin-canvas rounded-admin-md border border-admin-border text-sm focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <option value="">Semua Karyawan</option>
                    @foreach($karyawans as $id => $name)
                        <option value="{{ $id }}" {{ request('karyawan_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('finance.salary-components.create') }}" class="bg-admin-indigo text-white px-4 py-2 rounded-admin-md text-sm font-medium hover:bg-admin-indigo-deep transition-colors">+ Komponen Baru</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-admin-canvas text-admin-slate text-xs font-semibold uppercase tracking-wider">
                        <th class="text-left px-5 py-3">Karyawan</th>
                        <th class="text-left px-5 py-3">Nama Komponen</th>
                        <th class="text-left px-5 py-3">Tipe</th>
                        <th class="text-right px-5 py-3">Jumlah</th>
                        <th class="text-center px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-admin-border">
                    @forelse($components as $comp)
                    <tr class="hover:bg-admin-canvas/50 transition-colors">
                        <td class="px-5 py-3.5 text-admin-ink font-medium">
                            <a href="{{ route('karyawan.edit', $comp->karyawan_id) }}" class="text-admin-indigo hover:underline">
                                {{ $comp->karyawan?->user?->name ?? 'PAY-' . $comp->karyawan?->nik }}
                            </a>
                        </td>
                        <td class="px-5 py-3.5 text-admin-slate">{{ $comp->name }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-admin-full text-xs font-medium {{ $comp->type == 'allowance' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $comp->type == 'allowance' ? 'Tunjangan' : 'Potongan' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-admin-ink text-right font-mono">Rp {{ number_format($comp->amount, 0, ',', '.') }}</td>
                        <td class="px-5 py-3.5 text-center">
                            <a href="{{ route('karyawan.edit', $comp->karyawan_id) }}" class="text-admin-indigo hover:text-admin-indigo-deep text-xs font-medium">Ke Karyawan</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-admin-mist">Belum ada komponen gaji.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-5 border-t border-admin-border">{{ $components->links() }}</div>
    </div>
</div>
@endsection
