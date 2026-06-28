<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\SalaryComponent;
use Illuminate\Http\Request;

class SalaryComponentController extends Controller
{
    public function index(Request $request)
    {
        $query = SalaryComponent::with('karyawan.user');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        $components = $query->latest()->paginate(10);
        $karyawans = Karyawan::with('user')->get()->mapWithKeys(fn($k) => [$k->id => $k->user?->name ?? 'PAY-' . $k->nik]);

        return view('finance.salary-components.index', compact('components', 'karyawans'));
    }

    public function create()
    {
        $karyawans = Karyawan::with('user')->get()->mapWithKeys(fn($k) => [$k->id => $k->user?->name ?? 'PAY-' . $k->nik]);
        return view('finance.salary-components.create', compact('karyawans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:allowance,deduction',
            'amount' => 'required|numeric|min:0',
        ]);

        SalaryComponent::create($validated);

        return redirect()->route('finance.salary-components.index')
            ->with('success', 'Komponen gaji berhasil dibuat.');
    }

    public function edit(SalaryComponent $salaryComponent)
    {
        $karyawans = Karyawan::with('user')->get()->mapWithKeys(fn($k) => [$k->id => $k->user?->name ?? 'PAY-' . $k->nik]);
        return view('finance.salary-components.edit', compact('salaryComponent', 'karyawans'));
    }

    public function update(Request $request, SalaryComponent $salaryComponent)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:allowance,deduction',
            'amount' => 'required|numeric|min:0',
        ]);

        $salaryComponent->update($validated);

        return redirect()->route('finance.salary-components.index')
            ->with('success', 'Komponen gaji berhasil diperbarui.');
    }

    public function destroy(SalaryComponent $salaryComponent)
    {
        $salaryComponent->delete();

        return redirect()->route('finance.salary-components.index')
            ->with('success', 'Komponen gaji berhasil dihapus.');
    }
}
