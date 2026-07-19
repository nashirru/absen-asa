<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AbsensiIzinTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_request_izin_for_today_h_0(): void
    {
        $user = User::factory()->create(['role' => 'siswa']);

        $response = $this->actingAs($user)
            ->post(route('absensi.store-izin'), [
                'status' => 'izin',
                'catatan' => 'Ada keperluan keluarga penting.',
                'tanggal' => Carbon::today()->toDateString(),
            ]);

        $response->assertSessionHas('error', 'Pengajuan izin atau cuti harus dilakukan minimal H-2 (2 hari sebelum tanggal yang diajukan).');
        // Absensi not created in DB
        $this->assertEquals(0, Absensi::count());
    }

    public function test_user_cannot_request_izin_for_tomorrow_h_1(): void
    {
        $user = User::factory()->create(['role' => 'siswa']);

        $response = $this->actingAs($user)
            ->post(route('absensi.store-izin'), [
                'status' => 'izin',
                'catatan' => 'Ada keperluan keluarga penting.',
                'tanggal' => Carbon::tomorrow()->toDateString(),
            ]);

        $response->assertSessionHas('error', 'Pengajuan izin atau cuti harus dilakukan minimal H-2 (2 hari sebelum tanggal yang diajukan).');
        $this->assertEquals(0, Absensi::count());
    }

    public function test_user_can_request_izin_for_h_2_or_later(): void
    {
        $user = User::factory()->create(['role' => 'siswa']);

        $targetDate = Carbon::today()->addDays(2)->toDateString();

        $response = $this->actingAs($user)
            ->post(route('absensi.store-izin'), [
                'status' => 'izin',
                'catatan' => 'Ada keperluan keluarga penting.',
                'tanggal' => $targetDate,
            ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');
        $this->assertEquals(1, Absensi::count());
        $this->assertEquals($targetDate, Absensi::first()->tanggal->toDateString());
    }

    public function test_user_can_request_sakit_immediately_for_today(): void
    {
        $user = User::factory()->create(['role' => 'siswa']);

        $response = $this->actingAs($user)
            ->post(route('absensi.store-sakit'), [
                'catatan' => 'Demam tinggi.',
            ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');
        $this->assertEquals(1, Absensi::count());
        $this->assertEquals(Carbon::today()->toDateString(), Absensi::first()->tanggal->toDateString());
        $this->assertEquals('sakit', Absensi::first()->status);
    }

    public function test_superadmin_can_delete_attendance(): void
    {
        $superadmin = User::factory()->create(['role' => 'super_admin']);
        $user = User::factory()->create(['role' => 'siswa']);
        $absensi = Absensi::create([
            'user_id' => $user->id,
            'tanggal' => Carbon::today()->toDateString(),
            'status' => 'hadir',
        ]);

        $response = $this->actingAs($superadmin)
            ->delete(route('absensi.destroy', $absensi->id));

        $response->assertStatus(302); // Redirect back
        $this->assertEquals(0, Absensi::count());
    }

    public function test_admin_cannot_delete_attendance(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'siswa']);
        $absensi = Absensi::create([
            'user_id' => $user->id,
            'tanggal' => Carbon::today()->toDateString(),
            'status' => 'hadir',
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('absensi.destroy', $absensi->id));

        $response->assertStatus(403);
        $this->assertEquals(1, Absensi::count());
    }
}
