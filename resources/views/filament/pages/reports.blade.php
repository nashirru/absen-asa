<x-filament-panels::page>
    <!-- ChartJS library load for category charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="flex flex-col gap-y-6">
        <!-- Tab Navigation Navigation Buttons -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-2 shadow-sm">
            <div class="flex flex-wrap gap-2">
                <button 
                    wire:click="$set('activeTab', 'cashflow')" 
                    class="flex items-center gap-x-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ $activeTab === 'cashflow' ? 'bg-amber-500 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Laporan Arus Kas
                </button>

                <button 
                    wire:click="$set('activeTab', 'category')" 
                    class="flex items-center gap-x-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ $activeTab === 'category' ? 'bg-amber-500 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.003 9.003 0 1020.945 13H11V3.055z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                    </svg>
                    Laporan Per Kategori
                </button>

                <button 
                    wire:click="$set('activeTab', 'payroll')" 
                    class="flex items-center gap-x-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all {{ $activeTab === 'payroll' ? 'bg-amber-500 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Laporan Penggajian
                </button>
            </div>
        </div>

        <!-- Dynamic Tab Contents -->
        @if($activeTab === 'cashflow')
            <!-- TAB 1: CASH FLOW REPORT -->
            <div class="p-6 bg-white rounded-xl border border-gray-200 dark:bg-gray-900 dark:border-gray-800 shadow-sm">
                <h2 class="text-base font-bold text-gray-900 dark:text-white mb-4">Filter Laporan Arus Kas</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Mulai Tanggal</label>
                        <input type="date" wire:model.live="cashflowStartDate" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 dark:text-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Sampai Tanggal</label>
                        <input type="date" wire:model.live="cashflowEndDate" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 dark:text-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Akun</label>
                        <select wire:model.live="cashflowAccountId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 dark:text-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Semua Akun</option>
                            @foreach($this->accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }} ({{ ucfirst($account->type) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
                        <select wire:model.live="cashflowCategoryId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 dark:text-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Semua Kategori</option>
                            @foreach($this->categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }} ({{ ucfirst($category->type) }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @php
                    $summary = $this->getCashflowSummary();
                    $transactions = $this->getCashflowTransactions();
                @endphp

                <!-- Summary Metrics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/30 rounded-xl">
                        <p class="text-xs font-semibold text-emerald-800 dark:text-emerald-400">Total Kas Masuk</p>
                        <h3 class="text-2xl font-bold text-emerald-900 dark:text-emerald-300 mt-1">Rp {{ number_format($summary['total_income'], 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-4 bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/30 rounded-xl">
                        <p class="text-xs font-semibold text-rose-800 dark:text-rose-400">Total Kas Keluar</p>
                        <h3 class="text-2xl font-bold text-rose-900 dark:text-rose-300 mt-1">Rp {{ number_format($summary['total_expense'], 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-4 bg-blue-50 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/30 rounded-xl">
                        <p class="text-xs font-semibold text-blue-800 dark:text-blue-400">Saldo Bersih</p>
                        <h3 class="text-2xl font-bold text-blue-900 dark:text-blue-300 mt-1">Rp {{ number_format($summary['balance'], 0, ',', '.') }}</h3>
                    </div>
                </div>

                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Daftar Transaksi Arus Kas</h3>
                    <button wire:click="exportExcel" class="inline-flex items-center py-2 px-4 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-sm transition-all">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Ekspor Excel
                    </button>
                </div>

                <!-- Cashflow Table -->
                <div class="overflow-x-auto border border-gray-200 dark:border-gray-800 rounded-lg">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 uppercase font-semibold text-xs tracking-wider border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="p-3">Tanggal</th>
                                <th class="p-3">Kategori</th>
                                <th class="p-3">Akun</th>
                                <th class="p-3">Keterangan</th>
                                <th class="p-3 text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            @forelse($transactions as $tx)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                    <td class="p-3 text-gray-900 dark:text-white font-medium">{{ $tx->date ? $tx->date->format('d/m/Y') : '-' }}</td>
                                    <td class="p-3">
                                        @if($tx->category)
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-semibold" style="background-color: {{ $tx->category->color }}20; color: {{ $tx->category->color }}">
                                                <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $tx->category->color }}"></span>
                                                {{ $tx->category->name }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 italic text-xs">Tanpa Kategori</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-gray-500">{{ $tx->account?->name ?? '-' }}</td>
                                    <td class="p-3 text-gray-600 dark:text-gray-400 limit-cell" title="{{ $tx->description }}">{{ $tx->description ?? '-' }}</td>
                                    <td class="p-3 text-right font-bold {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $tx->type === 'income' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-gray-500 italic">Tidak ada transaksi ditemukan pada filter ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if($activeTab === 'category')
            <!-- TAB 2: CATEGORY BREAKDOWN REPORT -->
            <div class="p-6 bg-white rounded-xl border border-gray-200 dark:bg-gray-900 dark:border-gray-800 shadow-sm">
                <h2 class="text-base font-bold text-gray-900 dark:text-white mb-4">Filter Kategori Laporan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Mulai Tanggal</label>
                        <input type="date" wire:model.live="categoryStartDate" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 dark:text-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Sampai Tanggal</label>
                        <input type="date" wire:model.live="categoryEndDate" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 dark:text-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                </div>

                @php
                    $breakdown = $this->getCategoryBreakdown();
                    $incomeData = $breakdown['income'];
                    $expenseData = $breakdown['expense'];
                @endphp

                <!-- Chart & Graphics Rendering Area -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Income Chart -->
                    <div class="p-4 border border-gray-200 dark:border-gray-800 rounded-xl flex flex-col items-center">
                        <h4 class="text-sm font-bold text-emerald-600 mb-4 uppercase tracking-wider">Distribusi Pemasukan</h4>
                        <div class="w-full max-w-xs aspect-square flex items-center justify-center">
                            @if($incomeData->isEmpty())
                                <p class="text-gray-400 italic text-sm">Tidak ada data pemasukan</p>
                            @else
                                <div class="w-full h-full" x-data="{
                                    labels: {{ json_encode($incomeData->map(fn($item) => $item->category?->name ?? 'Tanpa Kategori')->toArray()) }},
                                    totals: {{ json_encode($incomeData->pluck('total')->map(fn($v) => (float)$v)->toArray()) }},
                                    colors: {{ json_encode($incomeData->map(fn($item) => $item->category?->color ?? '#10b981')->toArray()) }},
                                    chart: null,
                                    init() {
                                        this.render();
                                        $watch('totals', () => this.render());
                                    },
                                    render() {
                                        if (this.chart) this.chart.destroy();
                                        this.chart = new Chart(this.$refs.canvas, {
                                            type: 'doughnut',
                                            data: {
                                                labels: this.labels,
                                                datasets: [{
                                                    data: this.totals,
                                                    backgroundColor: this.colors,
                                                    borderWidth: 1
                                                }]
                                            },
                                            options: {
                                                responsive: true,
                                                plugins: {
                                                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } }
                                                }
                                            }
                                        });
                                    }
                                }">
                                    <canvas x-ref="canvas"></canvas>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Expense Chart -->
                    <div class="p-4 border border-gray-200 dark:border-gray-800 rounded-xl flex flex-col items-center">
                        <h4 class="text-sm font-bold text-rose-600 mb-4 uppercase tracking-wider">Distribusi Pengeluaran</h4>
                        <div class="w-full max-w-xs aspect-square flex items-center justify-center">
                            @if($expenseData->isEmpty())
                                <p class="text-gray-400 italic text-sm">Tidak ada data pengeluaran</p>
                            @else
                                <div class="w-full h-full" x-data="{
                                    labels: {{ json_encode($expenseData->map(fn($item) => $item->category?->name ?? 'Tanpa Kategori')->toArray()) }},
                                    totals: {{ json_encode($expenseData->pluck('total')->map(fn($v) => (float)$v)->toArray()) }},
                                    colors: {{ json_encode($expenseData->map(fn($item) => $item->category?->color ?? '#ef4444')->toArray()) }},
                                    chart: null,
                                    init() {
                                        this.render();
                                        $watch('totals', () => this.render());
                                    },
                                    render() {
                                        if (this.chart) this.chart.destroy();
                                        this.chart = new Chart(this.$refs.canvas, {
                                            type: 'doughnut',
                                            data: {
                                                labels: this.labels,
                                                datasets: [{
                                                    data: this.totals,
                                                    backgroundColor: this.colors,
                                                    borderWidth: 1
                                                }]
                                            },
                                            options: {
                                                responsive: true,
                                                plugins: {
                                                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } }
                                                }
                                            }
                                        });
                                    }
                                }">
                                    <canvas x-ref="canvas"></canvas>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Breakdown Tables Side-by-side -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Income breakdown table -->
                    <div>
                        <h3 class="text-xs font-bold text-emerald-700 uppercase tracking-wider mb-2 border-b border-emerald-100 dark:border-emerald-950 pb-2">Detail Pemasukan Kategori</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="text-gray-500 font-semibold border-b border-gray-150">
                                        <th class="py-2">Kategori</th>
                                        <th class="py-2 text-right">Total Transaksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $subtotalIn = 0; @endphp
                                    @forelse($incomeData as $tx)
                                        @php $subtotalIn += $tx->total; @endphp
                                        <tr class="border-b border-gray-100 dark:border-gray-800">
                                            <td class="py-2.5 flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $tx->category?->color ?? '#cbd5e1' }}"></span>
                                                <span class="font-medium text-gray-900 dark:text-white">{{ $tx->category?->name ?? 'Tanpa Kategori' }}</span>
                                            </td>
                                            <td class="py-2.5 text-right font-bold text-emerald-600">Rp {{ number_format($tx->total, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="py-4 text-center text-gray-400 italic">Belum ada transaksi pemasukan</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if(!$incomeData->isEmpty())
                                    <tfoot>
                                        <tr class="font-bold">
                                            <td class="py-2.5 text-gray-900 dark:text-white">TOTAL</td>
                                            <td class="py-2.5 text-right text-emerald-600">Rp {{ number_format($subtotalIn, 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Expense breakdown table -->
                    <div>
                        <h3 class="text-xs font-bold text-rose-700 uppercase tracking-wider mb-2 border-b border-rose-100 dark:border-rose-950 pb-2">Detail Pengeluaran Kategori</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="text-gray-500 font-semibold border-b border-gray-150">
                                        <th class="py-2">Kategori</th>
                                        <th class="py-2 text-right">Total Transaksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $subtotalOut = 0; @endphp
                                    @forelse($expenseData as $tx)
                                        @php $subtotalOut += $tx->total; @endphp
                                        <tr class="border-b border-gray-100 dark:border-gray-800">
                                            <td class="py-2.5 flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $tx->category?->color ?? '#cbd5e1' }}"></span>
                                                <span class="font-medium text-gray-900 dark:text-white">{{ $tx->category?->name ?? 'Tanpa Kategori' }}</span>
                                            </td>
                                            <td class="py-2.5 text-right font-bold text-rose-600">Rp {{ number_format($tx->total, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="py-4 text-center text-gray-400 italic">Belum ada transaksi pengeluaran</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if(!$expenseData->isEmpty())
                                    <tfoot>
                                        <tr class="font-bold">
                                            <td class="py-2.5 text-gray-900 dark:text-white">TOTAL</td>
                                            <td class="py-2.5 text-right text-rose-600">Rp {{ number_format($subtotalOut, 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($activeTab === 'payroll')
            <!-- TAB 3: PAYROLL REPORT -->
            <div class="p-6 bg-white rounded-xl border border-gray-200 dark:bg-gray-900 dark:border-gray-800 shadow-sm">
                <h2 class="text-base font-bold text-gray-900 dark:text-white mb-4">Filter Periode Penggajian</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Pilih Periode</label>
                        <select wire:model.live="payrollPeriodId" class="w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 dark:text-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Pilih Periode Gaji</option>
                            @foreach($this->getPayrollPeriodsProperty() as $period)
                                @php
                                    $months = [
                                        1 => "Januari", 2 => "Februari", 3 => "Maret", 4 => "April",
                                        5 => "Mei", 6 => "Juni", 7 => "Juli", 8 => "Agustus",
                                        9 => "September", 10 => "Oktober", 11 => "November", 12 => "Desember"
                                    ];
                                    $periodLabel = ($months[$period->month] ?? '') . ' ' . $period->year;
                                @endphp
                                <option value="{{ $period->id }}">{{ $periodLabel }} ({{ ucfirst($period->status) }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @php
                    $details = $this->getPayrollDetails();
                @endphp

                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Detail Pembayaran Karyawan</h3>
                    @if(!$details->isEmpty())
                        <button wire:click="exportPayrollPdf" class="inline-flex items-center py-2 px-4 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-semibold text-sm shadow-sm transition-all">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Ekspor PDF Rekap
                        </button>
                    @endif
                </div>

                <!-- Payroll Recap Table -->
                <div class="overflow-x-auto border border-gray-200 dark:border-gray-800 rounded-lg">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 uppercase font-semibold text-xs tracking-wider border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="p-3">Nama Karyawan</th>
                                <th class="p-3">Gaji Pokok</th>
                                <th class="p-3">Tunjangan</th>
                                <th class="p-3">Potongan</th>
                                <th class="p-3">Bonus</th>
                                <th class="p-3 text-right">Gaji Bersih (Net)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            @php
                                $sumBase = 0;
                                $sumAllow = 0;
                                $sumDeduct = 0;
                                $sumBonus = 0;
                                $sumNet = 0;
                            @endphp
                            @forelse($details as $detail)
                                @php
                                    $sumBase += $detail->base_salary;
                                    $sumAllow += $detail->total_allowance;
                                    $sumDeduct += $detail->total_deduction;
                                    $sumBonus += $detail->bonus;
                                    $sumNet += $detail->net_salary;
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                    <td class="p-3 text-gray-900 dark:text-white font-medium">
                                        {{ $detail->employee->name }}<br>
                                        <span class="text-xs text-gray-400 font-normal">{{ $detail->employee->position }}</span>
                                    </td>
                                    <td class="p-3 text-gray-600 dark:text-gray-300">Rp {{ number_format($detail->base_salary, 0, ',', '.') }}</td>
                                    <td class="p-3 text-emerald-600">Rp {{ number_format($detail->total_allowance, 0, ',', '.') }}</td>
                                    <td class="p-3 text-rose-600">Rp {{ number_format($detail->total_deduction, 0, ',', '.') }}</td>
                                    <td class="p-3 text-cyan-600">Rp {{ number_format($detail->bonus, 0, ',', '.') }}</td>
                                    <td class="p-3 text-right font-bold text-gray-950 dark:text-white">Rp {{ number_format($detail->net_salary, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-gray-500 italic">Silakan pilih periode penggajian yang valid.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(!$details->isEmpty())
                            <tfoot class="bg-gray-50 dark:bg-gray-800 font-bold border-t border-gray-200 dark:border-gray-700">
                                <tr>
                                    <td class="p-3 text-gray-900 dark:text-white">TOTAL</td>
                                    <td class="p-3 text-gray-900 dark:text-white">Rp {{ number_format($sumBase, 0, ',', '.') }}</td>
                                    <td class="p-3 text-emerald-600">Rp {{ number_format($sumAllow, 0, ',', '.') }}</td>
                                    <td class="p-3 text-rose-600">Rp {{ number_format($sumDeduct, 0, ',', '.') }}</td>
                                    <td class="p-3 text-cyan-600">Rp {{ number_format($sumBonus, 0, ',', '.') }}</td>
                                    <td class="p-3 text-right text-gray-950 dark:text-white">Rp {{ number_format($sumNet, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
