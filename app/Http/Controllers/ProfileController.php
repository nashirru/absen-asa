<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'foto' => 'nullable',
        ]);

        // Handle base64 selfie photo from check-in camera
        if ($request->filled('foto') && str_starts_with($request->foto, 'data:image')) {
            $imageData = explode(',', $request->foto)[1] ?? $request->foto;
            $imageData = base64_decode($imageData);
            $filename = 'profile_' . $user->id . '_' . time() . '.jpg';
            $path = public_path('uploads/foto/' . $filename);

            if (!file_exists(public_path('uploads/foto'))) {
                mkdir(public_path('uploads/foto'), 0755, true);
            }

            // Hapus foto lama
            if ($user->foto && file_exists(public_path('uploads/foto/' . $user->foto))) {
                unlink(public_path('uploads/foto/' . $user->foto));
            }

            file_put_contents($path, $imageData);
            $validated['foto'] = $filename;
        }
        // Handle uploaded file
        elseif ($request->hasFile('foto')) {
            if ($user->foto && file_exists(public_path('uploads/foto/' . $user->foto))) {
                unlink(public_path('uploads/foto/' . $user->foto));
            }
            $foto = $request->file('foto');
            $filename = time() . '_' . $foto->getClientOriginalName();
            $foto->move(public_path('uploads/foto'), $filename);
            $validated['foto'] = $filename;
        }

        // Only update fields that are provided
        $updateData = array_filter($validated, fn($v) => $v !== null);
        if (!empty($updateData)) {
            $user->update($updateData);
        }

        return redirect()->route('profile.index')->with('success', 'Profil berhasil diupdate.');
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama salah.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.index')->with('success', 'Password berhasil diubah.');
    }
}
