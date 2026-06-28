@extends('layouts.admin')
@section('title', 'Data Jadwal')
@section('header', 'Jadwal')
@section('content')
<div class="space-y-6 animate-fade-in-up">
    <!-- Page Header & Actions -->
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-admin-ink">Daftar Jadwal</h2>
        <a href="{{ route('jadwal.create') }}" class="px-5 py-2.5 bg-admin-indigo text-white rounded-admin-md text-sm font-semibold hover:bg-admin-indigo-deep transition-colors">
            + Tambah Jadwal
        </a>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-5">
        <form class="flex flex-col md:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1 relative">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-admin-slate">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari mata pelajaran..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-admin-canvas rounded-admin-full text-sm placeholder-admin-mist text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
            </div>

            <!-- Hari Select -->
            <select name="hari" class="px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-slate focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                <option value="">Semua Hari</option>
                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                    <option value="{{ $h }}" {{ request('hari') === $h ? 'selected' : '' }}>{{ $h }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-5 py-2.5 bg-admin-indigo text-white rounded-admin-md text-sm font-semibold hover:bg-admin-indigo-deep transition-colors">
                Filter
            </button>
        </form>
    </div>

    <!-- Jadwal Table Card -->
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-admin-border bg-admin-canvas/30">
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Hari</th>
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Jam</th>
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Mata Pelajaran</th>
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Kelas</th>
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Sensei</th>
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Modul</th>
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y border-admin-border">
                    @forelse($jadwal as $j)
                        <tr class="hover:bg-admin-canvas/30 transition-colors">
                            <td class="py-4 px-6 text-sm font-semibold text-admin-ink">{{ $j->hari }}</td>
                            <td class="py-4 px-6 text-xs text-admin-slate font-mono">
                                {{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}
                            </td>
                            <td class="py-4 px-6 text-sm text-admin-ink">{{ $j->mata_pelajaran }}</td>
                            <td class="py-4 px-6 text-sm text-admin-slate">{{ $j->kelas->nama_kelas ?? '-' }}</td>
                            <td class="py-4 px-6 text-sm text-admin-slate">{{ $j->sensei->user->name ?? '-' }}</td>
                            <td class="py-4 px-6 text-sm text-admin-slate">
                                <div class="flex flex-col gap-1">
                                    @if($j->modul_file)
                                        <a href="{{ asset('uploads/modul/' . $j->modul_file) }}" target="_blank" class="inline-flex items-center gap-1 text-xs text-admin-indigo hover:underline">
                                            <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                            PDF/Gambar
                                        </a>
                                    @endif
                                    @if($j->modul_link)
                                        <a href="{{ $j->modul_link }}" target="_blank" class="inline-flex items-center gap-1 text-xs text-admin-indigo hover:underline">
                                            <i data-lucide="link" class="w-3.5 h-3.5"></i>
                                            Link Modul
                                        </a>
                                    @endif
                                    @if(!$j->modul_file && !$j->modul_link)
                                        <span class="text-admin-mist text-xs italic">Belum ada</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('jadwal.edit', $j) }}" class="p-2 rounded-admin-md border border-admin-border text-admin-indigo hover:bg-admin-indigo-tint hover:border-admin-indigo/20 transition-all duration-150" title="Edit">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('jadwal.destroy', $j) }}" method="POST" onsubmit="return confirm('Yakin hapus jadwal ini?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 rounded-admin-md border border-admin-border text-admin-danger hover:bg-admin-danger-tint hover:border-admin-danger/20 transition-all duration-150" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-admin-mist">
                                <i data-lucide="calendar" class="w-10 h-10 mx-auto mb-2 opacity-40"></i>
                                <p class="text-sm">Belum ada data jadwal</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Area -->
        @if($jadwal->hasPages())
            <div class="px-6 py-4 border-t border-admin-border bg-admin-canvas/10">
                {{ $jadwal->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

