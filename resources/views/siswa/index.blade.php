@extends('layouts.admin')
@section('title', 'Data Siswa')
@section('header', 'Siswa')

@section('content')
<div class="space-y-6 animate-fade-in-up">
    <!-- Page Header & Actions -->
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-admin-ink">Daftar Siswa</h2>
        <a href="{{ route('siswa.create') }}" class="px-5 py-2.5 bg-admin-indigo text-white rounded-admin-md text-sm font-semibold hover:bg-admin-indigo-deep transition-colors">
            + Tambah Siswa
        </a>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-5">
        <form class="flex flex-col md:flex-row gap-4">
            <!-- Search input pill -->
            <div class="flex-1 relative">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-admin-slate">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIS..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-admin-canvas rounded-admin-full text-sm placeholder-admin-mist text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
            </div>

            <!-- Kelas Select -->
            <select name="kelas_id" class="px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-slate focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                <option value="">Semua Kelas</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-5 py-2.5 bg-admin-indigo text-white rounded-admin-md text-sm font-semibold hover:bg-admin-indigo-deep transition-colors">
                Filter
            </button>
        </form>
    </div>

    <!-- Siswa Table Card -->
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-admin-border bg-admin-canvas/30">
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Siswa</th>
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">NIS</th>
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Kelas</th>
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Gender</th>
                        <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y border-admin-border">
                    @forelse($siswa as $s)
                        <tr class="hover:bg-admin-canvas/30 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $s->user->foto_url }}" class="w-9 h-9 rounded-admin-full object-cover border border-admin-border" alt="">
                                    <span class="text-sm font-semibold text-admin-ink">{{ $s->user->name }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-sm text-admin-slate">{{ $s->nis }}</td>
                            <td class="py-4 px-6 text-sm text-admin-ink">{{ $s->kelas->nama_kelas ?? 'Belum ada kelas' }}</td>
                            <td class="py-4 px-6 text-sm text-admin-slate">
                                {{ $s->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('siswa.edit', $s) }}" class="p-2 rounded-admin-md border border-admin-border text-admin-indigo hover:bg-admin-indigo-tint hover:border-admin-indigo/20 transition-all duration-150" title="Edit">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                    @if(auth()->user()->isSuperAdmin())
                                    <form action="{{ route('siswa.destroy', $s) }}" method="POST" class="inline" onsubmit="confirmDelete(event, 'Data siswa akan dihapus permanen.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 rounded-admin-md border border-admin-border text-admin-danger hover:bg-admin-danger-tint hover:border-admin-danger/20 transition-all duration-150" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-admin-mist">
                                <i data-lucide="users" class="w-10 h-10 mx-auto mb-2 opacity-40"></i>
                                <p class="text-sm">Belum ada data siswa</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Area -->
        @if($siswa->hasPages())
            <div class="px-6 py-4 border-t border-admin-border bg-admin-canvas/10">
                {{ $siswa->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

