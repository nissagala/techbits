@extends('layouts.storefront')
@section('title', 'Search — TechBits')

@section('content')

<h1 style="font-size:1.3rem;margin-bottom:1rem;">
    @if(isset($shortQuery) && $shortQuery)
        Search
    @elseif($q)
        Showing results for: <em>"{{ $q }}"</em>
        @if($products) — {{ $products->total() }} result(s) @endif
    @else
        Search products
    @endif
</h1>

{{-- Short query hint --}}
@if(isset($shortQuery) && $shortQuery)
    <div class="alert alert-warning">Please enter at least 2 characters to search.</div>
@endif

@if($q && strlen($q) >= 2)
{{-- Filter bar --}}
<form method="GET" action="{{ route('search') }}" style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;margin-bottom:1.25rem;">
    <input type="hidden" name="q" value="{{ $q }}">
    <select name="category" class="form-control" style="width:auto;padding:.4rem .75rem;font-size:.85rem;" onchange="this.form.submit()">
        <option value="">All Categories</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category')==$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
        @endforeach
    </select>
    <label style="font-size:.85rem;">
        <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }} onchange="this.form.submit()">
        In stock only
    </label>
    <select name="sort" class="form-control" style="width:auto;padding:.4rem .75rem;font-size:.85rem;" onchange="this.form.submit()">
        <option value="newest" {{ request('sort','newest')==='newest' ? 'selected' : '' }}>Newest</option>
        <option value="price_asc" {{ request('sort')==='price_asc' ? 'selected' : '' }}>Price: Low → High</option>
        <option value="price_desc" {{ request('sort')==='price_desc' ? 'selected' : '' }}>Price: High → Low</option>
        <option value="name_az" {{ request('sort')==='name_az' ? 'selected' : '' }}>Name A–Z</option>
    </select>
</form>

@if($products && $products->count())
    <div class="product-grid">
        @foreach($products as $product)
            @php $img = $product->primaryImage(); @endphp
            <a href="{{ route('product.show', $product) }}" class="product-card">
                <img class="product-card-img"
                     src="{{ $img ? asset('storage/'.$img->path) : asset('img/placeholder.png') }}"
                     alt="{{ $product->name }}">
                <div class="product-card-body">
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
        <div class="empty-icon">🔍</div>
        <p>No products match your search.</p>
        <a href="{{ route('home') }}" class="btn btn-primary">Back to home</a>
    </div>
@endif
@endif

@endsection
