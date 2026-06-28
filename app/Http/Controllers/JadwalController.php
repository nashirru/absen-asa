<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Sensei;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $query = Jadwal::with(['kelas', 'sensei.user']);
        $user = auth()->user();

        if ($user->isSensei() && $user->sensei) {
            $query->where('sensei_id', $user->sensei->id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('mata_pelajaran', 'like', "%{$search}%")
                  ->orWhere('hari', 'like', "%{$search}%");
            });
        }

        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        $jadwal = $query->orderBy('hari')->orderBy('jam_mulai')->paginate(15)->withQueryString();

        return view('jadwal.index', compact('jadwal'));
    }

    public function create()
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $user = auth()->user();
        if ($user->isSensei() && $user->sensei) {
            $senseiList = Sensei::where('id', $user->sensei->id)->with('user')->get();
        } else {
            $senseiList = Sensei::with('user')->get();
        }
        return view('jadwal.create', compact('kelasList', 'senseiList'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $rules = [
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'mata_pelajaran' => 'required|string|max:255',
            'kelas_id' => 'required|exists:kelas,id',
            'modul_link' => 'nullable|url|max:255',
            'modul_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];

        if ($user->isSensei() && $user->sensei) {
            // Already scoped to current sensei
        } else {
            $rules['sensei_id'] = 'required|exists:sensei,id';
        }

        $validated = $request->validate($rules);

        if ($user->isSensei() && $user->sensei) {
            $validated['sensei_id'] = $user->sensei->id;
        }

        if ($request->hasFile('modul_file')) {
            $file = $request->file('modul_file');
            $filename = 'modul_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            if (!file_exists(public_path('uploads/modul'))) {
                mkdir(public_path('uploads/modul'), 0755, true);
            }
            $file->move(public_path('uploads/modul'), $filename);
            $validated['modul_file'] = $filename;
        }

        Jadwal::create($validated);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(Jadwal $jadwal)
    {
        $user = auth()->user();
        if ($user->isSensei() && $user->sensei && $jadwal->sensei_id !== $user->sensei->id) {
            abort(403, 'Unauthorized action.');
        }

        $kelasList = Kelas::orderBy('nama_kelas')->get();
        if ($user->isSensei() && $user->sensei) {
            $senseiList = Sensei::where('id', $user->sensei->id)->with('user')->get();
        } else {
            $senseiList = Sensei::with('user')->get();
        }
        return view('jadwal.edit', compact('jadwal', 'kelasList', 'senseiList'));
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $user = auth()->user();
        if ($user->isSensei() && $user->sensei && $jadwal->sensei_id !== $user->sensei->id) {
            abort(403, 'Unauthorized action.');
        }

        $rules = [
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'mata_pelajaran' => 'required|string|max:255',
            'kelas_id' => 'required|exists:kelas,id',
            'modul_link' => 'nullable|url|max:255',
            'modul_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];

        if ($user->isSensei() && $user->sensei) {
            // Scoped
        } else {
            $rules['sensei_id'] = 'required|exists:sensei,id';
        }

        $validated = $request->validate($rules);

        if ($user->isSensei() && $user->sensei) {
            $validated['sensei_id'] = $user->sensei->id;
        }

        if ($request->hasFile('modul_file')) {
            if ($jadwal->modul_file && file_exists(public_path('uploads/modul/' . $jadwal->modul_file))) {
                unlink(public_path('uploads/modul/' . $jadwal->modul_file));
            }
            $file = $request->file('modul_file');
            $filename = 'modul_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            if (!file_exists(public_path('uploads/modul'))) {
                mkdir(public_path('uploads/modul'), 0755, true);
            }
            $file->move(public_path('uploads/modul'), $filename);
            $validated['modul_file'] = $filename;
        }

        $jadwal->update($validated);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil diupdate.');
    }

    public function editModul(Jadwal $jadwal)
    {
        $user = auth()->user();
        if ($user->isSensei() && $user->sensei && $jadwal->sensei_id !== $user->sensei->id) {
            abort(403, 'Unauthorized action.');
        }
        return view('jadwal.edit-modul', compact('jadwal'));
    }

    public function updateModul(Request $request, Jadwal $jadwal)
    {
        $user = auth()->user();
        if ($user->isSensei() && $user->sensei && $jadwal->sensei_id !== $user->sensei->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'modul_link' => 'nullable|url|max:255',
            'modul_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        if ($request->hasFile('modul_file')) {
            if ($jadwal->modul_file && file_exists(public_path('uploads/modul/' . $jadwal->modul_file))) {
                unlink(public_path('uploads/modul/' . $jadwal->modul_file));
            }
            $file = $request->file('modul_file');
            $filename = 'modul_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            if (!file_exists(public_path('uploads/modul'))) {
                mkdir(public_path('uploads/modul'), 0755, true);
            }
            $file->move(public_path('uploads/modul'), $filename);
            $validated['modul_file'] = $filename;
        }

        $jadwal->update($validated);

        return redirect()->route('jadwal.my-schedule')->with('success', 'Modul jadwal berhasil diupdate.');
    }

    public function destroy(Jadwal $jadwal)
    {
        $user = auth()->user();
        if ($user->isSensei() && $user->sensei && $jadwal->sensei_id !== $user->sensei->id) {
            abort(403, 'Unauthorized action.');
        }

        $jadwal->delete();
        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }

    public function mySchedule(Request $request)
    {
        $user = auth()->user();
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        if ($user->isSiswa() && $user->siswa) {
            $jadwal = Jadwal::with(['kelas', 'sensei.user'])
                ->where('kelas_id', $user->siswa->kelas_id)
                ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu')")
                ->orderBy('jam_mulai')
                ->get()
                ->groupBy('hari');
        } elseif ($user->isSensei() && $user->sensei) {
            $jadwal = Jadwal::with(['kelas', 'sensei.user'])
                ->where('sensei_id', $user->sensei->id)
                ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu')")
                ->orderBy('jam_mulai')
                ->get()
                ->groupBy('hari');
        } else {
            $jadwal = collect();
        }

        return view('jadwal.my-schedule', compact('jadwal', 'hariList'));
    }
}
