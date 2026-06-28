@extends('layouts.admin')
@section('title', isset($siswa) ? 'Edit Siswa' : 'Tambah Siswa')
@section('header', isset($siswa) ? 'Edit Siswa' : 'Tambah Siswa')

@section('content')
<div class="max-w-2xl mx-auto animate-fade-in-up">
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-6">
        <form action="{{ isset($siswa) ? route('siswa.update', $siswa) : route('siswa.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @if(isset($siswa)) @method('PUT') @endif

            <fieldset class="border border-admin-border rounded-admin-md p-4">
                <legend class="text-xs font-bold uppercase tracking-wider text-admin-indigo px-2">Data Akun</legend>
                <div class="space-y-3">
                    <input type="text" name="name" value="{{ old('name', $siswa->user->name ?? '') }}" required placeholder="Nama Lengkap" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <input type="email" name="email" value="{{ old('email', $siswa->user->email ?? '') }}" required placeholder="Email" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <input type="password" name="password" placeholder="Password {{ isset($siswa) ? '(kosongkan jika tidak diubah)' : '' }}" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <input type="text" name="phone" value="{{ old('phone', $siswa->user->phone ?? '') }}" placeholder="No HP" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <input type="file" name="foto" accept="image/*" class="w-full px-4 py-2 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink">
                </div>
            </fieldset>

            <fieldset class="border border-admin-border rounded-admin-md p-4">
                <legend class="text-xs font-bold uppercase tracking-wider text-admin-indigo px-2">Data Siswa</legend>
                <div class="space-y-3">
                    <input type="text" name="nis" value="{{ old('nis', $siswa->nis ?? '') }}" required placeholder="NIS" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <select name="gender" required class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                        <option value="">Pilih Gender</option>
                        <option value="L" {{ old('gender', $siswa->gender ?? '') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender', $siswa->gender ?? '') === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', isset($siswa) ? $siswa->tanggal_lahir->format('Y-m-d') : '') }}" required class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <textarea name="alamat" rows="2" placeholder="Alamat" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">{{ old('alamat', $siswa->alamat ?? '') }}</textarea>
                    <select name="kelas_id" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                        <option value="">Pilih Kelas</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}" {{ old('kelas_id', $siswa->kelas_id ?? '') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
            </fieldset>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('siswa.index') }}" class="flex-1 py-3 text-center rounded-admin-md border border-admin-border font-semibold text-sm hover:bg-admin-canvas transition-colors">Batal</a>
                <button type="submit" class="flex-1 py-3 bg-admin-indigo text-white rounded-admin-md font-semibold text-sm hover:bg-admin-indigo-deep transition-colors">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
