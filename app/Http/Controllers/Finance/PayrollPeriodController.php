<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Category;
use App\Models\Karyawan;
use App\Models\PayrollDetail;
use App\Models\PayrollPeriod;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollPeriodController extends Controller
{
    public function index()
    {
        $periods = PayrollPeriod::withCount('payrollDetails')
            ->withSum('payrollDetails', 'net_salary')
            ->latest()
            ->paginate(10);

        return view('finance.payroll-periods.index', compact('periods'));
    }

    public function create()
    {
        return view('finance.payroll-periods.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000|max:2099',
        ]);

        // Check for existing period with same month/year
        $exists = PayrollPeriod::where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->with('error', 'Periode gaji untuk bulan dan tahun tersebut sudah ada.');
        }

        $period = PayrollPeriod::create([
            'month' => $validated['month'],
            'year' => $validated['year'],
            'status' => 'draft',
        ]);

        // Auto-generate PayrollDetails for active karyawan
        $activeKaryawans = Karyawan::where('status', 'active')->get();
        foreach ($activeKaryawans as $karyawan) {
            $allowances = $karyawan->salaryComponents()
                ->where('type', 'allowance')
                ->sum('amount');
            $deductions = $karyawan->salaryComponents()
                ->where('type', 'deduction')
                ->sum('amount');
            $netSalary = $karyawan->base_salary + $allowances - $deductions;
            if ($netSalary < 0) $netSalary = 0;

            PayrollDetail::create([
                'payroll_period_id' => $period->id,
                'karyawan_id' => $karyawan->id,
                'base_salary' => $karyawan->base_salary,
                'total_allowance' => $allowances,
                'total_deduction' => $deductions,
                'bonus' => 0,
                'net_salary' => $netSalary,
            ]);
        }

        return redirect()->route('finance.payroll-periods.index')
            ->with('success', 'Periode gaji berhasil dibuat.');
    }

    public function edit(PayrollPeriod $payrollPeriod)
    {
        $payrollPeriod->load(['payrollDetails.karyawan.user']);
        $accounts = Account::pluck('name', 'id');
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return view('finance.payroll-periods.edit', compact('payrollPeriod', 'accounts', 'monthNames'));
    }

    public function update(Request $request, PayrollPeriod $payrollPeriod)
    {
        if ($payrollPeriod->status === 'paid') {
            return redirect()->route('finance.payroll-periods.index')
                ->with('error', 'Periode gaji yang sudah dibayar tidak dapat diubah.');
        }

        $validated = $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000|max:2099',
            'status' => 'required|in:draft,processed,paid',
        ]);

        $payrollPeriod->update($validated);

        return redirect()->route('finance.payroll-periods.index')
            ->with('success', 'Periode gaji berhasil diperbarui.');
    }

    public function destroy(PayrollPeriod $payrollPeriod)
    {
        if ($payrollPeriod->status === 'paid') {
            return redirect()->route('finance.payroll-periods.index')
                ->with('error', 'Periode gaji yang sudah dibayar tidak dapat dihapus.');
        }

        $payrollPeriod->delete();

        return redirect()->route('finance.payroll-periods.index')
            ->with('success', 'Periode gaji berhasil dihapus.');
    }

    public function process(Request $request, PayrollPeriod $payrollPeriod)
    {
        if ($payrollPeriod->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Hanya periode gaji dengan status draft yang dapat diproses.');
        }

        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
        ]);

        $totalNetSalary = $payrollPeriod->payrollDetails()->sum('net_salary');

        if ($totalNetSalary <= 0) {
            return redirect()->back()
                ->with('error', 'Tidak ada karyawan yang digaji atau total gaji bersih bernilai 0.');
        }

        DB::transaction(function () use ($payrollPeriod, $validated, $totalNetSalary) {
            $payrollPeriod->update(['status' => 'processed']);

            $category = Category::firstOrCreate(
                ['name' => 'Gaji Karyawan'],
                ['type' => 'expense', 'color' => '#ef4444']
            );

            Transaction::create([
                'type' => 'expense',
                'account_id' => $validated['account_id'],
                'category_id' => $category->id,
                'amount' => $totalNetSalary,
                'description' => 'Penggajian Karyawan Periode ' . $payrollPeriod->month . '/' . $payrollPeriod->year,
                'date' => Carbon::today(),
                'ref_payroll_id' => $payrollPeriod->id,
            ]);
        });

        return redirect()->route('finance.payroll-periods.edit', $payrollPeriod)
            ->with('success', 'Penggajian periode ' . $payrollPeriod->month . '/' . $payrollPeriod->year . ' berhasil diproses.');
    }

    public function pay(PayrollPeriod $payrollPeriod)
    {
        if ($payrollPeriod->status !== 'processed') {
            return redirect()->back()
                ->with('error', 'Hanya periode gaji dengan status processed yang dapat dibayarkan.');
        }

        DB::transaction(function () use ($payrollPeriod) {
            $payrollPeriod->update(['status' => 'paid']);
            $payrollPeriod->payrollDetails()->update(['paid_at' => Carbon::now()]);
        });

        return redirect()->route('finance.payroll-periods.edit', $payrollPeriod)
            ->with('success', 'Gaji periode ' . $payrollPeriod->month . '/' . $payrollPeriod->year . ' telah dibayarkan.');
    }

    public function updatePayrollDetail(Request $request, PayrollDetail $payrollDetail)
    {
        $period = $payrollDetail->payrollPeriod;

        if ($period->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Hanya dapat mengubah rincian gaji pada periode draft.');
        }

        $validated = $request->validate([
            'bonus' => 'required|numeric|min:0',
        ]);

        // Recalculate net_salary
        $netSalary = $payrollDetail->base_salary + $payrollDetail->total_allowance - $payrollDetail->total_deduction + $validated['bonus'];
        if ($netSalary < 0) $netSalary = 0;

        $payrollDetail->update([
            'bonus' => $validated['bonus'],
            'net_salary' => $netSalary,
        ]);

        return redirect()->route('finance.payroll-periods.edit', $period)
            ->with('success', 'Rincian gaji berhasil diperbarui.');
    }

    public function destroyPayrollDetail(PayrollDetail $payrollDetail)
    {
        $period = $payrollDetail->payrollPeriod;

        if ($period->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Hanya dapat menghapus rincian gaji pada periode draft.');
        }

        $payrollDetail->delete();

        return redirect()->route('finance.payroll-periods.edit', $period)
            ->with('success', 'Rincian gaji berhasil dihapus.');
    }
}
