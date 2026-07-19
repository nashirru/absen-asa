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
            'ttd_nama' => 'Manajemen LPK Asa',
            'ttd_digital' => '',
            'slip_logo' => '',
            'slip_subtitle' => 'Sistem Penggajian & Arus Kas Digital',
        ];

        foreach ($defaults as $key => $default) {
            if (!isset($settings[$key]) || $settings[$key] === '') {
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
            'ttd_nama' => 'required|string|max:255',
            'ttd_digital' => 'nullable|image|max:2048',
            'ttd_image_base64' => 'nullable|string',
            'slip_logo' => 'nullable|image|max:2048',
            'slip_subtitle' => 'required|string|max:255',
        ]);

        if ($request->filled('ttd_image_base64')) {
            $base64Image = $request->input('ttd_image_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $data = substr($base64Image, strpos($base64Image, ',') + 1);
                $data = base64_decode($data);
                if ($data !== false) {
                    $existingTtd = Setting::get('ttd_digital');
                    if ($existingTtd && file_exists(public_path('uploads/ttd/' . $existingTtd))) {
                        unlink(public_path('uploads/ttd/' . $existingTtd));
                    }
                    $filename = 'ttd_' . time() . '.png';
                    
                    if (!file_exists(public_path('uploads/ttd'))) {
                        mkdir(public_path('uploads/ttd'), 0755, true);
                    }
                    
                    file_put_contents(public_path('uploads/ttd/' . $filename), $data);
                    Setting::set('ttd_digital', $filename);
                }
            }
        } elseif ($request->hasFile('ttd_digital')) {
            $existingTtd = Setting::get('ttd_digital');
            if ($existingTtd && file_exists(public_path('uploads/ttd/' . $existingTtd))) {
                unlink(public_path('uploads/ttd/' . $existingTtd));
            }
            $file = $request->file('ttd_digital');
            $filename = 'ttd_' . time() . '_' . $file->getClientOriginalName();
            
            if (!file_exists(public_path('uploads/ttd'))) {
                mkdir(public_path('uploads/ttd'), 0755, true);
            }
            
            $file->move(public_path('uploads/ttd'), $filename);
            Setting::set('ttd_digital', $filename);
        }

        if ($request->hasFile('slip_logo')) {
            $existingLogo = Setting::get('slip_logo');
            if ($existingLogo && file_exists(public_path('uploads/logo/' . $existingLogo))) {
                unlink(public_path('uploads/logo/' . $existingLogo));
            }
            $file = $request->file('slip_logo');
            $filename = 'logo_' . time() . '_' . $file->getClientOriginalName();
            
            if (!file_exists(public_path('uploads/logo'))) {
                mkdir(public_path('uploads/logo'), 0755, true);
            }
            
            $file->move(public_path('uploads/logo'), $filename);
            Setting::set('slip_logo', $filename);
        }

        foreach ($validated as $key => $value) {
            if ($key === 'hari_libur_mingguan') {
                $value = implode(',', $value ?? []);
            }
            if ($key === 'ttd_digital' || $key === 'ttd_image_base64' || $key === 'slip_logo') {
                continue;
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
