@extends('layouts.member')
@section('title', 'Dashboard Karyawan')
@section('header', 'Dashboard')

@section('content')
<div class="space-y-4 animate-fade-in-up" x-data="dashboardApp()">
    <!-- Profile + Clock -->
    <div class="bg-white rounded-xl border border-gray-100 p-4 flex items-center justify-between shadow-sm gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <img src="{{ $user->foto_url }}" class="w-11 h-11 rounded-full object-cover border border-gray-200" alt="">
            <div class="min-w-0">
                <h2 class="text-sm font-bold text-gray-900 truncate">{{ $user->name }}</h2>
                <p class="text-xs text-gray-500 truncate">{{ $karyawan->jabatan ?? '-' }} &bull; {{ $karyawan->divisi ?? '-' }}</p>
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

    <!-- Perizinan & Cuti Buttons -->
    <div class="grid grid-cols-2 gap-3">
        <a href="{{ route('absensi.izin') }}"
           class="flex items-center justify-center gap-2 w-full py-3 bg-white border border-gray-200 text-gray-700 rounded-xl font-bold text-xs hover:bg-gray-50 transition-all duration-150 shadow-sm active:scale-[0.98]">
            <i data-lucide="file-text" class="w-4 h-4 text-blue-500"></i>
            Ajukan Izin / Cuti
        </a>
        <a href="{{ route('absensi.sakit') }}"
           class="flex items-center justify-center gap-2 w-full py-3 bg-white border border-gray-200 text-gray-700 rounded-xl font-bold text-xs hover:bg-gray-50 transition-all duration-150 shadow-sm active:scale-[0.98]">
            <i data-lucide="heart-pulse" class="w-4 h-4 text-red-500"></i>
            Ajukan Sakit
        </a>
    </div>

    <!-- Menu Utama -->
    <div>
        <div class="flex items-center gap-2 mb-3 px-0.5">
            <i data-lucide="grid-3x3" class="w-4 h-4 text-gray-400"></i>
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Menu Utama</span>
        </div>
        <div class="grid grid-cols-3 gap-3">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1.5 p-2.5 bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                    <i data-lucide="home" class="w-5 h-5"></i>
                </div>
                <span class="text-[10px] font-medium text-gray-500">Home</span>
            </a>
            <a href="{{ route('jadwal.my-schedule') }}" class="flex flex-col items-center gap-1.5 p-2.5 bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                </div>
                <span class="text-[10px] font-medium text-gray-500">Jadwal</span>
            </a>
            <a href="{{ route('absensi.riwayat') }}" class="flex flex-col items-center gap-1.5 p-2.5 bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="w-10 h-10 rounded-lg bg-pink-50 flex items-center justify-center text-pink-500">
                    <i data-lucide="history" class="w-5 h-5"></i>
                </div>
                <span class="text-[10px] font-medium text-gray-500">Riwayat</span>
            </a>
            <a href="{{ route('absensi.izin') }}" class="flex flex-col items-center gap-1.5 p-2.5 bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                </div>
                <span class="text-[10px] font-medium text-gray-500">Izin / Cuti</span>
            </a>
            <a href="{{ route('absensi.sakit') }}" class="flex flex-col items-center gap-1.5 p-2.5 bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center text-red-500">
                    <i data-lucide="heart-pulse" class="w-5 h-5"></i>
                </div>
                <span class="text-[10px] font-medium text-gray-500">Sakit</span>
            </a>
            <a href="{{ route('profile.index') }}" class="flex flex-col items-center gap-1.5 p-2.5 bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center text-green-600">
                    <i data-lucide="user" class="w-5 h-5"></i>
                </div>
                <span class="text-[10px] font-medium text-gray-500">Profile</span>
            </a>
        </div>
    </div>

    <!-- Kehadiran Bulan Ini -->
    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                <i data-lucide="calendar-check" class="w-5 h-5"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-900">Kehadiran Bulan Ini</p>
                <p class="text-xs text-gray-500">Total hari kerja masuk</p>
            </div>
            <div class="text-right">
                <span class="text-xl font-bold text-blue-600">{{ $totalKehadiranBulanIni }} Hari</span>
            </div>
        </div>
    </div>

    <!-- Status Pengajuan Izin & Cuti -->
    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="file-text" class="w-4 h-4 text-blue-600"></i>
                <h3 class="font-semibold text-sm text-gray-900">Status Pengajuan Izin & Cuti</h3>
            </div>
        </div>
        @forelse($izinCutiRequests as $req)
            <div class="p-3 rounded-lg bg-gray-50 space-y-2 border border-gray-100/50">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded-full 
                            {{ $req->status === 'cuti' ? 'bg-indigo-50 text-indigo-700' : 
                               ($req->status === 'sakit' ? 'bg-orange-50 text-orange-700' : 'bg-blue-50 text-blue-700') }}">
                            {{ ucfirst($req->status) }}
                        </span>
                        <span class="text-[10px] text-gray-400 ml-1.5">{{ $req->tanggal->format('d M Y') }}</span>
                    </div>
                    @if($req->is_approved === null)
                        <span class="s-badge s-badge-warning text-[10px] text-amber-700 bg-amber-50">Menunggu</span>
                    @elseif($req->is_approved)
                        <span class="s-badge s-badge-success text-[10px] text-emerald-700 bg-emerald-50">Disetujui</span>
                    @else
                        <span class="s-badge s-badge-danger text-[10px] text-red-700 bg-red-50">Ditolak</span>
                    @endif
                </div>
                @if($req->catatan)
                    <p class="text-xs text-gray-600 italic">"{{ $req->catatan }}"</p>
                @endif
            </div>
        @empty
            <p class="text-center text-xs text-gray-400 py-4">Belum ada pengajuan izin atau cuti</p>
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
            setTimeout(() => lucide.createIcons(), 100);
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