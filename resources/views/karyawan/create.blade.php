@extends('layouts.admin')
@section('title', isset($karyawan) ? 'Edit Karyawan' : 'Tambah Karyawan')
@section('header', isset($karyawan) ? 'Edit Karyawan' : 'Tambah Karyawan')
@section('content')
<div class="max-w-2xl mx-auto animate-fade-in-up">
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-6">
        <form action="{{ isset($karyawan) ? route('karyawan.update', $karyawan) : route('karyawan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf @if(isset($karyawan)) @method('PUT') @endif
            <fieldset class="border border-admin-border rounded-admin-md p-4">
                <legend class="text-xs font-bold uppercase tracking-wider text-admin-indigo px-2">Data Akun</legend>
                <div class="space-y-3">
                    <input type="text" name="name" value="{{ old('name', $karyawan->user->name ?? '') }}" required placeholder="Nama" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <input type="email" name="email" value="{{ old('email', $karyawan->user->email ?? '') }}" required placeholder="Email" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <input type="password" name="password" placeholder="Password {{ isset($karyawan) ? '(kosongkan jika tidak diubah)' : '' }}" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <input type="text" name="phone" value="{{ old('phone', $karyawan->user->phone ?? '') }}" placeholder="No HP" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <input type="file" name="foto" accept="image/*" class="w-full px-4 py-2 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink">
                </div>
            </fieldset>
            <fieldset class="border border-admin-border rounded-admin-md p-4">
                <legend class="text-xs font-bold uppercase tracking-wider text-admin-indigo px-2">Data Karyawan</legend>
                <div class="space-y-3">
                    <input type="text" name="nik" value="{{ old('nik', $karyawan->nik ?? '') }}" required placeholder="NIK" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <input type="text" name="jabatan" value="{{ old('jabatan', $karyawan->jabatan ?? '') }}" required placeholder="Jabatan" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <input type="text" name="divisi" value="{{ old('divisi', $karyawan->divisi ?? '') }}" placeholder="Divisi" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <textarea name="alamat" rows="2" placeholder="Alamat" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">{{ old('alamat', $karyawan->alamat ?? '') }}</textarea>
                </div>
            </fieldset>
            <div class="flex gap-3 pt-2">
                <a href="{{ route('karyawan.index') }}" class="flex-1 py-3 text-center rounded-admin-md border border-admin-border font-semibold text-sm hover:bg-admin-canvas transition-colors">Batal</a>
                <button type="submit" class="flex-1 py-3 bg-admin-indigo text-white rounded-admin-md font-semibold text-sm hover:bg-admin-indigo-deep transition-colors">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
