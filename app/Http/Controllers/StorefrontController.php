<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class StorefrontController extends Controller
{
    public function home()
    {
        $featured = Product::with(['images'])
            ->active()
            ->featured()
            ->latest()
            ->limit(8)
            ->get();

        if ($featured->count() < 8) {
            $featuredIds = $featured->pluck('id');
            $fill = Product::with(['images'])
                ->active()
                ->whereNotIn('id', $featuredIds)
                ->latest()
                ->limit(8 - $featured->count())
                ->get();
            $featured = $featured->merge($fill);
        }

        $categories = Category::withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        return view('storefront.home', compact('featured', 'categories'));
    }

    public function about()    { return view('static.about'); }
    public function terms()    { return view('static.terms'); }
    public function privacy()  { return view('static.privacy'); }
    public function shippingInfo() { return view('static.shipping-info'); }
    public function faq()      { return view('static.faq'); }
}
