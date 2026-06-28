@extends('layouts.member')
@section('title', 'Izin')
@section('header', 'Izin')
@section('content')
<div class="space-y-5 animate-fade-in-up">
    <!-- Page Header -->
    <div class="space-y-1">
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Ajukan Izin</h1>
        <p class="text-sm text-slate-500">Isi form berikut untuk mengajukan izin hari ini</p>
    </div>

    <div class="s-card p-6">
        <!-- Icon Header -->
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-slate-900">Form Izin</h2>
                <p class="text-xs text-slate-500">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
            </div>
        </div>

        <form action="{{ route('absensi.store-izin') }}" method="POST" class="space-y-5">
            @csrf
            
            <div class="space-y-2">
                <label class="s-label">Tipe Pengajuan</label>
                <select name="status" class="s-select" required>
                    <option value="izin" {{ old('status') == 'izin' ? 'selected' : '' }}>Izin (Keperluan Pribadi/Lain-lain)</option>
                    <option value="cuti" {{ old('status') == 'cuti' ? 'selected' : '' }}>Cuti Tahunan / Khusus</option>
                </select>
                @error('status') <p class="text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="s-label">Tanggal Pengajuan</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required class="s-input">
                @error('tanggal') <p class="text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="s-label">Keterangan / Alasan</label>
                <textarea name="catatan" rows="4" required placeholder="Tuliskan alasan pengajuan Anda..."
                          class="s-textarea">{{ old('catatan') }}</textarea>
                @error('catatan') <p class="text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
            </div>

            @if(session('error'))
                <div class="flex items-start gap-3 p-3 rounded-lg bg-blue-50 border border-blue-100">
                    <i data-lucide="info" class="w-4 h-4 text-blue-500 shrink-0 mt-0.5"></i>
                    <p class="text-xs font-medium text-blue-600">{{ session('error') }}</p>
                </div>
            @endif

            <div class="s-separator"></div>

            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('dashboard') }}" class="btn btn-outline btn-md btn-full">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary btn-md btn-full">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    Kirim
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
