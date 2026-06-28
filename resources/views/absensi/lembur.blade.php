@extends('layouts.member')
@section('title', 'Lembur')
@section('header', 'Absensi - Lembur')

@section('content')
<div class="space-y-5 animate-fade-in-up" x-data="lemburApp()" x-init="init()">

    <!-- Page Header -->
    <div class="space-y-1">
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Lembur</h1>
        <p class="text-sm text-slate-500">Catat waktu kerja lembur Anda</p>
    </div>

    <!-- Already doing lembur? -->
    @if($existingAbsensi && $existingAbsensi->is_lembur && $existingAbsensi->jam_lembur_mulai && !$existingAbsensi->jam_lembur_selesai)
        <div class="s-card p-6 text-center space-y-5">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center mx-auto">
                <i data-lucide="clock" class="w-7 h-7 text-amber-600"></i>
            </div>
            <div class="space-y-1">
                <h3 class="text-lg font-semibold text-slate-900 tracking-tight">Sedang Lembur</h3>
                <p class="text-sm text-slate-500">Mulai: <span class="font-semibold text-slate-700">{{ $existingAbsensi->jam_lembur_mulai }}</span></p>
            </div>

            <!-- Timer -->
            <div class="py-3">
                <p class="text-4xl font-bold tracking-tight text-amber-600 font-mono leading-none" x-text="lemburTimer">00:00:00</p>
                <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-widest mt-2">Durasi Lembur</p>
            </div>

            <a href="{{ route('absensi.lembur') }}" id="lemburCheckOutBtn"
               class="btn btn-amber btn-lg btn-full">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                Check Out Lembur
            </a>
        </div>

    @elseif($existingAbsensi && $existingAbsensi->jam_lembur_selesai)
        <div class="s-card p-6 text-center space-y-5">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center mx-auto">
                <i data-lucide="check-circle-2" class="w-7 h-7 text-emerald-600"></i>
            </div>
            <div class="space-y-1">
                <h3 class="text-lg font-semibold text-slate-900 tracking-tight">Lembur Selesai</h3>
                <p class="text-sm text-slate-500">Kerja lembur Anda telah tercatat</p>
            </div>

            <div class="bg-slate-50 rounded-xl p-4 space-y-2.5">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500">Mulai</span>
                    <span class="text-sm font-semibold text-slate-900 font-mono">{{ $existingAbsensi->jam_lembur_mulai }}</span>
                </div>
                <div class="h-px bg-slate-100"></div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500">Selesai</span>
                    <span class="text-sm font-semibold text-slate-900 font-mono">{{ $existingAbsensi->jam_lembur_selesai }}</span>
                </div>
                <div class="h-px bg-slate-100"></div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500">Total Durasi</span>
                    <span class="s-badge s-badge-warning">{{ $existingAbsensi->durasi_lembur_formatted }}</span>
                </div>
            </div>
        </div>

    @else
        <!-- Check In Lembur Form -->
        <div x-show="!selfieTaken && !showCamera && !showFileUpload" class="space-y-4">
            <!-- Info Card -->
            <div class="s-card p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                        <i data-lucide="moon" class="w-5 h-5 text-amber-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-slate-900">Check In Lembur</p>
                        <p class="text-xs text-slate-400">{{ now()->locale('id')->isoFormat('dddd, D MMM Y - HH:mm') }}</p>
                    </div>
                </div>
                @if($existingAbsensi && $existingAbsensi->jam_keluar)
                    <p class="text-xs text-slate-400 mt-2 pl-[52px]">Check out normal: {{ $existingAbsensi->jam_keluar }}</p>
                @endif
            </div>

            <!-- Location Selector -->
            <div class="s-card p-4 space-y-3">
                <label class="s-label">
                    <i data-lucide="map-pin" class="w-3 h-3 inline-block"></i>
                    Pilih Lokasi Lembur
                </label>
                <select x-model="selectedLocationId" @change="onLocationChange()" class="s-select">
                    <option value="">-- Pilih Lokasi --</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->name }} (Radius: {{ $loc->radius }}m)</option>
                    @endforeach
                </select>
            </div>

            <!-- GPS Status -->
            <div class="s-card p-4 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="navigation" class="w-4 h-4 text-slate-400"></i>
                        <span class="text-sm font-medium text-slate-700">Status GPS</span>
                    </div>
                    <span x-show="gpsLoading" class="s-badge s-badge-info animate-pulse-soft">
                        <i data-lucide="loader" class="w-3 h-3"></i> Mencari...
                    </span>
                    <span x-show="!gpsLoading && gpsReady" class="s-badge s-badge-success">
                        <i data-lucide="check" class="w-3 h-3"></i> Aktif
                    </span>
                    <span x-show="!gpsLoading && !gpsReady" class="s-badge s-badge-danger">
                        <i data-lucide="x" class="w-3 h-3"></i> Error
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div class="bg-slate-50 rounded-lg p-3 text-center">
                        <p class="text-[10px] font-medium uppercase tracking-wider text-slate-400">Lat</p>
                        <p class="text-xs font-mono font-semibold text-slate-700 mt-1" x-text="lat ? lat.toFixed(5) : '—'"></p>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-3 text-center">
                        <p class="text-[10px] font-medium uppercase tracking-wider text-slate-400">Lng</p>
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
                        Jarak: <span x-text="distance ? distance.toFixed(0) + 'm' : 'â€”'"></span> (Max: <span x-text="locationRadius"></span>m)
                    </span>
                </div>
            </div>

            <!-- Error message -->
            <div x-show="errorMessage" class="flex items-start gap-3 p-4 rounded-xl bg-red-50 border border-red-100">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-red-500 shrink-0 mt-0.5"></i>
                <p class="text-sm font-medium text-red-600" x-text="errorMessage"></p>
            </div>

            <!-- Camera & Upload Buttons -->
            <div class="grid grid-cols-2 gap-3">
                <button @click="openCamera()" :disabled="gpsLoading || !gpsReady || !selectedLocationId"
                        :class="(gpsReady && selectedLocationId) ? 'btn-amber' : 'btn-secondary opacity-60'"
                        class="btn btn-lg btn-full">
                    <i data-lucide="camera" class="w-4 h-4"></i>
                    Kamera
                </button>
                <button @click="showFileUpload = true" :disabled="gpsLoading || !gpsReady || !selectedLocationId"
                        :class="(gpsReady && selectedLocationId) ? 'btn-outline' : 'btn-secondary opacity-60'"
                        class="btn btn-lg btn-full">
                    <i data-lucide="upload" class="w-4 h-4"></i>
                    Upload
                </button>
            </div>
        </div>

        <!-- File Upload Fallback -->
        <div x-show="showFileUpload && !selfieTaken" x-cloak class="space-y-5">
            <div class="s-card p-5 text-center">
                <h2 class="text-base font-semibold text-slate-900">Upload Selfie Lembur</h2>
                <p class="text-xs text-slate-500 mt-1">Upload foto sebagai bukti lembur</p>
            </div>

            <div class="s-card p-5">
                <div class="border-2 border-dashed border-slate-200 rounded-xl p-8 text-center relative transition-colors duration-200"
                     @dragover.prevent="dragging = true" @dragleave="dragging = false" @drop.prevent="handleFileDrop($event)"
                     :class="dragging ? 'border-amber-400 bg-amber-50/50' : ''">
                    <i data-lucide="image-plus" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                    <p class="text-sm text-slate-500 mb-4">Seret foto atau klik untuk memilih</p>
                    <input type="file" accept="image/*" capture="user" @change="handleFileSelect($event)"
                           class="absolute inset-0 opacity-0 cursor-pointer" style="width: 100%; height: 100%;">
                    <button type="button" @click="$refs.lemburFileInput.click()"
                            class="btn btn-amber btn-sm relative z-10">
                        <i data-lucide="image" class="w-3.5 h-3.5"></i>
                        Pilih Foto
                    </button>
                    <input type="file" x-ref="lemburFileInput" accept="image/*" capture="user" @change="handleFileSelect($event)" class="hidden">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <button @click="showFileUpload = false" class="btn btn-outline btn-md btn-full">
                    Kembali
                </button>
                <button @click="submitFileUpload()" :disabled="!selfieData || submitting"
                        :class="selfieData ? 'btn-amber' : 'btn-secondary opacity-60'"
                        class="btn btn-md col-span-2 btn-full">
                    <span x-show="!submitting" class="flex items-center gap-2">Check In Lembur</span>
                    <span x-show="submitting" class="animate-pulse-soft">Memproses...</span>
                </button>
            </div>
        </div>

        <!-- Step 3: Confirmation Detail Card -->
        <div x-show="selfieTaken && !showCamera" x-cloak class="space-y-5">
            <div class="s-card p-6 flex flex-col items-center text-center space-y-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Konfirmasi Lembur</p>

                <div class="relative w-24 h-24 rounded-2xl overflow-hidden border-2 border-slate-100">
                    <img :src="selfieData" class="w-full h-full object-cover" alt="Selfie preview">
                </div>

                <div class="py-2">
                    <p class="text-4xl font-bold text-amber-600 font-mono tracking-tight leading-none" x-text="capturedTime">00:00</p>
                    <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-widest mt-2">Waktu Check In</p>
                </div>
            </div>

            <!-- Error message -->
            <div x-show="errorMessage" class="flex items-start gap-3 p-4 rounded-xl bg-red-50 border border-red-100">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-red-500 shrink-0 mt-0.5"></i>
                <p class="text-sm font-medium text-red-600" x-text="errorMessage"></p>
            </div>

            <!-- Submit Buttons -->
            <div class="grid grid-cols-3 gap-3">
                <button @click="retake()" :disabled="submitting" class="btn btn-outline btn-md btn-full">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    Ambil Ulang
                </button>
                <button @click="submitCheckIn()" :disabled="!canSubmit || submitting"
                        :class="canSubmit ? 'btn-amber' : 'btn-secondary opacity-60'"
                        class="btn btn-md col-span-2 btn-full">
                    <span x-show="!submitting" class="flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        Check In Lembur
                    </span>
                    <span x-show="submitting" class="animate-pulse-soft">Memproses...</span>
                </button>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function lemburApp() {
    return {
        lat: null, lng: null, accuracy: null, distance: null,
        gpsLoading: false, gpsReady: false,
        selfieData: null, selfieTaken: false, showCamera: false, showFileUpload: false,
        errorMessage: '', submitting: false, dragging: false,
        capturedTime: '',
        selectedLocationId: '',
        locationLat: null, locationLng: null, locationRadius: 100,
        locations: @json($locations),
        maxAccuracy: {{ $settings['max_accuracy'] ?? 50 }},
        stream: null,
        lemburTimer: '00:00:00',
        lemburStartTime: @json($existingAbsensi && $existingAbsensi->jam_lembur_mulai ? $existingAbsensi->jam_lembur_mulai : null),

        get canSubmit() {
            return this.gpsReady && this.selfieTaken && !this.submitting && this.selectedLocationId && this.distance <= this.locationRadius;
        },

        init() {
            this.getGPS();
            if (this.lemburStartTime) {
                this.startTimer();
            }
        },

        startTimer() {
            setInterval(() => {
                const start = new Date();
                const parts = this.lemburStartTime.split(':');
                start.setHours(parseInt(parts[0]), parseInt(parts[1]), parseInt(parts[2] || 0));
                const now = new Date();
                const diff = Math.floor((now - start) / 1000);
                const h = String(Math.floor(diff / 3600)).padStart(2, '0');
                const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
                const s = String(diff % 60).padStart(2, '0');
                this.lemburTimer = `${h}:${m}:${s}`;
            }, 1000);
        },

        onLocationChange() {
            if (!this.selectedLocationId) return;
            const loc = this.locations.find(l => l.id == this.selectedLocationId);
            if (loc) {
                this.locationLat = parseFloat(loc.latitude);
                this.locationLng = parseFloat(loc.longitude);
                this.locationRadius = parseFloat(loc.radius);
                this.recalcDistance();
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
                this.errorMessage = 'Anda berada di luar area lembur. Jarak: ' + Math.round(this.distance) + ' meter.';
            } else if (this.accuracy > this.maxAccuracy) {
                this.errorMessage = 'Akurasi GPS terlalu rendah.';
            }
        },

        getGPS() {
            if (!navigator.geolocation) {
                this.errorMessage = 'Browser tidak mendukung GPS.';
                return;
            }
            this.gpsLoading = true;
            this.errorMessage = '';
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.lat = pos.coords.latitude;
                    this.lng = pos.coords.longitude;
                    this.accuracy = pos.coords.accuracy;
                    this.gpsLoading = false;
                    this.gpsReady = true;
                    if (this.selectedLocationId) this.recalcDistance();
                },
                (err) => {
                    this.gpsLoading = false;
                    this.errorMessage = 'Gagal mendapatkan lokasi.';
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        },

        async openCamera() {
            this.showCamera = true;
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
                document.getElementById('camera').srcObject = this.stream;
            } catch(e) {
                this.showCamera = false;
                this.showFileUpload = true;
                this.errorMessage = 'Kamera tidak tersedia. Gunakan upload foto.';
            }
        },

        closeCamera() {
            this.showCamera = false;
            if (this.stream) { this.stream.getTracks().forEach(t => t.stop()); }
        },

        captureSelfie() {
            const video = document.getElementById('camera');
            const canvas = document.getElementById('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            this.selfieData = canvas.toDataURL('image/jpeg', 0.8);
            this.selfieTaken = true;
            this.closeCamera();
            const now = new Date();
            this.capturedTime = now.toTimeString().split(' ')[0].substring(0, 5);
            setTimeout(() => lucide.createIcons(), 10);
        },

        retake() {
            this.selfieTaken = false;
            this.selfieData = null;
            this.openCamera();
        },

        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) this.processFile(file);
        },

        handleFileDrop(event) {
            this.dragging = false;
            const file = event.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) this.processFile(file);
        },

        processFile(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.selfieData = e.target.result;
                this.selfieTaken = true;
                this.showFileUpload = false;
                const now = new Date();
                this.capturedTime = now.toTimeString().split(' ')[0].substring(0, 5);
                setTimeout(() => lucide.createIcons(), 10);
            };
            reader.readAsDataURL(file);
        },

        async submitFileUpload() {
            if (!this.selfieData || !this.canSubmit) return;
            await this.doSubmit();
        },

        async submitCheckIn() {
            if (!this.canSubmit) return;
            await this.doSubmit();
        },

        async doSubmit() {
            this.submitting = true;
            this.errorMessage = '';
            try {
                const response = await fetch('{{ route("absensi.lembur-check-in") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        latitude: this.lat, longitude: this.lng, accuracy: this.accuracy,
                        selfie: this.selfieData, location_id: this.selectedLocationId
                    })
                });
                const data = await response.json();
                if (data.success) {
                    window.location.reload();
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
