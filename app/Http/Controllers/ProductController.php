<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function category(Category $category, Request $request)
    {
        $query = $category->products()->with(['images'])->active();

        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        $query = match($request->sort) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name_az'    => $query->orderBy('name'),
            default      => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        return view('storefront.category', compact('category', 'products'));
    }

    public function search(Request $request)
    {
        $q = $request->input('q', '');

        if (mb_strlen($q) < 2) {
            $categories = Category::orderBy('name')->get();
            return view('storefront.search', [
                'q' => $q, 'products' => null, 'categories' => $categories,
                'shortQuery' => true,
            ]);
        }

        $query = Product::with(['images'])->active()
            ->where('name', 'like', '%' . $q . '%');

        if ($request->category) {
            $query->where('category_id', $request->category);
        }
        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        $query = match($request->sort) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name_az'    => $query->orderBy('name'),
            default      => $query->latest(),
        };

        $products   = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('storefront.search', compact('q', 'products', 'categories'));
    }

    public function show(Product $product)
    {
        if (! $product->is_active || $product->trashed()) {
            abort(404);
        }
        $product->load(['images', 'specs', 'category']);
        return view('storefront.product', compact('product'));
    }
}
