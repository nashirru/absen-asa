<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $categories = $query->latest()->paginate(10);

        return view('finance.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('finance.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'color' => 'required|string|max:7',
        ]);

        $subCategoriesRaw = $request->input('sub_categories');
        $subCategories = [];
        if (!empty($subCategoriesRaw)) {
            $subCategories = array_values(array_filter(array_map('trim', explode("\n", $subCategoriesRaw))));
        }
        $validated['sub_categories'] = $subCategories;

        Category::create($validated);

        return redirect()->route('finance.categories.index')
            ->with('success', 'Kategori berhasil dibuat.');
    }

    public function edit(Category $category)
    {
        return view('finance.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'color' => 'required|string|max:7',
        ]);

        $subCategoriesRaw = $request->input('sub_categories');
        $subCategories = [];
        if (!empty($subCategoriesRaw)) {
            $subCategories = array_values(array_filter(array_map('trim', explode("\n", $subCategoriesRaw))));
        }
        $validated['sub_categories'] = $subCategories;

        $category->update($validated);

        return redirect()->route('finance.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat menghapus kategori.');
        }

        $category->delete();

        return redirect()->route('finance.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
