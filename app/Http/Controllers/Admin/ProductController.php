<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSpec;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images'])
            ->when($request->search, fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->when($request->category, fn ($q) => $q->where('category_id', $request->category))
            ->latest();

        $products   = $query->paginate(20)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.form', compact('categories'));
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        $product = DB::transaction(function () use ($data, $request) {
            $product = Product::create([
                'category_id'       => $data['category_id'],
                'name'              => $data['name'],
                'sku'               => $data['sku'],
                'short_description' => $data['short_description'],
                'description'       => $data['description'],
                'price'             => $data['price'],
                'stock'             => $data['stock'],
                'is_featured'       => $request->boolean('is_featured'),
                'is_active'         => $request->boolean('is_active'),
            ]);

            $this->saveImages($request, $product);
            $this->saveSpecs($request, $product);

            return $product;
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $product->load(['images', 'specs', 'category']);
        $categories = Category::orderBy('name')->get();
        return view('admin.products.form', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $request, $product) {
            $product->update([
                'category_id'       => $data['category_id'],
                'name'              => $data['name'],
                'sku'               => $data['sku'],
                'short_description' => $data['short_description'],
                'description'       => $data['description'],
                'price'             => $data['price'],
                'stock'             => $data['stock'],
                'is_featured'       => $request->boolean('is_featured'),
                'is_active'         => $request->boolean('is_active'),
            ]);

            $this->saveImages($request, $product);
            $this->saveSpecs($request, $product);
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted.');
    }

    public function destroyImage(Product $product, ProductImage $image)
    {
        if ($image->product_id !== $product->id) {
            abort(404);
        }

        $remaining = $product->images()->count();
        if ($remaining <= 1 && $product->is_active) {
            return back()->withErrors(['image' => 'Cannot delete the last image of an active product.']);
        }

        Storage::disk('public')->delete($image->path);

        if ($image->is_primary) {
            $next = $product->images()->where('id', '!=', $image->id)->first();
            $next?->update(['is_primary' => true]);
        }

        $image->delete();

        return back()->with('success', 'Image removed.');
    }

    private function saveImages(Request $request, Product $product): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $primaryIndex = (int) $request->input('primary_image_index', 0);
        $existingCount = $product->images()->count();

        foreach ($request->file('images') as $i => $file) {
            $path = $file->store('products', 'public');
            $isPrimary = ($existingCount === 0 && $i === $primaryIndex);

            if ($isPrimary) {
                $product->images()->update(['is_primary' => false]);
            }

            ProductImage::create([
                'product_id' => $product->id,
                'path'       => $path,
                'is_primary' => $isPrimary,
                'sort_order' => $existingCount + $i,
            ]);
        }

        if ($existingCount === 0 && $product->images()->where('is_primary', true)->count() === 0) {
            $product->images()->first()?->update(['is_primary' => true]);
        }
    }

    private function saveSpecs(Request $request, Product $product): void
    {
        $product->specs()->delete();

        $keys   = $request->input('specs.keys', []);
        $values = $request->input('specs.values', []);

        foreach ($keys as $i => $key) {
            if (blank($key) || blank($values[$i] ?? null)) {
                continue;
            }
            ProductSpec::create([
                'product_id' => $product->id,
                'spec_key'   => $key,
                'spec_value' => $values[$i],
                'sort_order' => $i,
            ]);
        }
    }
}
