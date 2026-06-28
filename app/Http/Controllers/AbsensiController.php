<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Location;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $query = Absensi::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tanggal')) {
            $query->where('tanggal', $request->tanggal);
        }

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('tanggal', [$request->from, $request->to]);
        }

        $absensi = $query->latest('tanggal')->paginate(20)->withQueryString();

        return view('absensi.index', compact('absensi'));
    }

    public function checkIn()
    {
        $user = auth()->user();
        $today = Carbon::today();

        // Get the latest check-in of today that hasn't checked out yet
        $existingAbsensi = Absensi::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->whereNull('jam_keluar')
            ->latest()
            ->first();

        // If there's no active check-in (without check-out), let's get the latest absensi record of today (could be check-out or izin/sakit)
        if (!$existingAbsensi) {
            $existingAbsensi = Absensi::where('user_id', $user->id)
                ->where('tanggal', $today)
                ->latest()
                ->first();
        }

        $userRole = $user->role;
        $locations = Location::active()->with('shifts')->get()->filter(function ($loc) use ($userRole) {
            return $loc->isAllowedForRole($userRole);
        })->values();

        $settings = [
            'office_lat' => Setting::get('office_lat', '-7.2575'),
            'office_lng' => Setting::get('office_lng', '112.7521'),
            'geofence_radius' => Setting::get('geofence_radius', '100'),
            'max_accuracy' => Setting::get('max_accuracy', '50'),
        ];

        return view('absensi.check-in', compact('user', 'existingAbsensi', 'settings', 'locations'));
    }

    public function storeCheckIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'required|numeric',
            'location_id' => 'required|exists:locations,id',
            'shift_id' => 'required|exists:shifts,id',
            'selfie' => 'nullable|string',
            'device_id' => 'nullable|string',
            'is_mocked' => 'nullable|boolean',
        ]);

        $user = auth()->user();
        $today = Carbon::today();

        // Enforce One-device policy
        if ($user->role === 'siswa' || $user->role === 'karyawan' || $user->role === 'sensei') {
            $deviceId = $request->input('device_id');
            if ($deviceId) {
                if (empty($user->device_uuid)) {
                    $user->update(['device_uuid' => $deviceId]);
                } elseif ($user->device_uuid !== $deviceId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Perangkat ini tidak terdaftar untuk akun Anda. Absensi hanya dapat dilakukan dari satu perangkat terdaftar. Silakan hubungi admin untuk reset.',
                    ], 422);
                }
            }
        }

        // GPS Spoofing check
        $isMocked = (bool) $request->input('is_mocked', false);
        if ($isMocked) {
            return response()->json([
                'success' => false,
                'message' => 'Terdeteksi penggunaan GPS Palsu (Mock Location). Absensi dibatalkan.',
            ], 422);
        }

        // Load shift and check if it belongs to selected location
        $shift = \App\Models\Shift::where('location_id', $request->location_id)->findOrFail($request->shift_id);

        // Enforce Time window
        $now = Carbon::now();
        $jamMasuk = Carbon::parse($shift->jam_masuk);
        $jamKeluar = Carbon::parse($shift->jam_keluar);
        
        $startWindow = $jamMasuk->copy()->subHours(2);
        $endWindow = $jamKeluar;
        
        $currentTime = Carbon::createFromFormat('H:i:s', $now->format('H:i:s'));
        $startTime = Carbon::createFromFormat('H:i:s', $startWindow->format('H:i:s'));
        $endTime = Carbon::createFromFormat('H:i:s', $endWindow->format('H:i:s'));
        
        if ($currentTime->lt($startTime) || $currentTime->gt($endTime)) {
            return response()->json([
                'success' => false,
                'message' => 'Absen masuk shift ini hanya dapat dilakukan antara jam ' . $startTime->format('H:i') . ' dan ' . $endTime->format('H:i') . '.',
            ], 422);
        }

        // Check if already checked in to this specific shift today
        $existing = Absensi::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->where('shift_id', $shift->id)
            ->first();

        if ($existing && $existing->jam_masuk) {
            return response()->json(['success' => false, 'message' => 'Anda sudah check in untuk shift ini hari ini.'], 422);
        }

        // Get selected location
        $location = Location::findOrFail($request->location_id);
        $officeLat = (float) $location->latitude;
        $officeLng = (float) $location->longitude;
        $geofenceRadius = (float) $location->radius;
        $maxAccuracy = (float) Setting::get('max_accuracy', '50');

        // Validate accuracy — use higher of setting or 200m tolerance
        $effectiveMaxAccuracy = max((float) $maxAccuracy, 200);
        if ($request->accuracy > $effectiveMaxAccuracy) {
            return response()->json([
                'success' => false,
                'message' => 'Akurasi GPS terlalu rendah (' . round($request->accuracy) . 'm). Silakan tunggu hingga lokasi lebih akurat.',
            ], 422);
        }

        // Calculate distance using Haversine
        $distance = $this->haversineDistance(
            $request->latitude, $request->longitude,
            $officeLat, $officeLng
        );

        // Check geofence
        if ($distance > $geofenceRadius) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada di luar area absensi. Jarak: ' . round($distance) . ' meter.',
            ], 422);
        }

        // Determine status based on shift start time and late limit
        $batasTerlambat = Carbon::parse($shift->batas_terlambat);

        if ($now->gt($batasTerlambat)) {
            $status = 'terlambat';
        } else {
            $status = 'hadir';
        }

        // Save selfie if provided → set as profile photo
        $selfieFilename = null;
        if ($request->filled('selfie')) {
            $selfieFilename = $this->saveSelfie($request->selfie, 'checkin_' . $user->id . '_' . time());
            // Update user profile photo
            $this->updateProfilePhoto($user, $request->selfie);
        }

        $absensi = Absensi::create([
            'user_id' => $user->id,
            'location_id' => $location->id,
            'shift_id' => $shift->id,
            'shift' => $shift->nama_shift,
            'is_approved' => true,
            'tanggal' => $today,
            'jam_masuk' => $now->format('H:i:s'),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy,
            'distance' => round($distance, 2),
            'radius' => $geofenceRadius,
            'status' => $status,
            'device' => $request->header('User-Agent'),
            'browser' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'selfie_check_in' => $selfieFilename,
            'catatan' => $request->catatan,
            'is_mocked' => false,
            'is_anomaly' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check in berhasil! Status: ' . ucfirst($status),
            'data' => [
                'absensi' => $absensi,
                'foto_url' => $user->fresh()->foto_url,
            ],
        ]);
    }

    public function checkOut()
    {
        $user = auth()->user();
        $today = Carbon::today();

        // Get the active check-in of today (with jam_masuk, but no jam_keluar)
        $existingAbsensi = Absensi::with('location')->where('user_id', $user->id)
            ->where('tanggal', $today)
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_keluar')
            ->latest()
            ->first();

        $settings = [
            'office_lat' => Setting::get('office_lat', '-7.2575'),
            'office_lng' => Setting::get('office_lng', '112.7521'),
            'max_accuracy' => Setting::get('max_accuracy', '50'),
        ];

        return view('absensi.check-out', compact('user', 'existingAbsensi', 'settings'));
    }

    public function storeCheckOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'required|numeric',
            'device_id' => 'nullable|string',
            'is_mocked' => 'nullable|boolean',
        ]);

        $user = auth()->user();
        $today = Carbon::today();

        // Get the active check-in of today
        $existing = Absensi::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_keluar')
            ->latest()
            ->first();

        if (!$existing) {
            return response()->json(['success' => false, 'message' => 'Anda belum check in hari ini atau sudah check out semua.'], 422);
        }

        // Enforce One-device policy
        if ($user->role === 'siswa' || $user->role === 'karyawan' || $user->role === 'sensei') {
            $deviceId = $request->input('device_id');
            if ($deviceId && $user->device_uuid && $user->device_uuid !== $deviceId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perangkat ini tidak cocok dengan perangkat terdaftar Anda.',
                ], 422);
            }
        }

        // GPS Spoofing check
        $isMocked = (bool) $request->input('is_mocked', false);
        if ($isMocked) {
            return response()->json([
                'success' => false,
                'message' => 'Terdeteksi penggunaan GPS Palsu (Mock Location). Check out dibatalkan.',
            ], 422);
        }

        // Geofence check
        $location = $existing->location;
        $distance = null;
        if ($location) {
            $officeLat = (float) $location->latitude;
            $officeLng = (float) $location->longitude;
            $geofenceRadius = (float) $location->radius;
            $maxAccuracy = (float) Setting::get('max_accuracy', '50');

            $effectiveMaxAccuracy = max((float) $maxAccuracy, 200);
            if ($request->accuracy > $effectiveMaxAccuracy) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akurasi GPS terlalu rendah (' . round($request->accuracy) . 'm). Silakan tunggu hingga lokasi lebih akurat.',
                ], 422);
            }

            // Calculate distance using Haversine
            $distance = $this->haversineDistance(
                $request->latitude, $request->longitude,
                $officeLat, $officeLng
            );

            // Check geofence
            if ($distance > $geofenceRadius) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda berada di luar area absensi untuk check-out. Jarak: ' . round($distance) . ' meter.',
                ], 422);
            }
        }

        $existing->update([
            'jam_keluar' => Carbon::now()->format('H:i:s'),
            'latitude_keluar' => $request->latitude,
            'longitude_keluar' => $request->longitude,
            'accuracy_keluar' => $request->accuracy,
            'distance_keluar' => $distance !== null ? round($distance, 2) : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check out berhasil!',
        ]);
    }

    public function izin(Request $request)
    {
        return view('absensi.izin');
    }

    public function storeIzin(Request $request)
    {
        $request->validate([
            'status' => 'required|in:izin,sakit,cuti',
            'catatan' => 'required|string|max:500',
            'tanggal' => 'nullable|date',
        ]);

        $user = auth()->user();
        $tanggal = $request->filled('tanggal') ? Carbon::parse($request->tanggal)->toDateString() : Carbon::today()->toDateString();

        // Check if there is already an active absensi or izin/sakit/cuti for this date
        $existing = Absensi::where('user_id', $user->id)
            ->where('tanggal', $tanggal)
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah memiliki catatan kehadiran untuk tanggal ' . Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM Y') . '.');
        }

        Absensi::create([
            'user_id' => $user->id,
            'tanggal' => $tanggal,
            'status' => $request->status,
            'catatan' => $request->catatan,
            'ip_address' => $request->ip(),
            'device' => $request->header('User-Agent'),
            'browser' => $request->userAgent(),
            'is_approved' => null, // Pending approval
        ]);

        $statusLabel = $request->status === 'cuti' ? 'Cuti' : ($request->status === 'sakit' ? 'Sakit' : 'Izin');
        return redirect()->route('dashboard')->with('success', 'Absensi ' . $statusLabel . ' berhasil diajukan dan menunggu persetujuan admin.');
    }

    public function sakit(Request $request)
    {
        return view('absensi.sakit');
    }

    public function storeSakit(Request $request)
    {
        $request->validate([
            'catatan' => 'required|string|max:500',
        ]);

        $user = auth()->user();
        $today = Carbon::today();

        $existing = Absensi::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah memiliki catatan kehadiran hari ini.');
        }

        Absensi::create([
            'user_id' => $user->id,
            'tanggal' => $today,
            'status' => 'sakit',
            'catatan' => $request->catatan,
            'ip_address' => $request->ip(),
            'device' => $request->header('User-Agent'),
            'browser' => $request->userAgent(),
            'is_approved' => null, // Pending approval
        ]);

        return redirect()->route('dashboard')->with('success', 'Absensi sakit berhasil diajukan dan menunggu persetujuan admin.');
    }

    public function approve(Absensi $absensi)
    {
        $absensi->update([
            'is_approved' => true
        ]);
        return back()->with('success', 'Permohonan izin/sakit berhasil disetujui.');
    }

    public function reject(Absensi $absensi)
    {
        $absensi->update([
            'is_approved' => false
        ]);
        return back()->with('success', 'Permohonan izin/sakit telah ditolak.');
    }

    public function riwayat(Request $request)
    {
        $user = auth()->user();
        $query = Absensi::where('user_id', $user->id);

        // Month/year params for calendar
        $calMonth = (int) $request->get('cal_month', now()->month);
        $calYear = (int) $request->get('cal_year', now()->year);
        $calMonth = max(1, min(12, $calMonth));

        if ($request->filled('period')) {
            switch ($request->period) {
                case 'week':
                    $query->where('tanggal', '>=', Carbon::now()->startOfWeek());
                    break;
                case 'month':
                    $query->where('tanggal', '>=', Carbon::now()->startOfMonth());
                    break;
                case 'year':
                    $query->where('tanggal', '>=', Carbon::now()->startOfYear());
                    break;
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('status', 'like', "%{$search}%")
                  ->orWhere('tanggal', 'like', "%{$search}%");
            });
        }

        // Ambil data absensi untuk kalender bulan ini
        $calStart = Carbon::createFromDate($calYear, $calMonth, 1)->startOfMonth();
        $calEnd = $calStart->copy()->endOfMonth();
        $monthAbsensi = Absensi::where('user_id', $user->id)
            ->where('tanggal', '>=', $calStart)
            ->where('tanggal', '<=', $calEnd)
            ->get()
            ->keyBy(fn($a) => $a->tanggal->format('Y-m-d'));

        $riwayat = $query->latest('tanggal')->paginate(15)->withQueryString();

        return view('absensi.riwayat', compact('riwayat', 'monthAbsensi', 'calMonth', 'calYear'));
    }

    public function adminMarkAlpha(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:alpha,hadir,izin,sakit',
        ]);

        $existing = Absensi::where('user_id', $request->user_id)
            ->where('tanggal', $request->tanggal)
            ->first();

        if ($existing) {
            $existing->update(['status' => $request->status]);
        } else {
            Absensi::create([
                'user_id' => $request->user_id,
                'tanggal' => $request->tanggal,
                'status' => $request->status,
            ]);
        }

        return back()->with('success', 'Status absensi berhasil diupdate.');
    }

    /**
     * Hapus data absensi (sandbox mode only)
     */
    public function adminDestroy($id)
    {
        $absensi = Absensi::findOrFail($id);
        $absensi->delete();
        return back()->with('success', 'Data absensi berhasil dihapus.');
    }

    public function lembur()
    {
        $user = auth()->user();
        $today = Carbon::today();

        $existingAbsensi = Absensi::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->where('is_lembur', true)
            ->first();

        $userRole = $user->role;
        $locations = Location::active()->get()->filter(function ($loc) use ($userRole) {
            return $loc->isAllowedForRole($userRole);
        })->values();

        $settings = [
            'jam_keluar' => Setting::get('jam_keluar', '17:00'),
        ];

        return view('absensi.lembur', compact('user', 'existingAbsensi', 'settings', 'locations'));
    }

    public function storeLemburCheckIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'required|numeric',
            'location_id' => 'required|exists:locations,id',
        ]);

        $user = auth()->user();
        $today = Carbon::today();

        $existing = Absensi::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->where('is_lembur', true)
            ->first();

        if ($existing && $existing->jam_lembur_mulai) {
            return response()->json(['success' => false, 'message' => 'Anda sudah check in lembur hari ini.'], 422);
        }

        $location = Location::findOrFail($request->location_id);
        $officeLat = (float) $location->latitude;
        $officeLng = (float) $location->longitude;
        $geofenceRadius = (float) $location->radius;
        $maxAccuracy = (float) Setting::get('max_accuracy', '50');

        if ($request->accuracy > $maxAccuracy) {
            return response()->json(['success' => false, 'message' => 'Akurasi GPS terlalu rendah.'], 422);
        }

        $distance = $this->haversineDistance($request->latitude, $request->longitude, $officeLat, $officeLng);

        if ($distance > $geofenceRadius) {
            return response()->json(['success' => false, 'message' => 'Anda berada di luar area lembur. Jarak: ' . round($distance) . ' meter.'], 422);
        }

        $now = Carbon::now();

        $absensi = Absensi::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->first();

        if ($absensi) {
            $absensi->update([
                'is_lembur' => true,
                'jam_lembur_mulai' => $now->format('H:i:s'),
            ]);
        } else {
            $absensi = Absensi::create([
                'user_id' => $user->id,
                'location_id' => $location->id,
                'tanggal' => $today,
                'is_lembur' => true,
                'jam_lembur_mulai' => $now->format('H:i:s'),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => $request->accuracy,
                'distance' => round($distance, 2),
                'radius' => $geofenceRadius,
                'status' => 'hadir',
                'device' => $request->header('User-Agent'),
                'browser' => $request->userAgent(),
                'ip_address' => $request->ip(),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Check in lembur berhasil!', 'data' => $absensi]);
    }

    public function storeLemburCheckOut(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today();

        $absensi = Absensi::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->where('is_lembur', true)
            ->first();

        if (!$absensi || !$absensi->jam_lembur_mulai) {
            return response()->json(['success' => false, 'message' => 'Anda belum check in lembur hari ini.'], 422);
        }

        if ($absensi->jam_lembur_selesai) {
            return response()->json(['success' => false, 'message' => 'Anda sudah check out lembur hari ini.'], 422);
        }

        $now = Carbon::now();
        $mulai = Carbon::parse($absensi->jam_lembur_mulai);
        $durasi = $mulai->diffInMinutes($now) / 60;

        $absensi->update([
            'jam_lembur_selesai' => $now->format('H:i:s'),
            'durasi_lembur' => round($durasi, 2),
        ]);

        return response()->json(['success' => true, 'message' => 'Check out lembur berhasil!']);
    }

    /**
     * Simpan selfie dari base64 ke uploads/foto/ dan update profile user.
     */
    private function updateProfilePhoto(User $user, string $base64Image): void
    {
        $imageData = explode(',', $base64Image)[1] ?? $base64Image;
        $imageData = base64_decode($imageData);
        $filename = 'profile_' . $user->id . '_' . time() . '.jpg';
        $path = public_path('uploads/foto/' . $filename);

        if (!file_exists(public_path('uploads/foto'))) {
            mkdir(public_path('uploads/foto'), 0755, true);
        }

        file_put_contents($path, $imageData);

        // Hapus foto lama jika ada
        if ($user->foto && file_exists(public_path('uploads/foto/' . $user->foto))) {
            unlink(public_path('uploads/foto/' . $user->foto));
        }

        $user->update(['foto' => $filename]);
    }

    private function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    private function saveSelfie(string $base64Image, string $filename): string
    {
        $imageData = explode(',', $base64Image)[1] ?? $base64Image;
        $imageData = base64_decode($imageData);
        $filename = $filename . '.jpg';
        $path = public_path('uploads/selfie/' . $filename);

        if (!file_exists(public_path('uploads/selfie'))) {
            mkdir(public_path('uploads/selfie'), 0755, true);
        }

        file_put_contents($path, $imageData);
        return $filename;
    }

}


