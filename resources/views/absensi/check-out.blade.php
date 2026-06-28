@extends('layouts.member')
@section('title', 'Check Out')
@section('header', 'Absensi - Check Out')
@section('content')
<div class="space-y-5 animate-fade-in-up" x-data="checkOutApp()" x-init="init()">

    <!-- Page Header -->
    <div class="space-y-1">
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Check Out</h1>
        <p class="text-sm text-slate-500">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
    </div>

    @if(!$existingAbsensi || !$existingAbsensi->jam_masuk)
        <!-- Not checked in -->
        <div class="s-card p-6 text-center space-y-4">
            <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center mx-auto">
                <i data-lucide="alert-triangle" class="w-7 h-7 text-red-500"></i>
            </div>
            <div class="space-y-1">
                <h3 class="text-base font-semibold text-slate-900">Belum Check In</h3>
                <p class="text-sm text-slate-500">Anda harus check in terlebih dahulu sebelum check out.</p>
            </div>
            <a href="{{ route('absensi.check-in') }}" class="btn btn-primary btn-md btn-full">
                <i data-lucide="check-square" class="w-4 h-4"></i>
                Check In Sekarang
            </a>
        </div>

    @elseif($existingAbsensi->jam_keluar)
        <!-- Already checked out -->
        <div class="s-card p-6 text-center space-y-4">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center mx-auto">
                <i data-lucide="check-circle-2" class="w-7 h-7 text-emerald-600"></i>
            </div>
            <div class="space-y-1">
                <h3 class="text-base font-semibold text-slate-900">Sudah Check Out</h3>
                <p class="text-sm text-slate-500">Absensi hari ini sudah selesai</p>
            </div>

            <div class="bg-slate-50 rounded-xl p-4 space-y-2.5">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500">Jam Masuk</span>
                    <span class="text-sm font-semibold text-slate-900 font-mono">{{ $existingAbsensi->jam_masuk }}</span>
                </div>
                <div class="h-px bg-slate-100"></div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500">Jam Keluar</span>
                    <span class="text-sm font-semibold text-slate-900 font-mono">{{ $existingAbsensi->jam_keluar }}</span>
                </div>
            </div>

            @if(!$existingAbsensi->is_lembur)
                <a href="{{ route('absensi.lembur') }}" class="btn btn-amber btn-md btn-full">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                    Mulai Lembur
                </a>
            @endif
        </div>

    @else
        <!-- Check Out Form -->
        <!-- Shift & Time Info -->
        <div class="s-card p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-semibold text-slate-900">Shift {{ ucfirst($existingAbsensi->shift ?? 'Tidak diketahui') }}</p>
                    </div>
                    <p class="text-xs text-slate-400">Masuk: {{ $existingAbsensi->jam_masuk }}</p>
                </div>
            </div>
        </div>

        <!-- Duration Card -->
        @php
            $jamMasuk = \Carbon\Carbon::parse($existingAbsensi->jam_masuk);
            $sekarang = \Carbon\Carbon::now();
            $durasi = $jamMasuk->diff($sekarang);
            $jamKerja = $durasi->h . 'j ' . $durasi->i . 'm';
        @endphp
        <div class="s-card p-4 space-y-3">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Durasi Kerja</p>
            <p class="text-3xl font-bold tracking-tight text-slate-900">{{ $jamKerja }}</p>
            <div class="h-px bg-slate-100"></div>
            <div class="flex justify-between items-center">
                <span class="text-xs text-slate-500">Waktu Sekarang</span>
                <span class="text-sm font-semibold text-slate-700 font-mono">{{ $sekarang->format('H:i') }}</span>
            </div>
        </div>

        <!-- GPS Status -->
        <div class="s-card p-4 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="navigation" class="w-4 h-4 text-slate-400"></i>
                    <span class="text-sm font-medium text-slate-700">Status GPS</span>
                </div>
                <span x-show="gpsLoading" class="s-badge s-badge-info animate-pulse-soft">
                    <i data-lucide="loader" class="w-3 h-3"></i> Mencari lokasi...
                </span>
                <span x-show="!gpsLoading && gpsReady" class="s-badge s-badge-success">
                    <i data-lucide="check" class="w-3 h-3"></i> GPS Aktif
                </span>
                <span x-show="!gpsLoading && !gpsReady" class="s-badge s-badge-danger">
                    <i data-lucide="x" class="w-3 h-3"></i> GPS Error
                </span>
            </div>

            <div class="grid grid-cols-3 gap-2">
                <div class="bg-slate-50 rounded-lg p-3 text-center">
                    <p class="text-[10px] font-medium uppercase tracking-wider text-slate-400">Latitude</p>
                    <p class="text-xs font-mono font-semibold text-slate-700 mt-1" x-text="lat ? lat.toFixed(5) : '—'"></p>
                </div>
                <div class="bg-slate-50 rounded-lg p-3 text-center">
                    <p class="text-[10px] font-medium uppercase tracking-wider text-slate-400">Longitude</p>
                    <p class="text-xs font-mono font-semibold text-slate-700 mt-1" x-text="lng ? lng.toFixed(5) : '—'"></p>
                </div>
                <div class="bg-slate-50 rounded-lg p-3 text-center">
                    <p class="text-[10px] font-medium uppercase tracking-wider text-slate-400">Akurasi</p>
                    <p class="text-xs font-mono font-semibold text-slate-700 mt-1" x-text="accuracy ? accuracy.toFixed(0) + 'm' : '—'"></p>
                </div>
            </div>

            <div class="text-center" x-show="distance !== null">
                <span class="s-badge inline-flex text-xs"
                       :class="distance <= locationRadius ? 's-badge-success' : 's-badge-danger'">
                    <i data-lucide="ruler" class="w-3 h-3"></i>
                    Jarak: <span x-text="distance ? distance.toFixed(0) + 'm' : '—'"></span> / <span x-text="locationRadius"></span>m
                </span>
            </div>
        </div>

        <!-- Map -->
        <div class="s-card overflow-hidden">
            <div id="map" class="w-full h-48"></div>
        </div>

        <!-- Error message -->
        <div x-show="error" class="flex items-start gap-3 p-4 rounded-xl bg-red-50 border border-red-100">
            <i data-lucide="alert-triangle" class="w-4 h-4 text-red-500 shrink-0 mt-0.5"></i>
            <p class="text-sm font-medium text-red-600" x-text="error"></p>
        </div>

        <!-- Check Out Button -->
        <button @click="submit()" :disabled="!canSubmit || loading"
                class="btn btn-lg btn-full"
                :class="canSubmit && !loading ? 'btn-primary' : 'btn-secondary opacity-60'">
            <span x-show="!loading" class="flex items-center gap-2">
                <i data-lucide="log-out" class="w-4 h-4"></i> Check Out Sekarang
            </span>
            <span x-show="loading" class="animate-pulse-soft flex items-center gap-2">
                <i data-lucide="loader" class="w-4 h-4 animate-spin"></i> Memproses...
            </span>
        </button>
    @endif
</div>

@push('scripts')
<script>
function checkOutApp() {
    return {
        lat: null, lng: null, accuracy: null, distance: null,
        gpsLoading: false, gpsReady: false, isMocked: false,
        error: '', loading: false,
        locationLat: {{ $existingAbsensi && $existingAbsensi->location ? $existingAbsensi->location->latitude : 'null' }},
        locationLng: {{ $existingAbsensi && $existingAbsensi->location ? $existingAbsensi->location->longitude : 'null' }},
        locationRadius: {{ $existingAbsensi && $existingAbsensi->location ? $existingAbsensi->location->radius : '100' }},
        maxAccuracy: {{ $settings['max_accuracy'] }},
        map: null, markerUser: null, locationMarker: null, locationCircle: null,

        get canSubmit() {
            if (!this.locationLat) return this.gpsReady; // If no location configuration, allow submission
            return this.gpsReady && this.distance !== null && this.distance <= this.locationRadius;
        },

        init() {
            this.getGPS();
        },

        getDeviceId() {
            let deviceId = localStorage.getItem('absen_asa_device_uuid');
            if (!deviceId) {
                deviceId = 'device_' + Math.random().toString(36).substring(2, 15) + '_' + Date.now().toString(36);
                localStorage.setItem('absen_asa_device_uuid', deviceId);
            }
            return deviceId;
        },

        recalcDistance() {
            if (!this.lat || !this.locationLat) return;
            const R = 6371000;
            const dLat = (this.locationLat - this.lat) * Math.PI / 180;
            const dLon = (this.locationLng - this.lng) * Math.PI / 180;
            const a = Math.sin(dLat/2)**2 + Math.cos(this.lat*Math.PI/180) * Math.cos(this.locationLat*Math.PI/180) * Math.sin(dLon/2)**2;
            this.distance = R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            this.error = '';
            if (this.distance > this.locationRadius) {
                this.error = 'Anda berada di luar area absensi untuk check-out. Jarak: ' + Math.round(this.distance) + ' meter.';
            }
        },

        getGPS() {
            if (!navigator.geolocation) { this.error = 'Browser tidak mendukung GPS.'; return; }
            this.gpsLoading = true; this.error = '';
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.lat = pos.coords.latitude;
                    this.lng = pos.coords.longitude;
                    this.accuracy = pos.coords.accuracy;
                    this.isMocked = pos.mocked || (pos.coords && pos.coords.mocked) || pos.coords.accuracy === 0;
                    this.gpsLoading = false;
                    this.gpsReady = true;
                    if (this.locationLat) this.recalcDistance();
                    this.initMap();
                },
                (err) => {
                    this.gpsLoading = false;
                    this.error = 'Gagal mendapatkan lokasi. Pastikan GPS/lokasi aktif di browser.';
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        },

        initMap() {
            if (!document.getElementById('map')) return;
            if (this.map) {
                this.map.setView([this.lat, this.lng], 16);
                if (this.markerUser) this.markerUser.setLatLng([this.lat, this.lng]);
                else this.markerUser = L.marker([this.lat, this.lng]).addTo(this.map).bindPopup('Lokasi Anda').openPopup();
                this.updateMapLocation(); return;
            }
            this.map = L.map('map', { zoomControl: false }).setView([this.lat, this.lng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OSM' }).addTo(this.map);
            this.markerUser = L.marker([this.lat, this.lng]).addTo(this.map).bindPopup('Lokasi Anda').openPopup();
            this.updateMapLocation();
        },

        updateMapLocation() {
            if (!this.map) return;
            if (this.locationMarker) this.map.removeLayer(this.locationMarker);
            if (this.locationCircle) this.map.removeLayer(this.locationCircle);
            if (this.locationLat && this.locationLng) {
                this.locationMarker = L.marker([this.locationLat, this.locationLng]).addTo(this.map).bindPopup('Lokasi Absensi');
                this.locationCircle = L.circle([this.locationLat, this.locationLng], {
                    radius: this.locationRadius, color: '#1A6DFF', fillColor: '#1A6DFF', fillOpacity: 0.08, weight: 1.5
                }).addTo(this.map);
            }
        },

        async submit() {
            if (!this.canSubmit) return;
            this.loading = true;
            this.error = '';
            try {
                const r = await fetch('{{ route("absensi.store-check-out") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        latitude: this.lat,
                        longitude: this.lng,
                        accuracy: this.accuracy,
                        device_id: this.getDeviceId(),
                        is_mocked: this.isMocked
                    })
                });
                const d = await r.json();
                if (d.success) {
                    window.location.href = '{{ route("dashboard") }}';
                } else {
                    this.error = d.message || 'Gagal memproses check out.';
                }
            } catch(e) {
                this.error = 'Terjadi kesalahan jaringan.';
            }
            this.loading = false;
        }
    }
}
</script>
@endpush
@endsection
