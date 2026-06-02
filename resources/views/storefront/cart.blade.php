@extends('layouts.storefront')
@section('title', 'Shopping Cart — TechBits')

@section('content')

<h1 style="font-size:1.4rem;font-weight:700;margin-bottom:1.25rem;">Shopping Cart</h1>

@if(count($lines) === 0)
    <div class="empty-state">
        <div class="empty-icon">🛒</div>
        <p>Your cart is empty. Browse products to get started.</p>
        <a href="{{ route('home') }}" class="btn btn-primary">Browse Products</a>
    </div>
@else

<div style="display:grid;grid-template-columns:2fr 1fr;gap:2rem;align-items:start;">

{{-- Line items --}}
<div>
    @foreach($lines as $line)
    @php $p = $line['product']; @endphp
    <div style="display:flex;gap:1rem;border:1px solid var(--border);border-radius:6px;padding:1rem;margin-bottom:.75rem;
                {{ $line['blocked'] ? 'border-color:#dc2626;background:#fff5f5;' : ($line['warning']==='partial' ? 'border-color:#f59e0b;background:#fffbeb;' : '') }}">

        {{-- Image --}}
        <img src="{{ $p && $p->primaryImage() ? asset('storage/'.$p->primaryImage()->path) : asset('img/placeholder.png') }}"
             alt="{{ $p?->name ?? 'Product' }}"
             style="width:80px;height:80px;object-fit:cover;border-radius:4px;flex-shrink:0;">

        <div style="flex:1;">
            <div style="font-weight:500;margin-bottom:.25rem;">{{ $p?->name ?? 'Unknown product' }}</div>
            <div style="color:var(--primary);font-weight:700;margin-bottom:.5rem;">@lkr($p?->price ?? 0)</div>

            @if($line['warning'] === 'unavailable')
                <div class="alert alert-error" style="padding:.35rem .65rem;font-size:.82rem;margin-bottom:.5rem;">This product is unavailable and cannot be checked out.</div>
            @elseif($line['warning'] === 'out_of_stock')
                <div class="alert alert-error" style="padding:.35rem .65rem;font-size:.82rem;margin-bottom:.5rem;">Out of stock. Please remove to continue.</div>
            @elseif($line['warning'] === 'partial')
                <div class="alert alert-warning" style="padding:.35rem .65rem;font-size:.82rem;margin-bottom:.5rem;">Only {{ $p->stock }} left. Quantity capped.</div>
            @endif

            <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
                @if(! $line['blocked'] && $p && $p->stock > 0)
                <form method="POST" action="{{ route('cart.update', $line['id']) }}" style="display:flex;align-items:center;gap:.5rem;">
                    @csrf @method('PATCH')
                    <div class="qty-selector">
                        <button type="button" onclick="let i=this.nextElementSibling;i.value=Math.max(1,+i.value-1);this.form.submit()">−</button>
                        <input type="number" name="quantity" value="{{ $line['quantity'] }}" min="1" max="{{ $p->maxCartQty() }}" style="width:2.8rem;text-align:center;border:none;border-left:1px solid var(--border);border-right:1px solid var(--border);padding:.3rem 0;">
                        <button type="button" onclick="let i=this.previousElementSibling;i.value=Math.min({{ $p->maxCartQty() }},+i.value+1);this.form.submit()">+</button>
                    </div>
                </form>
                @endif

                <form method="POST" action="{{ route('cart.remove', $line['id']) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-secondary" style="color:var(--error);border-color:var(--error);">Remove</button>
                </form>
            </div>
        </div>

        <div style="font-weight:700;white-space:nowrap;padding-top:.25rem;">
            @lkr(($p?->price ?? 0) * $line['quantity'])
        </div>
    </div>
    @endforeach
</div>

{{-- Summary --}}
<div style="border:1px solid var(--border);border-radius:6px;padding:1.25rem;position:sticky;top:80px;">
    <h2 style="font-size:1rem;font-weight:700;margin-bottom:1rem;">Order Summary</h2>
    <div style="display:flex;justify-content:space-between;margin-bottom:.5rem;font-size:.92rem;">
        <span>Subtotal</span><span>@lkr($subtotal)</span>
    </div>
    <div style="display:flex;justify-content:space-between;margin-bottom:.75rem;font-size:.92rem;">
        <span>Shipping</span><span>@lkr($shipping)</span>
    </div>
    <div style="display:flex;justify-content:space-between;font-weight:700;font-size:1.05rem;border-top:2px solid var(--border);padding-top:.75rem;margin-bottom:1.25rem;">
        <span>Total</span><span>@lkr($total)</span>
    </div>

    @if($blocked)
        <div class="alert alert-error" style="font-size:.83rem;margin-bottom:.75rem;">Remove unavailable items to proceed.</div>
        <button class="btn btn-primary" style="width:100%;" disabled>Proceed to checkout</button>
    @else
        <a href="{{ route('checkout.shipping') }}" class="btn btn-primary" style="width:100%;display:block;text-align:center;">Proceed to checkout</a>
    @endif

    <a href="{{ route('home') }}" style="display:block;text-align:center;margin-top:.75rem;font-size:.88rem;color:var(--text-muted);">← Continue shopping</a>
</div>

</div>
@endif

@endsection
