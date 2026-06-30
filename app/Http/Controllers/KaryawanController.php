<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\SalaryComponent;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class KaryawanController extends Controller
{
    protected function isAjax(Request $request): bool
    {
        return $request->ajax() || $request->wantsJson();
    }

    protected function jsonOrRedirect($request, $redirectRoute, $message, $data = [])
    {
        if ($this->isAjax($request)) {
            return response()->json(array_merge([
                'message' => $message,
                'success' => true,
            ], $data));
        }
        return redirect()->route($redirectRoute)->with('success', $message);
    }

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

        if ($this->isAjax($request)) {
            $html = view('karyawan.partials.table', compact('karyawan'))->render();
            return response()->json(['table_html' => $html, 'success' => true]);
        }

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
            'base_salary' => 'nullable|numeric|min:0',
            'join_date' => 'nullable|date',
            'status' => 'nullable|in:active,inactive',
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
            'base_salary' => $validated['base_salary'] ?? 0,
            'join_date' => $validated['join_date'] ?? now(),
            'status' => $validated['status'] ?? 'active',
        ]);

        // Re-render table for AJAX response
        if ($this->isAjax($request)) {
            $query = Karyawan::with('user')->latest()->paginate(15);
            $tableHtml = view('karyawan.partials.table', compact('karyawan'))->with('karyawan', $query)->render();
            return response()->json([
                'message' => 'Karyawan berhasil ditambahkan!',
                'success' => true,
                'table_html' => $tableHtml,
                'table_target' => 'table-container',
            ]);
        }

        return redirect()->route('karyawan.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function edit(Karyawan $karyawan)
    {
        $karyawan->load(['user', 'salaryComponents']);
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
            'base_salary' => 'nullable|numeric|min:0',
            'join_date' => 'nullable|date',
            'status' => 'nullable|in:active,inactive',
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
            'base_salary' => $validated['base_salary'] ?? 0,
            'join_date' => $validated['join_date'],
            'status' => $validated['status'] ?? 'active',
        ]);

        if ($this->isAjax($request)) {
            return response()->json([
                'message' => 'Karyawan berhasil diupdate!',
                'success' => true,
            ]);
        }

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

    /**
     * Store salary component inline from karyawan edit page.
     */
    public function storeSalaryComponent(Request $request, Karyawan $karyawan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:allowance,deduction',
            'amount' => 'required|numeric|min:0',
        ]);

        $karyawan->salaryComponents()->create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
        ]);

        if ($this->isAjax($request)) {
            return response()->json([
                'message' => 'Komponen gaji berhasil ditambahkan!',
                'success' => true,
            ]);
        }

        return redirect()->route('karyawan.edit', $karyawan)
            ->with('success', 'Komponen gaji berhasil ditambahkan.');
    }

    /**
     * Update salary component inline from karyawan edit page.
     */
    public function updateSalaryComponent(Request $request, SalaryComponent $salaryComponent)
    {
        $karyawan = $salaryComponent->karyawan;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:allowance,deduction',
            'amount' => 'required|numeric|min:0',
        ]);

        $salaryComponent->update($validated);

        return redirect()->route('karyawan.edit', $karyawan)
            ->with('success', 'Komponen gaji berhasil diperbarui.');
    }

    /**
     * Delete salary component inline from karyawan edit page.
     */
    public function destroySalaryComponent(Request $request, SalaryComponent $salaryComponent)
    {
        $karyawan = $salaryComponent->karyawan;
        $salaryComponent->delete();

        if ($this->isAjax($request)) {
            return response()->json([
                'message' => 'Komponen gaji berhasil dihapus!',
                'success' => true,
            ]);
        }

        return redirect()->route('karyawan.edit', $karyawan)
            ->with('success', 'Komponen gaji berhasil dihapus.');
    }
}
