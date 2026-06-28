@extends('layouts.member')
@section('title', 'Detail Kelas')
@section('header', 'Detail Kelas')
@section('content')
<div class="space-y-5 animate-fade-in-up" x-data="{ tab: 'siswa' }">

    <!-- Back Button -->
    <a href="{{ route('kelas.saya') }}" class="inline-flex items-center gap-1.5 text-xs text-member-slate hover:text-member-ink font-semibold">
        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
        Kembali ke Daftar Kelas
    </a>

    <!-- Class Banner -->
    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm space-y-2">
        <h2 class="text-xl font-bold text-gray-900">{{ $kelas->nama_kelas }}</h2>
        <div class="grid grid-cols-2 gap-4 text-xs text-gray-500 pt-1">
            <div>
                <p>Tingkat: <span class="font-semibold text-gray-800">{{ $kelas->tingkat }}</span></p>
                <p class="mt-1">Kapasitas: <span class="font-semibold text-gray-800">{{ $kelas->kapasitas }} Siswa</span></p>
            </div>
            <div>
                <p>Jumlah Murid: <span class="font-semibold text-gray-800">{{ $kelas->siswa->count() }} Terdaftar</span></p>
                <p class="mt-1">Instruktur: <span class="font-semibold text-gray-800">{{ $kelas->sensei->user->name ?? '-' }}</span></p>
            </div>
        </div>
    </div>

    <!-- Segmented Tab -->
    <div class="bg-gray-100 p-1 rounded-lg flex items-center">
        <button @click="tab = 'siswa'" :class="tab === 'siswa' ? 'bg-white shadow-sm font-semibold text-gray-900' : 'text-gray-500'" class="flex-1 text-center py-2 text-xs rounded-md transition-all">
            Daftar Murid
        </button>
        <button @click="tab = 'absensi'" :class="tab === 'absensi' ? 'bg-white shadow-sm font-semibold text-gray-900' : 'text-gray-500'" class="flex-1 text-center py-2 text-xs rounded-md transition-all">
            Kehadiran Hari Ini
        </button>
    </div>

    <!-- Tab 1: Siswa (Progress & Nilai) -->
    <div x-show="tab === 'siswa'" class="space-y-4">
        @forelse($kelas->siswa as $s)
            <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm space-y-3">
                <div class="flex items-center gap-3">
                    <img src="{{ $s->user->foto_url }}" class="w-10 h-10 rounded-full object-cover border border-gray-200" alt="">
                    <div class="min-w-0 flex-1">
                        <h4 class="text-sm font-bold text-gray-900 truncate">{{ $s->user->name }}</h4>
                        <p class="text-xs text-gray-500 font-mono">{{ $s->nis }}</p>
                    </div>
                </div>

                <!-- Progress & Nilai Form -->
                <form action="{{ route('kelas.saya.update-progress') }}" method="POST" class="bg-gray-50 p-3 rounded-lg space-y-3 border border-gray-100">
                    @csrf
                    <input type="hidden" name="siswa_id" value="{{ $s->id }}">

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Progress (%)</label>
                            <input type="number" name="progress_pelatihan" min="0" max="100" value="{{ old('progress_pelatihan', $s->progress_pelatihan) }}" required
                                   class="w-full px-2.5 py-1.5 bg-white border border-gray-200 rounded text-xs text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Nilai / Grade</label>
                            <input type="text" name="nilai_pelatihan" value="{{ old('nilai_pelatihan', $s->nilai_pelatihan) }}" placeholder="Contoh: A, B, 85"
                                   class="w-full px-2.5 py-1.5 bg-white border border-gray-200 rounded text-xs text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-1.5 bg-blue-600 text-white rounded text-[11px] font-semibold hover:bg-blue-700 transition-colors flex items-center justify-center gap-1">
                        <i data-lucide="save" class="w-3.5 h-3.5"></i> Update Progress
                    </button>
                </form>
            </div>
        @empty
            <p class="text-center text-xs text-gray-400 py-6">Tidak ada murid terdaftar di kelas ini</p>
        @endforelse
    </div>

    <!-- Tab 2: Kehadiran Hari Ini (Realtime Monitoring & Absensi Actions) -->
    <div x-show="tab === 'absensi'" class="space-y-4">
        <div class="bg-blue-50/50 rounded-lg p-3 border border-blue-100 flex items-center justify-between text-xs">
            <span class="font-medium text-blue-700">Tanggal Absensi:</span>
            <span class="font-bold text-blue-800">{{ now()->locale('id')->isoFormat('D MMMM Y') }}</span>
        </div>

        @forelse($kelas->siswa as $s)
            @php
                $att = $attendanceToday->get($s->user_id);
            @endphp
            <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm space-y-4" x-data="{ showAction: false }">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="{{ $s->user->foto_url }}" class="w-10 h-10 rounded-full object-cover border border-gray-200" alt="">
                        <div class="min-w-0">
                            <h4 class="text-sm font-bold text-gray-900 truncate">{{ $s->user->name }}</h4>
                            @if($att)
                                <p class="text-[10px] text-gray-400 mt-0.5 font-mono">
                                    {{ $att->jam_masuk ?? '-' }} - {{ $att->jam_keluar ?? 'Aktif' }}
                                </p>
                            @else
                                <p class="text-[10px] text-red-500 font-semibold mt-0.5">Belum Absen</p>
                            @endif
                        </div>
                    </div>

                    <!-- Badge Status -->
                    @if($att)
                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold
                            {{ $att->status === 'hadir' ? 'bg-green-50 text-green-700' :
                               ($att->status === 'terlambat' ? 'bg-amber-50 text-amber-700' :
                               ($att->status === 'izin' ? 'bg-blue-50 text-blue-700' :
                               ($att->status === 'sakit' ? 'bg-orange-50 text-orange-700' :
                               'bg-red-50 text-red-700'))) }}">
                            {{ ucfirst($att->status) }}
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-50 text-gray-400">
                            Alpha / Belum
                        </span>
                    @endif
                </div>

                <!-- Action Button Trigger -->
                <div class="flex justify-end">
                    <button @click="showAction = !showAction" class="inline-flex items-center gap-1 text-[11px] text-blue-600 font-semibold hover:underline">
                        <i data-lucide="edit-3" class="w-3 h-3"></i> Kelola Kehadiran
                    </button>
                </div>

                <!-- Kehadiran Action Form Box -->
                <div x-show="showAction" x-cloak class="bg-gray-50 p-3 rounded-lg border border-gray-100 space-y-4 animate-fade-in-up">
                    
                    <!-- Quick Override -->
                    <form action="{{ route('kelas.saya.override', $kelas->id) }}" method="POST" class="space-y-2">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $s->user_id }}">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider">Koreksi Kehadiran (Override)</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="submit" name="status" value="hadir" class="py-1 px-2 bg-green-600 text-white rounded text-[10px] font-semibold hover:bg-green-700">Hadir</button>
                            <button type="submit" name="status" value="terlambat" class="py-1 px-2 bg-amber-500 text-white rounded text-[10px] font-semibold hover:bg-amber-600">Terlambat</button>
                            <button type="submit" name="status" value="alpha" class="py-1 px-2 bg-red-600 text-white rounded text-[10px] font-semibold hover:bg-red-700">Alpha</button>
                        </div>
                    </form>

                    <div class="h-px bg-gray-200"></div>

                    <!-- Mark Sick / Permit -->
                    <form action="{{ route('kelas.saya.mark-absent', $kelas->id) }}" method="POST" class="space-y-2.5">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $s->user_id }}">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider">Tandai Izin / Sakit</label>
                        <select name="status" required class="w-full px-2.5 py-1.5 bg-white border border-gray-200 rounded text-xs text-gray-900 focus:outline-none">
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                        </select>
                        <input type="text" name="catatan" required placeholder="Catatan/Alasan (contoh: Demam, Acara Keluarga)" 
                               class="w-full px-2.5 py-1.5 bg-white border border-gray-200 rounded text-xs text-gray-900 focus:outline-none">
                        <button type="submit" class="w-full py-1.5 bg-blue-600 text-white rounded text-[10px] font-semibold hover:bg-blue-700">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-center text-xs text-gray-400 py-6">Tidak ada murid terdaftar di kelas ini</p>
        @endforelse
    </div>
</div>
@endsection
