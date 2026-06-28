<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('device_uuid')->nullable()->after('status_aktif');
        });

        // 2. Update absensi table
        Schema::table('absensi', function (Blueprint $table) {
            $table->string('status')->default('hadir')->change();
            
            // Check out geolocation
            $table->decimal('latitude_keluar', 10, 7)->nullable()->after('selfie_check_out');
            $table->decimal('longitude_keluar', 10, 7)->nullable()->after('latitude_keluar');
            $table->decimal('accuracy_keluar', 8, 2)->nullable()->after('longitude_keluar');
            $table->decimal('distance_keluar', 8, 2)->nullable()->after('accuracy_keluar');
            
            // Anomaly detection
            $table->boolean('is_mocked')->default(false)->after('distance_keluar');
            $table->boolean('is_anomaly')->default(false)->after('is_mocked');
            $table->text('anomaly_details')->nullable()->after('is_anomaly');
        });

        // 3. Update siswa table
        Schema::table('siswa', function (Blueprint $table) {
            $table->integer('progress_pelatihan')->default(0)->after('kelas_id');
            $table->string('nilai_pelatihan')->nullable()->after('progress_pelatihan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('device_uuid');
        });

        Schema::table('absensi', function (Blueprint $table) {
            // Note: changing back to enum is database-specific and can be tricky,
            // keeping it as string is safe for down too or changing it back if needed.
            $table->dropColumn([
                'latitude_keluar', 'longitude_keluar', 'accuracy_keluar', 'distance_keluar',
                'is_mocked', 'is_anomaly', 'anomaly_details'
            ]);
        });

        Schema::table('siswa', function (Blueprint $table) {
            $table->dropColumn(['progress_pelatihan', 'nilai_pelatihan']);
        });
    }
};
