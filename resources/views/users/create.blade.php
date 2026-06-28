@extends('layouts.admin')
@section('title', isset($user) ? 'Edit User' : 'Tambah User')
@section('header', isset($user) ? 'Edit User' : 'Tambah User')

@section('content')
<div class="max-w-2xl mx-auto animate-fade-in-up">
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-6">
        <h2 class="text-lg font-semibold text-admin-ink mb-6">{{ isset($user) ? 'Edit User' : 'Tambah User Baru' }}</h2>

        <form action="{{ isset($user) ? route('users.update', $user) : route('users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @if(isset($user)) @method('PUT') @endif

            <div>
                <label class="block text-xs font-semibold text-admin-slate mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required class="w-full px-4 py-2 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:ring-2 focus:ring-primary-500 outline-none">
                @error('name') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-admin-slate mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required class="w-full px-4 py-2 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:ring-2 focus:ring-primary-500 outline-none">
                @error('email') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div x-data="{ showPassword: false }">
                <label class="block text-xs font-semibold text-admin-slate mb-1">Password {{ isset($user) ? '(kosongkan jika tidak diubah)' : '' }}</label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" name="password" class="w-full px-4 py-2 pr-10 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:ring-2 focus:ring-primary-500 outline-none">
                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-admin-slate hover:text-admin-ink">
                        <span x-show="!showPassword"><i data-lucide="eye" class="w-5 h-5"></i></span>
                        <span x-show="showPassword" style="display: none;"><i data-lucide="eye-off" class="w-5 h-5"></i></span>
                    </button>
                </div>
                @error('password') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-admin-slate mb-1">Role</label>
                <select name="role" required class="w-full px-4 py-2 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:ring-2 focus:ring-primary-500 outline-none">
                    @foreach($roles as $role)
                        <option value="{{ $role }}" {{ old('role', $user->role ?? '') === $role ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$role)) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-admin-slate mb-1">No HP</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}" class="w-full px-4 py-2 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:ring-2 focus:ring-primary-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-admin-slate mb-1">Foto</label>
                <input type="file" name="foto" accept="image/*" class="w-full px-4 py-2 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink">
                @if(isset($user) && $user->foto)
                    <img src="{{ $user->foto_url }}" class="w-16 h-16 rounded-xl mt-2 object-cover">
                @endif
            </div>

            @if(isset($user) && $user->device_uuid)
                <div class="p-4 rounded-admin-md bg-admin-canvas border border-admin-border space-y-2">
                    <p class="text-xs font-semibold text-admin-slate uppercase tracking-wider">Perangkat Terdaftar (One-Device Lock)</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono bg-admin-surface border border-admin-border px-2.5 py-1 rounded text-admin-ink">{{ $user->device_uuid }}</span>
                        <button type="button" onclick="document.getElementById('reset-device-form').submit()" 
                                class="px-3 py-1.5 bg-red-600 text-white rounded-admin-md text-xs font-semibold hover:bg-red-700 transition-colors flex items-center gap-1.5">
                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                            Reset Kunci Perangkat
                        </button>
                    </div>
                </div>
            @endif

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="status_aktif" {{ old('status_aktif', $user->status_aktif ?? true) ? 'checked' : '' }} class="w-4 h-4 rounded text-admin-indigo focus:ring-admin-indigo/25">
                <span class="text-sm">Status Aktif</span>
            </label>

            @if(isset($user) && $user->device_uuid)
                <form id="reset-device-form" action="{{ route('users.reset-device', $user) }}" method="POST" class="hidden">
                    @csrf
                </form>
            @endif

            <div class="flex gap-3 pt-4">
                <a href="{{ route('users.index') }}" class="flex-1 py-3 text-center rounded-admin-md border border-admin-border font-semibold text-sm hover:bg-admin-canvas transition-colors">Batal</a>
                <button type="submit" class="flex-1 py-3 bg-admin-indigo text-white rounded-admin-md font-semibold text-sm hover:bg-admin-indigo-deep transition-colors">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
