@extends('layouts.admin')
@section('title', 'Rekap Absensi')
@section('header', 'Rekap Absensi')

@section('content')
<div class="space-y-6 animate-fade-in-up">

    {{-- ===== FILTER & CONTROLS ===== --}}
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-5">
        <form method="GET" action="{{ route('rekap.absensi') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-admin-slate uppercase tracking-widest">Bulan</label>
                <select name="month"
                        class="px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25 min-w-[140px]">
                    @foreach($months as $num => $label)
                        <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold text-admin-slate uppercase tracking-widest">Tahun</label>
                <select name="year"
                        class="px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" name="tab" value="{{ $tab }}">
            <button type="submit"
                    class="px-5 py-2.5 bg-admin-indigo text-white rounded-admin-md text-sm font-semibold hover:bg-admin-indigo-deep transition-colors duration-150">
                Tampilkan
            </button>
            <a href="{{ route('holidays.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 border border-admin-border bg-admin-canvas text-admin-slate rounded-admin-md text-sm font-semibold hover:bg-admin-surface hover:text-admin-ink transition-colors duration-150">
                <i data-lucide="calendar-off" class="w-4 h-4"></i>
                Hari Libur
            </a>
        </form>
    </div>

    {{-- ===== TABS ===== --}}
    <div class="flex items-center justify-between gap-4">
        <div class="flex gap-1 p-1 bg-admin-canvas rounded-admin-md border border-admin-border">
            @foreach(['siswa' => 'Siswa', 'sensei' => 'Sensei', 'karyawan' => 'Karyawan'] as $tabKey => $tabLabel)
                <a href="{{ route('rekap.absensi', ['tab' => $tabKey, 'month' => $month, 'year' => $year]) }}"
                   class="px-5 py-2 rounded-[8px] text-sm font-semibold transition-all duration-150
                          {{ $tab === $tabKey ? 'bg-admin-surface text-admin-ink shadow-sm' : 'text-admin-slate hover:text-admin-ink' }}">
                    {{ $tabLabel }}
                </a>
            @endforeach
        </div>

        {{-- Legend --}}
        <div class="hidden lg:flex items-center gap-4 text-xs text-admin-slate">
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-[#DCFCE7] border border-[#16A34A]/30 inline-block"></span>Hadir
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-[#FEF3C7] border border-[#D97706]/30 inline-block"></span>Terlambat
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-[#DBEAFE] border border-[#2563EB]/30 inline-block"></span>Izin
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-[#FFEDD5] border border-[#EA580C]/30 inline-block"></span>Sakit
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-[#FEE2E2] border border-[#DC2626]/30 inline-block"></span>Alpha
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-admin-canvas border border-admin-border inline-block"></span>Libur
            </span>
        </div>
    </div>

    {{-- ===== SPREADSHEET TABLE ===== --}}
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg overflow-hidden">

        {{-- Table header bar --}}
        <div class="px-6 py-4 border-b border-admin-border flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-admin-ink">
                    Rekap
                    @if($tab === 'siswa') Siswa @elseif($tab === 'sensei') Sensei @else Karyawan @endif
                </h3>
                <p class="text-xs text-admin-slate mt-0.5">{{ $months[$month] }} {{ $year }} &bull; {{ $daysInMonth }} hari</p>
            </div>
            <div class="flex items-center gap-2 text-xs text-admin-slate">
                <i data-lucide="info" class="w-3.5 h-3.5"></i>
                <span>Hover sel Sakit / Izin untuk keterangan</span>
            </div>
        </div>

        @if(count($usersData) === 0)
            <div class="flex flex-col items-center justify-center py-20 text-center text-admin-mist">
                <div class="w-12 h-12 rounded-admin-lg bg-admin-canvas border border-admin-border flex items-center justify-center mb-4">
                    <i data-lucide="table" class="w-6 h-6"></i>
                </div>
                <p class="text-sm font-medium text-admin-slate">Tidak ada data untuk periode ini.</p>
                <p class="text-xs mt-1">Belum ada
                    @if($tab === 'siswa') siswa @elseif($tab === 'sensei') sensei @else karyawan @endif
                    yang terdaftar.
                </p>
            </div>
        @else
            <div class="overflow-x-auto" style="max-height: 72vh; overflow-y: auto;">
                <table style="min-width: max-content; border-collapse: collapse;">

                    {{-- ======= THEAD ======= --}}
                    <thead class="sticky top-0 z-20">
                        <tr style="background: #F6F6FB; border-bottom: 1px solid #E6E6F0;">

                            {{-- Sticky name header --}}
                            <th style="
                                position: sticky; left: 0; z-index: 30;
                                background: #F6F6FB;
                                border-right: 1px solid #E6E6F0;
                                padding: 12px 20px;
                                text-align: left;
                                white-space: nowrap;
                                min-width: 210px;
                                font-size: 11px;
                                font-weight: 600;
                                text-transform: uppercase;
                                letter-spacing: 0.06em;
                                color: #6F6C84;
                            ">Nama</th>

                            {{-- Day headers --}}
                            @foreach($days as $dayInfo)
                                @php
                                    $isHoliday = $dayInfo['is_holiday'];
                                    $isToday   = $dayInfo['is_today'];
                                    $dayOfWeek = \Carbon\Carbon::parse($dayInfo['date'])->locale('id')->isoFormat('ddd');
                                    $isWeekend = in_array(\Carbon\Carbon::parse($dayInfo['date'])->dayOfWeek, [0, 6]);
                                @endphp
                                <th title="{{ $isHoliday ? ($dayInfo['holiday_label'] ?? 'Hari Libur') : $dayOfWeek }}"
                                    style="
                                        border-right: 1px solid #E6E6F0;
                                        padding: 8px 4px;
                                        text-align: center;
                                        min-width: 60px;
                                        max-width: 60px;
                                        {{ $isHoliday ? 'background: #F1F5F9; color: #94A3B8;' : ($isToday ? 'background: #EFEBFF; color: #6D5DFC;' : ($isWeekend ? 'background: #FAFAFA; color: #ABA8BD;' : 'background: #F6F6FB; color: #6F6C84;')) }}
                                    ">
                                    <div style="font-size: 13px; font-weight: 700; line-height: 1.2;">{{ $dayInfo['day'] }}</div>
                                    <div style="font-size: 10px; font-weight: 400; margin-top: 2px; opacity: 0.8;">{{ $dayOfWeek }}</div>
                                    @if($isHoliday)
                                        <div style="font-size: 9px; margin-top: 2px; color: #94A3B8; font-weight: 500;">LIBUR</div>
                                    @endif
                                </th>
                            @endforeach

                            {{-- Summary headers --}}
                            @foreach(['Hadir' => '#16A34A', 'Telat' => '#D97706', 'Izin' => '#2563EB', 'Sakit' => '#EA580C', 'Alpha' => '#DC2626'] as $label => $color)
                                <th style="
                                    border-left: 2px solid #E6E6F0;
                                    border-right: 1px solid #E6E6F0;
                                    padding: 12px 14px;
                                    text-align: center;
                                    white-space: nowrap;
                                    background: #F6F6FB;
                                    font-size: 11px;
                                    font-weight: 600;
                                    text-transform: uppercase;
                                    letter-spacing: 0.04em;
                                    color: {{ $color }};
                                ">{{ $label }}</th>
                            @endforeach
                        </tr>
                    </thead>

                    {{-- ======= TBODY ======= --}}
                    <tbody>
                        @foreach($usersData as $idx => $row)

                            {{-- Main data row --}}
                            <tr style="{{ $idx % 2 === 0 ? 'background: #FFFFFF;' : 'background: #FDFDFFF2;' }} border-bottom: 1px solid #F0F0F8;">

                                {{-- Sticky name cell --}}
                                <td style="
                                    position: sticky; left: 0; z-index: 10;
                                    border-right: 1px solid #E6E6F0;
                                    padding: 10px 20px;
                                    background: inherit;
                                    min-width: 210px;
                                ">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <img src="{{ $row['user']->foto_url }}"
                                             style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1px solid #E6E6F0; flex-shrink: 0;"
                                             alt="">
                                        <div style="min-width: 0;">
                                            <p style="font-size: 12px; font-weight: 600; color: #15131F; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px;">
                                                {{ $row['user']->name }}
                                            </p>
                                            <p style="font-size: 10px; color: #6F6C84; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px; margin-top: 1px;">
                                                {{ $row['meta']['sub_label'] ?? '' }}
                                            </p>
                                            @if(!empty($row['meta']['nis']))
                                                <p style="font-size: 9px; color: #ABA8BD; font-family: monospace; margin-top: 1px;">NIS {{ $row['meta']['nis'] }}</p>
                                            @elseif(!empty($row['meta']['nik']))
                                                <p style="font-size: 9px; color: #ABA8BD; font-family: monospace; margin-top: 1px;">NIK {{ $row['meta']['nik'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Day cells --}}
                                @foreach($days as $dayInfo)
                                    @php
                                        $dateStr = $dayInfo['date'];
                                        $cell    = $row['cells'][$dateStr] ?? ['type' => 'empty', 'label' => '', 'sub' => null];
                                        $type    = $cell['type'];
                                        $label   = $cell['label'];
                                        $sub     = $cell['sub'];
                                        $isToday = $dayInfo['is_today'];
                                        $isWeekend = in_array(\Carbon\Carbon::parse($dayInfo['date'])->dayOfWeek, [0, 6]);

                                        [$bgCell, $textCell, $borderCell] = match($type) {
                                            'hadir'     => ['#F0FDF4', '#15803D', '#BBF7D0'],
                                            'terlambat' => ['#FFFBEB', '#B45309', '#FDE68A'],
                                            'izin'      => ['#EFF6FF', '#1D4ED8', '#BFDBFE'],
                                            'sakit'     => ['#FFF7ED', '#C2410C', '#FED7AA'],
                                            'alpha'     => ['#FEF2F2', '#B91C1C', '#FECACA'],
                                            'libur'     => ['#F8FAFC', '#94A3B8', '#E2E8F0'],
                                            default     => [$isToday ? '#EFEBFF' : ($isWeekend ? '#FAFAFA' : '#FFFFFF'), '#ABA8BD', 'transparent'],
                                        };
                                    @endphp
                                    <td title="{{ $sub ?? '' }}"
                                        style="
                                            border-right: 1px solid #E6E6F0;
                                            padding: 4px;
                                            text-align: center;
                                            vertical-align: middle;
                                            min-width: 60px;
                                            max-width: 60px;
                                        ">
                                        <div style="
                                            background: {{ $bgCell }};
                                            border: 1px solid {{ $borderCell }};
                                            border-radius: 6px;
                                            padding: 5px 2px;
                                            min-height: 38px;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            position: relative;
                                            {{ ($type === 'sakit' || $type === 'izin') && $sub ? 'cursor: help;' : '' }}
                                        ">
                                            @if($type === 'hadir' || $type === 'terlambat')
                                                <span style="font-family: monospace; font-size: 8.5px; font-weight: 700; color: {{ $textCell }}; line-height: 1.3;">
                                                    {{ $label }}
                                                </span>
                                            @elseif($type === 'empty')
                                                <span style="color: #E2E8F0; font-size: 12px;">—</span>
                                            @else
                                                <span style="font-size: 10px; font-weight: 600; color: {{ $textCell }};">
                                                    {{ $label }}
                                                    @if($sub && ($type === 'sakit' || $type === 'izin'))
                                                        <span style="font-size: 8px; opacity: 0.6; display: block; margin-top: 1px;">ⓘ</span>
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                @endforeach

                                {{-- Summary cells --}}
                                @foreach([
                                    ['hadir',     '#16A34A', '#F0FDF4'],
                                    ['terlambat', '#D97706', '#FFFBEB'],
                                    ['izin',      '#2563EB', '#EFF6FF'],
                                    ['sakit',     '#EA580C', '#FFF7ED'],
                                    ['alpha',     '#DC2626', '#FEF2F2'],
                                ] as [$key, $color, $bg])
                                    <td style="
                                        border-left: {{ $key === 'hadir' ? '2px' : '1px' }} solid #E6E6F0;
                                        border-right: 1px solid #E6E6F0;
                                        padding: 10px 14px;
                                        text-align: center;
                                        background: {{ $row['summary'][$key] > 0 ? $bg : '#FFFFFF' }};
                                    ">
                                        <span style="font-size: 13px; font-weight: 700; color: {{ $row['summary'][$key] > 0 ? $color : '#ABA8BD' }};">
                                            {{ $row['summary'][$key] }}
                                        </span>
                                    </td>
                                @endforeach
                            </tr>

                            {{-- Remarks row (hanya jika ada catatan sakit/izin) --}}
                            @if(count($row['remarks']) > 0)
                                <tr style="background: #FFFBF0; border-bottom: 1px solid #FEF3C7;">
                                    <td style="
                                        position: sticky; left: 0; z-index: 10;
                                        border-right: 1px solid #E6E6F0;
                                        padding: 6px 20px;
                                        background: #FFFBF0;
                                    ">
                                        <span style="font-size: 10px; font-weight: 600; color: #B45309; text-transform: uppercase; letter-spacing: 0.05em;">
                                            Keterangan
                                        </span>
                                    </td>
                                    <td colspan="{{ $daysInMonth + 5 }}" style="
                                        padding: 6px 12px;
                                    ">
                                        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                            @foreach($row['remarks'] as $remark)
                                                <span style="
                                                    display: inline-flex;
                                                    align-items: center;
                                                    gap: 5px;
                                                    padding: 3px 10px;
                                                    border-radius: 999px;
                                                    font-size: 11px;
                                                    font-weight: 500;
                                                    {{ $remark['tipe'] === 'Sakit'
                                                        ? 'background: #FFEDD5; color: #C2410C; border: 1px solid #FED7AA;'
                                                        : 'background: #DBEAFE; color: #1D4ED8; border: 1px solid #BFDBFE;'
                                                    }}
                                                ">
                                                    <span style="font-weight: 700;">{{ \Carbon\Carbon::parse($remark['tanggal'])->format('d/m') }}</span>
                                                    <span style="opacity: 0.6;">[{{ $remark['tipe'] }}]</span>
                                                    {{ $remark['catatan'] }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endif

                        @endforeach
                    </tbody>

                </table>
            </div>
        @endif
    </div>

    {{-- Footer --}}
    <div class="flex items-center justify-between text-xs text-admin-mist">
        <div class="lg:hidden flex flex-wrap items-center gap-3">
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-[#DCFCE7] border border-[#16A34A]/30 inline-block"></span>Hadir</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-[#FEF3C7] border border-[#D97706]/30 inline-block"></span>Terlambat</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-[#DBEAFE] border border-[#2563EB]/30 inline-block"></span>Izin</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-[#FFEDD5] border border-[#EA580C]/30 inline-block"></span>Sakit</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-[#FEE2E2] border border-[#DC2626]/30 inline-block"></span>Alpha</span>
        </div>
        <p class="ml-auto">
            Periode:
            <span class="font-semibold text-admin-slate">{{ $months[$month] }} {{ $year }}</span>
            &bull; {{ $daysInMonth }} hari
            @if(count($holidayMap) > 0)
                &bull; <span class="font-semibold text-admin-slate">{{ count($holidayMap) }}</span> hari libur
            @endif
        </p>
    </div>

</div>
@endsection
