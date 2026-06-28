<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('karyawan', 'base_salary')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->decimal('base_salary', 15, 2)->default(0)->after('alamat');
                $table->date('join_date')->nullable()->after('base_salary');
                $table->enum('status', ['active', 'inactive'])->default('active')->after('join_date');
            });
        }
    }

    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropColumn(['base_salary', 'join_date', 'status']);
        });
    }
};
