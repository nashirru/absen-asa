@extends('layouts.member')
@section('title', 'Check In')
@section('header', 'Absensi - Check In')

@section('content')
<div class="space-y-5 animate-fade-in-up" x-data="checkInApp()" x-init="init()">

    <!-- ==================== SUCCESS STATE ==================== -->
    <div x-show="checkInSuccess" x-cloak class="space-y-5">
        <div class="s-card p-8 flex flex-col items-center text-center space-y-5">
            <!-- Check icon -->
            <div class="w-16 h-16 rounded-2xl bg-emerald-50 flex items-center justify-center">
                <i data-lucide="check-circle-2" class="w-8 h-8 text-emerald-600"></i>
            </div>
            <div class="space-y-1">
                <h3 class="text-lg font-semibold text-slate-900 tracking-tight">Absensi Berhasil!</h3>
                <p class="text-sm text-slate-500">Check in telah tercatat di sistem</p>
            </div>

            <!-- Profile Photo -->
            <div class="relative">
                <img :src="userFotoUrl" class="w-24 h-24 rounded-2xl object-cover border-2 border-emerald-100" alt="Foto Profil"
                     onerror="this.src='https://ui-avatars.com/api/?name=U&background=1A6DFF&color=fff&size=200'">
            </div>

            <div class="space-y-0.5">
                <p class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-400">{{ now()->locale('id')->isoFormat('dddd, D MMM Y') }}</p>
            </div>

            <div class="w-full bg-slate-50 rounded-xl p-4 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500">Waktu Masuk</span>
                    <span class="text-sm font-semibold text-slate-900 font-mono" x-text="checkInTime"></span>
                </div>
                <div class="h-px bg-slate-100"></div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500">Status</span>
                    <span class="s-badge s-badge-success" x-text="checkInStatus"></span>
                </div>
            </div>

            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-md btn-full">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- ==================== CHECK IN FORM ==================== -->
    <template x-if="!checkInSuccess">
        <div class="space-y-4">
            <!-- Already checked in / izin? -->
            @if($existingAbsensi)
                <div class="s-card p-6 text-center space-y-4">
                    @if($existingAbsensi->status === 'izin' || $existingAbsensi->status === 'sakit')
                        <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto">
                            <i data-lucide="file-text" class="w-7 h-7 text-blue-600"></i>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-base font-semibold text-slate-900">Pengajuan Absensi</h3>
                            <p class="text-sm text-slate-500">Anda telah mengajukan absensi {{ $existingAbsensi->status }} hari ini</p>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-4 space-y-2.5">
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-slate-500">Tipe</span>
                                <span class="s-badge s-badge-primary">{{ ucfirst($existingAbsensi->status) }}</span>
                            </div>
                            <div class="h-px bg-slate-100"></div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-slate-500">Status Persetujuan</span>
                                @if($existingAbsensi->is_approved === null)
                                    <span class="s-badge s-badge-warning text-amber-700 bg-amber-50">Menunggu Persetujuan</span>
                                @elseif($existingAbsensi->is_approved)
                                    <span class="s-badge s-badge-success text-emerald-700 bg-emerald-50">Disetujui</span>
                                @else
                                    <span class="s-badge s-badge-danger text-red-700 bg-red-50">Ditolak</span>
                                @endif
                            </div>
                            @if($existingAbsensi->catatan)
                                <div class="h-px bg-slate-100"></div>
                                <div class="flex flex-col items-start gap-1 text-left">
                                    <span class="text-xs text-slate-500">Catatan Anda:</span>
                                    <p class="text-xs text-slate-700 font-medium italic">"{{ $existingAbsensi->catatan }}"</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center mx-auto">
                            <i data-lucide="check-circle-2" class="w-7 h-7 text-emerald-600"></i>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-base font-semibold text-slate-900">Sudah Check In</h3>
                            <p class="text-sm text-slate-500">Anda sudah melakukan absensi hari ini</p>
                        </div>

                        <div class="bg-slate-50 rounded-xl p-4 space-y-2.5">
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-slate-500">Shift</span>
                                <span class="s-badge s-badge-primary">{{ $existingAbsensi->shift ?? 'Default' }}</span>
                            </div>
                            <div class="h-px bg-slate-100"></div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-slate-500">Jam Masuk</span>
                                <span class="text-sm font-semibold text-slate-900 font-mono">{{ $existingAbsensi->jam_masuk }}</span>
                            </div>
                            <div class="h-px bg-slate-100"></div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-slate-500">Jam Keluar</span>
                                <span class="text-sm font-semibold text-slate-900 font-mono">{{ $existingAbsensi->jam_keluar ?? '--:--' }}</span>
                            </div>
                            <div class="h-px bg-slate-100"></div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-slate-500">Status</span>
                                <span class="s-badge {{ $existingAbsensi->status == 'hadir' ? 's-badge-success' : 's-badge-warning' }}">{{ ucfirst($existingAbsensi->status) }}</span>
                            </div>
                        </div>

                        @if(!$existingAbsensi->jam_keluar)
                            <a href="{{ route('absensi.check-out') }}" class="btn btn-primary btn-md btn-full mt-4">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                                Check Out Sekarang
                            </a>
                        @endif
                    @endif
                </div>
            @else
                <!-- Page Header -->
                <div class="space-y-1">
                    <h1 class="text-xl font-bold tracking-tight text-slate-900">Check In</h1>
                    <p class="text-sm text-slate-500">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
                </div>

                <!-- Clock / Time Info -->
                <div class="s-card p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                            <i data-lucide="clock" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Waktu Sekarang</p>
                            <p class="text-sm font-semibold text-slate-900" x-text="new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})"></p>
                        </div>
                    </div>
                    <span class="text-xs font-mono text-slate-400">{{ now()->format('H:i') }}</span>
                </div>

                <!-- Location Selector -->
                <div class="s-card p-4 space-y-3">
                    <label class="s-label">
                        <i data-lucide="map-pin" class="w-3 h-3 inline-block"></i>
                        Pilih Lokasi Absensi
                    </label>
                    <select x-model="selectedLocationId" @change="onLocationChange()" class="s-select">
                        <option value="">-- Pilih Lokasi --</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }} (Radius: {{ $loc->radius }}m)</option>
                        @endforeach
                    </select>
                </div>

                <!-- Shift Selector (Dynamic dropdown) -->
                <div class="s-card p-4 space-y-3 animate-fade-in-up" x-show="selectedLocationId" x-cloak>
                    <label class="s-label">
                        <i data-lucide="calendar" class="w-3 h-3 inline-block"></i>
                        Pilih Shift
                    </label>
                    <select x-model="selectedShiftId" class="s-select">
                        <option value="">-- Pilih Shift --</option>
                        <template x-for="sh in activeShifts" :key="sh.id">
                            <option :value="sh.id" x-text="sh.nama_shift + ' (' + sh.jam_masuk.substring(0, 5) + ' - ' + sh.jam_keluar.substring(0, 5) + ')'"></option>
                        </template>
                    </select>
                    <div x-show="activeShifts.length === 0" class="text-xs text-amber-600 bg-amber-50 rounded p-2 flex items-center gap-1.5 mt-1">
                        <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                        Lokasi ini tidak memiliki shift kerja terdaftar.
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

                <!-- Error -->
                <div x-show="errorMessage" class="flex items-start gap-3 p-4 rounded-xl bg-red-50 border border-red-100">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-red-500 shrink-0 mt-0.5"></i>
                    <p class="text-sm text-red-600 font-medium" x-text="errorMessage"></p>
                </div>

                <!-- Check In Button -->
                <button @click="submitCheckIn()" :disabled="!canSubmit || submitting"
                        class="btn btn-md btn-full"
                        :class="canSubmit && !submitting ? 'btn-primary' : 'btn-secondary opacity-60'">
                    <span x-show="!submitting" class="flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4"></i> Check In Sekarang
                    </span>
                    <span x-show="submitting" class="animate-pulse-soft flex items-center gap-2">
                        <i data-lucide="loader" class="w-4 h-4 animate-spin"></i> Memproses...
                    </span>
                </button>

                <!-- Divider -->
                <div class="flex items-center gap-3">
                    <div class="h-px flex-1 bg-slate-200/70"></div>
                    <span class="text-[10px] text-slate-400 font-medium uppercase tracking-wider">atau</span>
                    <div class="h-px flex-1 bg-slate-200/70"></div>
                </div>

                <!-- Links -->
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('absensi.izin') }}" class="btn btn-outline btn-sm btn-full">
                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                        Ajukan Izin
                    </a>
                    <a href="{{ route('absensi.sakit') }}" class="btn btn-outline btn-sm btn-full text-red-600 border-red-200 hover:bg-red-50">
                        <i data-lucide="heart-pulse" class="w-3.5 h-3.5"></i>
                        Ajukan Sakit
                    </a>
                </div>
            @endif
        </div>
    </template>
</div>

@push('scripts')
<script>
function checkInApp() {
    return {
        lat: null, lng: null, accuracy: null, distance: null,
        gpsLoading: false, gpsReady: false, isMocked: false,
        errorMessage: '', submitting: false,
        selectedLocationId: '',
        selectedShiftId: '',
        locationLat: null, locationLng: null, locationRadius: 100,
        locations: @json($locations),
        activeShifts: [],
        maxAccuracy: {{ $settings['max_accuracy'] }},
        map: null, markerUser: null, locationMarker: null, locationCircle: null,
        checkInSuccess: false, userFotoUrl: '', checkInTime: '', checkInStatus: '',

        get canSubmit() {
            return this.gpsReady && this.selectedLocationId && this.selectedShiftId && this.distance !== null && this.distance <= this.locationRadius;
        },

        init() { this.getGPS(); },

        getDeviceId() {
            let deviceId = localStorage.getItem('absen_asa_device_uuid');
            if (!deviceId) {
                deviceId = 'device_' + Math.random().toString(36).substring(2, 15) + '_' + Date.now().toString(36);
                localStorage.setItem('absen_asa_device_uuid', deviceId);
            }
            return deviceId;
        },

        onLocationChange() {
            this.selectedShiftId = '';
            this.activeShifts = [];
            if (!this.selectedLocationId) {
                this.locationLat = null; this.locationLng = null; this.locationRadius = 100;
                return;
            }
            const loc = this.locations.find(l => l.id == this.selectedLocationId);
            if (loc) {
                this.locationLat = parseFloat(loc.latitude);
                this.locationLng = parseFloat(loc.longitude);
                this.locationRadius = parseFloat(loc.radius);
                this.activeShifts = loc.shifts || [];
                this.recalcDistance();
                this.updateMapLocation();
                setTimeout(() => {
                    lucide.createIcons();
                }, 50);
            }
        },

        recalcDistance() {
            if (!this.lat || !this.locationLat) return;
            const R = 6371000;
            const dLat = (this.locationLat - this.lat) * Math.PI / 180;
            const dLon = (this.locationLng - this.lng) * Math.PI / 180;
            const a = Math.sin(dLat/2)**2 + Math.cos(this.lat*Math.PI/180) * Math.cos(this.locationLat*Math.PI/180) * Math.sin(dLon/2)**2;
            this.distance = R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            this.errorMessage = '';
            if (this.distance > this.locationRadius) {
                this.errorMessage = 'Anda di luar area absensi. Jarak: ' + Math.round(this.distance) + ' meter.';
            }
        },

        getGPS() {
            if (!navigator.geolocation) { this.errorMessage = 'Browser tidak mendukung GPS.'; return; }
            this.gpsLoading = true; this.errorMessage = '';
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.lat = pos.coords.latitude;
                    this.lng = pos.coords.longitude;
                    this.accuracy = pos.coords.accuracy;
                    this.isMocked = pos.mocked || (pos.coords && pos.coords.mocked) || pos.coords.accuracy === 0;
                    this.gpsLoading = false;
                    this.gpsReady = true;
                    if (this.selectedLocationId) this.recalcDistance();
                    this.initMap();
                },
                (err) => {
                    this.gpsLoading = false;
                    this.errorMessage = 'Gagal mendapatkan lokasi. Pastikan GPS/lokasi aktif di browser.';
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

        async submitCheckIn() {
            if (!this.canSubmit) return;
            this.submitting = true;
            this.errorMessage = '';

            try {
                const response = await fetch('{{ route("absensi.store-check-in") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        latitude: this.lat,
                        longitude: this.lng,
                        accuracy: this.accuracy,
                        location_id: this.selectedLocationId,
                        shift_id: this.selectedShiftId,
                        device_id: this.getDeviceId(),
                        is_mocked: this.isMocked
                    })
                });
                const data = await response.json();
                if (data.success) {
                    this.userFotoUrl = data.data?.foto_url || '{{ asset("uploads/foto/" . auth()->user()->foto) }}';
                    this.checkInTime = data.data?.absensi?.jam_masuk || '{{ now()->format("H:i") }}';
                    this.checkInStatus = data.data?.absensi?.status === 'terlambat' ? 'Terlambat' : 'Hadir';
                    this.checkInSuccess = true;
                    setTimeout(() => lucide.createIcons(), 50);
                } else {
                    this.errorMessage = data.message || 'Terjadi kesalahan.';
                }
            } catch(e) {
                this.errorMessage = 'Terjadi kesalahan jaringan.';
            }
            this.submitting = false;
        }
    }
}
</script>
@endpush
@endsection
