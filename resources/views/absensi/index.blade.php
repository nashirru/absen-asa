@extends('layouts.admin')
@section('title', 'Data Absensi')
@section('header', 'Absensi')
@section('content')
<div class="space-y-6 animate-fade-in-up">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-admin-ink">Data Absensi</h2>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-5">
        <form class="flex flex-col md:flex-row gap-4 flex-wrap">
            <!-- Search Input -->
            <div class="flex-1 min-w-[200px] relative">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-admin-slate">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-admin-canvas rounded-admin-full text-sm placeholder-admin-mist text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
            </div>

            <!-- Date Input -->
            <input type="date" name="tanggal" value="{{ request('tanggal') }}" 
                   class="px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">

            <!-- Status Select -->
            <select name="status" class="px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-slate focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                <option value="">Semua Status</option>
                @foreach(['hadir','terlambat','izin','sakit','alpha'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-5 py-2.5 bg-admin-indigo text-white rounded-admin-md text-sm font-semibold hover:bg-admin-indigo-deep transition-colors">
                Filter
            </button>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-admin-border bg-admin-canvas/30">
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Nama</th>
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Tanggal</th>
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Masuk</th>
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Keluar</th>
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Status</th>
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate text-right">Jarak</th>
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y border-admin-border">
                    @forelse($absensi as $a)
                        <tr class="hover:bg-admin-canvas/30 transition-colors">
                            <td class="py-4 px-6 text-sm font-semibold text-admin-ink">{{ $a->user->name ?? '-' }}</td>
                            <td class="py-4 px-6 text-sm text-admin-slate">{{ $a->tanggal->format('d/m/Y') }}</td>
                            <td class="py-4 px-6 text-sm text-admin-ink font-mono">{{ $a->jam_masuk ?? '-' }}</td>
                            <td class="py-4 px-6 text-sm text-admin-ink font-mono">{{ $a->jam_keluar ?? '-' }}</td>
                            <td class="py-4 px-6">
                                <div class="flex flex-col gap-1 items-start">
                                    <span class="px-2.5 py-0.5 rounded-admin-full text-xs font-semibold border
                                        {{ $a->status === 'hadir' ? 'bg-admin-success-tint text-admin-success border-admin-success/20' : 
                                           ($a->status === 'terlambat' ? 'bg-amber-50 text-amber-700 border-amber-200' :
                                           ($a->status === 'alpha' ? 'bg-admin-danger-tint text-admin-danger border-admin-danger/20' : 
                                           'bg-admin-indigo-tint text-admin-indigo border-admin-indigo/20')) }}">
                                        {{ ucfirst($a->status) }}
                                    </span>
                                    
                                    @if($a->status === 'izin' || $a->status === 'sakit')
                                        @if($a->is_approved === null)
                                            <span class="text-[10px] font-semibold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200/50">
                                                Menunggu Persetujuan
                                            </span>
                                        @elseif($a->is_approved)
                                            <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200/50">
                                                Disetujui
                                            </span>
                                        @else
                                            <span class="text-[10px] font-semibold text-red-600 bg-red-50 px-1.5 py-0.5 rounded border border-red-200/50">
                                                Ditolak
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6 text-right text-xs text-admin-slate font-mono">
                                {{ $a->distance ? round($a->distance).'m' : '-' }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(($a->status === 'izin' || $a->status === 'sakit') && $a->is_approved === null)
                                        <form action="{{ route('absensi.approve', $a->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 rounded-admin-md text-[11px] font-semibold bg-emerald-600 text-white hover:bg-emerald-700 transition-colors">
                                                Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('absensi.reject', $a->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 rounded-admin-md text-[11px] font-semibold bg-red-600 text-white hover:bg-red-700 transition-colors">
                                                Tolak
                                            </button>
                                        </form>
                                    @endif

                                    @if(app()->environment('local', 'development'))
                                        <form action="{{ route('absensi.destroy', $a->id) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Hapus data absensi {{ $a->user->name }} tanggal {{ $a->tanggal->format('d/m/Y') }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 rounded-admin-md text-[11px] font-semibold bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 transition-colors">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ app()->environment('local', 'development') ? 7 : 6 }}" class="py-12 text-center text-admin-mist">
                                <i data-lucide="calendar" class="w-10 h-10 mx-auto mb-2 opacity-40"></i>
                                <p class="text-sm">Belum ada data absensi</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Area -->
        @if($absensi->hasPages())
            <div class="px-6 py-4 border-t border-admin-border bg-admin-canvas/10">
                {{ $absensi->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
