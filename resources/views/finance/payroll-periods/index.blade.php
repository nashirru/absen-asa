@extends('layouts.admin')
@section('title', 'Periode Gaji')
@section('header', 'Periode Gaji')
@section('content')
<div class="animate-fade-in-up">
    @if(session('success'))
        <div class="mb-4 p-4 bg-admin-success-tint text-admin-success rounded-admin-md text-sm font-medium">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-admin-danger-tint text-admin-danger rounded-admin-md text-sm font-medium">{{ session('error') }}</div>
    @endif
    @php $monthNames = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember']; @endphp
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg">
        <div class="p-5 border-b border-admin-border flex items-center justify-between">
            <span class="text-sm text-admin-slate">Total: {{ $periods->total() }} periode</span>
            <a href="{{ route('finance.payroll-periods.create') }}" class="bg-admin-indigo text-white px-4 py-2 rounded-admin-md text-sm font-medium hover:bg-admin-indigo-deep transition-colors">+ Periode Baru</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-admin-canvas text-admin-slate text-xs font-semibold uppercase tracking-wider">
                        <th class="text-left px-5 py-3">Periode</th>
                        <th class="text-left px-5 py-3">Status</th>
                        <th class="text-right px-5 py-3">Total Gaji Bersih</th>
                        <th class="text-right px-5 py-3">Jml Karyawan</th>
                        <th class="text-center px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-admin-border">
                    @forelse($periods as $p)
                    <tr class="hover:bg-admin-canvas/50 transition-colors">
                        <td class="px-5 py-3.5 text-admin-ink font-medium">{{ $monthNames[$p->month] }} {{ $p->year }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-admin-full text-xs font-medium
                                {{ $p->status == 'draft' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $p->status == 'processed' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $p->status == 'paid' ? 'bg-green-100 text-green-700' : '' }}">
                                {{ $p->status == 'draft' ? 'Draft' : ($p->status == 'processed' ? 'Diproses' : 'Dibayar') }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-admin-ink text-right font-mono font-semibold">Rp {{ number_format($p->payroll_details_sum_net_salary ?? 0, 0, ',', '.') }}</td>
                        <td class="px-5 py-3.5 text-admin-ink text-right">{{ $p->payroll_details_count }} org</td>
                        <td class="px-5 py-3.5 text-center">
                            <a href="{{ route('finance.payroll-periods.edit', $p) }}" class="text-admin-indigo hover:text-admin-indigo-deep text-xs font-medium">Kelola</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-admin-mist">Belum ada periode gaji.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-5 border-t border-admin-border">{{ $periods->links() }}</div>
    </div>
</div>
@endsection
