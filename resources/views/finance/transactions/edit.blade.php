@extends('layouts.admin')
@section('title', 'Edit Transaksi')
@section('header', 'Edit Transaksi')
@section('content')
<div class="max-w-xl mx-auto animate-fade-in-up">
    <div class="bg-admin-surface border border-admin-border rounded-admin-lg p-6">
        <form method="POST" action="{{ route('finance.transactions.update', $transaction) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-admin-slate">Tipe</label>
                    <select name="type" id="txnType" required onchange="updateCategories()"
                            class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                        <option value="income" {{ old('type', $transaction->type) == 'income' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="expense" {{ old('type', $transaction->type) == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-admin-slate">Tanggal</label>
                    <input type="date" name="date" value="{{ old('date', $transaction->date->format('Y-m-d')) }}" required
                           class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                </div>
            </div>
            <div>
                <label class="text-xs font-semibold text-admin-slate">Akun</label>
                <select name="account_id" required
                        class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    @foreach($accounts as $id => $name)
                        <option value="{{ $id }}" {{ old('account_id', $transaction->account_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-admin-slate">Kategori</label>
                <select name="category_id" id="txnCategory" required
                        class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $id => $name)
                        <option value="{{ $id }}" {{ old('category_id', $transaction->category_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div id="jenisPengeluaranContainer" class="hidden">
                <label id="jenisPengeluaranLabel" class="text-xs font-semibold text-admin-slate">Jenis Pengeluaran (Bisa dicentang)</label>
                <div id="jenisPengeluaranCheckboxes" class="mt-2 grid grid-cols-2 gap-3 bg-admin-canvas p-4 rounded-admin-md border border-admin-border">
                    <!-- Checkboxes will be dynamically rendered here -->
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-admin-slate">Jumlah</label>
                    <input type="number" step="0.01" name="amount" value="{{ old('amount', $transaction->amount) }}" required min="0"
                           class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">
                </div>
                <div>
                    <label class="text-xs font-semibold text-admin-slate">Bukti Transaksi</label>
                    <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full mt-1 px-4 py-2 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink file:mr-3 file:py-1 file:px-3 file:rounded-admin-md file:border-0 file:text-xs file:font-medium file:bg-admin-indigo file:text-white">
                    @if($transaction->attachment)
                        <div class="flex items-center gap-2 mt-1">
                            <a href="{{ Storage::url($transaction->attachment) }}" target="_blank" class="text-xs text-admin-indigo hover:underline flex items-center gap-1">
                                <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>
                                Lihat bukti transaksi: {{ basename($transaction->attachment) }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            <div>
                <label class="text-xs font-semibold text-admin-slate">Keterangan</label>
                <textarea name="description" rows="3"
                          class="w-full mt-1 px-4 py-2.5 bg-admin-canvas rounded-admin-md border border-admin-border text-sm text-admin-ink focus:outline-none focus:ring-2 focus:ring-admin-indigo/25">{{ old('description', $transaction->description) }}</textarea>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-admin-indigo text-white px-6 py-2.5 rounded-admin-md text-sm font-medium hover:bg-admin-indigo-deep transition-colors">Simpan</button>
                <a href="{{ route('finance.transactions.index') }}" class="text-admin-slate hover:text-admin-ink text-sm font-medium transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
let loadedCategories = [];

function updateCategories(selectedCategoryId = null, selectedSubCategories = []) {
    const type = document.getElementById('txnType').value;
    fetch('{{ route("finance.transactions.categories-by-type") }}?type=' + type)
        .then(r => r.json())
        .then(data => {
            loadedCategories = data;
            const sel = document.getElementById('txnCategory');
            sel.innerHTML = '<option value="">Pilih Kategori</option>';
            
            loadedCategories.forEach(cat => {
                const selected = (selectedCategoryId == cat.id) ? 'selected' : '';
                sel.innerHTML += `<option value="${cat.id}" ${selected}>${cat.name}</option>`;
            });

            handleCategoryChange(selectedSubCategories);
        });
}

function handleCategoryChange(selectedSubCategories = []) {
    const categoryId = document.getElementById('txnCategory').value;
    const container = document.getElementById('jenisPengeluaranContainer');
    const checkboxDiv = document.getElementById('jenisPengeluaranCheckboxes');
    const label = document.getElementById('jenisPengeluaranLabel');
    const type = document.getElementById('txnType').value;
    
    if (type === 'income') {
        label.innerText = 'Jenis Pemasukan (Bisa dicentang)';
    } else {
        label.innerText = 'Jenis Pengeluaran (Bisa dicentang)';
    }
    
    if (!categoryId) {
        container.classList.add('hidden');
        return;
    }

    const category = loadedCategories.find(c => c.id == categoryId);
    if (category && category.sub_categories && category.sub_categories.length > 0) {
        container.classList.remove('hidden');
        checkboxDiv.innerHTML = '';
        
        category.sub_categories.forEach(sub => {
            const isChecked = selectedSubCategories.includes(sub) ? 'checked' : '';
            checkboxDiv.innerHTML += `
                <label class="flex items-center gap-2 cursor-pointer select-none text-sm text-admin-ink">
                    <input type="checkbox" name="jenis_pengeluaran[]" value="${sub}" ${isChecked}
                           class="w-4 h-4 rounded border-admin-border text-admin-indigo accent-admin-indigo cursor-pointer">
                    <span>${sub}</span>
                </label>
            `;
        });
    } else {
        container.classList.add('hidden');
        checkboxDiv.innerHTML = '';
    }
}

document.getElementById('txnCategory').addEventListener('change', () => {
    handleCategoryChange([]);
});

document.addEventListener('DOMContentLoaded', () => {
    const initialCategoryId = "{{ old('category_id', $transaction->category_id) }}";
    const initialSubCategories = {!! json_encode(old('jenis_pengeluaran', $transaction->jenis_pengeluaran ?? [])) !!};
    updateCategories(initialCategoryId, initialSubCategories);
});
</script>
@endpush
