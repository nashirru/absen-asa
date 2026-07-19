@extends('layouts.admin')
@section('title', 'Buat Kategori')
@section('header', 'Buat Kategori')
@section('content')
<div class="max-w-xl mx-auto animate-fade-in-up">
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-6">
        <form method="POST" action="{{ route('finance.categories.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="text-xs font-semibold text-admin-slate">Nama Kategori</label>
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="255"
                       class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                @error('name') <p class="text-xs text-admin-danger mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs font-semibold text-admin-slate">Tipe</label>
                <select name="type" id="categoryType" required onchange="updateSubCategoriesLabel()"
                        class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Pemasukan</option>
                    <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                </select>
            </div>
            <div>
                <label id="subCategoriesLabel" class="text-xs font-semibold text-admin-slate">Jenis Pemasukan (Satu per baris)</label>
                <textarea name="sub_categories" id="subCategoriesInput" rows="4" placeholder="Contoh:&#10;SPP Reguler&#10;SPP Intensif"
                          class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">{{ old('sub_categories') }}</textarea>
                <p id="subCategoriesHint" class="text-[10px] text-admin-slate mt-1">Masukkan rincian jenis pemasukan. Kosongkan jika tidak ada rincian khusus.</p>
            </div>
            <div>
                <label class="text-xs font-semibold text-admin-slate">Warna</label>
                <input type="color" name="color" value="{{ old('color', '#6366f1') }}" required
                       class="w-12 h-10 p-1 rounded-admin-md border border-admin-border cursor-pointer">
                @error('color') <p class="text-xs text-admin-danger mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-admin-indigo text-white px-6 py-2.5 rounded-admin-md text-sm font-medium hover:bg-admin-indigo-deep transition-colors">Simpan</button>
                <a href="{{ route('finance.categories.index') }}" class="text-admin-slate hover:text-admin-ink text-sm font-medium transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateSubCategoriesLabel() {
    const type = document.getElementById('categoryType').value;
    const label = document.getElementById('subCategoriesLabel');
    const hint = document.getElementById('subCategoriesHint');
    const input = document.getElementById('subCategoriesInput');
    
    if (type === 'expense') {
        label.innerText = 'Jenis Pengeluaran (Satu per baris)';
        hint.innerText = 'Masukkan rincian jenis pengeluaran. Kosongkan jika tidak ada rincian khusus.';
        input.placeholder = 'Contoh:\nModul pelatihan\nAlat Tulis kantor';
    } else {
        label.innerText = 'Jenis Pemasukan (Satu per baris)';
        hint.innerText = 'Masukkan rincian jenis pemasukan. Kosongkan jika tidak ada rincian khusus.';
        input.placeholder = 'Contoh:\nSPP Reguler\nSPP Intensif';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    updateSubCategoriesLabel();
});
</script>
@endpush
