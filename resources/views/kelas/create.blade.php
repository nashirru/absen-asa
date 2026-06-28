@extends('layouts.admin')
@section('title', isset($kelas) ? 'Edit Kelas' : 'Tambah Kelas')
@section('header', isset($kelas) ? 'Edit Kelas' : 'Tambah Kelas')
@section('content')
<div class="max-w-2xl mx-auto animate-fade-in-up">
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-6">
        <form action="{{ isset($kelas) ? route('kelas.update', $kelas) : route('kelas.store') }}" method="POST" class="space-y-4">
            @csrf @if(isset($kelas)) @method('PUT') @endif
            <input type="text" name="nama_kelas" value="{{ old('nama_kelas', $kelas->nama_kelas ?? '') }}" required placeholder="Nama Kelas" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
            <input type="text" name="tingkat" value="{{ old('tingkat', $kelas->tingkat ?? '') }}" required placeholder="Tingkat" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
            <select name="sensei_id" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                <option value="">Pilih Sensei</option>
                @foreach($senseiList as $s)<option value="{{ $s->id }}" {{ old('sensei_id', $kelas->sensei_id ?? '') == $s->id ? 'selected' : '' }}>{{ $s->user->name }} - {{ $s->mata_pelajaran }}</option>@endforeach
            </select>
            <input type="number" name="kapasitas" value="{{ old('kapasitas', $kelas->kapasitas ?? 30) }}" required min="1" placeholder="Kapasitas" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
            <div class="flex gap-3 pt-2">
                <a href="{{ route('kelas.index') }}" class="flex-1 py-3 text-center rounded-admin-md border border-admin-border font-semibold text-sm hover:bg-admin-canvas transition-colors">Batal</a>
                <button type="submit" class="flex-1 py-3 bg-admin-indigo text-white rounded-admin-md font-semibold text-sm hover:bg-admin-indigo-deep transition-colors">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
