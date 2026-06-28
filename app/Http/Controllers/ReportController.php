<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Absensi::with('user');

        $period = $request->get('period', 'month');
        switch ($period) {
            case 'week':
                $query->where('tanggal', '>=', Carbon::now()->startOfWeek());
                break;
            case 'year':
                $query->where('tanggal', '>=', Carbon::now()->startOfYear());
                break;
            default:
                $query->where('tanggal', '>=', Carbon::now()->startOfMonth());
                break;
        }

        if ($request->filled('role')) {
            $query->whereHas('user', fn($u) => $u->where('role', $request->role));
        }

        if ($request->filled('kelas_id')) {
            $query->whereHas('user.siswa', fn($s) => $s->where('kelas_id', $request->kelas_id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $absensi = $query->latest('tanggal')->paginate(20)->withQueryString();

        // Summary
        $summary = Absensi::query();
        switch ($period) {
            case 'week':
                $summary->where('tanggal', '>=', Carbon::now()->startOfWeek());
                break;
            case 'year':
                $summary->where('tanggal', '>=', Carbon::now()->startOfYear());
                break;
            default:
                $summary->where('tanggal', '>=', Carbon::now()->startOfMonth());
                break;
        }

        $totalHadir = (clone $summary)->whereIn('status', ['hadir', 'terlambat'])->count();
        $totalTerlambat = (clone $summary)->where('status', 'terlambat')->count();
        $totalIzin = (clone $summary)->where('status', 'izin')->count();
        $totalSakit = (clone $summary)->where('status', 'sakit')->count();
        $totalAlpha = (clone $summary)->where('status', 'alpha')->count();
        $totalLembur = (clone $summary)->where('is_lembur', true)->count();
        $totalDurasiLembur = (clone $summary)->where('is_lembur', true)->sum('durasi_lembur');
        $total = (clone $summary)->count();
        $persentase = $total > 0 ? round(($totalHadir / $total) * 100, 1) : 0;

        $kelasList = \App\Models\Kelas::orderBy('nama_kelas')->get();

        // Payroll Recap (Monthly Employee Recap)
        $payrollRecap = [];
        $tab = $request->get('tab', 'attendance');
        
        if ($tab === 'payroll') {
            $karyawans = \App\Models\Karyawan::with('user')->get();
            $month = (int) $request->get('month', now()->month);
            $year = (int) $request->get('year', now()->year);
            
            foreach ($karyawans as $k) {
                if (!$k->user) continue;
                $userAbsensi = Absensi::where('user_id', $k->user_id)
                    ->whereYear('tanggal', $year)
                    ->whereMonth('tanggal', $month)
                    ->get();
                    
                $present = $userAbsensi->whereIn('status', ['hadir', 'terlambat'])->count();
                $late = $userAbsensi->where('status', 'terlambat')->count();
                $izin = $userAbsensi->where('status', 'izin')->count();
                $sakit = $userAbsensi->where('status', 'sakit')->count();
                $cuti = $userAbsensi->where('status', 'cuti')->count();
                $alpha = $userAbsensi->where('status', 'alpha')->count();
                
                $lemburCount = $userAbsensi->where('is_lembur', true)->count();
                $lemburHours = $userAbsensi->where('is_lembur', true)->sum('durasi_lembur');
                
                $workMinutes = 0;
                foreach ($userAbsensi as $abs) {
                    if ($abs->jam_masuk && $abs->jam_keluar) {
                        $m = Carbon::parse($abs->jam_masuk);
                        $kTime = Carbon::parse($abs->jam_keluar);
                        $workMinutes += $m->diffInMinutes($kTime);
                    }
                }
                $workHours = round($workMinutes / 60, 2);

                $payrollRecap[] = [
                    'karyawan' => $k,
                    'present' => $present,
                    'late' => $late,
                    'izin' => $izin,
                    'sakit' => $sakit,
                    'cuti' => $cuti,
                    'alpha' => $alpha,
                    'lembur_count' => $lemburCount,
                    'lembur_hours' => $lemburHours,
                    'work_hours' => $workHours
                ];
            }
        }

        return view('report.index', compact(
            'absensi', 'totalHadir', 'totalTerlambat', 'totalIzin', 'totalSakit', 'totalAlpha', 'total', 'persentase',
            'totalLembur', 'totalDurasiLembur', 'kelasList', 'payrollRecap', 'tab'
        ));
    }

    public function exportExcel(Request $request)
    {
        $period = $request->get('period', 'month');
        $query = Absensi::with('user');

        switch ($period) {
            case 'week':
                $query->where('tanggal', '>=', Carbon::now()->startOfWeek());
                break;
            case 'year':
                $query->where('tanggal', '>=', Carbon::now()->startOfYear());
                break;
            default:
                $query->where('tanggal', '>=', Carbon::now()->startOfMonth());
                break;
        }

        $absensi = $query->latest('tanggal')->get();

        $filename = 'laporan-absensi-' . $period . '-' . now()->format('Y-m-d') . '.csv';

        // Simple CSV export
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($absensi) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Nama', 'Email', 'Role', 'Tanggal', 'Jam Masuk', 'Jam Keluar', 'Status', 'Jarak (m)', 'Lembur', 'Durasi Lembur (jam)']);
            foreach ($absensi as $item) {
                fputcsv($file, [
                    $item->user->name ?? '-',
                    $item->user->email ?? '-',
                    $item->user->role_label ?? '-',
                    $item->tanggal->format('Y-m-d'),
                    $item->jam_masuk ?? '-',
                    $item->jam_keluar ?? '-',
                    ucfirst($item->status),
                    $item->distance ?? '-',
                    $item->is_lembur ? 'Ya' : 'Tidak',
                    $item->durasi_lembur ?? '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $period = $request->get('period', 'month');
        $query = Absensi::with('user');

        switch ($period) {
            case 'week':
                $query->where('tanggal', '>=', Carbon::now()->startOfWeek());
                break;
            case 'year':
                $query->where('tanggal', '>=', Carbon::now()->startOfYear());
                break;
            default:
                $query->where('tanggal', '>=', Carbon::now()->startOfMonth());
                break;
        }

        $absensi = $query->latest('tanggal')->get();
        $total = $absensi->count();
        $totalHadir = $absensi->whereIn('status', ['hadir', 'terlambat'])->count();
        $persentase = $total > 0 ? round(($totalHadir / $total) * 100, 1) : 0;

        $pdf = Pdf::loadView('report.pdf', compact('absensi', 'total', 'totalHadir', 'persentase', 'period'));
        return $pdf->download('laporan-absensi-' . $period . '-' . now()->format('Y-m-d') . '.pdf');
    }
}
