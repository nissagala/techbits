@extends('layouts.storefront')
@section('title', $order->order_number . ' — TechBits')
@section('content')
<a href="{{ route('account.orders.index') }}" style="font-size:.88rem;display:inline-block;margin-bottom:1rem;">← My Orders</a>
<div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap;">
    <h1 style="font-size:1.3rem;font-weight:700;">{{ $order->order_number }}</h1>
    <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
    <span style="color:var(--text-muted);font-size:.88rem;">Placed {{ $order->placed_at->timezone('Asia/Colombo')->format('d M Y') }}</span>
</div>

{{-- Line items --}}
<h2 style="font-size:1rem;font-weight:700;margin-bottom:.75rem;">Items</h2>
<table class="data-table" style="margin-bottom:1.5rem;">
    <thead><tr><th>Product</th><th>SKU</th><th>Unit Price</th><th>Qty</th><th>Line Total</th></tr></thead>
    <tbody>
    @foreach($order->items as $item)
    <tr>
        <td>{{ $item->product_name }}</td>
        <td style="color:var(--text-muted);font-size:.83rem;">{{ $item->product_sku }}</td>
        <td>@lkr($item->unit_price)</td>
        <td>{{ $item->quantity }}</td>
        <td>@lkr($item->line_total)</td>
    </tr>
    @endforeach
    </tbody>
</table>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
{{-- Shipping address --}}
<div>
    <h2 style="font-size:1rem;font-weight:700;margin-bottom:.5rem;">Shipped to</h2>
    @php $addr = $order->shipping_address; @endphp
    <div style="font-size:.9rem;line-height:1.8;color:var(--text);">
        <strong>{{ $addr['recipient'] }}</strong><br>
        {{ $addr['line1'] }}<br>
        @if($addr['line2']){{ $addr['line2'] }}<br>@endif
        {{ $addr['city'] }}, {{ $addr['district'] }} {{ $addr['postal_code'] }}<br>
        {{ $addr['phone'] }}
    </div>
</div>
{{-- Payment + Totals --}}
<div>
    <h2 style="font-size:1rem;font-weight:700;margin-bottom:.5rem;">Payment</h2>
    <p style="font-size:.9rem;margin-bottom:1rem;">Card ending in <strong>{{ $order->payment_last4 }}</strong> ({{ $order->payment_expiry }})</p>
    <div style="font-size:.92rem;line-height:2;">
        <div style="display:flex;justify-content:space-between;"><span>Subtotal</span><span>@lkr($order->subtotal)</span></div>
        <div style="display:flex;justify-content:space-between;"><span>Shipping</span><span>@lkr($order->shipping_fee)</span></div>
        <div style="display:flex;justify-content:space-between;font-weight:700;border-top:2px solid var(--border);padding-top:.4rem;"><span>Total</span><span>@lkr($order->total)</span></div>
    </div>
</div>
</div>
@endsection
