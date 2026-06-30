# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**LPK Asa Hikari Mulya** — A school/institution management system built with Laravel 12 + Filament PHP 3.3. Handles attendance (absensi) with GPS geofencing, selfie capture, student/teacher/employee management, scheduling, financial accounting, and payroll.

## Key Commands

### Setup & Dev Server
```bash
# Full setup from scratch
composer install && cp .env.example .env && php artisan key:generate && php artisan migrate --seed && npm install && npm run build

# Development server (runs server + queue + logs + Vite concurrently)
composer dev

# Single server only
php artisan serve
```

### Database
```bash
# Migrate + seed
php artisan migrate --seed

# Fresh migrate + seed (destroys all data)
php artisan migrate:fresh --seed

# Run a specific seeder
php artisan db:seed --class=DummyAbsensiSeeder
```

### Frontend
```bash
npm run dev    # Vite dev (hot reload)
npm run build  # Production build
```

### Testing
```bash
composer test   # Runs php artisan config:clear + phpunit
# or directly:
php artisan test
```

### Other
```bash
php artisan storage:link    # Create public/storage → storage/app/public symlink
php artisan queue:listen    # Process queue jobs (session/cache expiry, etc.)
php artisan pail            # Tail log file (dev only)
git rm --cached <file>      # Remove tracked files that should be ignored
```

## Architecture

### Role System
5 roles enforced via `RoleMiddleware` (`app/Http/Middleware/RoleMiddleware.php`):
- **super_admin** — Full access: settings, all CRUD, all reports
- **admin** — All CRUD except global settings
- **sensei** — Teacher: own classes, schedules, student attendance
- **karyawan** — Employee: check-in/out, own schedule
- **siswa** — Student: check-in/out, own schedule

All users in single `users` table with `role` column. Person-specific data in separate tables (`siswa`, `sensei`, `karyawan`) linked by `user_id` FK.

### Attendance Flow
- Check-in with GPS geofence (Haversine distance against `locations` table), optional selfie (base64 via camera), device binding (`device_uuid`), and spoofing detection
- Multi-location support with per-location shifts and allowed roles
- Statuses: hadir, terlambat, izin, sakit, alpha
- Soft deletes on absensi records

### Routes (`routes/web.php`)
- Login/logout (custom AuthController, no Laravel Breeze/Jetstream)
- Attendance CRUD: role-gated (siswa/karyawan/sensei can check-in; admin manages all)
- Schedule viewing/editing per role
- Admin CRUD for users, siswa, sensei, karyawan, kelas, locations, holidays
- Filament panel at `/admin` for finance/payroll (super_admin + admin only)
- Super admin: `/settings` for app config

### Filament Admin Panel (`/admin`)
- **Finance**: Accounts, Categories, Transactions, Fund Transfers (observers auto-update balances)
- **Payroll**: Employees, Salary Components, Payroll Periods (auto-generates payroll details for active employees, linked transactions)
- Observers in `app/Observers/`: TransactionObserver, FundTransferObserver, PayrollPeriodObserver

### UI Guidelines (DESAIN.md)
Two design surfaces:
- **Admin Console** (Super Admin/Admin) — desktop-first, indigo `#6D5DFC`, flat cards (1px borders, no shadows), Inter typeface
- **Member App** (Siswa/Karyawan/Sensei) — mobile-first PWA, blue `#1A6DFF`, soft-shadow floating cards, Plus Jakarta Sans
- **No emoji** in code — use CSS-based indicators (badges with Tailwind classes via `getStatusBadgeAttribute()`)
- Base spacing unit: 8px

### Models (`app/Models/`)
- **User** → hasOne(Siswa|Karyawan|Sensei), hasMany(Absensi)
- **Siswa** → belongsTo(User|Kelas)
- **Sensei** → belongsTo(User), hasMany(Kelas|Jadwal)
- **Karyawan** → belongsTo(User)
- **Absensi** → belongsTo(User|Location|Shift), soft deletes
- **PayrollPeriod** → hasMany(PayrollDetail|Transaction)
- **Setting** key-value store for app config (coordinates, radius, hours)
- Finance: Account→Transaction, Account→FundTransfer, Category→Transaction, Employee→SalaryComponent→PayrollDetail, PayrollPeriod→PayrollDetail

### Key Config
- Timezone: `Asia/Jakarta` (config/app.php)
- Default DB: MySQL (`DB_CONNECTION=mysql` in .env)
- Session/Cache/Queue: database driver
- Vite with Tailwind CSS v4 via `@tailwindcss/vite`

## Audit Results (2026-06-29)

**Skor: 72/100 — DITOLAK** (belum siap produksi penuh)

### Issues Found & Fixed

| Issue | Severity | Status |
|-------|----------|--------|
| N+1 query di PayrollPeriodObserver (salaryComponents dalam loop) | TINGGI | ✅ Fixed |
| N+1 query di ReportPrintController (Transaction per foreach) | TINGGI | ✅ Fixed |
| Emoji HTML entities di Absensi model (inkonsisten DESAIN.md) | RENDAH | ✅ Fixed |
| .env.example masih default Laravel (APP_DEBUG=true, sqlite, locale en) | SEDANG | ✅ Fixed |
| .gitignore tidak lengkap (composer.phar, *.sql, *.zip, dll tracked) | SEDANG | ✅ Fixed |
| Kurang composite index untuk query umum (absensi, transactions) | SEDANG | ✅ Fixed (migration baru) |
| composer.phar & composer-setup.php tracked di git | SEDANG | ✅ Fixed (git rm --cached) |

### Issues Butuh Tindakan Manual

| Issue | Severity | Rekomendasi |
|-------|----------|-------------|
| Tidak ada test coverage (skor 15/100) | KRITIS | Buat Feature/Unit test untuk AuthController, AbsensiController, RoleMiddleware, Finance |
| AbsensiController (689 baris) & DashboardController (598 baris) terlalu panjang | SEDANG | Pisahkan logic ke Service classes |
| Tidak ada try/catch di controllers | SEDANG | Tambahkan global exception handler di AppServiceProvider |
| Tidak ada rate limiting login/check-in | SEDANG | Tambahkan throttle middleware ke route login & absensi |
| Validasi password hanya min:6 | RENDAH | Naikkan ke min:8 |
| Upload selfie tanpa validasi tipe file (base64) | RENDAH | Validasi magic bytes, bukan hanya prefix data:image |

## Known Issues / Gotchas

- **DummyAbsensiSeeder** exists but is NOT called from DatabaseSeeder — run manually if needed
- Composer dev script uses `concurrently` (npm package) for parallel processes
- No automated test coverage yet (placeholder tests only)
- Filament resources auto-discover — register new resources in the default panel
- `.env` aman (tidak di-track git) tapi pastikan tidak pernah di `git add` secara manual
- `composer.phar` dan `composer-setup.php` sudah dihapus dari tracking — jalankan `git commit` untuk finalisasi
