@extends('layouts.member')
@section('title', 'Jadwal Saya')
@section('header', 'Jadwal Saya')
@section('content')
<div class="space-y-5 animate-fade-in-up">
    @foreach($hariList as $hari)
        @if(isset($jadwal[$hari]) && $jadwal[$hari]->count())
            <div class="bg-member-surface rounded-member-xl p-5 shadow-member-card border-0">
                <div class="flex items-center justify-between mb-4 border-b border-member-border/30 pb-2">
                    <span class="text-sm font-bold text-member-ink flex items-center gap-2">
                        <span class="w-1.5 h-3.5 bg-member-blue rounded-full"></span>
                        {{ $hari }}
                    </span>
                    <span class="text-xs text-member-slate font-medium">
                        {{ $jadwal[$hari]->count() }} Sesi
                    </span>
                </div>
                <div class="space-y-3">
                    @foreach($jadwal[$hari] as $j)
                        <div class="p-3.5 rounded-member-lg bg-member-canvas border border-member-border/30 transition-all duration-150 space-y-3">
                            <div class="flex items-center gap-4">
                                <!-- Time block -->
                                <div class="text-center min-w-[50px] border-r border-member-border/50 pr-3">
                                    <p class="text-xs font-bold text-member-blue">{{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }}</p>
                                    <p class="text-[10px] text-member-slate mt-0.5">{{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}</p>
                                </div>
                                <!-- Info block -->
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-member-ink truncate">{{ $j->mata_pelajaran }}</p>
                                    <div class="flex items-center gap-1.5 mt-1 text-xs text-member-slate">
                                        <span class="font-medium text-member-ink bg-member-blue-tint text-member-blue px-1.5 py-0.5 rounded text-[10px]">{{ $j->kelas->nama_kelas ?? '-' }}</span>
                                        <span>&bull;</span>
                                        <span class="truncate text-member-slate">{{ $j->sensei->user->name ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Modul & Action block -->
                            <div class="pt-2.5 border-t border-member-border/20 flex flex-wrap items-center justify-between gap-2 w-full text-xs">
                                <!-- Modul info -->
                                <div class="flex flex-wrap items-center gap-2">
                                    @if($j->modul_file)
                                        <a href="{{ asset('uploads/modul/' . $j->modul_file) }}" target="_blank" class="inline-flex items-center gap-1 font-bold text-member-blue hover:underline bg-member-blue-tint/50 px-2 py-0.5 rounded transition-colors text-[10px]">
                                            <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                            File Modul
                                        </a>
                                    @endif
                                    @if($j->modul_link)
                                        <a href="{{ $j->modul_link }}" target="_blank" class="inline-flex items-center gap-1 font-bold text-member-blue hover:underline bg-member-blue-tint/50 px-2 py-0.5 rounded transition-colors text-[10px]">
                                            <i data-lucide="link" class="w-3.5 h-3.5"></i>
                                            Link Modul
                                        </a>
                                    @endif
                                    @if(!$j->modul_file && !$j->modul_link)
                                        <span class="text-member-slate/50 text-[10px] italic">Tidak ada modul</span>
                                    @endif
                                </div>

                                <!-- Sensei Actions -->
                                @if(auth()->user()->isSensei())
                                    <a href="{{ route('sensei.jadwal.edit-modul', $j) }}" class="inline-flex items-center gap-1 font-bold text-member-blue hover:text-member-blue-deep bg-member-surface border border-member-border/50 hover:bg-member-blue-tint/30 px-2 py-0.5 rounded transition-all text-[10px]">
                                        <i data-lucide="edit-3" class="w-3 h-3"></i>
                                        Kelola Modul
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach

    @if($jadwal->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center bg-member-surface rounded-member-xl shadow-member-card">
            <div class="w-12 h-12 rounded-member-full bg-member-canvas flex items-center justify-center mb-3 text-member-slate">
                <i data-lucide="calendar" class="w-6 h-6"></i>
            </div>
            <h3 class="text-sm font-bold text-member-ink mb-1">Belum ada jadwal</h3>
            <p class="text-xs text-member-slate max-w-[200px]">Jadwal pelajaran Anda akan ditampilkan di sini.</p>
        </div>
    @endif
</div>
@endsection

