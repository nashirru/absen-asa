<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\Kelas;
use App\Models\Jadwal;
use App\Models\Sensei;
use App\Models\Setting;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@lpkasa.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'phone' => '081234567890',
            'status_aktif' => true,
        ]);

        // Admin
        $admin = User::create([
            'name' => 'Admin LPK',
            'email' => 'admin@lpkasa.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567891',
            'status_aktif' => true,
        ]);

        // Sensei (3)
        $senseiData = [
            ['name' => 'Tanaka Sensei', 'email' => 'tanaka@lpkasa.com', 'mapel' => 'Bahasa Jepang N5'],
            ['name' => 'Yamada Sensei', 'email' => 'yamada@lpkasa.com', 'mapel' => 'Bahasa Jepang N4'],
            ['name' => 'Suzuki Sensei', 'email' => 'suzuki@lpkasa.com', 'mapel' => 'Kaiwa'],
        ];

        $senseiModels = [];
        foreach ($senseiData as $i => $s) {
            $user = User::create([
                'name' => $s['name'],
                'email' => $s['email'],
                'password' => Hash::make('password'),
                'role' => 'sensei',
                'phone' => '081234567' . str_pad($i + 100, 3, '0', STR_PAD_LEFT),
                'status_aktif' => true,
            ]);
            $senseiModels[] = Sensei::create([
                'user_id' => $user->id,
                'mata_pelajaran' => $s['mapel'],
            ]);
        }

        // Kelas (3)
        $kelasData = [
            ['nama' => 'Kelas A - N5', 'tingkat' => 'N5', 'sensei' => 0],
            ['nama' => 'Kelas B - N4', 'tingkat' => 'N4', 'sensei' => 1],
            ['nama' => 'Kelas C - Kaiwa', 'tingkat' => 'N3', 'sensei' => 2],
        ];

        $kelasModels = [];
        foreach ($kelasData as $k) {
            $kelasModels[] = Kelas::create([
                'nama_kelas' => $k['nama'],
                'tingkat' => $k['tingkat'],
                'sensei_id' => $senseiModels[$k['sensei']]->id,
                'kapasitas' => 30,
            ]);
        }

        // Siswa (5)
        $siswaData = [
            ['name' => 'Ahmad Rizki', 'email' => 'ahmad@lpkasa.com', 'nis' => 'S001', 'gender' => 'L', 'kelas' => 0],
            ['name' => 'Siti Nurhaliza', 'email' => 'siti@lpkasa.com', 'nis' => 'S002', 'gender' => 'P', 'kelas' => 0],
            ['name' => 'Budi Santoso', 'email' => 'budi@lpkasa.com', 'nis' => 'S003', 'gender' => 'L', 'kelas' => 1],
            ['name' => 'Dewi Lestari', 'email' => 'dewi@lpkasa.com', 'nis' => 'S004', 'gender' => 'P', 'kelas' => 1],
            ['name' => 'Rina Wati', 'email' => 'rina@lpkasa.com', 'nis' => 'S005', 'gender' => 'P', 'kelas' => 2],
        ];

        foreach ($siswaData as $i => $s) {
            $user = User::create([
                'name' => $s['name'],
                'email' => $s['email'],
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'phone' => '081234567' . str_pad($i + 200, 3, '0', STR_PAD_LEFT),
                'status_aktif' => true,
            ]);
            Siswa::create([
                'user_id' => $user->id,
                'nis' => $s['nis'],
                'gender' => $s['gender'],
                'tanggal_lahir' => '2000-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                'alamat' => 'Surabaya, Jawa Timur',
                'kelas_id' => $kelasModels[$s['kelas']]->id,
            ]);
        }

        // Karyawan (3)
        $karyawanData = [
            ['name' => 'Joko Widodo', 'email' => 'joko@lpkasa.com', 'nik' => 'K001', 'jabatan' => 'Staff Admin', 'divisi' => 'Administrasi'],
            ['name' => 'Maya Sari', 'email' => 'maya@lpkasa.com', 'nik' => 'K002', 'jabatan' => 'Staff Keuangan', 'divisi' => 'Keuangan'],
            ['name' => 'Eko Prasetyo', 'email' => 'eko@lpkasa.com', 'nik' => 'K003', 'jabatan' => 'Staff IT', 'divisi' => 'IT'],
        ];

        foreach ($karyawanData as $i => $k) {
            $user = User::create([
                'name' => $k['name'],
                'email' => $k['email'],
                'password' => Hash::make('password'),
                'role' => 'karyawan',
                'phone' => '081234567' . str_pad($i + 300, 3, '0', STR_PAD_LEFT),
                'status_aktif' => true,
            ]);
            Karyawan::create([
                'user_id' => $user->id,
                'nik' => $k['nik'],
                'jabatan' => $k['jabatan'],
                'divisi' => $k['divisi'],
                'alamat' => 'Surabaya, Jawa Timur',
            ]);
        }

        // Jadwal
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        foreach ($hariList as $hari) {
            foreach ($kelasModels as $ki => $kelas) {
                Jadwal::create([
                    'hari' => $hari,
                    'jam_mulai' => '08:00',
                    'jam_selesai' => '09:30',
                    'mata_pelajaran' => $senseiData[$ki]['mapel'],
                    'kelas_id' => $kelas->id,
                    'sensei_id' => $senseiModels[$ki]->id,
                ]);
            }
        }

        // Settings
        $settings = [
            ['key' => 'app_name', 'value' => 'LPK Asa Hikari Mulya'],
            ['key' => 'office_lat', 'value' => '-7.2575'],
            ['key' => 'office_lng', 'value' => '112.7521'],
            ['key' => 'geofence_radius', 'value' => '100'],
            ['key' => 'max_accuracy', 'value' => '50'],
            ['key' => 'jam_masuk', 'value' => '08:00'],
            ['key' => 'jam_keluar', 'value' => '17:00'],
            ['key' => 'batas_terlambat', 'value' => '08:15'],
        ];
        foreach ($settings as $s) {
            Setting::create($s);
        }

        // Data Keuangan (Akun, Kategori, Transaksi, Komponen Gaji, Periode Gaji)
        $this->call(FinanceDatabaseSeeder::class);
    }
}
