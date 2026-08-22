<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index(Request $request)
    {
        $query = Category::withCount('products')->with('creator')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->paginate(15)->withQueryString();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_active']  = $request->boolean('is_active', true);

        $category = Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', "Category '{$category->name}' created.")->with('flash_key', 'flash_category_created')->with('flash_name', $category->name);
    }

    public function show(Category $category)
    {
        $category->load(['products', 'creator']);
        return view('categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', "Category '{$category->name}' updated.")->with('flash_key', 'flash_category_updated')->with('flash_name', $category->name);
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Cannot delete category that has products assigned to it.')->with('flash_key', 'flash_category_delete_error');
        }
        $category->delete();
        return redirect()->route('categories.index')
            ->with('success', "Category '{$category->name}' deleted.")->with('flash_key', 'flash_category_deleted')->with('flash_name', $category->name);
    }
}
