@extends('layouts.admin')
@section('title', isset($sensei) ? 'Edit Sensei' : 'Tambah Sensei')
@section('header', isset($sensei) ? 'Edit Sensei' : 'Tambah Sensei')
@section('content')
<div class="max-w-2xl mx-auto animate-fade-in-up">
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-6">
        <form action="{{ isset($sensei) ? route('sensei.update', $sensei) : route('sensei.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf @if(isset($sensei)) @method('PUT') @endif
            <div class="space-y-3">
                <input type="text" name="name" value="{{ old('name', $sensei->user->name ?? '') }}" required placeholder="Nama" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                <input type="email" name="email" value="{{ old('email', $sensei->user->email ?? '') }}" required placeholder="Email" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                <input type="password" name="password" placeholder="Password {{ isset($sensei) ? '(kosongkan jika tidak diubah)' : '' }}" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                <input type="text" name="phone" value="{{ old('phone', $sensei->user->phone ?? '') }}" placeholder="No HP" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                <input type="text" name="mata_pelajaran" value="{{ old('mata_pelajaran', $sensei->mata_pelajaran ?? '') }}" required placeholder="Mata Pelajaran" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                <input type="file" name="foto" accept="image/*" class="w-full px-4 py-2 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink">
            </div>
            <div class="flex gap-3 pt-2">
                <a href="{{ route('sensei.index') }}" class="flex-1 py-3 text-center rounded-admin-md border border-admin-border font-semibold text-sm hover:bg-admin-canvas transition-colors">Batal</a>
                <button type="submit" class="flex-1 py-3 bg-admin-indigo text-white rounded-admin-md font-semibold text-sm hover:bg-admin-indigo-deep transition-colors">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
