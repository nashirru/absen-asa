<?php

namespace App\Http\Controllers;

use App\Models\Sensei;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SenseiController extends Controller
{
    public function index(Request $request)
    {
        $query = Sensei::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('mata_pelajaran', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $sensei = $query->latest()->paginate(15)->withQueryString();

        return view('sensei.index', compact('sensei'));
    }

    public function create()
    {
        return view('sensei.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'phone' => 'nullable|string|max:20',
            'foto' => 'nullable|image|max:2048',
            'mata_pelajaran' => 'required|string|max:255',
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
            'role' => 'sensei',
            'phone' => $validated['phone'] ?? null,
            'foto' => $fotoFilename,
            'status_aktif' => true,
        ]);

        Sensei::create([
            'user_id' => $user->id,
            'mata_pelajaran' => $validated['mata_pelajaran'],
        ]);

        return redirect()->route('sensei.index')->with('success', 'Sensei berhasil ditambahkan.');
    }

    public function edit(Sensei $sensei)
    {
        $sensei->load('user');
        return view('sensei.edit', compact('sensei'));
    }

    public function update(Request $request, Sensei $sensei)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($sensei->user_id)],
            'password' => 'nullable|min:6',
            'phone' => 'nullable|string|max:20',
            'foto' => 'nullable|image|max:2048',
            'mata_pelajaran' => 'required|string|max:255',
        ]);

        $user = $sensei->user;
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

        $sensei->update([
            'mata_pelajaran' => $validated['mata_pelajaran'],
        ]);

        return redirect()->route('sensei.index')->with('success', 'Sensei berhasil diupdate.');
    }

    public function destroy(Sensei $sensei)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat menghapus sensei.');
        }

        $user = $sensei->user;
        if ($user->foto && file_exists(public_path('uploads/foto/' . $user->foto))) {
            unlink(public_path('uploads/foto/' . $user->foto));
        }
        $sensei->delete();
        $user->delete();

        return redirect()->route('sensei.index')->with('success', 'Sensei berhasil dihapus.');
    }
}
