@extends('layouts.admin')
@section('title', 'Buat Periode Gaji')
@section('header', 'Buat Periode Gaji')
@section('content')
<div class="max-w-xl mx-auto animate-fade-in-up">
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-6">
        <form method="POST" action="{{ route('finance.payroll-periods.store') }}" class="space-y-5">
            @csrf
            @php $monthNames = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember']; @endphp
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-admin-slate">Bulan</label>
                    <select name="month" required
                            class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                        @foreach($monthNames as $val => $label)
                            <option value="{{ $val }}" {{ old('month') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('month') <p class="text-xs text-admin-danger mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold text-admin-slate">Tahun</label>
                    <input type="number" name="year" value="{{ old('year', date('Y')) }}" required min="2000" max="2099"
                           class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    @error('year') <p class="text-xs text-admin-danger mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <p class="text-xs text-admin-mist bg-admin-canvas rounded-admin-md p-3">Periode gaji akan otomatis membuat rincian gaji untuk semua karyawan aktif.</p>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-admin-indigo text-white px-6 py-2.5 rounded-admin-md text-sm font-medium hover:bg-admin-indigo-deep transition-colors">Buat Periode</button>
                <a href="{{ route('finance.payroll-periods.index') }}" class="text-admin-slate hover:text-admin-ink text-sm font-medium transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
