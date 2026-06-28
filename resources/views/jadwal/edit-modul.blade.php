@extends('layouts.member')
@section('title', 'Kelola Modul')
@section('header', 'Kelola Modul')

@section('content')
<div class="space-y-5 animate-fade-in-up">
    <!-- Back to Schedule List -->
    <div class="flex items-center">
        <a href="{{ route('jadwal.my-schedule') }}" class="inline-flex items-center gap-1 text-xs font-bold text-member-blue hover:text-member-blue-deep transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Jadwal Saya
        </a>
    </div>

    <!-- Jadwal Info Card -->
    <div class="bg-member-surface rounded-member-xl p-5 shadow-member-card border border-member-border/30">
        <h2 class="text-sm font-bold text-member-ink mb-3 flex items-center gap-1.5">
            <span class="w-1.5 h-3.5 bg-member-blue rounded-full"></span>
            Informasi Jadwal Kelas
        </h2>
        <div class="grid grid-cols-2 gap-3 text-xs">
            <div class="space-y-1">
                <span class="text-member-slate block">Mata Pelajaran:</span>
                <span class="font-bold text-member-ink">{{ $jadwal->mata_pelajaran }}</span>
            </div>
            <div class="space-y-1">
                <span class="text-member-slate block">Kelas:</span>
                <span class="font-bold text-member-ink bg-member-blue-tint text-member-blue px-1.5 py-0.5 rounded inline-block">{{ $jadwal->kelas->nama_kelas ?? '-' }}</span>
            </div>
            <div class="space-y-1">
                <span class="text-member-slate block">Hari:</span>
                <span class="font-bold text-member-ink">{{ $jadwal->hari }}</span>
            </div>
            <div class="space-y-1">
                <span class="text-member-slate block">Waktu:</span>
                <span class="font-bold text-member-ink">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</span>
            </div>
        </div>
    </div>

    <!-- Edit Modul Form -->
    <div class="bg-member-surface rounded-member-xl p-5 shadow-member-card border border-member-border/30">
        <form action="{{ route('sensei.jadwal.update-modul', $jadwal) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Modul Link -->
            <div class="space-y-1.5">
                <label class="s-label" for="modul_link">
                    <i data-lucide="link" class="w-3.5 h-3.5 inline-block mr-1"></i>
                    Link Modul (Opsional)
                </label>
                <input type="url" name="modul_link" id="modul_link" value="{{ old('modul_link', $jadwal->modul_link) }}" placeholder="https://example.com/modul" class="s-input">
                @error('modul_link')
                    <span class="text-xs text-status-alpha mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Modul File -->
            <div class="space-y-1.5">
                <label class="s-label" for="modul_file">
                    <i data-lucide="file-up" class="w-3.5 h-3.5 inline-block mr-1"></i>
                    File Modul (PDF/Gambar, Opsional)
                </label>
                <div class="relative">
                    <input type="file" name="modul_file" id="modul_file" accept=".pdf,.jpg,.jpeg,.png" class="s-input py-2">
                </div>
                <p class="text-[10px] text-member-slate">Format: PDF, JPG, JPEG, PNG. Max: 10 MB.</p>
                
                @error('modul_file')
                    <span class="text-xs text-status-alpha mt-1 block">{{ $message }}</span>
                @enderror

                @if($jadwal->modul_file)
                    <div class="mt-3 flex items-center justify-between p-3 rounded-member-lg bg-member-canvas border border-member-border/40">
                        <div class="flex items-center gap-2 min-w-0">
                            <i data-lucide="file-text" class="w-4.5 h-4.5 text-member-blue shrink-0"></i>
                            <span class="text-xs font-semibold text-member-ink truncate block max-w-[200px]">{{ $jadwal->modul_file }}</span>
                        </div>
                        <a href="{{ asset('uploads/modul/' . $jadwal->modul_file) }}" target="_blank" class="text-xs font-bold text-member-blue hover:text-member-blue-deep shrink-0 flex items-center gap-1">
                            Lihat File
                            <i data-lucide="external-link" class="w-3 h-3"></i>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-3 pt-3">
                <a href="{{ route('jadwal.my-schedule') }}" class="flex-1 btn btn-outline btn-md">
                    Batal
                </a>
                <button type="submit" class="flex-1 btn btn-primary btn-md">
                    Simpan Modul
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
