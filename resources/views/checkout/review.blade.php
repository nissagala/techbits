@extends('layouts.storefront')
@section('title', 'Checkout — Review — TechBits')
@section('content')
<div class="steps">
    <div class="step">1. Shipping</div>
    <div class="step">2. Payment</div>
    <div class="step active">3. Review</div>
</div>
<h2 style="font-size:1.1rem;font-weight:700;margin-bottom:1.25rem;">Review your order</h2>
<div style="display:grid;grid-template-columns:2fr 1fr;gap:2rem;align-items:start;">
<div>
    {{-- Shipping --}}
    <div style="border:1px solid var(--border);border-radius:8px;padding:1rem;margin-bottom:1rem;">
        <div style="font-weight:700;margin-bottom:.5rem;">Shipping to</div>
        <div style="font-size:.9rem;line-height:1.7;">
            <strong>{{ $address->recipient }}</strong><br>
            {{ $address->line1 }}, {{ $address->city }}<br>
            {{ $address->district }} {{ $address->postal_code }}<br>
            {{ $address->phone }}
        </div>
    </div>
    {{-- Payment --}}
    <div style="border:1px solid var(--border);border-radius:8px;padding:1rem;margin-bottom:1rem;">
        <div style="font-weight:700;margin-bottom:.4rem;">Payment</div>
        <div style="font-size:.9rem;">Card ending in <strong>{{ $payment['last4'] }}</strong> &bull; {{ $payment['cardholder'] }}</div>
    </div>
    {{-- Items --}}
    <div style="border:1px solid var(--border);border-radius:8px;padding:1rem;">
        <div style="font-weight:700;margin-bottom:.75rem;">Items</div>
        @foreach($cartItems as $item)
        <div style="display:flex;justify-content:space-between;font-size:.9rem;margin-bottom:.5rem;">
            <span>{{ $item->product->name }} × {{ $item->quantity }}</span>
            <span>@lkr($item->product->price * $item->quantity)</span>
        </div>
        @endforeach
    </div>
</div>
<div style="border:1px solid var(--border);border-radius:8px;padding:1.25rem;position:sticky;top:80px;">
    <h3 style="font-size:1rem;font-weight:700;margin-bottom:1rem;">Order Total</h3>
    <div style="display:flex;justify-content:space-between;font-size:.92rem;margin-bottom:.4rem;"><span>Subtotal</span><span>@lkr($subtotal)</span></div>
    <div style="display:flex;justify-content:space-between;font-size:.92rem;margin-bottom:.75rem;"><span>Shipping</span><span>@lkr($shipping)</span></div>
    <div style="display:flex;justify-content:space-between;font-weight:700;font-size:1.05rem;border-top:2px solid var(--border);padding-top:.75rem;margin-bottom:1.25rem;"><span>Total</span><span>@lkr($total)</span></div>
    <form method="POST" action="{{ route('checkout.place') }}">
        @csrf
        <button type="submit" class="btn btn-primary" style="width:100%;">Place order</button>
    </form>
    <a href="{{ route('checkout.payment') }}" style="display:block;text-align:center;margin-top:.75rem;font-size:.88rem;">← Back</a>
</div>
</div>
@endsection
