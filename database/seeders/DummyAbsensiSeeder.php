<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DummyAbsensiSeeder extends Seeder
{
    public function run(): void
    {
        // Users yang akan diisi data absensi (karyawan + sensei)
        $users = User::whereIn('role', ['karyawan', 'sensei'])->get();

        if ($users->isEmpty()) {
            $this->command?->warn('Tidak ada user karyawan/sensei ditemukan. Jalankan DatabaseSeeder terlebih dahulu.');
            return;
        }

        $statuses = ['hadir', 'hadir', 'hadir', 'hadir', 'terlambat', 'hadir', 'hadir', 'izin', 'sakit', 'hadir'];
        $izinCatatan = [
            'Izin urusan keluarga',
            'Ada acara keluarga',
            'Izin mengurus dokumen',
            'Keperluan pribadi',
            'Izin karena ada janji dokter',
        ];
        $sakitCatatan = [
            'Demam dan flu',
            'Sakit kepala',
            'Sakit perut',
            'Batuk pilek',
            'Migrain',
            'Sakit gigi',
        ];

        // Generate data untuk 30 hari terakhir
        for ($day = 29; $day >= 0; $day--) {
            $date = Carbon::today()->subDays($day);

            // Skip weekend
            if ($date->isSaturday() || $date->isSunday()) {
                continue;
            }

            foreach ($users as $user) {
                // Random chance user hadir pada hari tersebut (70% chance)
                if (rand(1, 100) > 70) {
                    continue;
                }

                $status = $statuses[array_rand($statuses)];

                // Jam masuk antara 07:30 - 08:30
                $jamMasukHour = rand(7, 8);
                $jamMasukMinute = $jamMasukHour === 7 ? rand(30, 59) : rand(0, 30);
                $jamMasuk = sprintf('%02d:%02d:00', $jamMasukHour, $jamMasukMinute);

                // Jam keluar antara 16:30 - 17:30
                $jamKeluarHour = rand(16, 17);
                $jamKeluarMinute = $jamKeluarHour === 16 ? rand(30, 59) : rand(0, 30);
                $jamKeluar = sprintf('%02d:%02d:00', $jamKeluarHour, $jamKeluarMinute);

                // Jika izin/sakit, tidak ada jam masuk/keluar
                $catatan = null;
                if (in_array($status, ['izin', 'sakit'])) {
                    $jamMasuk = null;
                    $jamKeluar = null;
                    $catatan = $status === 'izin'
                        ? $izinCatatan[array_rand($izinCatatan)]
                        : $sakitCatatan[array_rand($sakitCatatan)];
                }

                // Jika terlambat, jam masuk setelah 08:15
                if ($status === 'terlambat') {
                    $jamMasuk = sprintf('%02d:%02d:00', 8, rand(16, 45));
                }

                Absensi::create([
                    'user_id' => $user->id,
                    'tanggal' => $date,
                    'jam_masuk' => $jamMasuk,
                    'jam_keluar' => $jamKeluar,
                    'latitude' => -7.2575 + (rand(-10, 10) / 10000),
                    'longitude' => 112.7521 + (rand(-10, 10) / 10000),
                    'accuracy' => rand(5, 30),
                    'distance' => rand(10, 80),
                    'radius' => 100,
                    'status' => $status,
                    'device' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                    'browser' => 'Chrome',
                    'ip_address' => '192.168.1.' . rand(1, 254),
                    'catatan' => $catatan,
                ]);
            }
        }

        $total = Absensi::count();
        $this->command?->info("Berhasil membuat {$total} data absensi dummy.");
    }
}
