@extends('layouts.admin')
@section('title', 'Kelola Lokasi')
@section('header', 'Lokasi')
@section('content')
<div class="space-y-6 animate-fade-in-up">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-admin-ink">Kelola Lokasi Absensi</h2>
        <a href="{{ route('locations.create') }}" class="px-4 py-2 bg-admin-indigo text-white rounded-admin-md text-sm font-semibold hover:bg-admin-indigo-deep transition-colors flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Lokasi
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-admin-success-tint/80 border border-admin-success/15 rounded-admin-lg p-4 flex items-start gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-admin-success flex-shrink-0 mt-0.5"></i>
            <p class="text-sm font-medium text-admin-success">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Table Card -->
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-admin-border bg-admin-canvas/30">
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Nama</th>
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Latitude</th>
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Longitude</th>
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Radius</th>
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Shift</th>
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Akses Role</th>
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Status</th>
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y border-admin-border">
                    @forelse($locations as $location)
                        <tr class="hover:bg-admin-canvas/30 transition-colors">
                            <td class="py-4 px-6 text-sm font-semibold text-admin-ink">{{ $location->name }}</td>
                            <td class="py-4 px-6 text-sm text-admin-slate font-mono">{{ $location->latitude }}</td>
                            <td class="py-4 px-6 text-sm text-admin-slate font-mono">{{ $location->longitude }}</td>
                            <td class="py-4 px-6 text-sm text-admin-slate font-mono">{{ $location->radius }}m</td>
                            <td class="py-4 px-6">
                                @if($location->shifts->count() > 0)
                                    <div class="space-y-1">
                                        @foreach($location->shifts as $shift)
                                            <div class="text-[11px] font-semibold text-admin-ink">
                                                {{ $shift->nama_shift }}: 
                                                <span class="font-mono text-admin-slate font-normal">{{ \Carbon\Carbon::parse($shift->jam_masuk)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->jam_keluar)->format('H:i') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-admin-mist">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                @if(!empty($location->allowed_roles) && count($location->allowed_roles) > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($location->allowed_roles as $role)
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold
                                                {{ $role === 'karyawan' ? 'bg-blue-100 text-blue-700' : ($role === 'sensei' ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700') }}">
                                                {{ ucfirst($role) }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-admin-mist">Semua</span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-0.5 rounded-admin-full text-xs font-semibold border
                                    {{ $location->is_active ? 'bg-admin-success-tint text-admin-success border-admin-success/20' : 'bg-admin-danger-tint text-admin-danger border-admin-danger/20' }}">
                                    {{ $location->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('locations.edit', $location) }}" class="p-2 rounded-admin-md hover:bg-admin-canvas transition-colors">
                                        <i data-lucide="edit" class="w-4 h-4 text-admin-slate"></i>
                                    </a>
                                    <form action="{{ route('locations.destroy', $location) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Yakin ingin menghapus lokasi ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 rounded-admin-md hover:bg-admin-danger-tint transition-colors">
                                            <i data-lucide="trash-2" class="w-4 h-4 text-admin-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-admin-mist">
                                <i data-lucide="map-pin" class="w-10 h-10 mx-auto mb-2 opacity-40"></i>
                                <p class="text-sm">Belum ada data lokasi</p>
                                <a href="{{ route('locations.create') }}" class="mt-2 text-xs text-admin-indigo hover:underline">Tambah Lokasi Pertama</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($locations->hasPages())
            <div class="px-6 py-4 border-t border-admin-border bg-admin-canvas/10">
                {{ $locations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
