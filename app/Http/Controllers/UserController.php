<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Sensei;
use App\Models\Siswa;
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

        $user = User::create($validated);

        // Auto-create role-specific record
        $extraMsg = '';
        if ($user->role === 'karyawan') {
            Karyawan::create([
                'user_id' => $user->id,
                'nik' => 'AUTO-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'jabatan' => '-',
                'status' => 'active',
                'join_date' => now(),
            ]);
            $extraMsg = ' Lengkapi data di menu Karyawan.';
        } elseif ($user->role === 'siswa') {
            Siswa::create([
                'user_id' => $user->id,
                'nis' => 'AUTO-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'gender' => 'L',
                'tanggal_lahir' => now(),
            ]);
            $extraMsg = ' Lengkapi data di menu Siswa.';
        } elseif ($user->role === 'sensei') {
            Sensei::create([
                'user_id' => $user->id,
                'mata_pelajaran' => '-',
            ]);
            $extraMsg = ' Lengkapi data di menu Sensei.';
        }

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.' . $extraMsg);
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

        $oldRole = $user->role;
        $user->update($validated);

        // Handle role change: auto-create or remove role-specific records
        $extraMsg = '';
        $newRole = $user->role;

        // Remove old role record if role changed
        if ($oldRole !== $newRole) {
            if ($oldRole === 'karyawan' && $user->karyawan) {
                $user->karyawan->delete();
            } elseif ($oldRole === 'siswa' && $user->siswa) {
                $user->siswa->delete();
            } elseif ($oldRole === 'sensei' && $user->sensei) {
                $user->sensei->delete();
            }
        }

        // Create new role record if needed
        if ($newRole === 'karyawan' && !$user->karyawan) {
            Karyawan::create([
                'user_id' => $user->id,
                'nik' => 'AUTO-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'jabatan' => '-',
                'status' => 'active',
                'join_date' => now(),
            ]);
            $extraMsg = ' Lengkapi data di menu Karyawan.';
        } elseif ($newRole === 'siswa' && !$user->siswa) {
            Siswa::create([
                'user_id' => $user->id,
                'nis' => 'AUTO-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'gender' => 'L',
                'tanggal_lahir' => now(),
            ]);
            $extraMsg = ' Lengkapi data di menu Siswa.';
        } elseif ($newRole === 'sensei' && !$user->sensei) {
            Sensei::create([
                'user_id' => $user->id,
                'mata_pelajaran' => '-',
            ]);
            $extraMsg = ' Lengkapi data di menu Sensei.';
        }

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate.' . $extraMsg);
    }

    public function destroy(User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat menghapus user.');
        }

        // Cannot delete self
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        // Hapus role-specific record terlebih dahulu
        if ($user->role === 'karyawan' && $user->karyawan) {
            $user->karyawan->delete();
        } elseif ($user->role === 'siswa' && $user->siswa) {
            $user->siswa->delete();
        } elseif ($user->role === 'sensei' && $user->sensei) {
            $user->sensei->delete();
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
