@extends('layouts.admin')
@section('title', 'Edit Lokasi')
@section('header', 'Edit Lokasi')
@php
    $shift1 = $location->shifts->get(0);
    $shift2 = $location->shifts->get(1);
    $shift3 = $location->shifts->get(2);
@endphp
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

        <form action="{{ route('locations.update', $location) }}" method="POST" class="space-y-6" x-data="{ 
            shiftsCount: {{ old('shift3_name', isset($shift3)) ? 3 : (old('shift2_name', isset($shift2)) ? 2 : 1) }},
            shift1Is24: {{ old('shift1_is_24_hours', $shift1->is_24_hours ?? false) ? 'true' : 'false' }},
            shift2Is24: {{ old('shift2_is_24_hours', $shift2->is_24_hours ?? false) ? 'true' : 'false' }},
            shift3Is24: {{ old('shift3_is_24_hours', $shift3->is_24_hours ?? false) ? 'true' : 'false' }}
        }">
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
                <label class="text-xs font-semibold text-admin-slate">Radius Geofence (meter)</label>
                <input type="number" name="radius" value="{{ old('radius', $location->radius) }}" required min="10" max="1000"
                       class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
            </div>

            <!-- Map Picker -->
            <div class="border border-admin-border rounded-admin-lg overflow-hidden">
                <div class="p-3 bg-admin-canvas border-b border-admin-border flex flex-wrap items-center gap-2">
                    <div class="relative flex-1 min-w-[200px]">
                        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-admin-slate"></i>
                        <input type="text" id="mapSearch" placeholder="Cari tempat... (contoh: Jl. Raya Malang)"
                               class="w-full pl-9 pr-3 py-2 bg-admin-surface border border-admin-border rounded-admin-md text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <button type="button" onclick="getLocationGPS()" id="gpsBtn"
                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-600 text-white rounded-admin-md text-sm font-medium hover:bg-emerald-700 transition-colors whitespace-nowrap">
                        <i data-lucide="crosshair" class="w-4 h-4"></i>
                        GPS Saya
                    </button>
                </div>
                <div id="locationMap" style="height:350px;min-height:350px;position:relative;z-index:1"></div>
                <div class="p-2 bg-admin-canvas border-t border-admin-border flex items-center justify-between text-xs text-admin-slate">
                    <span>Klik di peta untuk set lokasi, atau geser marker</span>
                    <span id="mapCoordsStatus">—</span>
                </div>
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


            <!-- Section Shift 1 -->
            <div class="border-t border-admin-border pt-6 space-y-4">
                <h3 class="text-sm font-semibold text-admin-ink">Shift 1 (Opsional)</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="text-xs font-semibold text-admin-slate">Nama Shift 1</label>
                        <input type="text" name="shift1_name" value="{{ old('shift1_name', $shift1->nama_shift ?? '') }}" placeholder="Contoh: Shift Pagi"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div :class="shift1Is24 ? 'col-span-2' : 'col-span-1'">
                        <label class="text-xs font-semibold text-admin-slate">Jam Masuk</label>
                        <input type="time" name="shift1_jam_masuk" value="{{ old('shift1_jam_masuk', isset($shift1) ? \Carbon\Carbon::parse($shift1->jam_masuk)->format('H:i') : '') }}"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div x-show="!shift1Is24" class="col-span-1">
                        <label class="text-xs font-semibold text-admin-slate">Jam Keluar</label>
                        <input type="time" name="shift1_jam_keluar" value="{{ old('shift1_jam_keluar', isset($shift1) ? \Carbon\Carbon::parse($shift1->jam_keluar)->format('H:i') : '') }}"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs font-semibold text-admin-slate">Batas Terlambat</label>
                        <input type="time" name="shift1_batas_terlambat" value="{{ old('shift1_batas_terlambat', isset($shift1) ? \Carbon\Carbon::parse($shift1->batas_terlambat)->format('H:i') : '') }}"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div class="col-span-2 flex items-center gap-3 mt-1">
                        <input type="checkbox" name="shift1_is_24_hours" id="shift1_is_24_hours" x-model="shift1Is24" value="1" {{ old('shift1_is_24_hours', $shift1->is_24_hours ?? false) ? 'checked' : '' }}
                               class="w-4 h-4 text-admin-indigo bg-admin-canvas border-admin-border rounded focus:ring-admin-indigo/25">
                        <label for="shift1_is_24_hours" class="text-xs font-semibold text-admin-slate cursor-pointer">Shift 24 Jam (Centang jika karyawan bekerja 24 jam penuh di shift ini)</label>
                    </div>
                </div>
            </div>

            <!-- Section Shift 2 -->
            <div x-show="shiftsCount >= 2" x-cloak class="border-t border-admin-border pt-6 space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-admin-ink">Shift 2 (Opsional)</h3>
                    <button type="button" @click="shiftsCount = 1; shift2Is24 = false; $refs.shift2_name.value = ''; $refs.shift2_masuk.value = ''; $refs.shift2_keluar.value = ''; $refs.shift2_terlambat.value = ''; $refs.shift2_24h.checked = false;" class="text-xs text-red-600 hover:text-red-800 font-semibold">Hapus Shift 2</button>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="text-xs font-semibold text-admin-slate">Nama Shift 2</label>
                        <input type="text" name="shift2_name" x-ref="shift2_name" value="{{ old('shift2_name', $shift2->nama_shift ?? '') }}" placeholder="Contoh: Shift Sore"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div :class="shift2Is24 ? 'col-span-2' : 'col-span-1'">
                        <label class="text-xs font-semibold text-admin-slate">Jam Masuk</label>
                        <input type="time" name="shift2_jam_masuk" x-ref="shift2_masuk" value="{{ old('shift2_jam_masuk', isset($shift2) ? \Carbon\Carbon::parse($shift2->jam_masuk)->format('H:i') : '') }}"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div x-show="!shift2Is24" class="col-span-1">
                        <label class="text-xs font-semibold text-admin-slate">Jam Keluar</label>
                        <input type="time" name="shift2_jam_keluar" x-ref="shift2_keluar" value="{{ old('shift2_jam_keluar', isset($shift2) ? \Carbon\Carbon::parse($shift2->jam_keluar)->format('H:i') : '') }}"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs font-semibold text-admin-slate">Batas Terlambat</label>
                        <input type="time" name="shift2_batas_terlambat" x-ref="shift2_terlambat" value="{{ old('shift2_batas_terlambat', isset($shift2) ? \Carbon\Carbon::parse($shift2->batas_terlambat)->format('H:i') : '') }}"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div class="col-span-2 flex items-center gap-3 mt-1">
                        <input type="checkbox" name="shift2_is_24_hours" id="shift2_is_24_hours" x-ref="shift2_24h" x-model="shift2Is24" value="1" {{ old('shift2_is_24_hours', $shift2->is_24_hours ?? false) ? 'checked' : '' }}
                               class="w-4 h-4 text-admin-indigo bg-admin-canvas border-admin-border rounded focus:ring-admin-indigo/25">
                        <label for="shift2_is_24_hours" class="text-xs font-semibold text-admin-slate cursor-pointer">Shift 24 Jam (Centang jika karyawan bekerja 24 jam penuh di shift ini)</label>
                    </div>
                </div>
            </div>

            <!-- Section Shift 3 -->
            <div x-show="shiftsCount >= 3" x-cloak class="border-t border-admin-border pt-6 space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-admin-ink">Shift 3 (Opsional)</h3>
                    <button type="button" @click="shiftsCount = 2; shift3Is24 = false; $refs.shift3_name.value = ''; $refs.shift3_masuk.value = ''; $refs.shift3_keluar.value = ''; $refs.shift3_terlambat.value = ''; $refs.shift3_24h.checked = false;" class="text-xs text-red-600 hover:text-red-800 font-semibold">Hapus Shift 3</button>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="text-xs font-semibold text-admin-slate">Nama Shift 3</label>
                        <input type="text" name="shift3_name" x-ref="shift3_name" value="{{ old('shift3_name', $shift3->nama_shift ?? '') }}" placeholder="Contoh: Shift Malam"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div :class="shift3Is24 ? 'col-span-2' : 'col-span-1'">
                        <label class="text-xs font-semibold text-admin-slate">Jam Masuk</label>
                        <input type="time" name="shift3_jam_masuk" x-ref="shift3_masuk" value="{{ old('shift3_jam_masuk', isset($shift3) ? \Carbon\Carbon::parse($shift3->jam_masuk)->format('H:i') : '') }}"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div x-show="!shift3Is24" class="col-span-1">
                        <label class="text-xs font-semibold text-admin-slate">Jam Keluar</label>
                        <input type="time" name="shift3_jam_keluar" x-ref="shift3_keluar" value="{{ old('shift3_jam_keluar', isset($shift3) ? \Carbon\Carbon::parse($shift3->jam_keluar)->format('H:i') : '') }}"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs font-semibold text-admin-slate">Batas Terlambat</label>
                        <input type="time" name="shift3_batas_terlambat" x-ref="shift3_terlambat" value="{{ old('shift3_batas_terlambat', isset($shift3) ? \Carbon\Carbon::parse($shift3->batas_terlambat)->format('H:i') : '') }}"
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    </div>
                    <div class="col-span-2 flex items-center gap-3 mt-1">
                        <input type="checkbox" name="shift3_is_24_hours" id="shift3_is_24_hours" x-ref="shift3_24h" x-model="shift3Is24" value="1" {{ old('shift3_is_24_hours', $shift3->is_24_hours ?? false) ? 'checked' : '' }}
                               class="w-4 h-4 text-admin-indigo bg-admin-canvas border-admin-border rounded focus:ring-admin-indigo/25">
                        <label for="shift3_is_24_hours" class="text-xs font-semibold text-admin-slate cursor-pointer">Shift 24 Jam (Centang jika karyawan bekerja 24 jam penuh di shift ini)</label>
                    </div>
                </div>
            </div>

            <!-- Tombol Tambah Shift -->
            <div class="pt-4 border-t border-admin-border/50" x-show="shiftsCount < 3">
                <button type="button" @click="shiftsCount++; $nextTick(() => { lucide.createIcons(); })" class="inline-flex items-center gap-1.5 px-4 py-2 bg-admin-indigo/10 text-admin-indigo rounded-admin-md text-sm font-semibold hover:bg-admin-indigo/20 transition-colors">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah Shift Kerja
                </button>
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

<script>
(function() {
    var latInput = document.getElementById('latitude');
    var lngInput = document.getElementById('longitude');
    var status = document.getElementById('mapCoordsStatus');
    var mapEl = document.getElementById('locationMap');

    if (!mapEl || typeof L === 'undefined') {
        console.error('Map container or Leaflet not found');
        if (status) status.innerHTML = 'Error: Leaflet tidak tersedia';
        return;
    }

    var centerLat = parseFloat(latInput.value) || -7.2575;
    var centerLng = parseFloat(lngInput.value) || 112.7521;

    var map = L.map('locationMap', {
        center: [centerLat, centerLng],
        zoom: 15,
        zoomControl: true,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
        maxZoom: 19,
    }).addTo(map);

    setTimeout(function() { map.invalidateSize(); }, 100);
    setTimeout(function() { map.invalidateSize(); }, 500);

    var marker = L.marker([centerLat, centerLng], { draggable: true }).addTo(map);
    updateCoords(centerLat, centerLng);

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateCoords(e.latlng.lat, e.latlng.lng);
    });

    marker.on('dragend', function() {
        var pos = marker.getLatLng();
        updateCoords(pos.lat, pos.lng);
    });

    function updateFromInputs() {
        var lat = parseFloat(latInput.value);
        var lng = parseFloat(lngInput.value);
        if (!isNaN(lat) && !isNaN(lng)) {
            marker.setLatLng([lat, lng]);
            map.setView([lat, lng], 15);
            updateCoords(lat, lng);
        }
    }
    latInput.addEventListener('change', updateFromInputs);
    lngInput.addEventListener('change', updateFromInputs);

    var searchInput = document.getElementById('mapSearch');
    var searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() { doSearch(searchInput.value); }, 600);
    });
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); doSearch(this.value); }
    });

    function doSearch(query) {
        if (!query || query.length < 3) return;
        status.innerHTML = 'Mencari...';
        fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query) + '&limit=5&countrycodes=id')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.length > 0) {
                    var loc = data[0];
                    var lat = parseFloat(loc.lat), lng = parseFloat(loc.lon);
                    marker.setLatLng([lat, lng]);
                    map.setView([lat, lng], 16);
                    updateCoords(lat, lng);
                    status.innerHTML = '&#128205; ' + loc.display_name.substring(0, 80) + '...';
                } else {
                    status.innerHTML = '<span style="color:#DC2626">Lokasi tidak ditemukan</span>';
                }
            })
            .catch(function() { status.innerHTML = '<span style="color:#DC2626">Gagal mencari</span>'; });
    }

    function updateCoords(lat, lng) {
        latInput.value = lat.toFixed(7);
        lngInput.value = lng.toFixed(7);
        status.innerHTML = 'Lat: <strong>' + lat.toFixed(5) + '</strong> / Lng: <strong>' + lng.toFixed(5) + '</strong>';
    }

    window.getLocationGPS = function() {
        if (!navigator.geolocation) { alert('GPS tidak didukung browser.'); return; }
        var btn = document.getElementById('gpsBtn');
        btn.disabled = true;
        btn.innerHTML = 'Mencari...';
        navigator.geolocation.getCurrentPosition(
            function(pos) {
                var lat = pos.coords.latitude, lng = pos.coords.longitude;
                marker.setLatLng([lat, lng]);
                map.setView([lat, lng], 16);
                updateCoords(lat, lng);
                status.innerHTML = 'GPS (akurasi ' + Math.round(pos.coords.accuracy) + 'm)';
                btn.disabled = false;
                btn.innerHTML = '<i data-lucide="crosshair" class="w-4 h-4"></i> GPS Saya';
                if (typeof lucide !== 'undefined') lucide.createIcons();
            },
            function() {
                alert('Gagal GPS. Cek izin lokasi.');
                btn.disabled = false;
                btn.innerHTML = '<i data-lucide="crosshair" class="w-4 h-4"></i> GPS Saya';
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    };
})();
</script>
@endsection
