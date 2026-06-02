@extends('layouts.storefront')
@section('title', $product->name . ' — TechBits')

@section('content')

<nav class="breadcrumb">
    <a href="{{ route('home') }}">Home</a>
    <span class="breadcrumb-sep">›</span>
    <a href="{{ route('category.show', $product->category) }}">{{ $product->category->name }}</a>
    <span class="breadcrumb-sep">›</span>
    <span>{{ $product->name }}</span>
</nav>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:2.5rem;margin-bottom:2.5rem;">

    {{-- Left: Images --}}
    <div>
        @php $primary = $product->primaryImage(); $images = $product->images; @endphp
        <img id="mainImg"
             src="{{ $primary ? asset('storage/'.$primary->path) : asset('img/placeholder.png') }}"
             alt="{{ $product->name }}"
             style="width:100%;aspect-ratio:1;object-fit:cover;border-radius:8px;border:1px solid var(--border);margin-bottom:.75rem;">
        @if($images->count() > 1)
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
            @foreach($images as $img)
            <img src="{{ asset('storage/'.$img->path) }}"
                 alt="{{ $product->name }}"
                 onclick="document.getElementById('mainImg').src=this.src"
                 style="width:70px;height:70px;object-fit:cover;border-radius:4px;border:2px solid var(--border);cursor:pointer;">
            @endforeach
        </div>
        @endif
    </div>

    {{-- Right: Details --}}
    <div>
        <h1 style="font-size:1.4rem;font-weight:700;margin-bottom:.5rem;">{{ $product->name }}</h1>
        <div style="font-size:1.6rem;font-weight:800;color:var(--primary);margin-bottom:.75rem;">@lkr($product->price)</div>

        @if($product->stock === 0)
            <span class="badge badge-out-of-stock" style="margin-bottom:1rem;">Out of stock</span>
        @elseif($product->stock <= 5)
            <span class="badge badge-low-stock" style="margin-bottom:1rem;">Only {{ $product->stock }} left</span>
        @else
            <span class="badge badge-in-stock" style="margin-bottom:1rem;">In stock</span>
        @endif

        <p style="color:var(--text-muted);font-size:.92rem;margin-bottom:1.25rem;">{{ $product->short_description }}</p>

        @if($product->stock > 0)
        <form id="addCartForm" action="{{ route('cart.add') }}" method="POST" style="display:flex;gap:.75rem;align-items:center;flex-wrap:wrap;">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <div class="qty-selector">
                <button type="button" data-delta="-1">−</button>
                <input type="number" name="quantity" id="qtyInput" value="1" min="1" max="{{ $product->maxCartQty() }}">
                <button type="button" data-delta="1">+</button>
            </div>
            <button type="submit" class="btn btn-primary">Add to cart</button>
        </form>
        @else
            <button class="btn btn-primary" disabled>Out of stock</button>
        @endif
    </div>
</div>

{{-- Full Description & Specs --}}
<div style="display:grid;grid-template-columns:3fr 2fr;gap:2rem;">
    <div>
        <h2 style="font-size:1rem;font-weight:700;margin-bottom:.75rem;border-bottom:2px solid var(--border);padding-bottom:.5rem;">Description</h2>
        <div style="font-size:.92rem;line-height:1.8;white-space:pre-line;">{{ $product->description }}</div>
    </div>

    @if($product->specs->count())
    <div>
        <h2 style="font-size:1rem;font-weight:700;margin-bottom:.75rem;border-bottom:2px solid var(--border);padding-bottom:.5rem;">Specifications</h2>
        <table style="width:100%;border-collapse:collapse;font-size:.88rem;">
            @foreach($product->specs as $spec)
            <tr style="border-bottom:1px solid var(--border);">
                <td style="padding:.5rem .5rem .5rem 0;font-weight:600;width:40%;color:var(--text-muted);">{{ $spec->spec_key }}</td>
                <td style="padding:.5rem 0;">{{ $spec->spec_value }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif
</div>

@push('scripts')
<script>
/* pass server-side max qty into the generic adjustQty helper */
const _maxQty = {{ $product->maxCartQty() }};
document.querySelectorAll('.qty-selector button').forEach(btn => {
    btn.addEventListener('click', () => {
        adjustQty(btn, btn.dataset.delta === '-1' ? -1 : 1, 1, _maxQty);
    });
});
</script>
@endpush

@endsection
