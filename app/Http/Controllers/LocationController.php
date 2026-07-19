<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::with('shifts')->latest()->paginate(20);
        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        return view('locations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|numeric|min:10|max:1000',
            'is_active' => 'boolean',
            'allowed_roles' => 'nullable|array',
            'allowed_roles.*' => 'in:siswa,karyawan,sensei',
            
            'shift1_name' => 'nullable|string|max:255',
            'shift1_jam_masuk' => 'nullable|required_with:shift1_name',
            'shift1_jam_keluar' => 'exclude_if:shift1_is_24_hours,1|nullable|required_with:shift1_name',
            'shift1_batas_terlambat' => 'nullable|required_with:shift1_name',
            'shift1_is_24_hours' => 'nullable|boolean',
            
            'shift2_name' => 'nullable|string|max:255',
            'shift2_jam_masuk' => 'nullable|required_with:shift2_name',
            'shift2_jam_keluar' => 'exclude_if:shift2_is_24_hours,1|nullable|required_with:shift2_name',
            'shift2_batas_terlambat' => 'nullable|required_with:shift2_name',
            'shift2_is_24_hours' => 'nullable|boolean',
            
            'shift3_name' => 'nullable|string|max:255',
            'shift3_jam_masuk' => 'nullable|required_with:shift3_name',
            'shift3_jam_keluar' => 'exclude_if:shift3_is_24_hours,1|nullable|required_with:shift3_name',
            'shift3_batas_terlambat' => 'nullable|required_with:shift3_name',
            'shift3_is_24_hours' => 'nullable|boolean',
        ]);

        $location = Location::create([
            'name' => $request->name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius,
            'is_active' => $request->boolean('is_active', true),
            'allowed_roles' => $request->allowed_roles ?: null,
        ]);

        if ($request->filled('shift1_name')) {
            $location->shifts()->create([
                'nama_shift' => $request->shift1_name,
                'jam_masuk' => $request->shift1_jam_masuk,
                'jam_keluar' => $request->shift1_jam_keluar,
                'batas_terlambat' => $request->shift1_batas_terlambat,
                'is_24_hours' => $request->boolean('shift1_is_24_hours'),
            ]);
        }

        if ($request->filled('shift2_name')) {
            $location->shifts()->create([
                'nama_shift' => $request->shift2_name,
                'jam_masuk' => $request->shift2_jam_masuk,
                'jam_keluar' => $request->shift2_jam_keluar,
                'batas_terlambat' => $request->shift2_batas_terlambat,
                'is_24_hours' => $request->boolean('shift2_is_24_hours'),
            ]);
        }

        if ($request->filled('shift3_name')) {
            $location->shifts()->create([
                'nama_shift' => $request->shift3_name,
                'jam_masuk' => $request->shift3_jam_masuk,
                'jam_keluar' => $request->shift3_jam_keluar,
                'batas_terlambat' => $request->shift3_batas_terlambat,
                'is_24_hours' => $request->boolean('shift3_is_24_hours'),
            ]);
        }

        return redirect()->route('locations.index')
            ->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function edit(Location $location)
    {
        $location->load('shifts');
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|numeric|min:10|max:1000',
            'is_active' => 'boolean',
            'allowed_roles' => 'nullable|array',
            'allowed_roles.*' => 'in:siswa,karyawan,sensei',
            
            'shift1_name' => 'nullable|string|max:255',
            'shift1_jam_masuk' => 'nullable|required_with:shift1_name',
            'shift1_jam_keluar' => 'exclude_if:shift1_is_24_hours,1|nullable|required_with:shift1_name',
            'shift1_batas_terlambat' => 'nullable|required_with:shift1_name',
            'shift1_is_24_hours' => 'nullable|boolean',
            
            'shift2_name' => 'nullable|string|max:255',
            'shift2_jam_masuk' => 'nullable|required_with:shift2_name',
            'shift2_jam_keluar' => 'exclude_if:shift2_is_24_hours,1|nullable|required_with:shift2_name',
            'shift2_batas_terlambat' => 'nullable|required_with:shift2_name',
            'shift2_is_24_hours' => 'nullable|boolean',
            
            'shift3_name' => 'nullable|string|max:255',
            'shift3_jam_masuk' => 'nullable|required_with:shift3_name',
            'shift3_jam_keluar' => 'exclude_if:shift3_is_24_hours,1|nullable|required_with:shift3_name',
            'shift3_batas_terlambat' => 'nullable|required_with:shift3_name',
            'shift3_is_24_hours' => 'nullable|boolean',
        ]);

        $location->update([
            'name' => $request->name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius,
            'is_active' => $request->boolean('is_active', true),
            'allowed_roles' => $request->allowed_roles ?: null,
        ]);

        // Only replace shifts if new shift data was provided (prevents data loss on partial updates)
        if ($request->filled('shift1_name') || $request->filled('shift2_name') || $request->filled('shift3_name')) {
            $location->shifts()->delete();
        }

        if ($request->filled('shift1_name')) {
            $location->shifts()->create([
                'nama_shift' => $request->shift1_name,
                'jam_masuk' => $request->shift1_jam_masuk,
                'jam_keluar' => $request->shift1_jam_keluar,
                'batas_terlambat' => $request->shift1_batas_terlambat,
                'is_24_hours' => $request->boolean('shift1_is_24_hours'),
            ]);
        }

        if ($request->filled('shift2_name')) {
            $location->shifts()->create([
                'nama_shift' => $request->shift2_name,
                'jam_masuk' => $request->shift2_jam_masuk,
                'jam_keluar' => $request->shift2_jam_keluar,
                'batas_terlambat' => $request->shift2_batas_terlambat,
                'is_24_hours' => $request->boolean('shift2_is_24_hours'),
            ]);
        }

        if ($request->filled('shift3_name')) {
            $location->shifts()->create([
                'nama_shift' => $request->shift3_name,
                'jam_masuk' => $request->shift3_jam_masuk,
                'jam_keluar' => $request->shift3_jam_keluar,
                'batas_terlambat' => $request->shift3_batas_terlambat,
                'is_24_hours' => $request->boolean('shift3_is_24_hours'),
            ]);
        }

        return redirect()->route('locations.index')
            ->with('success', 'Lokasi berhasil diupdate.');
    }

    public function destroy(Location $location)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat menghapus lokasi.');
        }

        $location->delete();
        return redirect()->route('locations.index')
            ->with('success', 'Lokasi berhasil dihapus.');
    }

    public function getActiveLocations()
    {
        return response()->json(Location::active()->with('shifts')->get());
    }
}
