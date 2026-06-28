@extends('layouts.admin')
@section('title', 'Laporan')
@section('header', 'Laporan')
@section('content')
<div class="space-y-6 animate-fade-in-up">
    <!-- Page Header & Actions -->
    <div class="flex items-center justify-between flex-wrap gap-4">
        <h2 class="text-lg font-semibold text-admin-ink">Laporan & Rekap Kehadiran</h2>
        @if($tab !== 'payroll')
            <div class="flex gap-2">
                <a href="{{ route('report.export-excel', request()->query()) }}" class="px-4 py-2 bg-admin-indigo text-white rounded-admin-md text-sm font-semibold hover:bg-admin-indigo-deep transition-colors">
                    Export Excel
                </a>
                <a href="{{ route('report.export-pdf', request()->query()) }}" class="px-4 py-2 bg-admin-indigo text-white rounded-admin-md text-sm font-semibold hover:bg-admin-indigo-deep transition-colors">
                    Export PDF
                </a>
            </div>
        @endif
    </div>

    <!-- Tab Navigation -->
    <div class="bg-admin-canvas p-1 rounded-admin-md flex items-center border border-admin-border w-fit">
        <a href="{{ route('report.index', array_merge(request()->query(), ['tab' => 'attendance'])) }}" 
           class="px-4 py-1.5 rounded-[8px] text-[13px] transition-all duration-150 {{ $tab !== 'payroll' ? 'bg-admin-surface text-admin-ink font-semibold' : 'text-admin-slate hover:text-admin-ink' }}">
            Laporan Kehadiran
        </a>
        <a href="{{ route('report.index', array_merge(request()->query(), ['tab' => 'payroll'])) }}" 
           class="px-4 py-1.5 rounded-[8px] text-[13px] transition-all duration-150 {{ $tab === 'payroll' ? 'bg-admin-surface text-admin-ink font-semibold' : 'text-admin-slate hover:text-admin-ink' }}">
            Rekap Payroll Karyawan
        </a>
    </div>

    @if($tab !== 'payroll')
        <!-- Search & Filter Card (Attendance Mode) -->
        <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-5">
            <form class="flex flex-wrap items-center gap-6" id="filterForm">
                <!-- Segmented Control for Period -->
                <div class="bg-admin-canvas rounded-admin-md p-1 flex items-center border border-admin-border" x-data="{ period: '{{ request('period', 'month') }}' }">
                    <input type="hidden" name="period" :value="period">
                    <button type="submit" @click="period = 'week'" :class="period === 'week' ? 'bg-admin-surface text-admin-ink font-semibold' : 'text-admin-slate hover:text-admin-ink'" class="px-4 py-1.5 rounded-[8px] text-[13px] transition-all duration-150">
                        Mingguan
                    </button>
                    <button type="submit" @click="period = 'month'" :class="period === 'month' ? 'bg-admin-surface text-admin-ink font-semibold' : 'text-admin-slate hover:text-admin-ink'" class="px-4 py-1.5 rounded-[8px] text-[13px] transition-all duration-150">
                        Bulanan
                    </button>
                    <button type="submit" @click="period = 'year'" :class="period === 'year' ? 'bg-admin-surface text-admin-ink font-semibold' : 'text-admin-slate hover:text-admin-ink'" class="px-4 py-1.5 rounded-[8px] text-[13px] transition-all duration-150">
                        Tahunan
                    </button>
                </div>

                <!-- Role Select -->
                <select name="role" onchange="document.getElementById('filterForm').submit()" 
                        class="px-4 py-2 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-slate focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <option value="">Semua Role</option>
                    @foreach(['siswa','karyawan','sensei'] as $r)
                        <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>

                <!-- Class Filter (Only shown for Siswa) -->
                @if(request('role') === 'siswa')
                    <select name="kelas_id" onchange="document.getElementById('filterForm').submit()" 
                            class="px-4 py-2 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-slate focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                @endif
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-4">
            <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-4 text-center">
                <p class="text-2xl font-bold text-admin-success leading-none">{{ $totalHadir }}</p>
                <p class="text-xs text-admin-slate mt-1.5">Hadir</p>
            </div>
            <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-4 text-center">
                <p class="text-2xl font-bold text-amber-600 leading-none">{{ $totalTerlambat }}</p>
                <p class="text-xs text-admin-slate mt-1.5">Terlambat</p>
            </div>
            <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-4 text-center">
                <p class="text-2xl font-bold text-admin-indigo leading-none">{{ $totalIzin }}</p>
                <p class="text-xs text-admin-slate mt-1.5">Izin</p>
            </div>
            <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-4 text-center">
                <p class="text-2xl font-bold text-orange-600 leading-none">{{ $totalSakit }}</p>
                <p class="text-xs text-admin-slate mt-1.5">Sakit</p>
            </div>
            <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-4 text-center">
                <p class="text-2xl font-bold text-admin-danger leading-none">{{ $totalAlpha }}</p>
                <p class="text-xs text-admin-slate mt-1.5">Alpha</p>
            </div>
            <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-4 text-center bg-amber-50/80">
                <p class="text-2xl font-bold text-amber-700 leading-none">{{ $totalLembur }}</p>
                <p class="text-xs text-admin-slate mt-1.5">Lembur</p>
                <p class="text-[10px] text-amber-600 mt-0.5">{{ number_format($totalDurasiLembur, 1) }} jam</p>
            </div>
            <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-4 text-center bg-admin-indigo-tint/30">
                <p class="text-2xl font-bold text-admin-indigo leading-none">{{ $persentase }}%</p>
                <p class="text-xs text-admin-slate mt-1.5">Kehadiran</p>
            </div>
        </div>

        <!-- Report Table Card -->
        <div class="bg-admin-surface border border-admin-border rounded-admin-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-admin-border bg-admin-canvas/30">
                            <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Nama</th>
                            <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Tanggal</th>
                            <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Masuk</th>
                            <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Keluar</th>
                            <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate text-right">Status</th>
                            <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate text-right">Lembur</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y border-admin-border">
                        @forelse($absensi as $a)
                            <tr class="hover:bg-admin-canvas/30 transition-colors">
                                <td class="py-4 px-6 text-sm font-semibold text-admin-ink">
                                    {{ $a->user->name ?? '-' }}
                                    @if($a->user && $a->user->role === 'siswa' && $a->user->siswa && $a->user->siswa->kelas)
                                        <span class="block text-[10px] text-admin-slate font-normal">{{ $a->user->siswa->kelas->nama_kelas }}</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-sm text-admin-slate">{{ $a->tanggal->format('d/m/Y') }}</td>
                                <td class="py-4 px-6 text-sm text-admin-ink font-mono">{{ $a->jam_masuk ?? '-' }}</td>
                                <td class="py-4 px-6 text-sm text-admin-ink font-mono">{{ $a->jam_keluar ?? '-' }}</td>
                                <td class="py-4 px-6 text-right">
                                    <span class="px-2.5 py-0.5 rounded-admin-full text-xs font-semibold border
                                        {{ $a->status === 'hadir' ? 'bg-admin-success-tint text-admin-success border-admin-success/20' :
                                           ($a->status === 'terlambat' ? 'bg-amber-50 text-amber-700 border-amber-200' :
                                           ($a->status === 'alpha' ? 'bg-admin-danger-tint text-admin-danger border-admin-danger/20' :
                                           'bg-admin-indigo-tint text-admin-indigo border-admin-indigo/20')) }}">
                                        {{ ucfirst($a->status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right text-xs font-mono">
                                    @if($a->is_lembur && $a->durasi_lembur)
                                        <span class="px-2 py-0.5 rounded-admin-full font-semibold bg-amber-100 text-amber-700 border border-amber-200">
                                            {{ $a->durasi_lembur_formatted }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-admin-mist">
                                    <i data-lucide="file-spreadsheet" class="w-10 h-10 mx-auto mb-2 opacity-40"></i>
                                    <p class="text-sm">Tidak ada data laporan</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Area -->
            @if($absensi->hasPages())
                <div class="px-6 py-4 border-t border-admin-border bg-admin-canvas/10">
                    {{ $absensi->links() }}
                </div>
            @endif
        </div>
    @else
        <!-- Payroll Recap Mode -->
        <!-- Payroll Filter Card -->
        <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-5">
            <form class="flex flex-wrap items-center gap-4" id="payrollForm">
                <input type="hidden" name="tab" value="payroll">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-admin-slate uppercase tracking-wider">Periode Payroll</span>
                </div>
                <select name="month" onchange="document.getElementById('payrollForm').submit()" 
                        class="px-4 py-2 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-slate focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $mNum => $mName)
                        <option value="{{ $mNum }}" {{ request('month', now()->month) == $mNum ? 'selected' : '' }}>{{ $mName }}</option>
                    @endforeach
                </select>
                <select name="year" onchange="document.getElementById('payrollForm').submit()" 
                        class="px-4 py-2 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-slate focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    @foreach(range(now()->year - 2, now()->year + 1) as $y)
                        <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- Payroll Recap Table Card -->
        <div class="bg-admin-surface border border-admin-border rounded-admin-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-admin-border bg-admin-canvas/30">
                            <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Karyawan</th>
                            <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">NIK</th>
                            <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Jabatan/Divisi</th>
                            <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate text-center">Hadir</th>
                            <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate text-center">Telat</th>
                            <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate text-center">Izin/Sakit/Cuti</th>
                            <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate text-center">Lembur</th>
                            <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate text-right">Jam Kerja</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y border-admin-border">
                        @forelse($payrollRecap as $pr)
                            <tr class="hover:bg-admin-canvas/30 transition-colors">
                                <td class="py-4 px-6 text-sm font-semibold text-admin-ink">{{ $pr['karyawan']->user->name ?? '-' }}</td>
                                <td class="py-4 px-6 text-sm text-admin-slate font-mono">{{ $pr['karyawan']->nik ?? '-' }}</td>
                                <td class="py-4 px-6 text-sm text-admin-slate">{{ $pr['karyawan']->jabatan ?? '-' }} / {{ $pr['karyawan']->divisi ?? '-' }}</td>
                                <td class="py-4 px-6 text-sm text-admin-ink text-center">{{ $pr['present'] }} hari</td>
                                <td class="py-4 px-6 text-sm text-amber-600 text-center font-semibold">{{ $pr['late'] }} kali</td>
                                <td class="py-4 px-6 text-sm text-admin-indigo text-center">
                                    {{ $pr['izin'] }} / {{ $pr['sakit'] }} / {{ $pr['cuti'] }}
                                </td>
                                <td class="py-4 px-6 text-sm text-center text-amber-700">
                                    {{ $pr['lembur_count'] }} kali <span class="block text-[10px] text-admin-slate">({{ number_format($pr['lembur_hours'], 1) }} jam)</span>
                                </td>
                                <td class="py-4 px-6 text-sm text-right font-mono font-semibold text-admin-success">{{ number_format($pr['work_hours'], 1) }} jam</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-admin-mist">
                                    <i data-lucide="calculator" class="w-10 h-10 mx-auto mb-2 opacity-40"></i>
                                    <p class="text-sm">Belum ada data payroll untuk bulan ini</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
