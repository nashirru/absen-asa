<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-admin-border bg-admin-canvas/30">
                <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Karyawan</th>
                <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Jabatan</th>
                <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate text-right">Gaji Pokok</th>
                <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate text-right">Total Tunjangan</th>
                <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate text-right">Total Potongan</th>
                <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate">Status</th>
                <th class="py-4 px-6 text-xs font-semibold uppercase tracking-wider text-admin-slate text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y border-admin-border">
            @forelse($karyawan as $k)
                <tr class="hover:bg-admin-canvas/30 transition-colors">
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <img src="{{ $k->user->foto_url }}" class="w-9 h-9 rounded-admin-full object-cover border border-admin-border" alt="">
                            <span class="text-sm font-semibold text-admin-ink">{{ $k->user->name }}</span>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-sm text-admin-ink">{{ $k->jabatan }}</td>
                    <td class="py-4 px-6 text-sm text-right font-mono text-admin-ink">Rp {{ number_format($k->base_salary ?? 0, 0, ',', '.') }}</td>
                    @php
                        $totalAllowance = $k->salaryComponents->where('type', 'allowance')->sum('amount');
                        $totalDeduction = $k->salaryComponents->where('type', 'deduction')->sum('amount');
                    @endphp
                    <td class="py-4 px-6 text-sm text-right font-mono {{ $totalAllowance > 0 ? 'text-admin-success' : 'text-admin-mist' }}">
                        {{ $totalAllowance > 0 ? 'Rp ' . number_format($totalAllowance, 0, ',', '.') : '-' }}
                    </td>
                    <td class="py-4 px-6 text-sm text-right font-mono {{ $totalDeduction > 0 ? 'text-red-600' : 'text-admin-mist' }}">
                        {{ $totalDeduction > 0 ? 'Rp ' . number_format($totalDeduction, 0, ',', '.') : '-' }}
                    </td>
                    <td class="py-4 px-6 text-sm">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-admin-full text-xs font-medium {{ ($k->status ?? 'active') == 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ($k->status ?? 'active') == 'active' ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('karyawan.edit', $k) }}" class="p-2 rounded-admin-md border border-admin-border text-admin-indigo hover:bg-admin-indigo-tint hover:border-admin-indigo/20 transition-all duration-150" title="Edit">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </a>
                            @if(auth()->user()->isSuperAdmin())
                            <form action="{{ route('karyawan.destroy', $k) }}" method="POST" class="inline" onsubmit="confirmDelete(event, 'Data karyawan akan dihapus permanen.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 rounded-admin-md border border-admin-border text-admin-danger hover:bg-admin-danger-tint hover:border-admin-danger/20 transition-all duration-150" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="py-12 text-center text-admin-mist">
                        <i data-lucide="users" class="w-10 h-10 mx-auto mb-2 opacity-40"></i>
                        <p class="text-sm">Belum ada data karyawan</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($karyawan->hasPages())
    <div class="px-6 py-4 border-t border-admin-border bg-admin-canvas/10">
        {{ $karyawan->links() }}
    </div>
@endif