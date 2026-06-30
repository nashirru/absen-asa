<?php

namespace Tests\Unit;

use App\Models\Karyawan;
use App\Models\PayrollDetail;
use App\Models\PayrollPeriod;
use App\Models\SalaryComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollPeriodObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_created_creates_payroll_details_for_active_karyawans(): void
    {
        // Create active karyawan with salary components
        $user = User::factory()->create(['role' => 'karyawan']);
        $karyawan = Karyawan::create([
            'user_id' => $user->id,
            'nik' => 'EMP-001',
            'jabatan' => 'Staff',
            'status' => 'active',
            'base_salary' => 5000000,
        ]);

        SalaryComponent::create([
            'karyawan_id' => $karyawan->id,
            'type' => 'allowance',
            'amount' => 500000,
            'name' => 'Transport',
        ]);

        SalaryComponent::create([
            'karyawan_id' => $karyawan->id,
            'type' => 'deduction',
            'amount' => 200000,
            'name' => 'BPJS',
        ]);

        // Trigger the observer via creation
        $payrollPeriod = PayrollPeriod::create([
            'status' => 'draft',
            'month' => 6,
            'year' => 2026,
        ]);

        // Check payroll details were generated
        $details = PayrollDetail::where('payroll_period_id', $payrollPeriod->id)->get();

        $this->assertCount(1, $details);
        $detail = $details->first();
        $this->assertEquals($karyawan->id, $detail->karyawan_id);
        $this->assertEquals(5000000, $detail->base_salary);
        $this->assertEquals(500000, $detail->total_allowance);
        $this->assertEquals(200000, $detail->total_deduction);
        $this->assertEquals(5300000, $detail->net_salary);
    }

    public function test_created_skips_when_status_is_not_draft(): void
    {
        $user = User::factory()->create(['role' => 'karyawan']);
        Karyawan::create([
            'user_id' => $user->id,
            'nik' => 'EMP-002',
            'jabatan' => 'Staff',
            'status' => 'active',
            'base_salary' => 5000000,
        ]);

        $payrollPeriod = PayrollPeriod::create([
            'status' => 'paid',
            'month' => 6,
            'year' => 2026,
        ]);

        $details = PayrollDetail::where('payroll_period_id', $payrollPeriod->id)->get();

        $this->assertCount(0, $details);
    }
}
