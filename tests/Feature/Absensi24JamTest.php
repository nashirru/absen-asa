<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Location;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Absensi24JamTest extends TestCase
{
    use RefreshDatabase;

    private User $karyawan;
    private Location $location;
    private Shift $shift24h;

    protected function setUp(): void
    {
        parent::setUp();

        $this->karyawan = User::factory()->create([
            'role' => 'karyawan',
            'status_aktif' => true,
        ]);

        $this->location = Location::create([
            'name' => 'Kantor Penguji',
            'latitude' => -7.2575,
            'longitude' => 112.7521,
            'radius' => 100,
            'is_active' => true,
            'allowed_roles' => ['karyawan'],
        ]);

        $this->shift24h = Shift::create([
            'location_id' => $this->location->id,
            'nama_shift' => 'Shift 24 Jam',
            'jam_masuk' => '08:00:00',
            'jam_keluar' => '08:00:00',
            'batas_terlambat' => '08:15:00',
            'is_24_hours' => true,
        ]);
    }

    public function test_check_in_on_24_hours_shift_succeeds_without_time_window_restriction(): void
    {
        // Set time to 14:00 (outside normal 08:00 - 08:15 but since it's 24h it should pass)
        Carbon::setTestNow(Carbon::today()->setTime(14, 0, 0));

        $response = $this->actingAs($this->karyawan)
            ->postJson(route('absensi.store-check-in'), [
                'latitude' => -7.2575,
                'longitude' => 112.7521,
                'accuracy' => 15,
                'location_id' => $this->location->id,
                'shift_id' => $this->shift24h->id,
            ]);

        $response->assertJson([
            'success' => true,
        ]);

        $abs = Absensi::where('user_id', $this->karyawan->id)->latest()->first();
        $this->assertNotNull($abs);
        $this->assertEquals($this->shift24h->id, $abs->shift_id);
        $this->assertEquals(Carbon::today()->toDateString(), $abs->tanggal->toDateString());
        $this->assertEquals('14:00:00', $abs->jam_masuk);
        $this->assertNull($abs->jam_keluar);
        $this->assertNull($abs->tanggal_keluar);
    }

    public function test_check_in_on_day_2_auto_checks_out_day_1_attendance(): void
    {
        // Day 1 check-in
        Carbon::setTestNow(Carbon::parse('2026-07-01 08:00:00'));
        $day1Absensi = Absensi::create([
            'user_id' => $this->karyawan->id,
            'location_id' => $this->location->id,
            'shift_id' => $this->shift24h->id,
            'shift' => $this->shift24h->nama_shift,
            'tanggal' => '2026-07-01',
            'jam_masuk' => '08:00:00',
            'latitude' => -7.2575,
            'longitude' => 112.7521,
            'accuracy' => 10,
            'distance' => 0.0,
            'status' => 'hadir',
        ]);

        // Day 2 check-in at 07:55:00
        Carbon::setTestNow(Carbon::parse('2026-07-02 07:55:00'));

        $response = $this->actingAs($this->karyawan)
            ->postJson(route('absensi.store-check-in'), [
                'latitude' => -7.2575,
                'longitude' => 112.7521,
                'accuracy' => 10,
                'location_id' => $this->location->id,
                'shift_id' => $this->shift24h->id,
            ]);

        $response->assertJson([
            'success' => true,
        ]);

        // Assert Day 1 attendance was auto-closed
        $day1Absensi->refresh();
        $this->assertEquals('07:55:00', $day1Absensi->jam_keluar);
        $this->assertEquals('2026-07-02', $day1Absensi->tanggal_keluar->toDateString());
        $this->assertEquals(-7.2575, $day1Absensi->latitude_keluar);
        $this->assertEquals(112.7521, $day1Absensi->longitude_keluar);

        // Assert Day 2 attendance was created
        $day2Absensi = Absensi::where('user_id', $this->karyawan->id)->latest('id')->first();
        $this->assertNotNull($day2Absensi);
        $this->assertEquals('2026-07-02', $day2Absensi->tanggal->toDateString());
        $this->assertEquals('07:55:00', $day2Absensi->jam_masuk);
        $this->assertNull($day2Absensi->jam_keluar);
        $this->assertNull($day2Absensi->tanggal_keluar);
    }

    public function test_manual_check_out_on_day_2_for_day_1_check_in_works(): void
    {
        // Day 1 check-in
        Carbon::setTestNow(Carbon::parse('2026-07-01 08:00:00'));
        $day1Absensi = Absensi::create([
            'user_id' => $this->karyawan->id,
            'location_id' => $this->location->id,
            'shift_id' => $this->shift24h->id,
            'shift' => $this->shift24h->nama_shift,
            'tanggal' => '2026-07-01',
            'jam_masuk' => '08:00:00',
            'latitude' => -7.2575,
            'longitude' => 112.7521,
            'accuracy' => 10,
            'distance' => 0.0,
            'status' => 'hadir',
        ]);

        // Day 2 manual check-out at 08:00:00
        Carbon::setTestNow(Carbon::parse('2026-07-02 08:00:00'));

        $response = $this->actingAs($this->karyawan)
            ->postJson(route('absensi.store-check-out'), [
                'latitude' => -7.2575,
                'longitude' => 112.7521,
                'accuracy' => 10,
            ]);

        $response->assertJson([
            'success' => true,
            'message' => 'Check out berhasil!',
        ]);

        $day1Absensi->refresh();
        $this->assertEquals('08:00:00', $day1Absensi->jam_keluar);
        $this->assertEquals('2026-07-02', $day1Absensi->tanggal_keluar->toDateString());
    }

    public function test_cross_day_work_hours_are_calculated_correctly(): void
    {
        $day1Absensi = Absensi::create([
            'user_id' => $this->karyawan->id,
            'tanggal' => '2026-07-01',
            'jam_masuk' => '08:00:00',
            'tanggal_keluar' => '2026-07-02',
            'jam_keluar' => '07:55:00',
            'status' => 'hadir',
        ]);

        $start = Carbon::parse($day1Absensi->tanggal->toDateString() . ' ' . $day1Absensi->jam_masuk);
        $end = Carbon::parse($day1Absensi->tanggal_keluar->toDateString() . ' ' . $day1Absensi->jam_keluar);
        $workMinutes = $start->diffInMinutes($end);

        $this->assertEquals(1435, $workMinutes); // 23 hours 55 minutes = 1435 minutes
        $this->assertEquals(23.92, round($workMinutes / 60, 2));
    }
}
