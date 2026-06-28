<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $karyawan = $query->latest()->paginate(15)->withQueryString();

        return view('karyawan.index', compact('karyawan'));
    }

    public function create()
    {
        return view('karyawan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'phone' => 'nullable|string|max:20',
            'foto' => 'nullable|image|max:2048',
            'nik' => 'required|string|unique:karyawan,nik',
            'jabatan' => 'required|string|max:255',
            'divisi' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
        ]);

        $fotoFilename = null;
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $fotoFilename = time() . '_' . $foto->getClientOriginalName();
            $foto->move(public_path('uploads/foto'), $fotoFilename);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'karyawan',
            'phone' => $validated['phone'] ?? null,
            'foto' => $fotoFilename,
            'status_aktif' => true,
        ]);

        Karyawan::create([
            'user_id' => $user->id,
            'nik' => $validated['nik'],
            'jabatan' => $validated['jabatan'],
            'divisi' => $validated['divisi'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
        ]);

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function edit(Karyawan $karyawan)
    {
        $karyawan->load('user');
        return view('karyawan.edit', compact('karyawan'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($karyawan->user_id)],
            'password' => 'nullable|min:6',
            'phone' => 'nullable|string|max:20',
            'foto' => 'nullable|image|max:2048',
            'nik' => ['required', 'string', Rule::unique('karyawan')->ignore($karyawan->id)],
            'jabatan' => 'required|string|max:255',
            'divisi' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
        ]);

        $user = $karyawan->user;
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ];

        if ($request->hasFile('foto')) {
            if ($user->foto && file_exists(public_path('uploads/foto/' . $user->foto))) {
                unlink(public_path('uploads/foto/' . $user->foto));
            }
            $foto = $request->file('foto');
            $fotoFilename = time() . '_' . $foto->getClientOriginalName();
            $foto->move(public_path('uploads/foto'), $fotoFilename);
            $userData['foto'] = $fotoFilename;
        }

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        $karyawan->update([
            'nik' => $validated['nik'],
            'jabatan' => $validated['jabatan'],
            'divisi' => $validated['divisi'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
        ]);

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil diupdate.');
    }

    public function destroy(Karyawan $karyawan)
    {
        $user = $karyawan->user;
        if ($user->foto && file_exists(public_path('uploads/foto/' . $user->foto))) {
            unlink(public_path('uploads/foto/' . $user->foto));
        }
        $karyawan->delete();
        $user->delete();

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil dihapus.');
    }
}
