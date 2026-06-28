@extends('layouts.member')
@section('title', 'Riwayat Absensi')
@section('header', 'Riwayat')
@section('content')
@php
    $calStart = \Carbon\Carbon::createFromDate($calYear, $calMonth, 1);
    $daysInMonth = $calStart->daysInMonth;
    $beforeStartDay = $calStart->copy()->startOfMonth()->dayOfWeek;
    $todayStr = now()->format('Y-m-d');
@endphp
<div class="space-y-4 animate-fade-in-up" x-data="{ mode: '{{ request("mode", "calendar") }}', selectedDay: null }">

    <!-- Page Header -->
    <div class="space-y-1">
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Riwayat Absensi</h1>
        <p class="text-sm text-slate-500">Pantau kehadiran Anda</p>
    </div>

    <!-- Mode Switch (Tabs) -->
    <div class="s-tabs">
        <button @click="mode = 'calendar'" class="s-tab" :class="mode === 'calendar' ? 's-tab-active' : 's-tab-inactive'">
            <span class="flex items-center justify-center gap-1.5"><i data-lucide="calendar" class="w-3.5 h-3.5"></i> Kalender</span>
        </button>
        <button @click="mode = 'list'" class="s-tab" :class="mode === 'list' ? 's-tab-active' : 's-tab-inactive'">
            <span class="flex items-center justify-center gap-1.5"><i data-lucide="list" class="w-3.5 h-3.5"></i> Daftar</span>
        </button>
    </div>

    <!-- ==================== MODE: KALENDER ==================== -->
    <div x-show="mode === 'calendar'" class="space-y-4">
        <div class="s-card p-5">
            <!-- Month Nav -->
            <div class="flex items-center justify-between mb-4">
                <a href="{{ route('absensi.riwayat', array_merge(request()->query(), ['mode' => 'calendar', 'cal_month' => $calMonth - 1 < 1 ? 12 : $calMonth - 1, 'cal_year' => $calMonth - 1 < 1 ? $calYear - 1 : $calYear])) }}"
                   class="btn btn-ghost btn-icon btn-sm">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </a>
                <h3 class="text-sm font-semibold text-slate-900 tracking-tight">
                    {{ ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$calMonth - 1] }} {{ $calYear }}
                </h3>
                <a href="{{ route('absensi.riwayat', array_merge(request()->query(), ['mode' => 'calendar', 'cal_month' => $calMonth + 1 > 12 ? 1 : $calMonth + 1, 'cal_year' => $calMonth + 1 > 12 ? $calYear + 1 : $calYear])) }}"
                   class="btn btn-ghost btn-icon btn-sm">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </a>
            </div>

            <!-- Calendar Table -->
            <table class="w-full" style="border-collapse:separate;border-spacing:2px;">
                <thead>
                    <tr>
                        @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $d)
                            <th class="text-center py-2 text-[10px] font-semibold uppercase tracking-wider text-slate-400">{{ $d }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                        $day = 1;
                        $started = false;
                    @endphp
                    @for($week = 0; $week < 6; $week++)
                        @if($day > $daysInMonth) @break @endif
                        <tr>
                            @for($dow = 0; $dow < 7; $dow++)
                                @if($week === 0 && $dow < $beforeStartDay)
                                    <td></td>
                                @elseif($day > $daysInMonth)
                                    <td></td>
                                @else
                                    @php
                                        $key = sprintf('%04d-%02d-%02d', $calYear, $calMonth, $day);
                                        $absen = $monthAbsensi[$key] ?? null;
                                        $isToday = $key === $todayStr;
                                        $isWeekend = in_array(\Carbon\Carbon::parse($key)->dayOfWeek, [0, 6]);
                                        $isPast = $key < $todayStr;
                                    @endphp
                                    <td class="text-center cursor-pointer rounded-lg transition-all duration-200 hover:scale-110"
                                        @click="selectedDay = selectedDay === '{{ $key }}' ? null : '{{ $key }}'"
                                        :class="selectedDay === '{{ $key }}' ? 'ring-2 ring-blue-500 ring-offset-1' : ''">
                                        <div class="aspect-square flex flex-col items-center justify-center rounded-lg text-xs relative transition-colors duration-150"
                                             style="@if($absen && $absen->status === 'hadir') background:#ecfdf5;color:#059669; @elseif($absen && $absen->status === 'terlambat') background:#fffbeb;color:#d97706; @elseif($absen && in_array($absen->status, ['izin','sakit'])) background:#eff6ff;color:#2563eb; @elseif($absen && $absen->status === 'alpha') background:#fef2f2;color:#dc2626; @elseif($isToday) background:#1A6DFF;color:#fff; @elseif($isWeekend) color:#cbd5e1; @else color:#64748b; @endif">
                                            <span class="font-medium {{ $isToday ? 'font-bold' : '' }}">{{ $day }}</span>
                                            @if($absen)
                                                <div class="w-1 h-1 rounded-full absolute bottom-1"
                                                     style="@if($absen->status === 'hadir') background:#059669; @elseif($absen->status === 'terlambat') background:#d97706; @elseif(in_array($absen->status, ['izin','sakit'])) background:#2563eb; @else background:#dc2626; @endif"></div>
                                            @elseif($isPast && !$isWeekend)
                                                <div class="w-1 h-1 rounded-full absolute bottom-1 bg-red-400"></div>
                                            @endif
                                        </div>
                                    </td>
                                    @php $day++; @endphp
                                @endif
                            @endfor
                        </tr>
                    @endfor
                </tbody>
            </table>

            <!-- Legend -->
            <div class="flex items-center justify-center gap-4 mt-4 pt-4 border-t border-slate-100">
                <div class="flex items-center gap-1.5"><div class="w-2 h-2 rounded-full bg-emerald-500"></div><span class="text-[10px] font-medium text-slate-500">Hadir</span></div>
                <div class="flex items-center gap-1.5"><div class="w-2 h-2 rounded-full bg-amber-500"></div><span class="text-[10px] font-medium text-slate-500">Terlambat</span></div>
                <div class="flex items-center gap-1.5"><div class="w-2 h-2 rounded-full bg-blue-500"></div><span class="text-[10px] font-medium text-slate-500">Izin/Sakit</span></div>
                <div class="flex items-center gap-1.5"><div class="w-2 h-2 rounded-full bg-red-500"></div><span class="text-[10px] font-medium text-slate-500">Alpha</span></div>
            </div>
        </div>

        <!-- Detail Hari (klik tanggal) -->
        <div x-show="selectedDay" x-cloak class="s-card p-5 space-y-4">
            <div class="flex items-center justify-between">
                <h4 class="text-sm font-semibold text-slate-900" x-text="selectedDay"></h4>
                <button @click="selectedDay = null" class="text-xs font-medium text-blue-600 hover:text-blue-700 transition-colors">Tutup</button>
            </div>
            @foreach($monthAbsensi as $dateKey => $absen)
                <div x-show="selectedDay === '{{ $dateKey }}'" class="space-y-3">
                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
                        <span class="text-xs text-slate-500">Status</span>
                        <span class="s-badge
                            @if($absen->status === 'hadir') s-badge-success
                            @elseif($absen->status === 'terlambat') s-badge-warning
                            @elseif(in_array($absen->status, ['izin','sakit'])) s-badge-info
                            @else s-badge-danger
                            @endif">
                            {{ ucfirst($absen->status) }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="p-3 rounded-lg bg-slate-50">
                            <p class="text-[10px] uppercase tracking-wider font-medium text-slate-400">Masuk</p>
                            <p class="text-sm font-semibold text-slate-900 font-mono mt-1">{{ $absen->jam_masuk ? substr($absen->jam_masuk, 0, 5) : '--:--' }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-slate-50">
                            <p class="text-[10px] uppercase tracking-wider font-medium text-slate-400">Keluar</p>
                            <p class="text-sm font-semibold text-slate-900 font-mono mt-1">{{ $absen->jam_keluar ? substr($absen->jam_keluar, 0, 5) : '--:--' }}</p>
                        </div>
                    </div>
                    @if($absen->catatan)
                        <div class="p-3 rounded-lg bg-slate-50">
                            <p class="text-[10px] uppercase tracking-wider font-medium text-slate-400">Keterangan</p>
                            <p class="text-xs text-slate-700 mt-1 leading-relaxed">{{ $absen->catatan }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
            <!-- Empty state for days without absen -->
            <div x-show="selectedDay" class="text-center py-6">
                <i data-lucide="calendar-x" class="w-8 h-8 text-slate-200 mx-auto mb-2"></i>
                <p class="text-xs text-slate-400">Tidak ada data absensi</p>
            </div>
        </div>

        <!-- Ringkasan -->
        @php
            $hadirCount = $monthAbsensi->where('status', 'hadir')->count();
            $terlambatCount = $monthAbsensi->where('status', 'terlambat')->count();
            $izinCount = $monthAbsensi->whereIn('status', ['izin', 'sakit'])->count();
            $alphaCount = $monthAbsensi->where('status', 'alpha')->count();
        @endphp
        <div class="s-card p-5">
            <h4 class="text-sm font-semibold text-slate-900 mb-4">Ringkasan Bulan Ini</h4>
            <div class="grid grid-cols-4 gap-2">
                <div class="stat-card bg-emerald-50">
                    <p class="text-2xl font-bold text-emerald-600 tracking-tight">{{ $hadirCount }}</p>
                    <p class="text-[10px] font-medium text-emerald-700 mt-1">Hadir</p>
                </div>
                <div class="stat-card bg-amber-50">
                    <p class="text-2xl font-bold text-amber-600 tracking-tight">{{ $terlambatCount }}</p>
                    <p class="text-[10px] font-medium text-amber-700 mt-1">Terlambat</p>
                </div>
                <div class="stat-card bg-blue-50">
                    <p class="text-2xl font-bold text-blue-600 tracking-tight">{{ $izinCount }}</p>
                    <p class="text-[10px] font-medium text-blue-700 mt-1">Izin</p>
                </div>
                <div class="stat-card bg-red-50">
                    <p class="text-2xl font-bold text-red-600 tracking-tight">{{ $alphaCount }}</p>
                    <p class="text-[10px] font-medium text-red-700 mt-1">Alpha</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== MODE: LIST ==================== -->
    <div x-show="mode === 'list'" class="space-y-4">
        <form class="flex gap-2 items-center" method="GET" action="{{ route('absensi.riwayat') }}">
            <input type="hidden" name="mode" value="list">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari riwayat..."
                       class="s-input pl-9">
            </div>
            <select name="period" class="s-select w-auto">
                <option value="">Semua</option>
                <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Minggu Ini</option>
                <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>Tahun Ini</option>
            </select>
            <button type="submit" class="btn btn-primary btn-icon">
                <i data-lucide="search" class="w-4 h-4"></i>
            </button>
        </form>

        <div class="space-y-3">
            @forelse($riwayat as $r)
                @php
                    $badgeClass = match ($r->status) {
                        'hadir' => 's-badge-success',
                        'terlambat' => 's-badge-warning',
                        'izin' => 's-badge-info',
                        'sakit' => 's-badge-danger',
                        'alpha' => 's-badge-danger',
                        default => 's-badge-default',
                    };
                @endphp
                <div class="s-card p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center
                                @if($r->status === 'hadir') bg-emerald-50
                                @elseif($r->status === 'terlambat') bg-amber-50
                                @elseif(in_array($r->status, ['izin','sakit'])) bg-blue-50
                                @else bg-red-50 @endif">
                                <i data-lucide="@if($r->status === 'hadir') check-circle-2 @elseif($r->status === 'terlambat') clock @elseif($r->status === 'izin') file-text @elseif($r->status === 'sakit') heart-pulse @else x-circle @endif"
                                   class="w-4 h-4
                                   @if($r->status === 'hadir') text-emerald-600
                                   @elseif($r->status === 'terlambat') text-amber-600
                                   @elseif(in_array($r->status, ['izin','sakit'])) text-blue-600
                                   @else text-red-500 @endif"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $r->tanggal->translatedFormat('d M Y') }}</p>
                                <p class="text-[10px] text-slate-400 font-medium">Shift {{ $r->shift == 'pagi' ? 'Pagi' : 'Siang' }}</p>
                            </div>
                        </div>
                        <span class="s-badge {{ $badgeClass }}">{{ ucfirst($r->status) }}</span>
                    </div>
                    <div class="s-separator mb-3"></div>
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 text-[10px] uppercase tracking-wider font-medium">Jam Masuk</span>
                            <span class="block font-semibold text-slate-900 font-mono mt-0.5">{{ $r->jam_masuk ? substr($r->jam_masuk, 0, 5) : '--:--' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] uppercase tracking-wider font-medium">Jam Keluar</span>
                            <span class="block font-semibold text-slate-900 font-mono mt-0.5">{{ $r->jam_keluar ? substr($r->jam_keluar, 0, 5) : '--:--' }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="s-card p-10 text-center">
                    <i data-lucide="calendar-x" class="w-10 h-10 text-slate-200 mx-auto mb-3"></i>
                    <p class="text-sm text-slate-400 font-medium">Belum ada riwayat absensi</p>
                </div>
            @endforelse
        </div>
        <div>{{ $riwayat->links() }}</div>
    </div>
</div>
@endsection
