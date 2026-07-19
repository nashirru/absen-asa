<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::with(['user', 'kelas']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $siswa = $query->latest()->paginate(15)->withQueryString();
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('siswa.index', compact('siswa', 'kelasList'));
    }

    public function create()
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        return view('siswa.create', compact('kelasList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'phone' => 'nullable|string|max:20',
            'foto' => 'nullable|image|max:2048',
            'nis' => 'required|string|unique:siswa,nis',
            'gender' => 'required|in:L,P',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'nullable|string',
            'kelas_id' => 'nullable|exists:kelas,id',
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
            'role' => 'siswa',
            'phone' => $validated['phone'] ?? null,
            'foto' => $fotoFilename,
            'status_aktif' => true,
        ]);

        Siswa::create([
            'user_id' => $user->id,
            'nis' => $validated['nis'],
            'gender' => $validated['gender'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'alamat' => $validated['alamat'] ?? null,
            'kelas_id' => $validated['kelas_id'] ?? null,
        ]);

        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function edit(Siswa $siswa)
    {
        $siswa->load('user');
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        return view('siswa.edit', compact('siswa', 'kelasList'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($siswa->user_id)],
            'password' => 'nullable|min:6',
            'phone' => 'nullable|string|max:20',
            'foto' => 'nullable|image|max:2048',
            'nis' => ['required', 'string', Rule::unique('siswa')->ignore($siswa->id)],
            'gender' => 'required|in:L,P',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'nullable|string',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        $user = $siswa->user;
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

        $siswa->update([
            'nis' => $validated['nis'],
            'gender' => $validated['gender'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'alamat' => $validated['alamat'] ?? null,
            'kelas_id' => $validated['kelas_id'] ?? null,
        ]);

        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil diupdate.');
    }

    public function destroy(Siswa $siswa)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat menghapus siswa.');
        }

        $user = $siswa->user;
        if ($user->foto && file_exists(public_path('uploads/foto/' . $user->foto))) {
            unlink(public_path('uploads/foto/' . $user->foto));
        }
        $siswa->delete();
        $user->delete();

        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil dihapus.');
    }
}
