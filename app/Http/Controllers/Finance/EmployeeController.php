<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Employee has been merged into Karyawan (attendance).
     * All employee management is now handled via /karyawan routes.
     */

    public function index()
    {
        return redirect()->route('karyawan.index')
            ->with('info', 'Manajemen karyawan telah digabung ke menu Karyawan.');
    }

    public function create()
    {
        return redirect()->route('karyawan.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('karyawan.index');
    }

    public function edit($id)
    {
        return redirect()->route('karyawan.index');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('karyawan.index');
    }

    public function destroy($id)
    {
        return redirect()->route('karyawan.index');
    }
}
