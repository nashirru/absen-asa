<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Admin cannot see super_admin users
        if (auth()->user()->isAdmin()) {
            $query->where('role', '!=', 'super_admin');
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = auth()->user()->isSuperAdmin()
            ? ['super_admin', 'admin', 'siswa', 'karyawan', 'sensei']
            : ['admin', 'siswa', 'karyawan', 'sensei'];

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:super_admin,admin,siswa,karyawan,sensei',
            'phone' => 'nullable|string|max:20',
            'foto' => 'nullable|image|max:2048',
            'status_aktif' => 'nullable',
        ]);

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $filename = time() . '_' . $foto->getClientOriginalName();
            $foto->move(public_path('uploads/foto'), $filename);
            $validated['foto'] = $filename;
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['status_aktif'] = $request->has('status_aktif');

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $roles = auth()->user()->isSuperAdmin()
            ? ['super_admin', 'admin', 'siswa', 'karyawan', 'sensei']
            : ['admin', 'siswa', 'karyawan', 'sensei'];

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        // Admin cannot edit super_admin
        if (auth()->user()->isAdmin() && $user->isSuperAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:6',
            'role' => 'required|in:super_admin,admin,siswa,karyawan,sensei',
            'phone' => 'nullable|string|max:20',
            'foto' => 'nullable|image|max:2048',
            'status_aktif' => 'nullable',
        ]);

        if ($request->hasFile('foto')) {
            if ($user->foto && file_exists(public_path('uploads/foto/' . $user->foto))) {
                unlink(public_path('uploads/foto/' . $user->foto));
            }
            $foto = $request->file('foto');
            $filename = time() . '_' . $foto->getClientOriginalName();
            $foto->move(public_path('uploads/foto'), $filename);
            $validated['foto'] = $filename;
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['status_aktif'] = $request->has('status_aktif');

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        // Admin cannot delete super_admin
        if (auth()->user()->isAdmin() && $user->isSuperAdmin()) {
            abort(403);
        }

        // Cannot delete self
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        if ($user->foto && file_exists(public_path('uploads/foto/' . $user->foto))) {
            unlink(public_path('uploads/foto/' . $user->foto));
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    public function resetDevice(User $user)
    {
        if (auth()->user()->isAdmin() && $user->isSuperAdmin()) {
            abort(403);
        }

        $user->update(['device_uuid' => null]);

        return back()->with('success', 'Kunci perangkat berhasil di-reset.');
    }
}
