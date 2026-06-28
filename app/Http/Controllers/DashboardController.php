<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Holiday;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Karyawan;
use App\Models\Sensei;
use App\Models\Setting;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $dayName = $today->locale('id')->isoFormat('dddd');

        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return $this->adminDashboard($today, $dayName);
        } elseif ($user->isSiswa()) {
            return $this->siswaDashboard($user, $today, $dayName);
        } elseif ($user->isKaryawan()) {
            return $this->karyawanDashboard($user, $today);
        } elseif ($user->isSensei()) {
            return $this->senseiDashboard($user, $today, $dayName);
        }

        return view('dashboard.index', compact('user'));
    }

    private function adminDashboard($today, $dayName)
    {
        $totalSiswa = Siswa::count();
        $totalSensei = Sensei::count();
        $totalKaryawan = Karyawan::count();
        $totalKelas = Kelas::count();
        $absensiHariIni = Absensi::where('tanggal', $today)->get();
        $totalHadirHariIni = $absensiHariIni->whereIn('status', ['hadir', 'terlambat'])->count();
        $totalTerlambat = $absensiHariIni->where('status', 'terlambat')->count();
        $totalIzin = $absensiHariIni->where('status', 'izin')->count();
        $totalSakit = $absensiHariIni->where('status', 'sakit')->count();
        $totalAlpha = $absensiHariIni->where('status', 'alpha')->count();
        $totalLemburHariIni = $absensiHariIni->where('is_lembur', true)->count();
        $totalDurasiLembur = $absensiHariIni->where('is_lembur', true)->sum('durasi_lembur');

        // Map View data - today's check-ins with coordinates
        $mapAbsensi = Absensi::with('user', 'location')
            ->where('tanggal', $today)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        // Live Monitoring - Already and Not Checked In lists
        $sudahAbsen = Absensi::with('user')->where('tanggal', $today)->get();
        $belumAbsen = User::whereIn('role', ['siswa', 'karyawan', 'sensei'])
            ->where('status_aktif', true)
            ->whereNotIn('id', $sudahAbsen->pluck('user_id'))
            ->orderBy('name')
            ->get();

        // Anomalies - Mocked location or other flags
        $anomaliHariIni = Absensi::with('user')
            ->where('tanggal', $today)
            ->where(function($q) {
                $q->where('is_anomaly', true)
                  ->orWhere('is_mocked', true);
            })
            ->get();

        // Chart data - last 7 days
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartData[] = [
                'date' => $date->format('d M'),
                'hadir' => Absensi::where('tanggal', $date)->whereIn('status', ['hadir', 'terlambat'])->count(),
                'izin' => Absensi::where('tanggal', $date)->where('status', 'izin')->count(),
                'sakit' => Absensi::where('tanggal', $date)->where('status', 'sakit')->count(),
                'alpha' => Absensi::where('tanggal', $date)->where('status', 'alpha')->count(),
            ];
        }

        $recentActivity = Absensi::with('user')->latest()->take(10)->get();

        return view('dashboard.admin', compact(
            'totalSiswa', 'totalSensei', 'totalKaryawan', 'totalKelas',
            'totalHadirHariIni', 'totalTerlambat', 'totalIzin', 'totalSakit', 'totalAlpha',
            'totalLemburHariIni', 'totalDurasiLembur',
            'chartData', 'recentActivity', 'mapAbsensi', 'sudahAbsen', 'belumAbsen', 'anomaliHariIni'
        ));
    }

    private function siswaDashboard($user, $today, $dayName)
    {
        $siswa = $user->siswa;
        // latest absensi of today for display
        $todayAbsensi = Absensi::where('user_id', $user->id)->where('tanggal', $today)->latest()->first();
        // active un-checked-out absensi for buttons
        $activeAbsensi = Absensi::where('user_id', $user->id)->where('tanggal', $today)->whereNotNull('jam_masuk')->whereNull('jam_keluar')->latest()->first();

        $totalAbsensi = Absensi::where('user_id', $user->id)->count();
        $totalHadir = Absensi::where('user_id', $user->id)->whereIn('status', ['hadir', 'terlambat'])->count();
        $persentase = $totalAbsensi > 0 ? round(($totalHadir / $totalAbsensi) * 100, 1) : 0;

        $jadwalHariIni = Jadwal::with(['kelas', 'sensei.user'])
            ->where('hari', $dayName)
            ->orderBy('jam_mulai')
            ->get();

        $historyAbsensi = Absensi::where('user_id', $user->id)
            ->latest('tanggal')
            ->take(5)
            ->get();

        $jamMasuk = Setting::get('jam_masuk', '08:00');
        $jamKeluar = Setting::get('jam_keluar', '17:00');

        return view('dashboard.siswa', compact(
            'user', 'siswa', 'todayAbsensi', 'activeAbsensi', 'persentase', 'jadwalHariIni', 'historyAbsensi',
            'jamMasuk', 'jamKeluar'
        ));
    }

    private function karyawanDashboard($user, $today)
    {
        $karyawan = $user->karyawan;
        $todayAbsensi = Absensi::where('user_id', $user->id)->where('tanggal', $today)->latest()->first();
        $activeAbsensi = Absensi::where('user_id', $user->id)->where('tanggal', $today)->whereNotNull('jam_masuk')->whereNull('jam_keluar')->latest()->first();

        $startOfMonth = Carbon::now()->startOfMonth();
        $totalKehadiranBulanIni = Absensi::where('user_id', $user->id)
            ->where('tanggal', '>=', $startOfMonth)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count();

        $riwayatAbsensi = Absensi::where('user_id', $user->id)
            ->latest('tanggal')
            ->take(5)
            ->get();

        // Load all leave/izin requests for the approval flow monitoring
        $izinCutiRequests = Absensi::where('user_id', $user->id)
            ->whereIn('status', ['izin', 'sakit', 'cuti'])
            ->latest('tanggal')
            ->take(5)
            ->get();

        $jamMasuk = Setting::get('jam_masuk', '08:00');
        $jamKeluar = Setting::get('jam_keluar', '17:00');

        return view('dashboard.karyawan', compact(
            'user', 'karyawan', 'todayAbsensi', 'activeAbsensi', 'totalKehadiranBulanIni', 'riwayatAbsensi',
            'jamMasuk', 'jamKeluar', 'izinCutiRequests'
        ));
    }

    private function senseiDashboard($user, $today, $dayName)
    {
        $sensei = $user->sensei;
        $todayAbsensi = Absensi::where('user_id', $user->id)->where('tanggal', $today)->latest()->first();
        $activeAbsensi = Absensi::where('user_id', $user->id)->where('tanggal', $today)->whereNotNull('jam_masuk')->whereNull('jam_keluar')->latest()->first();

        $totalKehadiran = Absensi::where('user_id', $user->id)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count();

        $jadwalHariIni = Jadwal::with('kelas')
            ->where('sensei_id', $sensei->id ?? 0)
            ->where('hari', $dayName)
            ->orderBy('jam_mulai')
            ->get();

        $riwayatMengajar = Jadwal::with('kelas')
            ->where('sensei_id', $sensei->id ?? 0)
            ->get();

        $riwayatAbsensi = Absensi::where('user_id', $user->id)
            ->latest('tanggal')
            ->take(5)
            ->get();

        $jamMasuk = Setting::get('jam_masuk', '08:00');
        $jamKeluar = Setting::get('jam_keluar', '17:00');

        return view('dashboard.sensei', compact(
            'user', 'sensei', 'todayAbsensi', 'activeAbsensi', 'totalKehadiran', 'jadwalHariIni', 'riwayatMengajar',
            'riwayatAbsensi', 'jamMasuk', 'jamKeluar'
        ));
    }

    public function workHours(Request $request)
    {
        $period = $request->get('period', '7d');
        $viewMode = $request->get('view', 'total');

        $endDate = Carbon::today();
        switch ($period) {
            case '1m':
                $startDate = Carbon::now()->startOfMonth();
                break;
            case '3m':
                $startDate = Carbon::now()->subMonths(2)->startOfMonth();
                break;
            case '7d':
            default:
                $startDate = Carbon::today()->subDays(6);
                break;
        }

        $query = Absensi::whereIn('status', ['hadir', 'terlambat'])
            ->whereNotNull('jam_masuk')
            ->whereNotNull('jam_keluar')
            ->where('tanggal', '>=', $startDate)
            ->where('tanggal', '<=', $endDate);

        if ($viewMode === 'per_user') {
            $records = (clone $query)
                ->join('users', 'absensi.user_id', '=', 'users.id')
                ->selectRaw('
                    absensi.tanggal,
                    users.name as user_name,
                    SUM(TIMESTAMPDIFF(MINUTE, absensi.jam_masuk, absensi.jam_keluar)) / 60.0 as hours
                ')
                ->groupBy('absensi.tanggal', 'absensi.user_id', 'users.name')
                ->orderBy('absensi.tanggal')
                ->get();

            $allDates = $records->pluck('tanggal')->unique()->sort()->values();
            $labels = $allDates->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray();

            $userNames = $records->pluck('user_name')->unique()->values()->toArray();

            $datasets = [];
            foreach ($userNames as $userName) {
                $userData = $records->where('user_name', $userName);
                $data = [];
                foreach ($allDates as $date) {
                    $match = $userData->first(fn($r) => $r->tanggal->format('Y-m-d') === $date->format('Y-m-d'));
                    $data[] = $match ? round($match->hours, 2) : 0;
                }
                $datasets[] = [
                    'label' => $userName,
                    'data' => $data,
                ];
            }

            return response()->json([
                'labels' => $labels,
                'datasets' => $datasets,
            ]);
        }

        // Total mode
        $records = (clone $query)
            ->selectRaw('
                tanggal,
                SUM(TIMESTAMPDIFF(MINUTE, jam_masuk, jam_keluar)) / 60.0 as total_hours,
                COUNT(*) as record_count
            ')
            ->groupBy('tanggal')
            ->havingRaw('total_hours > 0')
            ->orderBy('tanggal')
            ->get();

        $labels = $records->pluck('tanggal')->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray();
        $data = $records->pluck('total_hours')->map(fn($h) => round($h, 2))->toArray();
        $counts = $records->pluck('record_count')->toArray();

        return response()->json([
            'labels' => $labels,
            'data' => $data,
            'record_counts' => $counts,
        ]);
    }

    public function rekapAbsensi(Request $request)
    {
        $year  = (int) $request->get('year',  now()->year);
        $month = (int) $request->get('month', now()->month);
        $tab   = $request->get('tab', 'siswa'); // siswa | sensei | karyawan

        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();
        $today        = Carbon::today();
        $daysInMonth  = $startOfMonth->daysInMonth;

        // Build holiday map: ['Y-m-d' => 'keterangan']
        $holidayMap = Holiday::getMapForMonth($year, $month);
        
        $sysStartDate = Setting::get('tanggal_mulai_absensi', '2000-01-01');
        $weeklyHolidaysStr = Setting::get('hari_libur_mingguan', '0');
        $weeklyHolidays = $weeklyHolidaysStr !== '' ? explode(',', $weeklyHolidaysStr) : [];

        // Build days array with day number, date string, and holiday info
        $days = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dateObj = Carbon::createFromDate($year, $month, $d);
            $dateStr = $dateObj->format('Y-m-d');
            
            $isCustomHoliday = isset($holidayMap[$dateStr]);
            $isWeeklyHoliday = in_array((string)$dateObj->dayOfWeek, $weeklyHolidays);
            
            $days[]  = [
                'day'        => $d,
                'date'       => $dateStr,
                'is_holiday' => $isCustomHoliday || $isWeeklyHoliday,
                'holiday_label' => $isCustomHoliday ? $holidayMap[$dateStr] : 'Libur Mingguan',
                'is_future'  => $dateStr > $today->format('Y-m-d'),
                'is_today'   => $dateStr === $today->format('Y-m-d'),
            ];
        }

        // Fetch users per role/tab
        $usersData = [];

        if ($tab === 'siswa') {
            $siswas = Siswa::with('user', 'kelas')->get();
            foreach ($siswas as $siswa) {
                if (!$siswa->user) continue;
                $usersData[] = $this->buildUserRekapRow(
                    $siswa->user,
                    $year, $month, $days, $holidayMap, $today, $sysStartDate,
                    ['sub_label' => $siswa->kelas->nama_kelas ?? '-', 'nis' => $siswa->nis]
                );
            }
        } elseif ($tab === 'sensei') {
            $senseis = Sensei::with('user')->get();
            foreach ($senseis as $sensei) {
                if (!$sensei->user) continue;
                $usersData[] = $this->buildUserRekapRow(
                    $sensei->user,
                    $year, $month, $days, $holidayMap, $today, $sysStartDate,
                    ['sub_label' => $sensei->mata_pelajaran ?? '-']
                );
            }
        } elseif ($tab === 'karyawan') {
            $karyawans = Karyawan::with('user')->get();
            foreach ($karyawans as $karyawan) {
                if (!$karyawan->user) continue;
                $usersData[] = $this->buildUserRekapRow(
                    $karyawan->user,
                    $year, $month, $days, $holidayMap, $today, $sysStartDate,
                    ['sub_label' => $karyawan->jabatan . ' / ' . $karyawan->divisi, 'nik' => $karyawan->nik]
                );
            }
        }

        // Year/month options for filter
        $years  = range(now()->year - 2, now()->year + 1);
        $months = [
            1  => 'Januari',  2  => 'Februari', 3  => 'Maret',
            4  => 'April',    5  => 'Mei',       6  => 'Juni',
            7  => 'Juli',     8  => 'Agustus',   9  => 'September',
            10 => 'Oktober',  11 => 'November',  12 => 'Desember',
        ];

        return view('rekap.absensi', compact(
            'usersData', 'days', 'year', 'month', 'tab',
            'years', 'months', 'holidayMap', 'daysInMonth'
        ));
    }

    private function buildUserRekapRow(User $user, int $year, int $month, array $days, array $holidayMap, $today, string $sysStartDate, array $meta = []): array
    {
        // Fetch all absensi for this user this month
        $absensiList = Absensi::where('user_id', $user->id)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->get()
            ->keyBy(fn($a) => Carbon::parse($a->tanggal)->format('Y-m-d'));

        $cells   = []; // keyed by 'Y-m-d'
        $remarks = []; // sakit/izin catatan
        $totalHadir    = 0;
        $totalTerlambat = 0;
        $totalSakit    = 0;
        $totalIzin     = 0;
        $totalAlpha    = 0;
        $totalLibur    = 0;

        $userStartDateStr = $user->created_at ? $user->created_at->addDay()->format('Y-m-d') : '2000-01-01';

        foreach ($days as $dayInfo) {
            $dateStr   = $dayInfo['date'];
            $isPast    = !$dayInfo['is_future'] && !$dayInfo['is_today'];
            $isToday   = $dayInfo['is_today'];
            $isHoliday = $dayInfo['is_holiday'];

            if ($isHoliday) {
                $cells[$dateStr] = ['type' => 'libur', 'label' => 'Libur', 'sub' => $dayInfo['holiday_label']];
                $totalLibur++;
                continue;
            }

            if (isset($absensiList[$dateStr])) {
                $abs = $absensiList[$dateStr];
                switch ($abs->status) {
                    case 'hadir':
                    case 'terlambat':
                        $masuk  = $abs->jam_masuk  ? substr($abs->jam_masuk,  0, 5) : '?';
                        $keluar = $abs->jam_keluar ? substr($abs->jam_keluar, 0, 5) : '-';
                        $cells[$dateStr] = [
                            'type'  => $abs->status,
                            'label' => $masuk . '–' . $keluar,
                            'sub'   => null,
                        ];
                        if ($abs->status === 'terlambat') $totalTerlambat++;
                        else $totalHadir++;
                        break;
                    case 'izin':
                        $cells[$dateStr] = ['type' => 'izin', 'label' => 'Izin', 'sub' => $abs->catatan];
                        if ($abs->catatan) $remarks[] = ['tanggal' => $dateStr, 'tipe' => 'Izin', 'catatan' => $abs->catatan];
                        $totalIzin++;
                        break;
                    case 'sakit':
                        $cells[$dateStr] = ['type' => 'sakit', 'label' => 'Sakit', 'sub' => $abs->catatan];
                        if ($abs->catatan) $remarks[] = ['tanggal' => $dateStr, 'tipe' => 'Sakit', 'catatan' => $abs->catatan];
                        $totalSakit++;
                        break;
                    case 'alpha':
                        $cells[$dateStr] = ['type' => 'alpha', 'label' => 'Alpha', 'sub' => null];
                        $totalAlpha++;
                        break;
                    default:
                        $cells[$dateStr] = ['type' => 'other', 'label' => ucfirst($abs->status), 'sub' => null];
                }
            } elseif ($isPast) {
                // Past day with no record
                if ($dateStr < $sysStartDate || $dateStr < $userStartDateStr) {
                    // Before system started or before user's start date
                    $cells[$dateStr] = ['type' => 'empty', 'label' => '', 'sub' => null];
                } else {
                    // -> auto alpha
                    $cells[$dateStr] = ['type' => 'alpha', 'label' => 'Alpha', 'sub' => null];
                    $totalAlpha++;
                }
            } else {
                // Today with no record yet, or future day
                $cells[$dateStr] = ['type' => 'empty', 'label' => '', 'sub' => null];
            }
        }

        return [
            'user'    => $user,
            'meta'    => $meta,
            'cells'   => $cells,
            'remarks' => $remarks,
            'summary' => [
                'hadir'     => $totalHadir,
                'terlambat' => $totalTerlambat,
                'sakit'     => $totalSakit,
                'izin'      => $totalIzin,
                'alpha'     => $totalAlpha,
                'libur'     => $totalLibur,
            ],
        ];
    }

    public function akumulasiJam(Request $request)
    {
        $period = $request->get('period', '7d');
        $role = $request->get('role', '');

        $endDate = Carbon::today();
        switch ($period) {
            case '1m':
                $startDate = Carbon::now()->startOfMonth();
                break;
            case '3m':
                $startDate = Carbon::now()->subMonths(2)->startOfMonth();
                break;
            case '7d':
            default:
                $startDate = Carbon::today()->subDays(6);
                break;
        }

        $query = Absensi::whereIn('status', ['hadir', 'terlambat'])
            ->whereNotNull('jam_masuk')
            ->whereNotNull('jam_keluar')
            ->where('tanggal', '>=', $startDate)
            ->where('tanggal', '<=', $endDate)
            ->join('users', 'absensi.user_id', '=', 'users.id');

        if ($role && in_array($role, ['siswa', 'karyawan', 'sensei'])) {
            $query->where('users.role', $role);
        }

        $records = $query
            ->selectRaw('
                absensi.user_id,
                users.name as user_name,
                users.foto,
                users.role,
                SUM(TIMESTAMPDIFF(MINUTE, absensi.jam_masuk, absensi.jam_keluar)) / 60.0 as total_hours,
                COUNT(*) as total_days
            ')
            ->groupBy('absensi.user_id', 'users.name', 'users.foto', 'users.role')
            ->orderBy('total_hours', 'desc')
            ->get();

        $periodLabel = match ($period) {
            '7d' => '7 Hari Terakhir',
            '1m' => '1 Bulan Terakhir',
            '3m' => '3 Bulan Terakhir',
            default => '7 Hari Terakhir',
        };

        return view('dashboard.akumulasi-jam', compact('records', 'period', 'role', 'periodLabel'));
    }
}

