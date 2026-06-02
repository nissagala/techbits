@extends('layouts.storefront')
@section('title', 'TechBits — Computer Accessories Sri Lanka')

@section('content')

{{-- Hero --}}
<div style="background:linear-gradient(135deg,#1a6fc4 0%,#155ca0 100%);color:#fff;border-radius:8px;padding:3rem 2rem;margin-bottom:2.5rem;text-align:center;">
    <h1 style="font-size:2rem;margin-bottom:.75rem;">Computer accessories,<br>delivered across Sri Lanka.</h1>
    <p style="opacity:.85;margin-bottom:1.5rem;">Keyboards, mice, storage, monitors and more — shipped island-wide for a flat LKR 500.</p>
    <a href="{{ route('search') }}" class="btn btn-primary" style="background:#fff;color:#1a6fc4;font-weight:700;">Shop All Products</a>
</div>

{{-- Featured Products --}}
@if($featured->count())
<section style="margin-bottom:2.5rem;">
    <h2 style="font-size:1.2rem;margin-bottom:1rem;font-weight:700;">Featured Products</h2>
    <div class="product-grid">
        @foreach($featured as $product)
            @php $img = $product->primaryImage(); @endphp
            <a href="{{ route('product.show', $product) }}" class="product-card">
                <img class="product-card-img"
                     src="{{ $img ? asset('storage/'.$img->path) : asset('img/placeholder.png') }}"
                     alt="{{ $product->name }}">
                <div class="product-card-body">
                    @if($product->is_featured)
                        <span class="badge badge-featured" style="margin-bottom:.4rem;">Featured</span>
                    @endif
                    <div class="product-card-name">{{ $product->name }}</div>
                    <div class="product-card-price">@lkr($product->price)</div>
                    @if($product->stock === 0)
                        <span class="stock-out">Out of stock</span>
                    @elseif($product->stock <= 5)
                        <span class="stock-low">Only {{ $product->stock }} left</span>
                    @else
                        <span class="stock-in">In stock</span>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- Category quick links --}}
@if($categories->count())
<section>
    <h2 style="font-size:1.2rem;margin-bottom:1rem;font-weight:700;">Shop by Category</h2>
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:1rem;">
        @foreach($categories as $cat)
        <a href="{{ route('category.show', $cat) }}"
           style="border:1px solid var(--border);border-radius:6px;padding:1rem;text-align:center;color:var(--text);font-size:.88rem;font-weight:500;transition:box-shadow .15s;"
           onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,.1)'"
           onmouseout="this.style.boxShadow=''">
            {{ $cat->name }}
            <br>
            <span style="font-size:.75rem;color:var(--text-muted);">{{ $cat->products_count }} products</span>
        </a>
        @endforeach
    </div>
</section>
@endif

@endsection
