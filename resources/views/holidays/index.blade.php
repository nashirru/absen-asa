@extends('layouts.admin')
@section('title', 'Hari Libur')
@section('header', 'Manajemen Hari Libur')

@section('content')
<div class="space-y-6 animate-fade-in-up">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ===== FORM TAMBAH ===== --}}
        <div class="lg:col-span-1">
            <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-6">

                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-admin-border">
                    <div class="w-9 h-9 rounded-admin-md bg-admin-indigo-tint flex items-center justify-center">
                        <i data-lucide="plus-circle" class="w-4 h-4 text-admin-indigo"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-admin-ink text-sm">Tambah Hari Libur</h3>
                        <p class="text-[11px] text-admin-slate mt-0.5">Admin & Super Admin</p>
                    </div>
                </div>

                <form action="{{ route('holidays.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="text-xs font-semibold text-admin-slate uppercase tracking-widest block mb-1.5">Tanggal</label>
                        <input type="date" name="tanggal"
                               value="{{ old('tanggal') }}"
                               required
                               class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25 transition-shadow">
                        @error('tanggal')
                            <p class="text-xs text-admin-danger mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-admin-slate uppercase tracking-widest block mb-1.5">Keterangan</label>
                        <input type="text" name="keterangan"
                               value="{{ old('keterangan') }}"
                               required
                               placeholder="Contoh: Hari Raya Idul Fitri"
                               class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25 transition-shadow placeholder:text-admin-mist">
                        @error('keterangan')
                            <p class="text-xs text-admin-danger mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="w-full py-3 bg-admin-indigo text-white rounded-admin-md font-semibold text-sm hover:bg-admin-indigo-deep transition-colors duration-150 flex items-center justify-center gap-2">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Simpan Hari Libur
                    </button>
                </form>

                <div class="mt-5 pt-5 border-t border-admin-border">
                    <a href="{{ route('rekap.absensi') }}"
                       class="w-full flex items-center justify-center gap-2 py-2.5 border border-admin-border rounded-admin-md text-sm font-semibold text-admin-slate hover:bg-admin-canvas hover:text-admin-ink transition-colors duration-150">
                        <i data-lucide="table" class="w-4 h-4"></i>
                        Lihat Rekap Absensi
                    </a>
                </div>
            </div>

            {{-- Info box --}}
            <div class="mt-4 bg-admin-indigo-tint border border-admin-indigo/15 rounded-admin-lg p-4">
                <div class="flex items-start gap-3">
                    <i data-lucide="info" class="w-4 h-4 text-admin-indigo mt-0.5 shrink-0"></i>
                    <div class="text-xs text-admin-indigo/80 leading-relaxed">
                        Hari libur yang terdaftar akan ditandai di kolom rekap absensi dan
                        tidak dihitung sebagai <strong class="text-admin-indigo">Alpha</strong>.
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== TABLE DAFTAR LIBUR ===== --}}
        <div class="lg:col-span-2">
            <div class="bg-admin-surface border border-admin-border rounded-admin-lg overflow-hidden">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-admin-border">
                    <div>
                        <h3 class="font-semibold text-admin-ink">Daftar Hari Libur</h3>
                        <p class="text-xs text-admin-slate mt-0.5">{{ $allHolidays->total() }} entri terdaftar</p>
                    </div>
                </div>

                @if($allHolidays->isEmpty())
                    <div class="flex flex-col items-center justify-center py-20 text-center">
                        <div class="w-12 h-12 rounded-admin-lg bg-admin-canvas border border-admin-border flex items-center justify-center mb-4">
                            <i data-lucide="calendar-off" class="w-6 h-6 text-admin-mist"></i>
                        </div>
                        <p class="text-sm font-semibold text-admin-slate">Belum ada hari libur</p>
                        <p class="text-xs text-admin-mist mt-1">Gunakan form di samping untuk mendaftarkan hari libur.</p>
                    </div>
                @else
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="border-b border-admin-border bg-admin-canvas/40">
                                <th class="py-3 px-6 text-left text-xs font-semibold uppercase tracking-widest text-admin-slate">Tanggal</th>
                                <th class="py-3 px-6 text-left text-xs font-semibold uppercase tracking-widest text-admin-slate">Hari</th>
                                <th class="py-3 px-6 text-left text-xs font-semibold uppercase tracking-widest text-admin-slate">Keterangan</th>
                                <th class="py-3 px-6 text-right text-xs font-semibold uppercase tracking-widest text-admin-slate"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-admin-border">
                            @foreach($allHolidays as $holiday)
                                @php
                                    $tgl = \Carbon\Carbon::parse($holiday->tanggal);
                                    $isThisMonth = $tgl->month === now()->month && $tgl->year === now()->year;
                                    $isPast = $tgl->isPast();
                                @endphp
                                <tr class="hover:bg-admin-canvas/30 transition-colors duration-100">
                                    <td class="py-3.5 px-6">
                                        <span class="text-sm font-mono font-semibold text-admin-ink">
                                            {{ $tgl->format('d M Y') }}
                                        </span>
                                        @if($isThisMonth)
                                            <span class="ml-2 px-2 py-0.5 text-[10px] font-semibold rounded-admin-full bg-admin-indigo-tint text-admin-indigo">Bulan ini</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-6 text-sm text-admin-slate">
                                        {{ $tgl->locale('id')->isoFormat('dddd') }}
                                    </td>
                                    <td class="py-3.5 px-6">
                                        <div class="flex items-center gap-2">
                                            <div class="w-1.5 h-1.5 rounded-full bg-admin-slate/30 shrink-0"></div>
                                            <span class="text-sm text-admin-ink">{{ $holiday->keterangan }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-6 text-right">
                                        <form action="{{ route('holidays.destroy', $holiday) }}" method="POST"
                                              class="delete-holiday-form">
                                            @csrf @method('DELETE')
                                            <input type="hidden" class="confirm-date" value="{{ \Carbon\Carbon::parse($holiday->tanggal)->format('d M Y') }}">
                                            <button type="submit"
                                                    title="Hapus"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-admin-danger border border-admin-border rounded-admin-md hover:bg-admin-danger-tint hover:border-admin-danger/20 transition-all duration-150">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if($allHolidays->hasPages())
                        <div class="px-6 py-4 border-t border-admin-border">
                            {{ $allHolidays->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-holiday-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var dateVal = form.querySelector('.confirm-date') ? form.querySelector('.confirm-date').value : '';
            var msg = dateVal ? 'Hapus hari libur ' + dateVal + '?' : 'Yakin ingin menghapus hari libur ini?';
            if (!confirm(msg)) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endpush
