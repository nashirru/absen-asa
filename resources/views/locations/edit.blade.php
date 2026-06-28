@extends('layouts.admin')
@section('title', 'Edit Lokasi')
@section('header', 'Edit Lokasi')
@section('content')
<div class="max-w-2xl mx-auto animate-fade-in-up">
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-6">
        <h2 class="text-lg font-semibold text-admin-ink mb-6">Edit Lokasi: {{ $location->name }}</h2>

        @if($errors->any())
            <div class="bg-admin-danger-tint/80 border border-admin-danger/15 rounded-admin-lg p-4 mb-6">
                <ul class="list-disc list-inside text-sm text-admin-danger">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('locations.update', $location) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')

            <div>
                <label class="text-xs font-semibold text-admin-slate">Nama Lokasi</label>
                <input type="text" name="name" value="{{ old('name', $location->name) }}" required
                       class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-admin-slate">Latitude</label>
                    <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $location->latitude) }}" required
                           class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                </div>
                <div>
                    <label class="text-xs font-semibold text-admin-slate">Longitude</label>
                    <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $location->longitude) }}" required
                           class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                </div>
            </div>

            <div>
                <button type="button" onclick="setLocationFromBrowser()" id="setLocBtn"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white rounded-admin-md text-sm font-semibold hover:bg-emerald-700 transition-colors duration-150">
                    <i data-lucide="crosshair" class="w-4 h-4"></i>
                    Set Lokasi dari Browser
                </button>
                <p class="text-[11px] text-admin-slate mt-2">Klik tombol ini untuk mengisi koordinat dari lokasi PC Anda saat ini</p>
                <div id="locStatus" class="text-xs mt-2 hidden"></div>
            </div>

            <div>
                <label class="text-xs font-semibold text-admin-slate">Radius Geofence (meter)</label>
                <input type="number" name="radius" value="{{ old('radius', $location->radius) }}" required min="10" max="1000"
                       class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $location->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 text-admin-indigo bg-admin-canvas border-admin-border rounded focus:ring-admin-indigo/25">
                <label class="text-sm text-admin-ink font-medium">Aktif (bisa digunakan untuk absen)</label>
            </div>

            <div>
                <label class="text-xs font-semibold text-admin-slate">Akses Role (siapa yang boleh absen di sini)</label>
                <p class="text-[11px] text-admin-slate mt-1 mb-2">Kosongkan jika semua role boleh</p>
                <div class="flex flex-wrap gap-3">
                    @php $currentRoles = old('allowed_roles', $location->allowed_roles ?? []); @endphp
                    @foreach(['siswa' => 'Siswa', 'karyawan' => 'Karyawan', 'sensei' => 'Sensei'] as $val => $lbl)
                        <label class="inline-flex items-center gap-2 px-3 py-2 bg-admin-canvas border border-admin-border rounded-admin-md cursor-pointer hover:border-admin-indigo transition-colors">
                            <input type="checkbox" name="allowed_roles[]" value="{{ $val }}"
                                   {{ in_array($val, $currentRoles) ? 'checked' : '' }}
                                   class="w-4 h-4 text-admin-indigo bg-white border-admin-border rounded focus:ring-admin-indigo/25">
                            <span class="text-sm text-admin-ink font-medium">{{ $lbl }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            @php
                $shift1 = $location->shifts->get(0);
                $shift2 = $location->shifts->get(1);
            @endphp

            <!-- Section Shift 1 -->
            <div class="border-t border-admin-border pt-6 space-y-4">
                <h3 class="text-sm font-semibold text-admin-ink">Shift 1 (Opsional)</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="text-xs font-semibold text-admin-slate">Nama Shift 1</label>
                        <input type="text" name="shift1_name" value="{{ old('shift1_name', $shift1->nama_shift ?? '') }}" placeholder="Contoh: Shift Pagi"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-admin-slate">Jam Masuk</label>
                        <input type="time" name="shift1_jam_masuk" value="{{ old('shift1_jam_masuk', isset($shift1) ? \Carbon\Carbon::parse($shift1->jam_masuk)->format('H:i') : '') }}"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-admin-slate">Jam Keluar</label>
                        <input type="time" name="shift1_jam_keluar" value="{{ old('shift1_jam_keluar', isset($shift1) ? \Carbon\Carbon::parse($shift1->jam_keluar)->format('H:i') : '') }}"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs font-semibold text-admin-slate">Batas Terlambat</label>
                        <input type="time" name="shift1_batas_terlambat" value="{{ old('shift1_batas_terlambat', isset($shift1) ? \Carbon\Carbon::parse($shift1->batas_terlambat)->format('H:i') : '') }}"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                </div>
            </div>

            <!-- Section Shift 2 -->
            <div class="border-t border-admin-border pt-6 space-y-4">
                <h3 class="text-sm font-semibold text-admin-ink">Shift 2 (Opsional)</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="text-xs font-semibold text-admin-slate">Nama Shift 2</label>
                        <input type="text" name="shift2_name" value="{{ old('shift2_name', $shift2->nama_shift ?? '') }}" placeholder="Contoh: Shift Sore"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-admin-slate">Jam Masuk</label>
                        <input type="time" name="shift2_jam_masuk" value="{{ old('shift2_jam_masuk', isset($shift2) ? \Carbon\Carbon::parse($shift2->jam_masuk)->format('H:i') : '') }}"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-admin-slate">Jam Keluar</label>
                        <input type="time" name="shift2_jam_keluar" value="{{ old('shift2_jam_keluar', isset($shift2) ? \Carbon\Carbon::parse($shift2->jam_keluar)->format('H:i') : '') }}"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs font-semibold text-admin-slate">Batas Terlambat</label>
                        <input type="time" name="shift2_batas_terlambat" value="{{ old('shift2_batas_terlambat', isset($shift2) ? \Carbon\Carbon::parse($shift2->batas_terlambat)->format('H:i') : '') }}"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('locations.index') }}"
                   class="flex-1 py-3 bg-admin-canvas text-admin-ink rounded-admin-md font-semibold text-sm hover:bg-admin-border transition-colors text-center">
                    Batal
                </a>
                <button type="submit"
                        class="flex-[2] py-3 bg-admin-indigo text-white rounded-admin-md font-semibold hover:bg-admin-indigo-deep transition-colors">
                    Update Lokasi
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function setLocationFromBrowser() {
    if (!navigator.geolocation) {
        alert('Browser Anda tidak mendukung geolocation.');
        return;
    }

    const btn = document.getElementById('setLocBtn');
    const status = document.getElementById('locStatus');
    const originalText = btn.innerHTML;

    btn.innerHTML = '<span class="animate-pulse-soft">Mencari lokasi...</span>';
    btn.disabled = true;
    status.className = 'text-xs mt-2';
    status.style.color = '#6F6C84';
    status.textContent = 'Sedang mencari lokasi GPS...';
    status.classList.remove('hidden');

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            const lat = pos.coords.latitude.toFixed(7);
            const lng = pos.coords.longitude.toFixed(7);

            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;

            btn.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4"></i> Lokasi Terdeteksi!';
            btn.disabled = false;
            status.style.color = '#16A34A';
            status.innerHTML = 'Berhasil! Lat: <strong>' + lat + '</strong> | Lng: <strong>' + lng + '</strong> (Akurasi: ' + Math.round(pos.coords.accuracy) + 'm)';
            lucide.createIcons();

            setTimeout(() => {
                btn.innerHTML = originalText;
                lucide.createIcons();
            }, 3000);
        },
        (err) => {
            let msg = 'Gagal mendapatkan lokasi.';
            if (err.code === 1) msg = 'Izin lokasi ditolak. Mohon izinkan akses lokasi di browser.';
            else if (err.code === 2) msg = 'Lokasi tidak tersedia.';
            else if (err.code === 3) msg = 'Timeout mendapatkan lokasi.';

            btn.innerHTML = originalText;
            btn.disabled = false;
            status.style.color = '#DC2626';
            status.textContent = msg;
            lucide.createIcons();
        },
        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
    );
}
</script>
@endpush
@endsection
