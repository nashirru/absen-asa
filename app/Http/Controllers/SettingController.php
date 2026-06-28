<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        $defaults = [
            'app_name' => 'LPK Asa Hikari Mulya',
            'office_lat' => '-7.2575',
            'office_lng' => '112.7521',
            'geofence_radius' => '100',
            'max_accuracy' => '50',
            'jam_masuk' => '08:00',
            'jam_keluar' => '17:00',
            'batas_terlambat' => '08:15',
            'tanggal_mulai_absensi' => now()->format('Y-m-d'),
            'hari_libur_mingguan' => '0', // Default: 0 = Minggu
        ];

        foreach ($defaults as $key => $default) {
            if (!isset($settings[$key]) || empty($settings[$key])) {
                $settings[$key] = $default;
            }
        }

        // Convert string "0,6" to array for the view
        $settings['hari_libur_mingguan_arr'] = $settings['hari_libur_mingguan'] !== '' 
            ? explode(',', $settings['hari_libur_mingguan']) 
            : [];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'office_lat' => 'required|numeric',
            'office_lng' => 'required|numeric',
            'geofence_radius' => 'required|numeric|min:10',
            'max_accuracy' => 'required|numeric|min:10',
            'jam_masuk' => 'required|date_format:H:i',
            'jam_keluar' => 'required|date_format:H:i',
            'batas_terlambat' => 'required|date_format:H:i',
            'tanggal_mulai_absensi' => 'required|date',
            'hari_libur_mingguan' => 'nullable|array',
            'hari_libur_mingguan.*' => 'integer|min:0|max:6',
        ]);

        foreach ($validated as $key => $value) {
            if ($key === 'hari_libur_mingguan') {
                $value = implode(',', $value ?? []);
            }
            Setting::set($key, $value);
        }
        
        // Handle case where hari_libur_mingguan is unchecked completely
        if (!$request->has('hari_libur_mingguan')) {
            Setting::set('hari_libur_mingguan', '');
        }

        return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
