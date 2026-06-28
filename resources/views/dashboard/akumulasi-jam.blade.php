@extends('layouts.admin')
@section('title', 'Akumulasi Jam Kerja')
@section('header', 'Akumulasi Jam Kerja')

@section('content')
@php
    $totalHours = $records->sum('total_hours');
    $totalDays = $records->sum('total_days');
    $avgHours = $records->count() > 0 ? $totalHours / $records->count() : 0;
    $maxHours = $records->max('total_hours') ?: 1;

    $barColors = [
        'karyawan' => '#3B82F6',
        'sensei'   => '#8B5CF6',
        'siswa'    => '#10B981',
    ];
    $defaultBarColor = '#6D5DFC';

    $roleBadgeBg = [
        'karyawan' => '#DBEAFE',
        'sensei'   => '#EDE9FE',
        'siswa'    => '#D1FAE5',
    ];
    $roleBadgeFg = [
        'karyawan' => '#1D4ED8',
        'sensei'   => '#6D28D9',
        'siswa'    => '#047857',
    ];

    $rankBgs = ['#FEF3C7', '#E5E7EB', '#FFEDD5'];
@endphp

<div style="display:flex;flex-direction:column;gap:24px;">

    <!-- Top Bar: Period + Role Filter -->
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;">
        <div>
            <h2 style="font-size:18px;font-weight:600;color:#15131F;margin:0;">Akumulasi Jam Kerja</h2>
            <p style="font-size:13px;color:#6F6C84;margin:4px 0 0;">{{ $periodLabel }} &bull; {{ $records->count() }} pengguna</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            {{-- Period --}}
            <div style="display:flex;gap:2px;padding:4px;background:#F6F6FB;border:1px solid #E6E6F0;border-radius:10px;">
                @foreach(['7d' => '7 Hari', '1m' => '1 Bulan', '3m' => '3 Bulan'] as $p => $label)
                    <a href="{{ route('akumulasi-jam', array_filter(['period' => $p, 'role' => $role])) }}"
                       style="padding:6px 12px;border-radius:8px;font-size:12px;font-weight:500;text-decoration:none;transition:all 150ms;{{ $period === $p ? 'background:#FFFFFF;color:#15131F;font-weight:600;box-shadow:0 1px 3px rgba(0,0,0,0.08);' : 'color:#6F6C84;' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
            {{-- Role --}}
            <div style="display:flex;gap:2px;padding:4px;background:#F6F6FB;border:1px solid #E6E6F0;border-radius:10px;">
                <a href="{{ route('akumulasi-jam', ['period' => $period]) }}"
                   style="padding:6px 12px;border-radius:8px;font-size:12px;font-weight:500;text-decoration:none;transition:all 150ms;{{ !$role ? 'background:#FFFFFF;color:#15131F;font-weight:600;box-shadow:0 1px 3px rgba(0,0,0,0.08);' : 'color:#6F6C84;' }}">
                    Semua
                </a>
                @foreach(['karyawan' => 'Karyawan', 'sensei' => 'Sensei', 'siswa' => 'Siswa'] as $r => $label)
                    <a href="{{ route('akumulasi-jam', ['period' => $period, 'role' => $r]) }}"
                       style="padding:6px 12px;border-radius:8px;font-size:12px;font-weight:500;text-decoration:none;transition:all 150ms;{{ $role === $r ? 'background:#FFFFFF;color:#15131F;font-weight:600;box-shadow:0 1px 3px rgba(0,0,0,0.08);' : 'color:#6F6C84;' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;">
        <div style="background:#FFFFFF;border:1px solid #E6E6F0;border-radius:16px;padding:20px;display:flex;align-items:center;gap:16px;">
            <div style="width:40px;height:40px;background:#EFEBFF;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i data-lucide="users" style="width:20px;height:20px;color:#6D5DFC;"></i>
            </div>
            <div>
                <p style="font-size:28px;font-weight:700;color:#15131F;margin:0;line-height:1;">{{ $records->count() }}</p>
                <p style="font-size:11px;color:#6F6C84;margin:4px 0 0;">Pengguna Aktif</p>
            </div>
        </div>
        <div style="background:#FFFFFF;border:1px solid #E6E6F0;border-radius:16px;padding:20px;display:flex;align-items:center;gap:16px;">
            <div style="width:40px;height:40px;background:#D1FAE5;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i data-lucide="clock" style="width:20px;height:20px;color:#059669;"></i>
            </div>
            <div>
                <p style="font-size:28px;font-weight:700;color:#15131F;margin:0;line-height:1;">{{ number_format($totalHours, 1) }}</p>
                <p style="font-size:11px;color:#6F6C84;margin:4px 0 0;">Total Jam Kerja</p>
            </div>
        </div>
        <div style="background:#FFFFFF;border:1px solid #E6E6F0;border-radius:16px;padding:20px;display:flex;align-items:center;gap:16px;">
            <div style="width:40px;height:40px;background:#FEF3C7;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i data-lucide="trending-up" style="width:20px;height:20px;color:#D97706;"></i>
            </div>
            <div>
                <p style="font-size:28px;font-weight:700;color:#15131F;margin:0;line-height:1;">{{ number_format($avgHours, 1) }}</p>
                <p style="font-size:11px;color:#6F6C84;margin:4px 0 0;">Rata-rata / User</p>
            </div>
        </div>
        <div style="background:#FFFFFF;border:1px solid #E6E6F0;border-radius:16px;padding:20px;display:flex;align-items:center;gap:16px;">
            <div style="width:40px;height:40px;background:#DBEAFE;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i data-lucide="calendar-check" style="width:20px;height:20px;color:#2563EB;"></i>
            </div>
            <div>
                <p style="font-size:28px;font-weight:700;color:#15131F;margin:0;line-height:1;">{{ $totalDays }}</p>
                <p style="font-size:11px;color:#6F6C84;margin:4px 0 0;">Total Hari Hadir</p>
            </div>
        </div>
    </div>

    <!-- Horizontal Bar Chart -->
    <div style="background:#FFFFFF;border:1px solid #E6E6F0;border-radius:16px;padding:24px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
            <h3 style="font-size:16px;font-weight:600;color:#15131F;margin:0;">
                Ranking Jam Kerja
                @if($role)
                    <span style="margin-left:8px;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;background:#EFEBFF;color:#6D5DFC;border:1px solid rgba(109,93,252,0.2);">
                        {{ ucfirst($role) }}
                    </span>
                @endif
            </h3>
            <span style="font-size:12px;color:#6F6C84;">{{ $periodLabel }}</span>
        </div>

        @forelse($records as $index => $record)
            @php
                $percentage = ($record->total_hours / $maxHours) * 100;
                $barWidth = max($percentage, 8);
                $color = $barColors[$record->role] ?? $defaultBarColor;
                $rankBg = $index < 3 ? $rankBgs[$index] : '#F6F6FB';
                $rankFg = $index === 0 ? '#92400E' : ($index === 1 ? '#4B5563' : ($index === 2 ? '#9A3412' : '#6F6C84'));
                $badgeBg = $roleBadgeBg[$record->role] ?? '#F3F4F6';
                $badgeFg = $roleBadgeFg[$record->role] ?? '#6B7280';
                $ringColor = match($record->role) {
                    'karyawan' => '#BFDBFE',
                    'sensei'   => '#DDD6FE',
                    'siswa'    => '#A7F3D0',
                    default    => '#E6E6F0',
                };
                $medal = $index === 0 ? '🥇' : ($index === 1 ? '🥈' : ($index === 2 ? '🥉' : ''));
            @endphp
            <div style="display:flex;align-items:center;gap:16px;padding:14px 0;{{ !$loop->last ? 'border-bottom:1px solid #F0F0F5;' : '' }}">
                <!-- Rank -->
                <div style="width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;background:{{ $rankBg }};color:{{ $rankFg }};flex-shrink:0;">
                    {{ $index + 1 }}
                </div>

                <!-- Avatar + Name -->
                <div style="display:flex;align-items:center;gap:12px;width:220px;min-width:220px;">
                    <div style="position:relative;flex-shrink:0;">
                        <img src="{{ $record->foto ? asset('uploads/foto/' . $record->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($record->user_name) . '&background=random&size=80' }}"
                             style="width:42px;height:42px;border-radius:50%;object-fit:cover;border:2px solid {{ $ringColor }};"
                             alt="{{ $record->user_name }}">
                        @if($medal)
                            <span style="position:absolute;top:-6px;right:-6px;font-size:14px;">{{ $medal }}</span>
                        @endif
                    </div>
                    <div style="min-width:0;">
                        <p style="font-size:13px;font-weight:600;color:#15131F;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $record->user_name }}</p>
                        <span style="display:inline-block;margin-top:2px;padding:1px 6px;border-radius:4px;font-size:10px;font-weight:600;background:{{ $badgeBg }};color:{{ $badgeFg }};">
                            {{ ucfirst($record->role) }}
                        </span>
                    </div>
                </div>

                <!-- Bar -->
                <div style="flex:1;min-width:0;">
                    <div style="position:relative;height:36px;background:#F3F4F6;border-radius:8px;overflow:hidden;border:1px solid #E5E7EB;">
                        <div style="position:absolute;top:0;left:0;bottom:0;width:{{ $barWidth }}%;background:{{ $color }};border-radius:8px;display:flex;align-items:center;justify-content:flex-end;transition:width 0.7s ease-out;">
                            <span style="font-size:11px;font-weight:700;color:#FFFFFF;text-shadow:0 1px 2px rgba(0,0,0,0.2);padding-right:12px;white-space:nowrap;">
                                {{ number_format($record->total_hours, 1) }} jam
                            </span>
                        </div>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-top:4px;padding:0 4px;">
                        <span style="font-size:10px;color:#9CA3AF;">{{ $record->total_days }} hari hadir</span>
                        <span style="font-size:10px;color:#9CA3AF;">{{ number_format($percentage, 0) }}%</span>
                    </div>
                </div>
            </div>
        @empty
            <div style="text-align:center;padding:80px 0;">
                <div style="width:64px;height:64px;background:#F3F4F6;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i data-lucide="clock" style="width:32px;height:32px;color:#D1D5DB;"></i>
                </div>
                <p style="font-size:14px;font-weight:500;color:#6B7280;margin:0;">Tidak ada data jam kerja</p>
                <p style="font-size:12px;color:#9CA3AF;margin:4px 0 0;">Untuk periode {{ strtolower($periodLabel) }}{{ $role ? ' role ' . ucfirst($role) : '' }}</p>
            </div>
        @endforelse
    </div>

    <!-- Table Detail -->
    <div style="background:#FFFFFF;border:1px solid #E6E6F0;border-radius:16px;overflow:hidden;">
        <div style="padding:16px 24px;border-bottom:1px solid #E6E6F0;">
            <h3 style="font-size:16px;font-weight:600;color:#15131F;margin:0;">Detail Akumulasi</h3>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #E6E6F0;background:#FAFAFC;">
                        <th style="padding:14px 24px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#6F6C84;">No</th>
                        <th style="padding:14px 24px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#6F6C84;">Nama</th>
                        <th style="padding:14px 24px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#6F6C84;">Role</th>
                        <th style="padding:14px 24px;text-align:right;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#6F6C84;">Total Jam</th>
                        <th style="padding:14px 24px;text-align:right;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#6F6C84;">Hari Hadir</th>
                        <th style="padding:14px 24px;text-align:right;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#6F6C84;">Rata-rata</th>
                        <th style="padding:14px 24px;text-align:right;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#6F6C84;">Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $index => $record)
                        @php
                            $avgPerDay = $record->total_days > 0 ? $record->total_hours / $record->total_days : 0;
                            $pct = ($record->total_hours / $maxHours) * 100;
                            $color = $barColors[$record->role] ?? $defaultBarColor;
                            $badgeBg = $roleBadgeBg[$record->role] ?? '#F3F4F6';
                            $badgeFg = $roleBadgeFg[$record->role] ?? '#6B7280';
                            $rankBg = $index < 3 ? $rankBgs[$index] : '#F6F6FB';
                            $rankFg = $index === 0 ? '#92400E' : ($index === 1 ? '#4B5563' : ($index === 2 ? '#9A3412' : '#6F6C84'));
                        @endphp
                        <tr style="border-bottom:1px solid #F0F0F5;">
                            <td style="padding:16px 24px;">
                                <span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:50%;font-size:11px;font-weight:700;background:{{ $rankBg }};color:{{ $rankFg }};">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td style="padding:16px 24px;">
                                <div style="display:flex;align-items:center;gap:12px;">
                                    <img src="{{ $record->foto ? asset('uploads/foto/' . $record->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($record->user_name) . '&background=random&size=80' }}"
                                         style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:1px solid #E6E6F0;"
                                         alt="{{ $record->user_name }}">
                                    <span style="font-size:13px;font-weight:600;color:#15131F;">{{ $record->user_name }}</span>
                                </div>
                            </td>
                            <td style="padding:16px 24px;">
                                <span style="padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;background:{{ $badgeBg }};color:{{ $badgeFg }};">
                                    {{ ucfirst($record->role) }}
                                </span>
                            </td>
                            <td style="padding:16px 24px;text-align:right;font-size:13px;font-weight:700;color:{{ $color }};">
                                {{ number_format($record->total_hours, 1) }} jam
                            </td>
                            <td style="padding:16px 24px;text-align:right;font-size:13px;color:#4B5563;">
                                {{ $record->total_days }} hari
                            </td>
                            <td style="padding:16px 24px;text-align:right;font-size:13px;color:#6F6C84;">
                                {{ number_format($avgPerDay, 1) }} jam/hari
                            </td>
                            <td style="padding:16px 24px;text-align:right;">
                                <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px;">
                                    <div style="width:64px;height:6px;background:#F3F4F6;border-radius:999px;overflow:hidden;">
                                        <div style="height:100%;width:{{ $pct }}%;background:{{ $color }};border-radius:999px;"></div>
                                    </div>
                                    <span style="font-size:11px;color:#9CA3AF;width:36px;text-align:right;">{{ number_format($pct, 0) }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:60px 24px;text-align:center;">
                                <p style="font-size:13px;color:#9CA3AF;">Tidak ada data</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
