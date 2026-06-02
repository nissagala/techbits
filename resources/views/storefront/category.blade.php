@extends('layouts.storefront')
@section('title', $category->name . ' — TechBits')

@section('content')

<nav class="breadcrumb">
    <a href="{{ route('home') }}">Home</a>
    <span class="breadcrumb-sep">›</span>
    <span>{{ $category->name }}</span>
</nav>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
    <h1 style="font-size:1.4rem;font-weight:700;">{{ $category->name }}</h1>
    <form method="GET" action="{{ route('category.show', $category) }}" style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;">
        <label style="font-size:.85rem;">
            <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }}
                   onchange="this.form.submit()">
            In stock only
        </label>
        <select name="sort" onchange="this.form.submit()" class="form-control" style="width:auto;padding:.35rem .75rem;font-size:.85rem;">
            <option value="newest" {{ request('sort','newest')==='newest' ? 'selected' : '' }}>Newest</option>
            <option value="price_asc" {{ request('sort')==='price_asc' ? 'selected' : '' }}>Price: Low → High</option>
            <option value="price_desc" {{ request('sort')==='price_desc' ? 'selected' : '' }}>Price: High → Low</option>
            <option value="name_az" {{ request('sort')==='name_az' ? 'selected' : '' }}>Name A–Z</option>
        </select>
    </form>
</div>

@if($products->count())
    <div class="product-grid">
        @foreach($products as $product)
            @php $img = $product->primaryImage(); @endphp
            <a href="{{ route('product.show', $product) }}" class="product-card">
                <img class="product-card-img"
                     src="{{ $img ? asset('storage/'.$img->path) : asset('img/placeholder.png') }}"
                     alt="{{ $product->name }}">
                <div class="product-card-body">
                    @if($product->is_featured)<span class="badge badge-featured" style="margin-bottom:.35rem;">Featured</span>@endif
                    <div class="product-card-name">{{ $product->name }}</div>
                    <div class="product-card-price">@lkr($product->price)</div>
                    @if($product->stock===0)<span class="stock-out">Out of stock</span>
                    @elseif($product->stock<=5)<span class="stock-low">Only {{ $product->stock }} left</span>
                    @else<span class="stock-in">In stock</span>@endif
                </div>
            </a>
        @endforeach
    </div>
    <div style="margin-top:1.5rem;">{{ $products->links() }}</div>
@else
    <div class="empty-state">
        <div class="empty-icon">📦</div>
        <p>No products in this category yet.</p>
        <a href="{{ route('home') }}" class="btn btn-primary">Back to home</a>
    </div>
@endif

@endsection
