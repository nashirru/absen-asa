@extends('layouts.admin')
@section('title', isset($karyawan) ? 'Edit Karyawan' : 'Tambah Karyawan')
@section('header', isset($karyawan) ? 'Edit Karyawan' : 'Tambah Karyawan')
@section('content')
<div class="max-w-3xl mx-auto animate-fade-in-up space-y-6">
    @if(session('success'))
        <div class="p-4 bg-admin-success-tint text-admin-success rounded-admin-md text-sm font-medium">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-admin-danger-tint text-admin-danger rounded-admin-md text-sm font-medium">{{ session('error') }}</div>
    @endif

    {{-- Form Utama Karyawan --}}
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-6">
        <form x-data="ajaxForm({ action: '{{ isset($karyawan) ? route('karyawan.update', $karyawan) : route('karyawan.store') }}' })"
              @submit.prevent="submit" method="POST"
              enctype="multipart/form-data" class="space-y-4">
            @csrf @if(isset($karyawan)) @method('PUT') @endif

            {{-- Data Akun --}}
            <fieldset class="border border-admin-border rounded-admin-md p-4">
                <legend class="text-xs font-bold uppercase tracking-wider text-admin-indigo px-2">Data Akun</legend>
                <div class="space-y-3">
                    <input type="text" name="name" value="{{ old('name', $karyawan->user->name ?? '') }}" required placeholder="Nama" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <input type="email" name="email" value="{{ old('email', $karyawan->user->email ?? '') }}" required placeholder="Email" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <input type="password" name="password" placeholder="Password {{ isset($karyawan) ? '(kosongkan jika tidak diubah)' : '' }}" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <input type="text" name="phone" value="{{ old('phone', $karyawan->user->phone ?? '') }}" placeholder="No HP" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <input type="file" name="foto" accept="image/*" class="w-full px-4 py-2 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink">
                </div>
            </fieldset>

            {{-- Data Karyawan --}}
            <fieldset class="border border-admin-border rounded-admin-md p-4">
                <legend class="text-xs font-bold uppercase tracking-wider text-admin-indigo px-2">Data Karyawan</legend>
                <div class="space-y-3">
                    <input type="text" name="nik" value="{{ old('nik', $karyawan->nik ?? '') }}" required placeholder="NIK" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <input type="text" name="jabatan" value="{{ old('jabatan', $karyawan->jabatan ?? '') }}" required placeholder="Jabatan" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <input type="text" name="divisi" value="{{ old('divisi', $karyawan->divisi ?? '') }}" placeholder="Divisi" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <textarea name="alamat" rows="2" placeholder="Alamat" class="w-full px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">{{ old('alamat', $karyawan->alamat ?? '') }}</textarea>
                </div>
            </fieldset>

            {{-- Data Penggajian --}}
            <fieldset class="border border-admin-border rounded-admin-md p-4">
                <legend class="text-xs font-bold uppercase tracking-wider text-admin-indigo px-2">Data Penggajian</legend>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-semibold text-admin-slate">Gaji Pokok</label>
                        <input type="number" step="0.01" name="base_salary" value="{{ old('base_salary', $karyawan->base_salary ?? '') }}" min="0" placeholder="Rp 0"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-admin-slate">Tanggal Masuk</label>
                        <input type="date" name="join_date" value="{{ old('join_date', isset($karyawan) && $karyawan->join_date ? $karyawan->join_date->format('Y-m-d') : '') }}"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-admin-slate">Status</label>
                        <select name="status"
                                class="w-full mt-1 px-4 py-2.5 bg-admin-canvas border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                            <option value="active" {{ old('status', $karyawan->status ?? '') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status', $karyawan->status ?? '') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>
            </fieldset>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('karyawan.index') }}" class="flex-1 py-3 text-center rounded-admin-md border border-admin-border font-semibold text-sm hover:bg-admin-canvas transition-colors">Batal</a>
                <button type="submit" class="flex-1 py-3 bg-admin-indigo text-white rounded-admin-md font-semibold text-sm hover:bg-admin-indigo-deep transition-colors">Simpan</button>
            </div>
        </form>
    </div>

    {{-- Inline Salary Components (hanya saat edit) --}}
    @if(isset($karyawan))
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg">
        <div class="p-5 border-b border-admin-border flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-semibold text-admin-ink">Komponen Gaji</h3>
                <p class="text-xs text-admin-slate mt-0.5">Tunjangan & potongan untuk {{ $karyawan->user->name }}</p>
            </div>
        </div>

        {{-- Existing Components Table --}}
        @if($karyawan->salaryComponents->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-admin-canvas text-admin-slate text-xs font-semibold uppercase tracking-wider">
                        <th class="text-left px-5 py-3">Nama</th>
                        <th class="text-left px-5 py-3">Tipe</th>
                        <th class="text-right px-5 py-3">Jumlah</th>
                        <th class="text-center px-5 py-3" style="width:100px">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-admin-border">
                    @php
                        $totalAllowance = $karyawan->salaryComponents->where('type', 'allowance')->sum('amount');
                        $totalDeduction = $karyawan->salaryComponents->where('type', 'deduction')->sum('amount');
                        $netTotal = ($karyawan->base_salary ?? 0) + $totalAllowance - $totalDeduction;
                    @endphp
                    @foreach($karyawan->salaryComponents as $comp)
                    <tr class="hover:bg-admin-canvas/50 transition-colors">
                        <td class="px-5 py-3 text-admin-ink font-medium">{{ $comp->name }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-admin-full text-xs font-medium {{ $comp->type == 'allowance' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $comp->type == 'allowance' ? 'Tunjangan' : 'Potongan' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right font-mono {{ $comp->type == 'allowance' ? 'text-emerald-700' : 'text-red-700' }}">
                            Rp {{ number_format($comp->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3 text-center">
                            <form x-data="ajaxForm({ action: '{{ route('karyawan.salary-components.destroy', $comp) }}', callback: () => { window.location.reload(); } })"
                                  @submit.prevent="if(confirm('Hapus komponen {{ $comp->name }}?')) submit()" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-admin-danger hover:text-red-700 text-xs font-medium cursor-pointer">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-admin-canvas/50 border-t border-admin-border font-semibold text-xs">
                    <tr>
                        <td colspan="2" class="px-5 py-3 text-admin-slate">Ringkasan Gaji</td>
                        <td class="px-5 py-3 text-right text-admin-ink font-mono">Rp {{ number_format($karyawan->base_salary ?? 0, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                    <tr class="text-emerald-700">
                        <td colspan="2" class="px-5 py-3">Total Tunjangan</td>
                        <td class="px-5 py-3 text-right font-mono">+ Rp {{ number_format($totalAllowance, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                    <tr class="text-red-700">
                        <td colspan="2" class="px-5 py-3">Total Potongan</td>
                        <td class="px-5 py-3 text-right font-mono">- Rp {{ number_format($totalDeduction, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                    <tr class="text-admin-ink text-sm border-t border-admin-border">
                        <td colspan="2" class="px-5 py-3">Gaji Bersih</td>
                        <td class="px-5 py-3 text-right font-mono font-bold">Rp {{ number_format(max(0, $netTotal), 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="p-8 text-center text-admin-mist">
            <p class="text-sm">Belum ada komponen gaji. Tambahkan tunjangan atau potongan di bawah.</p>
        </div>
        @endif

        {{-- Add New Component Form --}}
        <div class="p-5 border-t border-admin-border bg-admin-canvas/20">
            <h4 class="text-xs font-semibold text-admin-slate mb-3 uppercase tracking-wider">Tambah Komponen Baru</h4>
            <form x-data="ajaxForm({ action: '{{ route('karyawan.salary-components.store', $karyawan) }}', callback: () => { window.location.reload(); } })"
                  @submit.prevent="submit" method="POST" class="flex flex-wrap items-end gap-3">
                @csrf
                <div class="flex-1 min-w-[160px]">
                    <input type="text" name="name" placeholder="Nama komponen (ex: Transport, BPJS)" required maxlength="255"
                           class="w-full px-3 py-2 bg-admin-surface border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                </div>
                <div>
                    <select name="type" required
                            class="px-3 py-2 bg-admin-surface border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                        <option value="allowance">Tunjangan (+)</option>
                        <option value="deduction">Potongan (-)</option>
                    </select>
                </div>
                <div>
                    <input type="number" step="0.01" name="amount" placeholder="Jumlah" required min="0"
                           class="w-28 px-3 py-2 bg-admin-surface border border-admin-border rounded-admin-md text-sm text-admin-ink font-mono focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                </div>
                <button type="submit" class="px-4 py-2 bg-admin-indigo text-white rounded-admin-md text-sm font-semibold hover:bg-admin-indigo-deep transition-colors whitespace-nowrap">
                    + Tambah
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
