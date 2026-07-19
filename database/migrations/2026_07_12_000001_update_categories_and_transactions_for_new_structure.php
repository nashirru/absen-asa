<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add columns
        Schema::table('categories', function (Blueprint $table) {
            $table->json('sub_categories')->nullable()->after('color');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->json('jenis_pengeluaran')->nullable()->after('category_id');
        });

        // 2. Insert/Update new categories
        $newCategories = [
            'Biaya Operasional' => [
                'type' => 'expense',
                'color' => '#f59e0b',
                'sub_categories' => json_encode(["Modul pelatihan", "Alat Tulis kantor", "Sertifikasi dan Ujian", "Perlengkapan peserta"]),
            ],
            'Biaya pemasaran / marketing' => [
                'type' => 'expense',
                'color' => '#ea580c',
                'sub_categories' => json_encode(["Biaya iklan", "Komisi dan afiliasi", "Pengelolaan konten"]),
            ],
            'Biaya sarana dan prasarana' => [
                'type' => 'expense',
                'color' => '#dc2626',
                'sub_categories' => json_encode(["Sewa gedung", "Tagihan utilitas", "Kebersihan dan keamanan", "Pemeliharaan sarana & prasarana"]),
            ],
            'Biaya administrasi dan umum' => [
                'type' => 'expense',
                'color' => '#ef4444',
                'sub_categories' => json_encode(["Gaji karyawan", "Legalitas dan perizinan", "Perangkat lunak dan sistem", "Pajak"]),
            ],
            'Biaya pengembangan' => [
                'type' => 'expense',
                'color' => '#38bdf8',
                'sub_categories' => json_encode(["Kunjungan kerjasama", "Pelatihan intruktur", "Matching job"]),
            ],
            'Lain-lain (Pengeluaran)' => [
                'type' => 'expense',
                'color' => '#be123c',
                'sub_categories' => json_encode([]),
            ]
        ];

        $categoryIds = [];
        foreach ($newCategories as $name => $data) {
            $existing = DB::table('categories')->where('name', $name)->where('type', 'expense')->first();
            if ($existing) {
                DB::table('categories')->where('id', $existing->id)->update([
                    'color' => $data['color'],
                    'sub_categories' => $data['sub_categories'],
                ]);
                $categoryIds[$name] = $existing->id;
            } else {
                $id = DB::table('categories')->insertGetId(array_merge(['name' => $name], $data));
                $categoryIds[$name] = $id;
            }
        }

        // 3. Migrate existing transactions
        $oldCategoryMapping = [
            'Gaji Karyawan' => ['new' => 'Biaya administrasi dan umum', 'jenis' => ["Gaji karyawan"]],
            'Listrik & Air' => ['new' => 'Biaya sarana dan prasarana', 'jenis' => ["Tagihan utilitas"]],
            'ATK & Perlengkapan' => ['new' => 'Biaya Operasional', 'jenis' => ["Alat Tulis kantor"]],
            'Maintenance & Perbaikan' => ['new' => 'Biaya sarana dan prasarana', 'jenis' => ["Pemeliharaan sarana & prasarana"]],
            'Transportasi' => ['new' => 'Biaya Operasional', 'jenis' => []],
            'Konsumsi' => ['new' => 'Biaya Operasional', 'jenis' => []],
            'Lain-lain (Pengeluaran)' => ['new' => 'Lain-lain (Pengeluaran)', 'jenis' => []],
        ];

        foreach ($oldCategoryMapping as $oldName => $mapping) {
            $oldCat = DB::table('categories')->where('name', $oldName)->where('type', 'expense')->first();
            if ($oldCat) {
                $newCatId = $categoryIds[$mapping['new']];
                DB::table('transactions')
                    ->where('category_id', $oldCat->id)
                    ->update([
                        'category_id' => $newCatId,
                        'jenis_pengeluaran' => json_encode($mapping['jenis']),
                    ]);
            }
        }

        // 4. Delete old categories (excluding new ones)
        $newCategoryNames = array_keys($newCategories);
        DB::table('categories')
            ->where('type', 'expense')
            ->whereNotIn('name', $newCategoryNames)
            ->delete();
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('sub_categories');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('jenis_pengeluaran');
        });
    }
};
