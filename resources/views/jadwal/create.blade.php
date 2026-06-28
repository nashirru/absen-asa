@extends('layouts.admin')
@section('title', isset($jadwal) ? 'Edit Jadwal' : 'Tambah Jadwal')
@section('header', isset($jadwal) ? 'Edit Jadwal' : 'Tambah Jadwal')
@section('content')
<div class="max-w-2xl mx-auto animate-fade-in-up">
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-6">
        <form action="{{ isset($jadwal) ? route('jadwal.update', $jadwal) : route('jadwal.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf @if(isset($jadwal)) @method('PUT') @endif
            <select name="hari" required class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                <option value="">Pilih Hari</option>
                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)<option value="{{ $h }}" {{ old('hari', $jadwal->hari ?? '') === $h ? 'selected' : '' }}>{{ $h }}</option>@endforeach
            </select>
            <div class="grid grid-cols-2 gap-3">
                <input type="time" name="jam_mulai" value="{{ old('jam_mulai', $jadwal->jam_mulai ?? '') }}" required class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                <input type="time" name="jam_selesai" value="{{ old('jam_selesai', $jadwal->jam_selesai ?? '') }}" required class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
            </div>
            <input type="text" name="mata_pelajaran" value="{{ old('mata_pelajaran', $jadwal->mata_pelajaran ?? '') }}" required placeholder="Mata Pelajaran" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
            <select name="kelas_id" required class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                <option value="">Pilih Kelas</option>
                @foreach($kelasList as $k)<option value="{{ $k->id }}" {{ old('kelas_id', $jadwal->kelas_id ?? '') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>@endforeach
            </select>
            @if(auth()->user()->isSensei() && auth()->user()->sensei)
                <input type="hidden" name="sensei_id" value="{{ auth()->user()->sensei->id }}">
            @else
                <select name="sensei_id" required class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <option value="">Pilih Sensei</option>
                    @foreach($senseiList as $s)<option value="{{ $s->id }}" {{ old('sensei_id', $jadwal->sensei_id ?? '') == $s->id ? 'selected' : '' }}>{{ $s->user->name }} - {{ $s->mata_pelajaran }}</option>@endforeach
                </select>
            @endif
            
            <div class="space-y-1">
                <label class="block text-xs font-semibold text-admin-slate uppercase tracking-wider">Link Modul (Opsional)</label>
                <input type="url" name="modul_link" value="{{ old('modul_link', $jadwal->modul_link ?? '') }}" placeholder="https://example.com/modul" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-semibold text-admin-slate uppercase tracking-wider">File Modul (PDF/Gambar, Opsional)</label>
                <input type="file" name="modul_file" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                @if(isset($jadwal) && $jadwal->modul_file)
                    <div class="mt-2 text-xs text-admin-slate flex items-center gap-1.5 bg-admin-canvas p-2.5 rounded-admin-md border border-admin-border">
                        <i data-lucide="file-text" class="w-4 h-4 text-admin-indigo"></i>
                        <span>File saat ini: <a href="{{ asset('uploads/modul/' . $jadwal->modul_file) }}" target="_blank" class="text-admin-indigo hover:underline font-medium">{{ $jadwal->modul_file }}</a></span>
                    </div>
                @endif
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('jadwal.index') }}" class="flex-1 py-3 text-center rounded-admin-md border border-admin-border font-semibold text-sm hover:bg-admin-canvas transition-colors">Batal</a>
                <button type="submit" class="flex-1 py-3 bg-admin-indigo text-white rounded-admin-md font-semibold text-sm hover:bg-admin-indigo-deep transition-colors">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
