@extends('layouts.admin')
@section('title', 'Kelola Periode Gaji')
@section('header', 'Kelola Periode Gaji')
@section('content')
<div class="animate-fade-in-up space-y-6">
    @if(session('success'))
        <div class="p-4 bg-admin-success-tint text-admin-success rounded-admin-md text-sm font-medium">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-admin-danger-tint text-admin-danger rounded-admin-md text-sm font-medium">{{ session('error') }}</div>
    @endif

    {{-- Info & Actions Card --}}
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h3 class="text-lg font-semibold text-admin-ink">{{ $monthNames[$payrollPeriod->month] }} {{ $payrollPeriod->year }}</h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-admin-full text-xs font-medium mt-1
                {{ $payrollPeriod->status == 'draft' ? 'bg-yellow-100 text-yellow-700' : '' }}
                {{ $payrollPeriod->status == 'processed' ? 'bg-blue-100 text-blue-700' : '' }}
                {{ $payrollPeriod->status == 'paid' ? 'bg-green-100 text-green-700' : '' }}">
                {{ $payrollPeriod->status == 'draft' ? 'Draft' : ($payrollPeriod->status == 'processed' ? 'Diproses' : 'Dibayar') }}
            </span>
        </div>
        <div class="flex items-center gap-2">
            @if($payrollPeriod->status == 'draft')
                <button type="button" onclick="document.getElementById('processModal').classList.remove('hidden')"
                        class="bg-emerald-600 text-white px-4 py-2 rounded-admin-md text-sm font-medium hover:bg-emerald-700 transition-colors">
                    Proses Penggajian
                </button>
            @endif
            @if($payrollPeriod->status == 'processed')
                <form method="POST" action="{{ route('finance.payroll-periods.pay', $payrollPeriod) }}" onsubmit="return confirm('Bayarkan gaji periode ini ke seluruh karyawan?')">
                    @csrf
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-admin-md text-sm font-medium hover:bg-blue-700 transition-colors">
                        Bayar Gaji
                    </button>
                </form>
            @endif
            @if($payrollPeriod->status != 'paid')
                <form method="POST" action="{{ route('finance.payroll-periods.destroy', $payrollPeriod) }}" onsubmit="return confirm('Hapus periode gaji ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-admin-danger hover:text-red-700 text-sm font-medium px-4 py-2">Hapus</button>
                </form>
            @endif
            <a href="{{ route('finance.payroll-periods.index') }}" class="text-admin-slate hover:text-admin-ink text-sm font-medium px-4 py-2">Kembali</a>
        </div>
    </div>

    {{-- Edit Period Info --}}
    @if($payrollPeriod->status != 'paid')
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-5">
        <h4 class="text-sm font-semibold text-admin-ink mb-4">Edit Periode</h4>
        <form method="POST" action="{{ route('finance.payroll-periods.update', $payrollPeriod) }}" class="flex items-end gap-4">
            @csrf @method('PUT')
            <div>
                <label class="text-xs font-semibold text-admin-slate">Bulan</label>
                <select name="month" {{ $payrollPeriod->payrollDetails->count() ? 'disabled' : '' }}
                        class="mt-1 px-3 py-2 bg-admin-canvas rounded-admin-md border border-admin-border text-sm focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    @foreach($monthNames as $val => $label)
                        <option value="{{ $val }}" {{ old('month', $payrollPeriod->month) == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-admin-slate">Tahun</label>
                <input type="number" name="year" value="{{ old('year', $payrollPeriod->year) }}" {{ $payrollPeriod->payrollDetails->count() ? 'disabled' : '' }}
                       class="mt-1 px-3 py-2 bg-admin-canvas rounded-admin-md border border-admin-border text-sm focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
            </div>
            <div>
                <label class="text-xs font-semibold text-admin-slate">Status</label>
                <select name="status"
                        class="mt-1 px-3 py-2 bg-admin-canvas rounded-admin-md border border-admin-border text-sm focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <option value="draft" {{ old('status', $payrollPeriod->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="processed" {{ old('status', $payrollPeriod->status) == 'processed' ? 'selected' : '' }}>Diproses</option>
                </select>
            </div>
            <button type="submit" class="bg-admin-indigo text-white px-4 py-2 rounded-admin-md text-sm font-medium hover:bg-admin-indigo-deep transition-colors">Simpan</button>
        </form>
    </div>
    @endif

    {{-- Payroll Details Table --}}
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg">
        <div class="p-5 border-b border-admin-border">
            <h4 class="text-sm font-semibold text-admin-ink">Rincian Gaji Karyawan</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-admin-canvas text-admin-slate text-xs font-semibold uppercase tracking-wider">
                        <th class="text-left px-5 py-3">Karyawan</th>
                        <th class="text-right px-5 py-3">Gaji Pokok</th>
                        <th class="text-right px-5 py-3">Tunjangan</th>
                        <th class="text-right px-5 py-3">Potongan</th>
                        <th class="text-right px-5 py-3">Bonus</th>
                        <th class="text-right px-5 py-3">Gaji Bersih</th>
                        <th class="text-left px-5 py-3">Dibayar</th>
                        <th class="text-center px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-admin-border">
                    @forelse($payrollPeriod->payrollDetails as $detail)
                    <tr class="hover:bg-admin-canvas/50 transition-colors">
                        <td class="px-5 py-3.5 text-admin-ink font-medium">{{ $detail->karyawan?->user?->name ?? 'PAY-' . $detail->karyawan?->nik }}</td>
                        <td class="px-5 py-3.5 text-admin-ink text-right font-mono">Rp {{ number_format($detail->base_salary, 0, ',', '.') }}</td>
                        <td class="px-5 py-3.5 text-emerald-700 text-right font-mono">Rp {{ number_format($detail->total_allowance, 0, ',', '.') }}</td>
                        <td class="px-5 py-3.5 text-red-700 text-right font-mono">Rp {{ number_format($detail->total_deduction, 0, ',', '.') }}</td>
                        <td class="px-5 py-3.5 text-admin-ink text-right font-mono">
                            @if($payrollPeriod->status == 'draft')
                                <form method="POST" action="{{ route('finance.payroll-details.update', $detail) }}" class="flex items-center justify-end gap-1">
                                    @csrf @method('PUT')
                                    <input type="number" step="0.01" name="bonus" value="{{ $detail->bonus }}" min="0"
                                           class="w-20 px-2 py-1 bg-admin-canvas rounded-admin-md border border-admin-border text-xs text-right font-mono focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                                    <button type="submit" class="text-admin-indigo hover:text-admin-indigo-deep text-xs font-medium px-1">OK</button>
                                </form>
                            @else
                                Rp {{ number_format($detail->bonus, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-admin-ink text-right font-mono font-semibold">Rp {{ number_format($detail->net_salary, 0, ',', '.') }}</td>
                        <td class="px-5 py-3.5 text-admin-slate text-xs">{{ $detail->paid_at ? $detail->paid_at->format('d/m/Y H:i') : '-' }}</td>
                        <td class="px-5 py-3.5 text-center">
                            <a href="{{ route('payroll.slip', $detail) }}" target="_blank" class="text-admin-indigo hover:text-admin-indigo-deep text-xs font-medium mr-2">Slip</a>
                            <a href="{{ route('payroll.slip', [$detail, 'format' => 'pdf']) }}" target="_blank" class="text-admin-success hover:text-green-700 text-xs font-medium">PDF</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-5 py-8 text-center text-admin-mist">Belum ada rincian gaji.</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-admin-canvas/50 border-t border-admin-border">
                    <tr>
                        <td class="px-5 py-3 text-xs font-semibold text-admin-ink">Total {{ $payrollPeriod->payrollDetails->count() }} karyawan</td>
                        <td colspan="3"></td>
                        <td class="px-5 py-3 text-right text-xs font-semibold text-admin-ink font-mono">Rp {{ number_format($payrollPeriod->payrollDetails->sum('net_salary'), 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

{{-- Process Payroll Modal --}}
<div id="processModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden" onclick="if(event.target==this)this.classList.add('hidden')">
    <div class="bg-admin-surface rounded-admin-lg p-6 w-full max-w-md mx-4 shadow-admin-float" onclick="event.stopPropagation()">
        <h3 class="text-lg font-semibold text-admin-ink mb-1">Proses Penggajian</h3>
        <p class="text-sm text-admin-slate mb-5">Pilih akun pengeluaran untuk mencatat transaksi gaji {{ $monthNames[$payrollPeriod->month] }} {{ $payrollPeriod->year }}.</p>
        <form method="POST" action="{{ route('finance.payroll-periods.process', $payrollPeriod) }}">
            @csrf
            <div class="mb-4">
                <label class="text-xs font-semibold text-admin-slate">Akun Pengeluaran (Kas/Bank)</label>
                <select name="account_id" required
                        class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <option value="">Pilih Akun</option>
                    @foreach($accounts as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="bg-emerald-600 text-white px-6 py-2.5 rounded-admin-md text-sm font-medium hover:bg-emerald-700 transition-colors">Proses</button>
                <button type="button" onclick="document.getElementById('processModal').classList.add('hidden')" class="text-admin-slate hover:text-admin-ink text-sm font-medium">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection
