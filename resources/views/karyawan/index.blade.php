@extends('layouts.admin')
@section('title', 'Data Karyawan')
@section('header', 'Karyawan')
@section('content')
<div class="space-y-6 animate-fade-in-up">
    <!-- Page Header & Actions -->
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-admin-ink">Daftar Karyawan</h2>
        <a href="{{ route('karyawan.create') }}" class="px-5 py-2.5 bg-admin-indigo text-white rounded-admin-md text-sm font-semibold hover:bg-admin-indigo-deep transition-colors">
            + Tambah Karyawan
        </a>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-5">
        <form class="flex gap-4" x-data="ajaxForm({ action: '{{ route('karyawan.index') }}', method: 'GET' })" @submit.prevent="submit">
            <div class="flex-1 relative">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-admin-slate">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIK, jabatan..."
                       class="w-full pl-10 pr-4 py-2.5 bg-admin-canvas rounded-admin-full text-sm placeholder-admin-mist text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
            </div>
            <button type="submit" class="px-5 py-2.5 bg-admin-indigo text-white rounded-admin-md text-sm font-semibold hover:bg-admin-indigo-deep transition-colors">
                Cari
            </button>
        </form>
    </div>

    <!-- Karyawan Table Card -->
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg overflow-hidden" id="table-container">
        @include('karyawan.partials.table', ['karyawan' => $karyawan])
    </div>
</div>
@endsection
