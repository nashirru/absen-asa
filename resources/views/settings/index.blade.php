@extends('layouts.admin')
@section('title', 'Pengaturan')
@section('header', 'Pengaturan')
@section('content')
<div class="max-w-2xl mx-auto animate-fade-in-up">
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-6">
        <h2 class="text-lg font-semibold text-admin-ink mb-6">Pengaturan Sistem</h2>
        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
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

            <!-- Tampilan Slip Gaji -->
            <fieldset class="border border-admin-border rounded-admin-md p-4">
                <legend class="text-xs font-bold uppercase tracking-wider text-admin-indigo px-2">Tampilan Slip Gaji</legend>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-2">
                    <div>
                        <label class="text-xs font-semibold text-admin-slate">Logo Perusahaan (Upload Gambar)</label>
                        <input type="file" name="slip_logo" accept="image/*"
                               class="w-full mt-1 px-4 py-2 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                        @error('slip_logo') <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p> @enderror
                        @if($settings['slip_logo'])
                            <div class="mt-3 p-2 border border-admin-border rounded-admin-md bg-white flex items-center gap-3">
                                <img src="{{ asset('uploads/logo/' . $settings['slip_logo']) }}" class="h-12 object-contain" alt="Logo Perusahaan">
                                <span class="text-xs text-admin-slate">Logo saat ini</span>
                            </div>
                        @else
                            <p class="text-[10px] text-admin-mist mt-1 leading-snug">Menggunakan nama aplikasi sebagai logo teks default.</p>
                        @endif
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-admin-slate">Sub-judul Slip Gaji</label>
                        <input type="text" name="slip_subtitle" value="{{ old('slip_subtitle', $settings['slip_subtitle']) }}" required
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                        @error('slip_subtitle') <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </fieldset>

            <!-- Tanda Tangan Digital -->
            <fieldset class="border border-admin-border rounded-admin-md p-4">
                <legend class="text-xs font-bold uppercase tracking-wider text-admin-indigo px-2">Tanda Tangan Slip Gaji</legend>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-2">
                    <!-- Column 1: Nama Penandatangan -->
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-admin-slate">Nama Penandatangan (Disetujui Oleh)</label>
                        <input type="text" name="ttd_nama" value="{{ old('ttd_nama', $settings['ttd_nama']) }}" required
                               class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                        @error('ttd_nama') <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <!-- Column 2: Upload File TTD -->
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-admin-slate">File TTD Digital (Gambar PNG/JPG)</label>
                        <input type="file" name="ttd_digital" id="ttd_digital_file" accept="image/*"
                               class="w-full mt-1 px-4 py-2 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                        @error('ttd_digital') <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p> @enderror
                        @if($settings['ttd_digital'])
                            <div class="mt-3 p-2 border border-admin-border rounded-admin-md bg-white flex items-center gap-3">
                                <img src="{{ asset('uploads/ttd/' . $settings['ttd_digital']) }}" class="h-12 object-contain" alt="TTD Digital">
                                <span class="text-xs text-admin-slate">Tanda tangan saat ini</span>
                            </div>
                        @else
                            <p class="text-[10px] text-admin-mist mt-1 leading-snug">Belum ada tanda tangan digital yang diunggah.</p>
                        @endif
                    </div>

                    <!-- Column 3: Drawing Pad -->
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-admin-slate">Atau Gambar TTD Langsung (Drawing Pad)</label>
                        <div class="mt-1 relative">
                            <canvas id="signature-pad" class="w-full border border-dashed border-admin-border rounded-admin-md bg-white cursor-crosshair" style="height: 120px; touch-action: none;"></canvas>
                            <input type="hidden" name="ttd_image_base64" id="ttd_image_base64">
                            <div class="flex justify-between items-center mt-2">
                                <button type="button" id="clear-signature" class="px-2 py-1 bg-red-50 text-red-600 border border-red-200 rounded-admin-md text-[10px] font-semibold hover:bg-red-100 transition-colors">
                                    Hapus Gambar (Clear)
                                </button>
                                <span class="text-[9px] text-admin-slate">Gambar dgn mouse/sentuh</span>
                            </div>
                        </div>
                    </div>
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

document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('signature-pad');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let drawing = false;
        let isCanvasDrawn = false;

        // Set internal resolution to match displayed size
        canvas.width = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;

        // Reset width and height on window resize to ensure drawing is accurate
        window.addEventListener('resize', () => {
            const tempImg = new Image();
            tempImg.src = canvas.toDataURL();
            tempImg.onload = () => {
                canvas.width = canvas.offsetWidth;
                canvas.height = canvas.offsetHeight;
                ctx.strokeStyle = '#000000';
                ctx.lineWidth = 3;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
                ctx.drawImage(tempImg, 0, 0, canvas.width, canvas.height);
            };
        });

        // Adjust line style
        ctx.strokeStyle = '#000000';
        ctx.lineWidth = 3;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        function getMousePos(canvasDom, touchOrMouseEvent) {
            const rect = canvasDom.getBoundingClientRect();
            const clientX = touchOrMouseEvent.touches ? touchOrMouseEvent.touches[0].clientX : touchOrMouseEvent.clientX;
            const clientY = touchOrMouseEvent.touches ? touchOrMouseEvent.touches[0].clientY : touchOrMouseEvent.clientY;
            return {
                x: clientX - rect.left,
                y: clientY - rect.top
            };
        }

        function startDrawing(e) {
            drawing = true;
            isCanvasDrawn = true;
            document.getElementById('ttd_digital_file').value = ''; // clear file upload
            const pos = getMousePos(canvas, e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
            e.preventDefault();
        }

        function draw(e) {
            if (!drawing) return;
            const pos = getMousePos(canvas, e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            e.preventDefault();
        }

        function stopDrawing() {
            drawing = false;
        }

        // Mouse Events
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseleave', stopDrawing);

        // Touch Events for Mobile/Tablet
        canvas.addEventListener('touchstart', startDrawing, { passive: false });
        canvas.addEventListener('touchmove', draw, { passive: false });
        canvas.addEventListener('touchend', stopDrawing);

        // Clear Signature
        document.getElementById('clear-signature').addEventListener('click', () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            isCanvasDrawn = false;
            document.getElementById('ttd_image_base64').value = '';
        });

        // Clear canvas if file input changes
        document.getElementById('ttd_digital_file').addEventListener('change', () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            isCanvasDrawn = false;
            document.getElementById('ttd_image_base64').value = '';
        });

        // Form Submission
        const form = canvas.closest('form');
        form.addEventListener('submit', (e) => {
            if (isCanvasDrawn) {
                document.getElementById('ttd_image_base64').value = canvas.toDataURL('image/png');
            }
        });
    }
});
</script>
@endpush
@endsection

