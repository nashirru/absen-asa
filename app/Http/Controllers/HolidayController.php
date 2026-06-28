<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        $year  = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $holidays = Holiday::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->orderBy('tanggal')
            ->get();

        $allHolidays = Holiday::orderBy('tanggal', 'desc')->paginate(20)->withQueryString();

        return view('holidays.index', compact('holidays', 'allHolidays', 'year', 'month'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal'     => 'required|date|unique:holidays,tanggal',
            'keterangan'  => 'required|string|max:255',
        ]);

        Holiday::create($validated);

        return redirect()->route('holidays.index')->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();
        return redirect()->route('holidays.index')->with('success', 'Hari libur berhasil dihapus.');
    }
}
