<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Sensei;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $query = Kelas::with(['sensei.user']);
        $user = auth()->user();

        if ($user->isSensei() && $user->sensei) {
            $query->where('sensei_id', $user->sensei->id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhere('tingkat', 'like', "%{$search}%");
            });
        }

        $kelas = $query->latest()->paginate(15)->withQueryString();

        return view('kelas.index', compact('kelas'));
    }

    public function create()
    {
        $senseiList = Sensei::with('user')->get();
        return view('kelas.create', compact('senseiList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'tingkat' => 'required|string|max:255',
            'sensei_id' => 'nullable|exists:sensei,id',
            'kapasitas' => 'required|integer|min:1',
        ]);

        Kelas::create($validated);

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kelas)
    {
        $senseiList = Sensei::with('user')->get();
        return view('kelas.edit', compact('kelas', 'senseiList'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'tingkat' => 'required|string|max:255',
            'sensei_id' => 'nullable|exists:sensei,id',
            'kapasitas' => 'required|integer|min:1',
        ]);

        $kelas->update($validated);

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil diupdate.');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }

    public function show($id)
    {
        $kelas = Kelas::with(['siswa.user', 'sensei.user', 'jadwal'])->findOrFail($id);
        $user = auth()->user();

        if ($user->isSensei() && $user->sensei && $kelas->sensei_id !== $user->sensei->id) {
            abort(403, 'Unauthorized action.');
        }

        $today = \Carbon\Carbon::today();
        $attendanceToday = \App\Models\Absensi::where('tanggal', $today)
            ->whereIn('user_id', $kelas->siswa->pluck('user_id'))
            ->get()
            ->keyBy('user_id');

        return view('kelas.show', compact('kelas', 'attendanceToday'));
    }

    public function overrideAbsensi(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);
        $user = auth()->user();
        if ($user->isSensei() && $user->sensei && $kelas->sensei_id !== $user->sensei->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:hadir,terlambat,alpha,izin,sakit',
            'tanggal' => 'nullable|date',
        ]);

        $tanggal = $request->input('tanggal', \Carbon\Carbon::today()->format('Y-m-d'));

        \App\Models\Absensi::updateOrCreate(
            ['user_id' => $request->user_id, 'tanggal' => $tanggal],
            [
                'status' => $request->status,
                'jam_masuk' => $request->status === 'hadir' || $request->status === 'terlambat' ? now()->format('H:i:s') : null,
                'is_approved' => true,
                'catatan' => 'Dikoreksi oleh Instruktur ' . $user->name,
            ]
         );

         return back()->with('success', 'Kehadiran siswa berhasil diperbarui.');
    }

    public function markStudentAbsent(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);
        $user = auth()->user();
        if ($user->isSensei() && $user->sensei && $kelas->sensei_id !== $user->sensei->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:izin,sakit',
            'catatan' => 'required|string|max:500',
            'tanggal' => 'nullable|date',
        ]);

        $tanggal = $request->input('tanggal', \Carbon\Carbon::today()->format('Y-m-d'));

        \App\Models\Absensi::updateOrCreate(
            ['user_id' => $request->user_id, 'tanggal' => $tanggal],
            [
                'status' => $request->status,
                'catatan' => $request->catatan,
                'is_approved' => true,
            ]
        );

        return back()->with('success', 'Siswa berhasil ditandai ' . $request->status . '.');
    }

    public function updateProgressNilai(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'progress_pelatihan' => 'required|integer|min:0|max:100',
            'nilai_pelatihan' => 'nullable|string|max:10',
        ]);

        $siswa = \App\Models\Siswa::findOrFail($request->siswa_id);
        $kelas = $siswa->kelas;
        $user = auth()->user();
        if ($user->isSensei() && $user->sensei && $kelas && $kelas->sensei_id !== $user->sensei->id) {
            abort(403, 'Unauthorized action.');
        }

        $siswa->update([
            'progress_pelatihan' => $request->progress_pelatihan,
            'nilai_pelatihan' => $request->nilai_pelatihan,
        ]);

        return back()->with('success', 'Progress dan nilai pelatihan berhasil diperbarui.');
    }
}
