@extends('layouts.admin')
@section('title', 'Pengaturan')
@section('header', 'Pengaturan')
@section('content')
<div class="max-w-2xl mx-auto animate-fade-in-up">
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-6">
        <h2 class="text-lg font-semibold text-admin-ink mb-6">Pengaturan Sistem</h2>
        <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
            @csrf @method('PUT')
            
            <!-- Umum -->
            <fieldset class="border border-admin-border rounded-admin-md p-4">
                <legend class="text-xs font-bold uppercase tracking-wider text-admin-indigo px-2">Umum</legend>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-2">
                    <div>
                        <label class="text-xs font-semibold text-admin-slate">Nama Aplikasi</label>
                        <input type="text" name="app_name" value="{{ $settings['app_name'] }}" 
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-admin-slate">Tanggal Mulai Absensi (Sistem)</label>
                        <input type="date" name="tanggal_mulai_absensi" value="{{ $settings['tanggal_mulai_absensi'] }}" 
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                        <p class="text-[10px] text-admin-mist mt-1 leading-snug">Sistem tidak akan menghitung Alpha sebelum tanggal ini.</p>
                    </div>
                </div>
            </fieldset>
            
            <!-- Libur Mingguan -->
            <fieldset class="border border-admin-border rounded-admin-md p-4">
                <legend class="text-xs font-bold uppercase tracking-wider text-admin-indigo px-2">Libur Mingguan Rutin</legend>
                <p class="text-xs text-admin-slate mt-1 mb-4 px-1">Pilih hari yang otomatis ditandai sebagai hari libur (tidak dihitung Alpha).</p>
                <div class="flex flex-wrap gap-4 px-1">
                    @php
                        $daysMap = [
                            1 => 'Senin',
                            2 => 'Selasa',
                            3 => 'Rabu',
                            4 => 'Kamis',
                            5 => 'Jumat',
                            6 => 'Sabtu',
                            0 => 'Minggu',
                        ];
                        $selectedDays = $settings['hari_libur_mingguan_arr'] ?? [];
                    @endphp
                    @foreach($daysMap as $val => $label)
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <div class="relative flex items-center justify-center">
                                <input type="checkbox" name="hari_libur_mingguan[]" value="{{ $val }}" 
                                       {{ in_array((string)$val, $selectedDays) ? 'checked' : '' }}
                                       class="appearance-none w-5 h-5 border border-admin-border rounded-md bg-admin-canvas checked:bg-admin-indigo checked:border-admin-indigo transition-colors cursor-pointer peer">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-white absolute pointer-events-none opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                            </div>
                            <span class="text-sm font-medium text-admin-ink group-hover:text-admin-indigo transition-colors">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>
            
            <!-- Jam Kerja -->
            <fieldset class="border border-admin-border rounded-admin-md p-4">
                <legend class="text-xs font-bold uppercase tracking-wider text-admin-indigo px-2">Jam Kerja</legend>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-2">
                    <div>
                        <label class="text-xs font-semibold text-admin-slate">Jam Masuk</label>
                        <input type="time" name="jam_masuk" value="{{ $settings['jam_masuk'] }}" 
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-admin-slate">Jam Keluar</label>
                        <input type="time" name="jam_keluar" value="{{ $settings['jam_keluar'] }}" 
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-admin-slate">Batas Terlambat</label>
                        <input type="time" name="batas_terlambat" value="{{ $settings['batas_terlambat'] }}" 
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                </div>
            </fieldset>
            
            <!-- GPS & Geofence -->
            <fieldset class="border border-admin-border rounded-admin-md p-4">
                <legend class="text-xs font-bold uppercase tracking-wider text-admin-indigo px-2">GPS & Geofence</legend>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-2">
                    <div>
                        <label class="text-xs font-semibold text-admin-slate">Latitude Kantor</label>
                        <input type="text" name="office_lat" id="office_lat" value="{{ $settings['office_lat'] }}"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-admin-slate">Longitude Kantor</label>
                        <input type="text" name="office_lng" id="office_lng" value="{{ $settings['office_lng'] }}"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-admin-slate">Radius (meter)</label>
                        <input type="number" name="geofence_radius" value="{{ $settings['geofence_radius'] }}"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-admin-slate">Max Accuracy (m)</label>
                        <input type="number" name="max_accuracy" value="{{ $settings['max_accuracy'] }}"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-admin-border/50">
                    <button type="button" onclick="getBrowserLocation()" id="getLocBtn"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-admin-indigo text-white rounded-admin-md text-sm font-semibold hover:bg-admin-indigo-deep transition-colors duration-150">
                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                        Ambil Lokasi dari Browser
                    </button>
                    <p class="text-[11px] text-admin-slate mt-2">Klik tombol ini untuk otomatis mengisi koordinat dari lokasi PC Anda saat ini</p>
                    <div id="locStatus" class="text-xs mt-2 hidden"></div>
                </div>
            </fieldset>
            
            <button type="submit" class="w-full py-3 bg-admin-indigo text-white rounded-admin-md font-semibold hover:bg-admin-indigo-deep transition-colors">
                Simpan Pengaturan
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function getBrowserLocation() {
    if (!navigator.geolocation) {
        alert('Browser Anda tidak mendukung geolocation.');
        return;
    }

    const btn = document.getElementById('getLocBtn');
    const status = document.getElementById('locStatus');
    const originalText = btn.innerHTML;

    btn.innerHTML = '<span class="animate-pulse-soft">Mencari lokasi...</span>';
    btn.disabled = true;
    status.className = 'text-xs mt-2 text-admin-slate';
    status.textContent = 'Sedang mencari lokasi GPS...';
    status.classList.remove('hidden');

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            const lat = pos.coords.latitude.toFixed(7);
            const lng = pos.coords.longitude.toFixed(7);

            document.getElementById('office_lat').value = lat;
            document.getElementById('office_lng').value = lng;

            btn.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4"></i> Lokasi Terdeteksi!';
            btn.disabled = false;
            status.className = 'text-xs mt-2 font-semibold';
            status.style.color = '#16A34A';
            status.textContent = 'Lat: ' + lat + ' | Lng: ' + lng + ' (Akurasi: ' + Math.round(pos.coords.accuracy) + 'm)';
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
            status.className = 'text-xs mt-2 font-semibold';
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

