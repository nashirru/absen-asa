<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardPdfController;
use App\Http\Controllers\Finance\AccountController;
use App\Http\Controllers\Finance\CategoryController;
use App\Http\Controllers\Finance\EmployeeController;
use App\Http\Controllers\Finance\FundTransferController;
use App\Http\Controllers\Finance\PayrollPeriodController;
use App\Http\Controllers\Finance\SalaryComponentController;
use App\Http\Controllers\Finance\TransactionController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SenseiController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PayrollSlipController;
use App\Http\Controllers\ReportPrintController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Financial / Keuangan Routes
    Route::middleware('role:super_admin,admin')->group(function () {
        Route::get('/admin/payroll-details/{payrollDetail}/slip', [PayrollSlipController::class, 'show'])->name('payroll.slip');
        Route::get('/admin/reports/print', [ReportPrintController::class, 'show'])->name('reports.print');
        Route::get('/admin/dashboard/pdf', [DashboardPdfController::class, 'export'])->name('dashboard.pdf');

        // Finance CRUD (Blade)
        Route::resource('finance/accounts', AccountController::class)->names('finance.accounts');
        Route::resource('finance/categories', CategoryController::class)->names('finance.categories');
        Route::resource('finance/salary-components', SalaryComponentController::class)->names('finance.salary-components');
        Route::get('finance/transactions/categories-by-type', [TransactionController::class, 'getCategoriesByType'])->name('finance.transactions.categories-by-type');
        Route::resource('finance/transactions', TransactionController::class)->names('finance.transactions');
        Route::resource('finance/fund-transfers', FundTransferController::class)->names('finance.fund-transfers');
        Route::resource('finance/payroll-periods', PayrollPeriodController::class)->names('finance.payroll-periods');
        Route::post('finance/payroll-periods/{payrollPeriod}/process', [PayrollPeriodController::class, 'process'])->name('finance.payroll-periods.process');
        Route::post('finance/payroll-periods/{payrollPeriod}/pay', [PayrollPeriodController::class, 'pay'])->name('finance.payroll-periods.pay');
        Route::put('finance/payroll-details/{payrollDetail}', [PayrollPeriodController::class, 'updatePayrollDetail'])->name('finance.payroll-details.update');
        Route::delete('finance/payroll-details/{payrollDetail}', [PayrollPeriodController::class, 'destroyPayrollDetail'])->name('finance.payroll-details.destroy');
    });

    // Profile (All roles)
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');

    // Absensi - Check In/Out (Siswa, Karyawan, Sensei)
    Route::middleware('role:siswa,karyawan,sensei')->group(function () {
        Route::get('/absensi/check-in', [AbsensiController::class, 'checkIn'])->name('absensi.check-in');
        Route::post('/absensi/check-in', [AbsensiController::class, 'storeCheckIn'])->name('absensi.store-check-in');
        Route::get('/absensi/check-out', [AbsensiController::class, 'checkOut'])->name('absensi.check-out');
        Route::post('/absensi/check-out', [AbsensiController::class, 'storeCheckOut'])->name('absensi.store-check-out');
        Route::get('/absensi/izin', [AbsensiController::class, 'izin'])->name('absensi.izin');
        Route::post('/absensi/izin', [AbsensiController::class, 'storeIzin'])->name('absensi.store-izin');
        Route::get('/absensi/sakit', [AbsensiController::class, 'sakit'])->name('absensi.sakit');
        Route::post('/absensi/sakit', [AbsensiController::class, 'storeSakit'])->name('absensi.store-sakit');
        Route::get('/absensi/riwayat', [AbsensiController::class, 'riwayat'])->name('absensi.riwayat');

        // Lembur
        Route::get('/absensi/lembur', [AbsensiController::class, 'lembur'])->name('absensi.lembur');
        Route::post('/absensi/lembur/check-in', [AbsensiController::class, 'storeLemburCheckIn'])->name('absensi.lembur-check-in');
        Route::post('/absensi/lembur/check-out', [AbsensiController::class, 'storeLemburCheckOut'])->name('absensi.lembur-check-out');
    });

    // Jadwal My Schedule (Siswa, Sensei, Karyawan)
    Route::middleware('role:siswa,sensei,karyawan')->group(function () {
        Route::get('/jadwal/saya', [JadwalController::class, 'mySchedule'])->name('jadwal.my-schedule');
    });

    // Kelas for Sensei
    Route::middleware('role:sensei')->group(function () {
        Route::get('/kelas-saya', [KelasController::class, 'index'])->name('kelas.saya');
        Route::get('/kelas-saya/{kelas}', [KelasController::class, 'show'])->name('kelas.saya.detail');
        Route::post('/kelas-saya/{kelas}/override-absensi', [KelasController::class, 'overrideAbsensi'])->name('kelas.saya.override');
        Route::post('/kelas-saya/{kelas}/mark-absent', [KelasController::class, 'markStudentAbsent'])->name('kelas.saya.mark-absent');
        Route::post('/kelas-saya/update-progress', [KelasController::class, 'updateProgressNilai'])->name('kelas.saya.update-progress');
        Route::get('/jadwal/{jadwal}/edit-modul', [JadwalController::class, 'editModul'])->name('sensei.jadwal.edit-modul');
        Route::put('/jadwal/{jadwal}/update-modul', [JadwalController::class, 'updateModul'])->name('sensei.jadwal.update-modul');
    });

    // Share Jadwal CRUD route for Admin and Sensei
    Route::middleware('role:super_admin,admin,sensei')->group(function () {
        Route::resource('jadwal', JadwalController::class);
    });

    // Admin & Super Admin routes
    Route::middleware('role:super_admin,admin')->group(function () {
        // Users
        Route::post('/users/{user}/reset-device', [UserController::class, 'resetDevice'])->name('users.reset-device');
        Route::resource('users', UserController::class);

        // Siswa
        Route::resource('siswa', SiswaController::class);

        // Karyawan (dengan inline salary components)
        Route::resource('karyawan', KaryawanController::class);
        Route::post('/karyawan/{karyawan}/salary-components', [KaryawanController::class, 'storeSalaryComponent'])->name('karyawan.salary-components.store');
        Route::put('/karyawan/salary-components/{salaryComponent}', [KaryawanController::class, 'updateSalaryComponent'])->name('karyawan.salary-components.update');
        Route::delete('/karyawan/salary-components/{salaryComponent}', [KaryawanController::class, 'destroySalaryComponent'])->name('karyawan.salary-components.destroy');

        // Sensei
        Route::resource('sensei', SenseiController::class);

        // Kelas
        Route::resource('kelas', KelasController::class);

        // Locations
        Route::resource('locations', LocationController::class);
        Route::get('/api/locations/active', [LocationController::class, 'getActiveLocations'])->name('locations.active');

        // Absensi Admin
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::post('/absensi/mark', [AbsensiController::class, 'adminMarkAlpha'])->name('absensi.mark');
        Route::post('/absensi/{absensi}/approve', [AbsensiController::class, 'approve'])->name('absensi.approve');
        Route::post('/absensi/{absensi}/reject', [AbsensiController::class, 'reject'])->name('absensi.reject');

        // Hapus data absensi (soft delete)
        Route::delete('/absensi/{absensi}', [AbsensiController::class, 'adminDestroy'])->name('absensi.destroy');

        // Reports
        Route::get('/report', [ReportController::class, 'index'])->name('report.index');
        Route::get('/report/export-excel', [ReportController::class, 'exportExcel'])->name('report.export-excel');
        Route::get('/report/export-pdf', [ReportController::class, 'exportPdf'])->name('report.export-pdf');

        // Rekap Absensi (Spreadsheet)
        Route::get('/rekap-absensi', [DashboardController::class, 'rekapAbsensi'])->name('rekap.absensi');

        // Hari Libur
        Route::resource('holidays', HolidayController::class)->only(['index', 'store', 'destroy']);

        // Dashboard API
        Route::get('/dashboard/work-hours', [DashboardController::class, 'workHours'])->name('dashboard.work-hours');

        // Akumulasi Jam Kerja
        Route::get('/akumulasi-jam', [DashboardController::class, 'akumulasiJam'])->name('akumulasi-jam');
    });

    // Settings - Only Super Admin
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });
});
