@extends('layouts.admin')
@section('title', 'Dasbor Keuangan')
@section('header', 'Dasbor Keuangan')
@section('content')
<div class="space-y-6 animate-fade-in-up">
    {{-- Mode Toggle + Export --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2 bg-admin-surface border border-admin-border rounded-admin-lg p-2 w-fit">
            <a href="{{ route('dashboard', ['mode' => 'absensi']) }}" class="px-4 py-2 rounded-admin-md text-sm font-medium transition-colors text-admin-slate hover:text-admin-ink hover:bg-admin-canvas">
                <i data-lucide="clipboard-check" class="w-4 h-4 inline-block mr-1.5"></i>Absensi
            </a>
            <a href="{{ route('dashboard', ['mode' => 'keuangan']) }}" class="px-4 py-2 rounded-admin-md text-sm font-medium transition-colors bg-admin-indigo text-white">
                <i data-lucide="wallet" class="w-4 h-4 inline-block mr-1.5"></i>Keuangan
            </a>
        </div>
        <a href="{{ route('dashboard.pdf') }}" target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2 bg-admin-danger-tint text-admin-danger rounded-admin-md text-sm font-medium hover:bg-red-100 transition-colors">
            <i data-lucide="file-text" class="w-4 h-4"></i>Export PDF
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-admin-slate">Total Akun</p>
            <p class="text-2xl font-bold text-admin-ink mt-1">{{ $totalAccounts }}</p>
        </div>
        <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-admin-slate">Total Saldo</p>
            <p class="text-2xl font-bold text-admin-ink mt-1 font-mono">Rp {{ number_format($totalBalance, 0, ',', '.') }}</p>
        </div>
        <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-admin-slate">Pemasukan</p>
            <p class="text-2xl font-bold text-green-600 mt-1 font-mono">Rp {{ number_format($incomeThisMonth, 0, ',', '.') }}</p>
        </div>
        <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-admin-slate">Pengeluaran</p>
            <p class="text-2xl font-bold text-red-600 mt-1 font-mono">Rp {{ number_format($expenseThisMonth, 0, ',', '.') }}</p>
        </div>
        <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-admin-slate">Net Bulan Ini</p>
            <p class="text-2xl font-bold mt-1 font-mono {{ $netMonthly >= 0 ? 'text-green-600' : 'text-red-600' }}">
                Rp {{ number_format($netMonthly, 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Payroll Quick Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-gradient-to-r from-indigo-50 to-indigo-100/50 border border-indigo-200 rounded-admin-lg p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-admin-md bg-indigo-100 flex items-center justify-center text-indigo-600">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">Karyawan Aktif</p>
                <p class="text-xl font-bold text-indigo-900">{{ $activePayrollCount }} orang</p>
            </div>
        </div>
        <div class="bg-gradient-to-r from-amber-50 to-amber-100/50 border border-amber-200 rounded-admin-lg p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-admin-md bg-amber-100 flex items-center justify-center text-amber-600">
                <i data-lucide="wallet" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Total Gaji Bulan Ini</p>
                <p class="text-xl font-bold text-amber-900 font-mono">Rp {{ number_format($salaryExpense, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="bg-gradient-to-r from-emerald-50 to-emerald-100/50 border border-emerald-200 rounded-admin-lg p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-admin-md bg-emerald-100 flex items-center justify-center text-emerald-600">
                <i data-lucide="trending-up" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Rata-rata per Karyawan</p>
                <p class="text-xl font-bold text-emerald-900 font-mono">
                    Rp {{ number_format($activePayrollCount > 0 ? $salaryExpense / $activePayrollCount : 0, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Account List --}}
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg">
        <div class="p-5 border-b border-admin-border flex items-center justify-between">
            <h3 class="text-sm font-semibold text-admin-ink">Akun Rekening</h3>
            <a href="{{ route('finance.accounts.index') }}" class="text-xs text-admin-indigo hover:text-admin-indigo-deep font-medium">Kelola →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-admin-canvas text-admin-slate text-xs font-semibold uppercase tracking-wider">
                        <th class="text-left px-5 py-3">Nama</th>
                        <th class="text-left px-5 py-3">Tipe</th>
                        <th class="text-right px-5 py-3">Saldo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-admin-border">
                    @forelse($accounts as $account)
                    <tr>
                        <td class="px-5 py-3 text-admin-ink font-medium">{{ $account->name }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded-admin-full text-xs font-medium {{ $account->type == 'cash' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $account->type == 'cash' ? 'Kas' : 'Bank' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-admin-ink text-right font-mono">Rp {{ number_format($account->balance, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-5 py-8 text-center text-admin-mist">Belum ada akun.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 3-Column Grid: Chart + Top Expenses + Latest Transactions --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Cashflow Chart (spans 2 cols) --}}
        <div class="lg:col-span-2 bg-admin-surface border border-admin-border rounded-admin-lg p-5">
            <h3 class="text-sm font-semibold text-admin-ink mb-4">Arus Kas (12 Bulan)</h3>
            <canvas id="cashflowChart" height="220"></canvas>
        </div>

        {{-- Top Expense Categories --}}
        <div class="bg-admin-surface border border-admin-border rounded-admin-lg">
            <div class="p-5 border-b border-admin-border">
                <h3 class="text-sm font-semibold text-admin-ink">Pengeluaran Teratas</h3>
            </div>
            <div class="divide-y divide-admin-border">
                @forelse($topExpenses as $te)
                <div class="px-5 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="inline-block w-3 h-3 rounded-admin-full" style="background-color: {{ $te->category?->color ?? '#ccc' }}"></span>
                        <span class="text-sm text-admin-ink">{{ $te->category?->name ?? 'Tanpa Kategori' }}</span>
                    </div>
                    <span class="text-sm font-mono font-semibold text-red-600">Rp {{ number_format($te->total, 0, ',', '.') }}</span>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-admin-mist">Belum ada pengeluaran bulan ini.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Latest Transactions --}}
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg">
        <div class="p-5 border-b border-admin-border flex items-center justify-between">
            <h3 class="text-sm font-semibold text-admin-ink">Transaksi Terbaru</h3>
            <a href="{{ route('finance.transactions.index') }}" class="text-xs text-admin-indigo hover:text-admin-indigo-deep font-medium">Lihat Semua →</a>
        </div>
        <div class="divide-y divide-admin-border">
            @forelse($latestTransactions as $t)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-admin-canvas/50 transition-colors">
                <div class="flex items-center gap-3">
                    <span class="w-2 h-2 rounded-admin-full {{ $t->type == 'income' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                    <div>
                        <p class="text-sm font-medium text-admin-ink">{{ $t->description ?? $t->category?->name ?? '-' }}</p>
                        <p class="text-xs text-admin-mist">{{ $t->account?->name }} · {{ $t->date->format('d/m/Y') }}</p>
                    </div>
                </div>
                <span class="font-mono text-sm whitespace-nowrap {{ $t->type == 'income' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $t->type == 'income' ? '+' : '-' }} Rp {{ number_format($t->amount, 0, ',', '.') }}
                </span>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-admin-mist">Belum ada transaksi.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('cashflowChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_column($cashflowData, 'month')) !!},
        datasets: [
            {
                label: 'Pemasukan',
                data: {!! json_encode(array_column($cashflowData, 'income')) !!},
                backgroundColor: '#22c55e',
                borderRadius: 4,
            },
            {
                label: 'Pengeluaran',
                data: {!! json_encode(array_column($cashflowData, 'expense')) !!},
                backgroundColor: '#ef4444',
                borderRadius: 4,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 16 } } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => 'Rp ' + (v/1000).toFixed(0) + 'rb' } }
        }
    }
});
</script>
@endsection
