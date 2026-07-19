<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Category;
use App\Models\FundTransfer;
use App\Models\Karyawan;
use App\Models\PayrollDetail;
use App\Models\PayrollPeriod;
use App\Models\SalaryComponent;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FinanceDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('Memulai seeding data keuangan...');

        // ========== 1. ACCOUNTS ==========
        $this->command?->info('Membuat akun rekening...');
        $accounts = [
            ['name' => 'Kas Tunai',           'type' => 'cash', 'balance' => 15_000_000, 'description' => 'Kas kecil operasional harian'],
            ['name' => 'Bank BCA',            'type' => 'bank', 'balance' => 50_000_000, 'description' => 'Rekening BCA 1234567890'],
            ['name' => 'Bank Mandiri',         'type' => 'bank', 'balance' => 25_000_000, 'description' => 'Rekening Mandiri 9876543210'],
            ['name' => 'Tabungan Pendidikan',  'type' => 'bank', 'balance' => 10_000_000, 'description' => 'Dana khusus pendidikan siswa'],
        ];
        foreach ($accounts as $acc) {
            Account::create($acc);
        }

        // ========== 2. CATEGORIES ==========
        $this->command?->info('Membuat kategori transaksi...');
        $categories = [
            // Income
            ['name' => 'SPP Siswa',              'type' => 'income',  'color' => '#22c55e', 'sub_categories' => []],
            ['name' => 'Donasi',                 'type' => 'income',  'color' => '#16a34a', 'sub_categories' => []],
            ['name' => 'Pendaftaran',             'type' => 'income',  'color' => '#15803d', 'sub_categories' => []],
            ['name' => 'Bantuan Pemerintah',      'type' => 'income',  'color' => '#6366f1', 'sub_categories' => []],
            ['name' => 'Lain-lain (Pemasukan)',   'type' => 'income',  'color' => '#8b5cf6', 'sub_categories' => []],
            // Expense
            [
                'name' => 'Biaya Operasional',
                'type' => 'expense',
                'color' => '#f59e0b',
                'sub_categories' => ["Modul pelatihan", "Alat Tulis kantor", "Sertifikasi dan Ujian", "Perlengkapan peserta"]
            ],
            [
                'name' => 'Biaya pemasaran / marketing',
                'type' => 'expense',
                'color' => '#ea580c',
                'sub_categories' => ["Biaya iklan", "Komisi dan afiliasi", "Pengelolaan konten"]
            ],
            [
                'name' => 'Biaya sarana dan prasarana',
                'type' => 'expense',
                'color' => '#dc2626',
                'sub_categories' => ["Sewa gedung", "Tagihan utilitas", "Kebersihan dan keamanan", "Pemeliharaan sarana & prasarana"]
            ],
            [
                'name' => 'Biaya administrasi dan umum',
                'type' => 'expense',
                'color' => '#ef4444',
                'sub_categories' => ["Gaji karyawan", "Legalitas dan perizinan", "Perangkat lunak dan sistem", "Pajak"]
            ],
            [
                'name' => 'Biaya pengembangan',
                'type' => 'expense',
                'color' => '#38bdf8',
                'sub_categories' => ["Kunjungan kerjasama", "Pelatihan intruktur", "Matching job"]
            ],
            [
                'name' => 'Lain-lain (Pengeluaran)',
                'type' => 'expense',
                'color' => '#be123c',
                'sub_categories' => []
            ],
        ];
        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // ========== 3. UPDATE KARYAWAN DENGAN DATA PAYROLL ==========
        $this->command?->info('Update data payroll karyawan...');
        $karyawanList = Karyawan::with('user')->get();
        $payrollKaryawan = [
            'Joko Widodo'  => ['base_salary' => 4_500_000, 'jabatan' => 'Staff Admin',     'divisi' => 'Administrasi'],
            'Maya Sari'    => ['base_salary' => 5_000_000, 'jabatan' => 'Staff Keuangan',   'divisi' => 'Keuangan'],
            'Eko Prasetyo' => ['base_salary' => 4_800_000, 'jabatan' => 'Staff IT',         'divisi' => 'IT'],
        ];

        foreach ($karyawanList as $k) {
            $name = $k->user?->name;
            if (isset($payrollKaryawan[$name])) {
                $data = $payrollKaryawan[$name];
                $k->update([
                    'base_salary' => $data['base_salary'],
                    'join_date'   => '2024-01-01',
                    'status'      => 'active',
                ]);
            }
        }

        // ========== 4. SALARY COMPONENTS ==========
        $this->command?->info('Membuat komponen gaji...');
        $activeKaryawan = Karyawan::where('status', 'active')->get();
        $components = [];
        foreach ($activeKaryawan as $k) {
            $name = $k->user?->name ?? '';
            $components = array_merge($components, [
                ['karyawan_id' => $k->id, 'name' => 'Tunjangan Makan',   'type' => 'allowance', 'amount' => 500_000],
                ['karyawan_id' => $k->id, 'name' => 'Tunjangan Transport','type' => 'allowance', 'amount' => 300_000],
                ['karyawan_id' => $k->id, 'name' => 'Potongan BPJS',     'type' => 'deduction', 'amount' => 200_000],
                ['karyawan_id' => $k->id, 'name' => 'Potongan PPh 21',   'type' => 'deduction', 'amount' => 150_000],
            ]);
        }
        foreach ($components as $comp) {
            SalaryComponent::create($comp);
        }

        // ========== 5. TRANSACTIONS (3 bulan terakhir) ==========
        $this->command?->info('Membuat transaksi 3 bulan terakhir...');
        $accounts = Account::all();
        $incomeCategories = Category::where('type', 'income')->get();
        $expenseCategories = Category::where('type', 'expense')->get();
        $kasId = $accounts->where('name', 'Kas Tunai')->first()->id;
        $bcaId = $accounts->where('name', 'Bank BCA')->first()->id;

        $transactions = [];
        for ($month = 2; $month >= 0; $month--) {
            $date = Carbon::now()->subMonths($month);

            // Pemasukan tiap bulan
            $transactions[] = [
                'type'        => 'income',
                'account_id'  => $bcaId,
                'category_id' => $incomeCategories->where('name', 'SPP Siswa')->first()->id,
                'amount'      => rand(8_000_000, 12_000_000),
                'description' => 'Pembayaran SPP siswa bulan ' . $date->locale('id')->isoFormat('MMMM YYYY'),
                'date'        => $date->copy()->day(5),
            ];
            $transactions[] = [
                'type'        => 'income',
                'account_id'  => $kasId,
                'category_id' => $incomeCategories->where('name', 'Pendaftaran')->first()->id,
                'amount'      => rand(1_000_000, 3_000_000),
                'description' => 'Pendaftaran siswa baru',
                'date'        => $date->copy()->day(15),
            ];

            // Pengeluaran tiap bulan
            $transactions[] = [
                'type'        => 'expense',
                'account_id'  => $kasId,
                'category_id' => $expenseCategories->where('name', 'Biaya sarana dan prasarana')->first()->id,
                'jenis_pengeluaran' => ['Tagihan utilitas'],
                'amount'      => 1_200_000,
                'description' => 'Tagihan listrik & air',
                'date'        => $date->copy()->day(10),
            ];
            $transactions[] = [
                'type'        => 'expense',
                'account_id'  => $kasId,
                'category_id' => $expenseCategories->where('name', 'Biaya Operasional')->first()->id,
                'jenis_pengeluaran' => ['Alat Tulis kantor'],
                'amount'      => rand(500_000, 1_000_000),
                'description' => 'Belanja ATK dan perlengkapan kantor',
                'date'        => $date->copy()->day(20),
            ];
            $transactions[] = [
                'type'        => 'expense',
                'account_id'  => $kasId,
                'category_id' => $expenseCategories->where('name', 'Biaya Operasional')->first()->id,
                'jenis_pengeluaran' => ['Perlengkapan peserta'],
                'amount'      => 600_000,
                'description' => 'Konsumsi rapat bulanan',
                'date'        => $date->copy()->day(25),
            ];
        }
        foreach ($transactions as $tx) {
            Transaction::create($tx);
        }

        // ========== 6. FUND TRANSFERS ==========
        $this->command?->info('Membuat transfer dana...');
        $mandiriId = $accounts->where('name', 'Bank Mandiri')->first()->id;
        $tabunganId = $accounts->where('name', 'Tabungan Pendidikan')->first()->id;
        $fundTransfers = [
            ['from_account_id' => $bcaId, 'to_account_id' => $kasId,        'amount' => 5_000_000, 'date' => Carbon::now()->subMonths(2)->day(1),  'note' => 'Isi ulang kas tunai'],
            ['from_account_id' => $bcaId, 'to_account_id' => $mandiriId,    'amount' => 3_000_000, 'date' => Carbon::now()->subMonth()->day(1),    'note' => 'Transfer ke Mandiri untuk operasional'],
            ['from_account_id' => $bcaId, 'to_account_id' => $tabunganId,   'amount' => 2_000_000, 'date' => Carbon::now()->day(1),                'note' => 'Setor dana pendidikan'],
        ];
        foreach ($fundTransfers as $ft) {
            FundTransfer::create($ft);
        }

        // ========== 7. PAYROLL PERIOD ==========
        $this->command?->info('Membuat periode gaji...');
        $activeKaryawan = Karyawan::where('status', 'active')->get();
        if ($activeKaryawan->isNotEmpty()) {
            $period = PayrollPeriod::create([
                'month'  => Carbon::now()->month,
                'year'   => Carbon::now()->year,
                'status' => 'draft',
            ]);

            foreach ($activeKaryawan as $k) {
                $allowances = SalaryComponent::where('karyawan_id', $k->id)->where('type', 'allowance')->sum('amount');
                $deductions = SalaryComponent::where('karyawan_id', $k->id)->where('type', 'deduction')->sum('amount');
                $netSalary  = $k->base_salary + $allowances - $deductions;
                if ($netSalary < 0) $netSalary = 0;

                PayrollDetail::create([
                    'payroll_period_id' => $period->id,
                    'karyawan_id'       => $k->id,
                    'base_salary'       => $k->base_salary,
                    'total_allowance'   => $allowances,
                    'total_deduction'   => $deductions,
                    'bonus'             => rand(0, 1) ? 200_000 : 0,
                    'net_salary'        => $netSalary + (rand(0, 1) ? 200_000 : 0),
                ]);
            }

            $this->command?->info('Periode gaji ' . $period->month . '/' . $period->year . ' (draft) dengan ' . $activeKaryawan->count() . ' karyawan.');
        }

        $this->command?->info('Seeding keuangan selesai!');
    }
}
