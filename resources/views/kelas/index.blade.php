@extends(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.member')
@section('title', 'Data Kelas')
@section('header', 'Kelas')
@section('content')
@php
    $isAdmin = auth()->user()->isSuperAdmin() || auth()->user()->isAdmin();
    $canvasClass = $isAdmin ? 'space-y-6 animate-fade-in-up' : 'space-y-4 animate-fade-in-up';
    $cardClass = $isAdmin ? 'bg-admin-surface border border-admin-border rounded-admin-lg p-5' : 'bg-member-surface rounded-member-xl p-5 shadow-member-card';
    $textInk = $isAdmin ? 'text-admin-ink' : 'text-member-ink';
    $textSlate = $isAdmin ? 'text-admin-slate' : 'text-member-slate';
    $inputClass = $isAdmin ? 'w-full pl-10 pr-4 py-2.5 bg-admin-canvas rounded-admin-full text-sm placeholder-admin-mist text-admin-ink focus:outline-none' : 'w-full pl-10 pr-4 py-2.5 bg-member-canvas rounded-member-full text-sm placeholder-member-mist text-member-ink focus:outline-none';
    $btnClass = $isAdmin ? 'px-5 py-2.5 bg-admin-indigo text-white rounded-admin-md text-sm font-semibold hover:bg-admin-indigo-deep transition-colors' : 'px-5 py-2.5 bg-member-blue text-white rounded-member-full text-sm font-semibold hover:bg-member-blue-deep transition-colors shadow-member-primary';
@endphp

<div class="{{ $canvasClass }}">
    <!-- Page Header & Actions -->
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold {{ $textInk }}">Daftar Kelas</h2>
        @if($isAdmin)
            <a href="{{ route('kelas.create') }}" class="{{ $btnClass }}">
                + Tambah Kelas
            </a>
        @endif
    </div>

    <!-- Search & Filter Card -->
    <div class="{{ $cardClass }}">
        <form class="flex gap-4">
            <div class="flex-1 relative">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none {{ $textSlate }}">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kelas atau tingkat..." 
                       class="{{ $inputClass }}">
            </div>
            <button type="submit" class="{{ $btnClass }}">
                Cari
            </button>
        </form>
    </div>

    <!-- Kelas Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($kelas as $k)
            <div class="{{ $cardClass }} flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-lg {{ $textInk }}">{{ $k->nama_kelas }}</h3>
                        
                        @if($isAdmin)
                            <div class="flex gap-2">
                                <a href="{{ route('kelas.edit', $k) }}" class="p-2 rounded-admin-md border border-admin-border text-admin-indigo hover:bg-admin-indigo-tint hover:border-admin-indigo/20 transition-all duration-150" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('kelas.destroy', $k) }}" method="POST" onsubmit="return confirm('Yakin hapus kelas ini?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-admin-md border border-admin-border text-admin-danger hover:bg-admin-danger-tint hover:border-admin-danger/20 transition-all duration-150" title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                    
                    <div class="space-y-1.5 text-sm">
                        <p class="{{ $textSlate }}">Tingkat: <span class="font-semibold {{ $textInk }}">{{ $k->tingkat }}</span></p>
                        <p class="{{ $textSlate }}">Sensei: <span class="font-semibold {{ $textInk }}">{{ $k->sensei->user->name ?? 'Belum ada' }}</span></p>
                        <p class="{{ $textSlate }}">Kapasitas: <span class="font-semibold {{ $textInk }}">{{ $k->kapasitas }} Siswa</span></p>
                    </div>

                    @if(!$isAdmin)
                        <div class="mt-4 pt-3 border-t border-member-border/20">
                            <a href="{{ route('kelas.saya.detail', $k) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-member-blue text-white rounded-member-full text-xs font-semibold hover:bg-member-blue-deep transition-all">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                Detail Kelas
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-2 text-center py-12 {{ $textSlate }}">
                <i data-lucide="book-open" class="w-10 h-10 mx-auto mb-2 opacity-40"></i>
                <p class="text-sm">Belum ada data kelas</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($kelas->hasPages())
        <div class="pt-4">
            {{ $kelas->links() }}
        </div>
    @endif
</div>
@endsection
