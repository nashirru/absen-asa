@extends('layouts.member')
@section('title', 'Dashboard Sensei')
@section('header', 'Dashboard')

@section('content')
<div class="space-y-4 animate-fade-in-up" x-data="dashboardApp()">
    <!-- Profile + Clock -->
    <div class="bg-white rounded-xl border border-gray-100 p-4 flex items-center justify-between shadow-sm gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <img src="{{ $user->foto_url }}" class="w-11 h-11 rounded-full object-cover border border-gray-200" alt="">
            <div class="min-w-0">
                <h2 class="text-sm font-bold text-gray-900 truncate">{{ $user->name }}</h2>
                <p class="text-xs text-gray-500 truncate">{{ $sensei->bidang ?? '-' }} &bull; {{ $sensei->keterangan ?? '-' }}</p>
            </div>
        </div>
        <div class="text-right flex-shrink-0">
            <p class="text-[11px] text-gray-400">{{ now()->locale('id')->isoFormat('D MMM Y') }}</p>
            <p class="text-lg font-extrabold text-gray-900 tracking-tight mt-0.5" x-text="timeString">--:--:--</p>
        </div>
    </div>

    <!-- Jam Kerja Info -->
    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <div class="flex items-center gap-2 mb-3">
            <i data-lucide="clock" class="w-4 h-4 text-blue-600"></i>
            <span class="text-xs font-semibold text-gray-700 uppercase tracking-wider">Jam Kerja</span>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-blue-50 rounded-lg p-3 text-center">
                <p class="text-[11px] font-medium text-blue-700">Masuk</p>
                <p class="text-lg font-bold text-blue-800 mt-1">{{ $jamMasuk }}</p>
            </div>
            <div class="bg-orange-50 rounded-lg p-3 text-center">
                <p class="text-[11px] font-medium text-orange-700">Keluar</p>
                <p class="text-lg font-bold text-orange-800 mt-1">{{ $jamKeluar }}</p>
            </div>
        </div>
    </div>

    <!-- Attendance Status -->
    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <div class="grid grid-cols-2 gap-3">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 text-blue-600 mb-2">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                </div>
                <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wider">Absen Masuk</p>
                <p class="text-xl font-bold mt-1 {{ $todayAbsensi && $todayAbsensi->jam_masuk ? 'text-gray-900' : 'text-gray-300' }}">
                    {{ $todayAbsensi->jam_masuk ?? '--:--' }}
                </p>
                @if($todayAbsensi && $todayAbsensi->jam_masuk)
                    <span class="inline-block mt-1.5 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-green-50 text-green-700">Hadir</span>
                @endif
            </div>
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-50 text-orange-600 mb-2">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </div>
                <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wider">Absen Keluar</p>
                <p class="text-xl font-bold mt-1 {{ $todayAbsensi && $todayAbsensi->jam_keluar ? 'text-gray-900' : 'text-gray-300' }}">
                    {{ $todayAbsensi->jam_keluar ?? '--:--' }}
                </p>
                @if($todayAbsensi && $todayAbsensi->jam_keluar)
                    <span class="inline-block mt-1.5 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-blue-50 text-blue-700">Selesai</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Action Button -->
    @if($activeAbsensi)
        <a href="{{ route('absensi.check-out') }}"
           class="flex items-center justify-center gap-2 w-full py-3.5 bg-blue-50 text-blue-700 rounded-lg font-semibold text-sm hover:bg-blue-100 transition-all duration-150 active:scale-[0.98]">
            <i data-lucide="log-out" class="w-5 h-5"></i>
            Clock Out
        </a>
    @else
        <a href="{{ route('absensi.check-in') }}"
           class="flex items-center justify-center gap-2 w-full py-3.5 bg-blue-600 text-white rounded-lg font-semibold text-sm hover:bg-blue-700 transition-all duration-150 active:scale-[0.98] shadow-sm">
            <i data-lucide="check-square" class="w-5 h-5"></i>
            Clock In Sekarang
        </a>
    @endif

    <!-- Jadwal Hari Ini -->
    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i>
                <h3 class="font-semibold text-sm text-gray-900">Jadwal Hari Ini</h3>
            </div>
        </div>
        @forelse($jadwalHariIni as $j)
            <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50">
                <div class="w-12 text-center">
                    <p class="text-xs font-bold text-blue-600">{{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}</p>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $j->mata_pelajaran }}</p>
                    <p class="text-xs text-gray-500 truncate mt-0.5">{{ $j->kelas->nama_kelas ?? '-' }}</p>
                </div>
            </div>
        @empty
            <p class="text-center text-xs text-gray-400 py-4">Tidak ada jadwal hari ini</p>
        @endforelse
    </div>

    <!-- Riwayat Absensi -->
    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4 text-blue-600"></i>
                <h3 class="font-semibold text-sm text-gray-900">Riwayat Absensi</h3>
            </div>
            <a href="{{ route('absensi.riwayat') }}" class="text-xs text-blue-600 font-semibold">Lihat Semua</a>
        </div>
        @forelse($riwayatAbsensi as $r)
            <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50">
                <div>
                    <p class="text-sm font-semibold text-gray-900">{{ $r->tanggal->format('d M Y') }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $r->jam_masuk ?? '-' }} - {{ $r->jam_keluar ?? '-' }}</p>
                </div>
                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold
                    {{ $r->status === 'hadir' ? 'bg-green-50 text-green-700' : 
                       ($r->status === 'terlambat' ? 'bg-amber-50 text-amber-700' :
                       ($r->status === 'alpha' ? 'bg-red-50 text-red-700' : 
                       'bg-blue-50 text-blue-700')) }}">
                    {{ ucfirst($r->status) }}
                </span>
            </div>
        @empty
            <p class="text-center text-xs text-gray-400 py-4">Belum ada riwayat absensi</p>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
function dashboardApp() {
    return {
        timeString: '--:--:--',
        init() {
            this.updateTime();
            setInterval(() => this.updateTime(), 1000);
        },
        updateTime() {
            const now = new Date();
            this.timeString = now.toTimeString().split(' ')[0];
        }
    }
}
</script>
@endpush
@endsection