@extends('layouts.member')
@section('title', 'Sakit')
@section('header', 'Laporan Sakit')
@section('content')
<div class="space-y-5 animate-fade-in-up">
    <!-- Page Header -->
    <div class="space-y-1">
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Laporan Sakit</h1>
        <p class="text-sm text-slate-500">Ajukan laporan sakit Anda hari ini</p>
    </div>

    <div class="s-card p-6">
        <!-- Icon Header -->
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                <i data-lucide="heart-pulse" class="w-5 h-5 text-red-500"></i>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-slate-900">Form Sakit</h2>
                <p class="text-xs text-slate-500">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
            </div>
        </div>

        <form action="{{ route('absensi.store-sakit') }}" method="POST" class="space-y-5">
            @csrf

            <div class="space-y-2">
                <label class="s-label">Keterangan Sakit</label>
                <textarea name="catatan" rows="4" required placeholder="Jelaskan kondisi sakit Anda..."
                          class="s-textarea">{{ old('catatan') }}</textarea>
                @error('catatan') <p class="text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
            </div>

            @if(session('error'))
                <div class="flex items-start gap-3 p-3 rounded-lg bg-red-50 border border-red-100">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-red-500 shrink-0 mt-0.5"></i>
                    <p class="text-xs font-medium text-red-600">{{ session('error') }}</p>
                </div>
            @endif

            <div class="s-separator"></div>

            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('dashboard') }}" class="btn btn-outline btn-md btn-full">
                    Batal
                </a>
                <button type="submit" class="btn btn-destructive btn-md btn-full">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    Kirim Laporan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
