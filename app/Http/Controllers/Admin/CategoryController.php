<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2|max:50',
        ]);

        $name = $request->input('name');

        if (Category::whereRaw('LOWER(name) = ?', [strtolower($name)])->exists()) {
            return back()->withErrors(['name' => 'A category with this name already exists.'])->withInput();
        }

        Category::create(['name' => $name]);

        return back()->with('success', 'Category created.');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|min:2|max:50',
        ]);

        $name = $request->input('name');

        $exists = Category::whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->where('id', '!=', $category->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'A category with this name already exists.'])->withInput();
        }

        $category->update(['name' => $name]);

        return back()->with('success', 'Category renamed.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->withErrors(['category' => 'Cannot delete a category that contains products.']);
        }

        $category->delete();

        return back()->with('success', 'Category deleted.');
    }
}
