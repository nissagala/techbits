@extends('layouts.storefront')
@section('title', 'Order Confirmed — TechBits')
@section('content')
<div style="text-align:center;padding:3rem 1rem;">
    <div style="font-size:3.5rem;margin-bottom:.75rem;">✅</div>
    <h1 style="font-size:1.6rem;font-weight:800;margin-bottom:.5rem;">Order placed successfully!</h1>
    <p style="color:var(--text-muted);margin-bottom:.5rem;">Your order number is:</p>
    <div style="font-size:1.8rem;font-weight:700;color:var(--primary);margin-bottom:1rem;">{{ $order->order_number }}</div>
    <p style="color:var(--text-muted);font-size:.92rem;margin-bottom:2rem;">A confirmation email has been sent to {{ Auth::user()->email }}</p>

    <div style="max-width:500px;margin:0 auto;text-align:left;border:1px solid var(--border);border-radius:8px;padding:1.25rem;margin-bottom:1.5rem;">
        <div style="font-weight:700;margin-bottom:.75rem;">Order Summary</div>
        @foreach($order->items as $item)
        <div style="display:flex;justify-content:space-between;font-size:.9rem;margin-bottom:.4rem;">
            <span>{{ $item->product_name }} × {{ $item->quantity }}</span>
            <span>@lkr($item->line_total)</span>
        </div>
        @endforeach
        <div style="border-top:1px solid var(--border);margin-top:.75rem;padding-top:.75rem;display:flex;justify-content:space-between;font-weight:700;">
            <span>Total paid</span><span>@lkr($order->total)</span>
        </div>
    </div>

    <div style="display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap;">
        <a href="{{ route('account.orders.show', $order) }}" class="btn btn-primary">View order details</a>
        <a href="{{ route('home') }}" class="btn btn-secondary">Continue shopping</a>
    </div>
</div>
@endsection
