@extends('layouts.admin')
@section('title', 'Dashboard Admin')
@section('header', 'Dashboard')

@section('content')
<div class="space-y-8 animate-fade-in-up">
    <!-- Mode Toggle -->
    <div class="flex items-center gap-2 bg-admin-surface border border-admin-border rounded-admin-lg p-2 w-fit">
        <a href="{{ route('dashboard', ['mode' => 'absensi']) }}" class="px-4 py-2 rounded-admin-md text-sm font-medium transition-colors bg-admin-indigo text-white">
            <i data-lucide="clipboard-check" class="w-4 h-4 inline-block mr-1.5"></i>Absensi
        </a>
        <a href="{{ route('dashboard', ['mode' => 'keuangan']) }}" class="px-4 py-2 rounded-admin-md text-sm font-medium transition-colors text-admin-slate hover:text-admin-ink hover:bg-admin-canvas">
            <i data-lucide="wallet" class="w-4 h-4 inline-block mr-1.5"></i>Keuangan
        </a>
    </div>

    <!-- Welcome Card (Premium CTA - Gradient allowed) -->
    <div class="bg-gradient-to-r from-admin-indigo to-admin-indigo-deep rounded-admin-lg p-6 text-white">
        <h2 class="text-xl font-semibold">Selamat Datang, {{ auth()->user()->name }}!</h2>
        <p class="text-admin-indigo-tint/80 text-sm mt-1">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card Siswa -->
        <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-5 flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <span class="text-xs font-semibold uppercase tracking-wider text-admin-slate">Total Siswa</span>
                <i data-lucide="users" class="w-5 h-5 text-admin-slate"></i>
            </div>
            <div class="mt-4">
                <p class="text-[32px] font-bold text-admin-ink leading-none">{{ $totalSiswa }}</p>
                <p class="text-xs text-admin-slate mt-1">Siswa Terdaftar</p>
            </div>
        </div>

        <!-- Card Sensei -->
        <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-5 flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <span class="text-xs font-semibold uppercase tracking-wider text-admin-slate">Total Sensei</span>
                <i data-lucide="award" class="w-5 h-5 text-admin-slate"></i>
            </div>
            <div class="mt-4">
                <p class="text-[32px] font-bold text-admin-ink leading-none">{{ $totalSensei }}</p>
                <p class="text-xs text-admin-slate mt-1">Tenaga Pengajar</p>
            </div>
        </div>

        <!-- Card Karyawan -->
        <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-5 flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <span class="text-xs font-semibold uppercase tracking-wider text-admin-slate">Total Karyawan</span>
                <i data-lucide="briefcase" class="w-5 h-5 text-admin-slate"></i>
            </div>
            <div class="mt-4">
                <p class="text-[32px] font-bold text-admin-ink leading-none">{{ $totalKaryawan }}</p>
                <p class="text-xs text-admin-slate mt-1">Staf & Karyawan</p>
            </div>
        </div>

        <!-- Card Kelas -->
        <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-5 flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <span class="text-xs font-semibold uppercase tracking-wider text-admin-slate">Total Kelas</span>
                <i data-lucide="book-open" class="w-5 h-5 text-admin-slate"></i>
            </div>
            <div class="mt-4">
                <p class="text-[32px] font-bold text-admin-ink leading-none">{{ $totalKelas }}</p>
                <p class="text-xs text-admin-slate mt-1">Kelas Aktif</p>
            </div>
        </div>
    </div>

    <!-- Today's Attendance -->
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-semibold text-lg text-admin-ink">Absensi Hari Ini</h3>
            <span class="text-xs text-admin-slate font-medium">Status Kehadiran Realtime</span>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 text-center">
            <div class="p-4 rounded-admin-md bg-admin-success-tint/50 border border-admin-success/10">
                <p class="text-2xl font-bold text-admin-success">{{ $totalHadirHariIni }}</p>
                <p class="text-xs text-admin-slate mt-1">Hadir</p>
            </div>
            <div class="p-4 rounded-admin-md bg-amber-50 border border-amber-200">
                <p class="text-2xl font-bold text-amber-600">{{ $totalTerlambat }}</p>
                <p class="text-xs text-admin-slate mt-1">Terlambat</p>
            </div>
            <div class="p-4 rounded-admin-md bg-admin-indigo-tint/50 border border-admin-indigo/10">
                <p class="text-2xl font-bold text-admin-indigo">{{ $totalIzin }}</p>
                <p class="text-xs text-admin-slate mt-1">Izin</p>
            </div>
            <div class="p-4 rounded-admin-md bg-orange-50 border border-orange-200">
                <p class="text-2xl font-bold text-orange-600">{{ $totalSakit }}</p>
                <p class="text-xs text-admin-slate mt-1">Sakit</p>
            </div>
            <div class="p-4 rounded-admin-md bg-admin-danger-tint/50 border border-admin-danger/10">
                <p class="text-2xl font-bold text-admin-danger">{{ $totalAlpha }}</p>
                <p class="text-xs text-admin-slate mt-1">Alpha</p>
            </div>
            <div class="p-4 rounded-admin-md bg-amber-50/80 border border-amber-300">
                <p class="text-2xl font-bold text-amber-700">{{ $totalLemburHariIni }}</p>
                <p class="text-xs text-admin-slate mt-1">Lembur ({{ number_format($totalDurasiLembur, 1) }} jam)</p>
            </div>
        </div>
    </div>

    <!-- Realtime Map View & Live Monitoring Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="adminMonitoringApp()" x-init="init()">
        <!-- Leaflet Map (2/3 width) -->
        <div class="lg:col-span-2 bg-admin-surface border border-admin-border rounded-admin-lg p-6 flex flex-col justify-between">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-lg text-admin-ink">Peta Kehadiran Realtime</h3>
                <span class="text-xs text-admin-slate">Lokasi absen masuk/keluar staf & siswa hari ini</span>
            </div>
            <div class="relative w-full h-[380px] rounded-admin-md overflow-hidden border border-admin-border">
                <div id="adminMap" class="w-full h-full"></div>
            </div>
        </div>

        <!-- Live Monitoring List (1/3 width) -->
        <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-6 flex flex-col h-[460px]">
            <!-- Tab list -->
            <div class="bg-admin-canvas p-1 rounded-admin-md flex items-center border border-admin-border mb-4">
                <button @click="tab = 'sudah'" :class="tab === 'sudah' ? 'bg-admin-surface text-admin-ink font-semibold' : 'text-admin-slate'" class="flex-1 text-center py-1.5 rounded-[8px] text-xs transition-all duration-150">
                    Sudah ({{ $sudahAbsen->count() }})
                </button>
                <button @click="tab = 'belum'" :class="tab === 'belum' ? 'bg-admin-surface text-admin-ink font-semibold' : 'text-admin-slate'" class="flex-1 text-center py-1.5 rounded-[8px] text-xs transition-all duration-150">
                    Belum ({{ $belumAbsen->count() }})
                </button>
                <button @click="tab = 'anomali'" :class="tab === 'anomali' ? 'bg-admin-surface text-admin-ink font-semibold' : 'text-admin-slate'" class="flex-1 text-center py-1.5 rounded-[8px] text-xs transition-all duration-150 relative">
                    Anomali ({{ $anomaliHariIni->count() }})
                    @if($anomaliHariIni->count() > 0)
                        <span class="absolute -top-1.5 -right-1 flex h-3.5 w-3.5 items-center justify-center rounded-full bg-red-600 text-[8px] font-bold text-white animate-pulse">!</span>
                    @endif
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto pr-1 space-y-3">
                <!-- Sudah Absen -->
                <div x-show="tab === 'sudah'" class="space-y-3">
                    @forelse($sudahAbsen as $sa)
                        <div class="flex items-center gap-3 p-3 rounded-admin-md border border-admin-border bg-admin-canvas/30 hover:bg-admin-canvas/50 transition-all">
                            <img src="{{ $sa->user->foto_url }}" class="w-9 h-9 rounded-admin-full object-cover border border-admin-border animate-fade-in" alt="">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-admin-ink truncate">{{ $sa->user->name }}</p>
                                <p class="text-[10px] text-admin-slate mt-0.5">{{ $sa->jam_masuk ?? '-' }} - {{ $sa->jam_keluar ?? 'Aktif' }} &bull; {{ $sa->shift ?? 'Default' }}</p>
                            </div>
                            <span class="px-2 py-0.5 rounded-admin-full text-[10px] font-semibold border {{ $sa->status === 'hadir' ? 'bg-admin-success-tint text-admin-success border-admin-success/20' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                {{ ucfirst($sa->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-center text-xs text-admin-mist py-8">Belum ada yang absen hari ini</p>
                    @endforelse
                </div>

                <!-- Belum Absen -->
                <div x-show="tab === 'belum'" class="space-y-3">
                    @forelse($belumAbsen as $ba)
                        <div class="flex items-center gap-3 p-3 rounded-admin-md border border-admin-border bg-admin-canvas/30 hover:bg-admin-canvas/50 transition-all">
                            <img src="{{ $ba->foto_url }}" class="w-9 h-9 rounded-admin-full object-cover border border-admin-border" alt="">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-admin-ink truncate">{{ $ba->name }}</p>
                                <p class="text-[10px] text-admin-slate mt-0.5">Role: {{ $ba->role_label }}</p>
                            </div>
                            <span class="px-2 py-0.5 rounded-admin-full text-[10px] font-semibold bg-red-50 text-red-600 border border-red-200">
                                Belum
                            </span>
                        </div>
                    @empty
                        <p class="text-center text-xs text-admin-mist py-8">Semua pengguna sudah absen hari ini</p>
                    @endforelse
                </div>

                <!-- Anomali -->
                <div x-show="tab === 'anomali'" class="space-y-3">
                    @forelse($anomaliHariIni as $an)
                        <div class="flex flex-col gap-2 p-3 rounded-admin-md border border-red-200 bg-red-50/20 hover:bg-red-50/40 transition-all">
                            <div class="flex items-center gap-3">
                                <img src="{{ $an->user->foto_url }}" class="w-9 h-9 rounded-admin-full object-cover border border-admin-border" alt="">
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-admin-ink truncate">{{ $an->user->name }}</p>
                                    <p class="text-[10px] text-admin-slate mt-0.5">{{ $an->jam_masuk ?? '-' }} &bull; {{ $an->shift ?? 'Default' }}</p>
                                </div>
                                <span class="px-2 py-0.5 rounded-admin-full text-[10px] font-bold bg-red-600 text-white">Anomali</span>
                            </div>
                            <div class="text-[10px] text-red-600 bg-red-50 p-2 rounded border border-red-100 font-medium">
                                <i data-lucide="alert-triangle" class="w-3.5 h-3.5 inline-block mr-1"></i>
                                {{ $an->anomaly_details ?? 'Aktivitas mencurigakan (GPS palsu atau manipulasi radius).' }}
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-xs text-admin-mist py-8">Tidak ada keanehan/anomali hari ini</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Activity Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Chart (2/3 width) -->
        <div class="lg:col-span-2 bg-admin-surface border border-admin-border rounded-admin-lg p-6">
            <h3 class="font-semibold text-lg text-admin-ink mb-6">Grafik Kehadiran 7 Hari</h3>
            <div class="relative w-full h-[260px]">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

        <!-- Recent Activity (1/3 width) -->
        <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-6">
            <h3 class="font-semibold text-lg text-admin-ink mb-6">Aktivitas Terbaru</h3>
            <div class="space-y-4 max-h-[260px] overflow-y-auto pr-1">
                @forelse($recentActivity as $activity)
                    <div class="flex items-center gap-3 p-3 rounded-admin-md border border-admin-border bg-admin-canvas/50">
                        <img src="{{ $activity->user->foto_url }}" class="w-9 h-9 rounded-admin-full object-cover border border-admin-border" alt="">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-admin-ink truncate">{{ $activity->user->name }}</p>
                            <p class="text-[10px] text-admin-slate mt-0.5">{{ $activity->tanggal->format('d M') }} &bull; {{ $activity->jam_masuk ?? '-' }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-admin-full text-[10px] font-semibold border
                            {{ $activity->status === 'hadir' ? 'bg-admin-success-tint text-admin-success border-admin-success/20' : 
                               ($activity->status === 'terlambat' ? 'bg-amber-50 text-amber-700 border-amber-200' :
                               ($activity->status === 'alpha' ? 'bg-admin-danger-tint text-admin-danger border-admin-danger/20' : 
                               'bg-admin-indigo-tint text-admin-indigo border-admin-indigo/20')) }}">
                            {{ ucfirst($activity->status) }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-12 text-admin-mist">
                        <i data-lucide="calendar" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                        <p class="text-xs">Belum ada aktivitas</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Work Hours Accumulation Chart -->
    <div x-data="workHoursChart()" x-init="loadChart()">
        <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-6 relative">
            <!-- Header with period filter -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <h3 class="font-semibold text-lg text-admin-ink">Akumulasi Jam Kerja</h3>

                <div class="flex items-center gap-3 flex-wrap">
                    <!-- View mode toggle -->
                    <div class="bg-admin-canvas rounded-admin-md p-1 flex items-center border border-admin-border">
                        <button @click="viewMode = 'total'; loadChart()"
                                :class="viewMode === 'total' ? 'bg-admin-surface text-admin-ink font-semibold' : 'text-admin-slate hover:text-admin-ink'"
                                class="px-3 py-1.5 rounded-[8px] text-[13px] transition-all duration-150">
                            Total
                        </button>
                        <button @click="viewMode = 'per_user'; loadChart()"
                                :class="viewMode === 'per_user' ? 'bg-admin-surface text-admin-ink font-semibold' : 'text-admin-slate hover:text-admin-ink'"
                                class="px-3 py-1.5 rounded-[8px] text-[13px] transition-all duration-150">
                            Per User
                        </button>
                    </div>

                    <!-- Period selector -->
                    <div class="bg-admin-canvas rounded-admin-md p-1 flex items-center border border-admin-border">
                        <button @click="period = '7d'; loadChart()"
                                :class="period === '7d' ? 'bg-admin-surface text-admin-ink font-semibold' : 'text-admin-slate hover:text-admin-ink'"
                                class="px-3 py-1.5 rounded-[8px] text-[13px] transition-all duration-150">
                            7 Hari
                        </button>
                        <button @click="period = '1m'; loadChart()"
                                :class="period === '1m' ? 'bg-admin-surface text-admin-ink font-semibold' : 'text-admin-slate hover:text-admin-ink'"
                                class="px-3 py-1.5 rounded-[8px] text-[13px] transition-all duration-150">
                            1 Bulan
                        </button>
                        <button @click="period = '3m'; loadChart()"
                                :class="period === '3m' ? 'bg-admin-surface text-admin-ink font-semibold' : 'text-admin-slate hover:text-admin-ink'"
                                class="px-3 py-1.5 rounded-[8px] text-[13px] transition-all duration-150">
                            3 Bulan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Summary stat -->
            <div class="flex items-center gap-2 mb-4">
                <div class="p-1.5 rounded-admin-md bg-admin-success-tint">
                    <i data-lucide="clock" class="w-4 h-4 text-admin-success"></i>
                </div>
                <span class="text-sm text-admin-slate">
                    Total: <span class="font-semibold text-admin-ink" x-text="totalHoursDisplay">--</span> jam
                    (<span class="text-admin-slate" x-text="totalRecordsDisplay">--</span>)
                </span>
            </div>

            <!-- Chart canvas -->
            <div class="relative w-full h-[280px]">
                <canvas id="workHoursChart"></canvas>
            </div>

            <!-- Loading overlay -->
            <div x-show="loading" x-transition.opacity
                 class="absolute inset-0 bg-admin-surface/60 rounded-admin-lg flex items-center justify-center z-10">
                <div class="flex items-center gap-2 text-admin-slate text-sm">
                    <div class="w-4 h-4 border-2 border-admin-indigo border-t-transparent rounded-full animate-spin"></div>
                    Memuat data...
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions (Integration Cards style) -->
    <div>
        <h3 class="font-semibold text-lg text-admin-ink mb-6">Akses Cepat</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <a href="{{ route('siswa.index') }}" class="group bg-admin-surface border border-admin-border rounded-admin-lg p-5 text-center hover:border-admin-indigo transition-all duration-150">
                <div class="w-10 h-10 bg-admin-canvas border border-admin-border rounded-admin-md flex items-center justify-center mx-auto group-hover:bg-admin-indigo-tint group-hover:border-admin-indigo/20 transition-all duration-150">
                    <i data-lucide="users" class="w-5 h-5 text-admin-slate group-hover:text-admin-indigo transition-colors"></i>
                </div>
                <p class="text-sm font-semibold mt-3 text-admin-ink">Kelola Siswa</p>
            </a>
            <a href="{{ route('sensei.index') }}" class="group bg-admin-surface border border-admin-border rounded-admin-lg p-5 text-center hover:border-admin-indigo transition-all duration-150">
                <div class="w-10 h-10 bg-admin-canvas border border-admin-border rounded-admin-md flex items-center justify-center mx-auto group-hover:bg-admin-indigo-tint group-hover:border-admin-indigo/20 transition-all duration-150">
                    <i data-lucide="award" class="w-5 h-5 text-admin-slate group-hover:text-admin-indigo transition-colors"></i>
                </div>
                <p class="text-sm font-semibold mt-3 text-admin-ink">Kelola Sensei</p>
            </a>
            <a href="{{ route('kelas.index') }}" class="group bg-admin-surface border border-admin-border rounded-admin-lg p-5 text-center hover:border-admin-indigo transition-all duration-150">
                <div class="w-10 h-10 bg-admin-canvas border border-admin-border rounded-admin-md flex items-center justify-center mx-auto group-hover:bg-admin-indigo-tint group-hover:border-admin-indigo/20 transition-all duration-150">
                    <i data-lucide="book-open" class="w-5 h-5 text-admin-slate group-hover:text-admin-indigo transition-colors"></i>
                </div>
                <p class="text-sm font-semibold mt-3 text-admin-ink">Kelola Kelas</p>
            </a>
            <a href="{{ route('jadwal.index') }}" class="group bg-admin-surface border border-admin-border rounded-admin-lg p-5 text-center hover:border-admin-indigo transition-all duration-150">
                <div class="w-10 h-10 bg-admin-canvas border border-admin-border rounded-admin-md flex items-center justify-center mx-auto group-hover:bg-admin-indigo-tint group-hover:border-admin-indigo/20 transition-all duration-150">
                    <i data-lucide="calendar" class="w-5 h-5 text-admin-slate group-hover:text-admin-indigo transition-colors"></i>
                </div>
                <p class="text-sm font-semibold mt-3 text-admin-ink">Kelola Jadwal</p>
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function workHoursChart() {
    return {
        period: '7d',
        viewMode: 'total',
        chart: null,
        loading: true,
        totalHoursDisplay: '0',
        totalRecordsDisplay: '0',

        async loadChart() {
            this.loading = true;
            try {
                const response = await axios.get('{{ route("dashboard.work-hours") }}', {
                    params: { period: this.period, view: this.viewMode }
                });
                const result = response.data;
                this.renderChart(result);

                if (this.viewMode === 'total') {
                    const totalH = result.data.reduce((a, b) => a + b, 0);
                    const totalR = result.record_counts.reduce((a, b) => a + b, 0);
                    this.totalHoursDisplay = totalH.toFixed(1);
                    this.totalRecordsDisplay = totalR + ' record';
                } else {
                    const totalH = result.datasets.reduce(
                        (sum, ds) => sum + ds.data.reduce((a, b) => a + b, 0), 0
                    );
                    this.totalHoursDisplay = totalH.toFixed(1);
                    this.totalRecordsDisplay = result.datasets.length + ' user';
                }
            } catch (error) {
                console.error('Failed to load work hours:', error);
            } finally {
                this.loading = false;
            }
        },

        renderChart(result) {
            const ctx = document.getElementById('workHoursChart').getContext('2d');

            if (this.chart) {
                this.chart.destroy();
            }

            const colors = [
                '#6D5DFC', '#16A34A', '#EA580C', '#DC2626',
                '#0891B2', '#D97706', '#7C3AED', '#059669'
            ];

            if (this.viewMode === 'total') {
                this.chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: result.labels,
                        datasets: [{
                            label: 'Jam Kerja',
                            data: result.data,
                            backgroundColor: '#6D5DFC',
                            borderRadius: 6,
                        }]
                    },
                    options: this.getChartOptions(false)
                });
            } else {
                const datasets = result.datasets.map((ds, i) => ({
                    label: ds.label,
                    data: ds.data,
                    backgroundColor: colors[i % colors.length] + '99',
                    borderColor: colors[i % colors.length],
                    borderWidth: 1,
                    borderRadius: 4,
                }));

                this.chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: result.labels,
                        datasets: datasets,
                    },
                    options: this.getChartOptions(true)
                });
            }
        },

        getChartOptions(isStacked) {
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: isStacked,
                        position: 'bottom',
                        labels: {
                            font: { family: 'Inter', size: 11 },
                            color: '#6F6C84',
                            boxWidth: 12,
                            padding: 16,
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(1) + ' jam';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: isStacked,
                        grid: { display: false },
                        ticks: { color: '#6F6C84', font: { family: 'Inter', size: 11 } }
                    },
                    y: {
                        stacked: isStacked,
                        beginAtZero: true,
                        grid: { color: '#E6E6F0' },
                        ticks: {
                            color: '#6F6C84',
                            font: { family: 'Inter', size: 11 },
                            callback: (val) => val + 'h'
                        }
                    }
                }
            };
        }
    };
}
</script>
<script>
function adminMonitoringApp() {
    return {
        tab: 'sudah',
        map: null,
        init() {
            setTimeout(() => {
                this.initMap();
            }, 100);
        },
        initMap() {
            if (!document.getElementById('adminMap')) return;
            const points = @json($mapAbsensi);
            
            let defaultLat = -7.2575;
            let defaultLng = 112.7521;
            
            if (points.length > 0) {
                defaultLat = parseFloat(points[0].latitude);
                defaultLng = parseFloat(points[0].longitude);
            }
            
            this.map = L.map('adminMap').setView([defaultLat, defaultLng], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(this.map);
            
            points.forEach(p => {
                const lat = parseFloat(p.latitude);
                const lng = parseFloat(p.longitude);
                if (lat && lng) {
                    const statusText = p.status.toUpperCase();
                    const popupContent = `
                        <div class="text-xs space-y-1">
                            <p class="font-bold text-gray-900">${p.user ? p.user.name : '-'}</p>
                            <p class="text-gray-500">Shift: ${p.shift || 'Default'}</p>
                            <p class="text-gray-500">Masuk: ${p.jam_masuk || '-'}</p>
                            <p class="text-gray-500">Keluar: ${p.jam_keluar || 'Aktif'}</p>
                            <p class="font-semibold text-blue-600">Jarak: ${Math.round(p.distance)}m</p>
                        </div>
                    `;
                    L.marker([lat, lng]).addTo(this.map).bindPopup(popupContent);
                }
            });
        }
    }
}
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    const chartData = @json($chartData);
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.map(d => d.date),
            datasets: [
                { label: 'Hadir', data: chartData.map(d => d.hadir), backgroundColor: '#16A34A', borderRadius: 6 },
                { label: 'Izin', data: chartData.map(d => d.izin), backgroundColor: '#6D5DFC', borderRadius: 6 },
                { label: 'Sakit', data: chartData.map(d => d.sakit), backgroundColor: '#EA580C', borderRadius: 6 },
                { label: 'Alpha', data: chartData.map(d => d.alpha), backgroundColor: '#DC2626', borderRadius: 6 },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { 
                    position: 'bottom',
                    labels: {
                        font: { family: 'Inter', size: 12 },
                        color: '#6F6C84'
                    }
                } 
            },
            scales: { 
                x: { 
                    stacked: true,
                    grid: { display: false },
                    ticks: { color: '#6F6C84', font: { family: 'Inter' } }
                }, 
                y: { 
                    stacked: true, 
                    beginAtZero: true,
                    grid: { color: '#E6E6F0' },
                    ticks: { color: '#6F6C84', font: { family: 'Inter' } }
                } 
            }
        }
    });
});
</script>
@endpush
@endsection
