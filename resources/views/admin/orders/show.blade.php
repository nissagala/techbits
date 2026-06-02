@extends('layouts.admin')
@section('title', $order->order_number . ' — TechBits Admin')
@section('content')
<a href="{{ route('admin.orders.index') }}" style="font-size:.88rem;display:inline-block;margin-bottom:1rem;">← Orders</a>
<div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.25rem;flex-wrap:wrap;">
    <h1>{{ $order->order_number }}</h1>
    <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:2rem;align-items:start;">
<div>
{{-- Customer + order info --}}
<div style="background:#fff;border:1px solid var(--border);border-radius:8px;padding:1.1rem;margin-bottom:1rem;font-size:.9rem;line-height:1.8;">
    <strong>Customer:</strong> {{ $order->user->name }} ({{ $order->user->email }}) — {{ $order->user->phone }}<br>
    <strong>Placed:</strong> {{ $order->placed_at->timezone('Asia/Colombo')->format('d M Y, H:i') }}
</div>

{{-- Line items --}}
<table class="data-table" style="margin-bottom:1rem;">
    <thead><tr><th>Product</th><th>SKU</th><th>Unit Price</th><th>Qty</th><th>Line Total</th></tr></thead>
    <tbody>
    @foreach($order->items as $item)
    <tr>
        <td>{{ $item->product_name }}</td>
        <td style="font-size:.82rem;color:var(--text-muted);">{{ $item->product_sku }}</td>
        <td>@lkr($item->unit_price)</td>
        <td>{{ $item->quantity }}</td>
        <td>@lkr($item->line_total)</td>
    </tr>
    @endforeach
    </tbody>
</table>

{{-- Shipping address + payment --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
<div style="background:#fff;border:1px solid var(--border);border-radius:8px;padding:1rem;font-size:.88rem;line-height:1.7;">
    <strong style="display:block;margin-bottom:.4rem;">Shipping Address</strong>
    @php $a = $order->shipping_address; @endphp
    {{ $a['recipient'] }}<br>{{ $a['line1'] }}<br>@if($a['line2']){{ $a['line2'] }}<br>@endif{{ $a['city'] }}, {{ $a['district'] }} {{ $a['postal_code'] }}<br>{{ $a['phone'] }}
</div>
<div style="background:#fff;border:1px solid var(--border);border-radius:8px;padding:1rem;font-size:.88rem;">
    <strong style="display:block;margin-bottom:.4rem;">Payment</strong>
    Card ending ****{{ $order->payment_last4 }}<br>{{ $order->payment_cardholder }}<br>Exp: {{ $order->payment_expiry }}<br>
    <div style="margin-top:.5rem;font-size:.92rem;font-weight:700;">Total: @lkr($order->total)</div>
</div>
</div>

{{-- Status timeline --}}
<div style="background:#fff;border:1px solid var(--border);border-radius:8px;padding:1rem;">
    <strong style="display:block;margin-bottom:.75rem;font-size:.9rem;">Status Timeline</strong>
    @foreach($order->statusLogs as $log)
    <div style="display:flex;gap:.75rem;margin-bottom:.4rem;font-size:.85rem;">
        <span style="color:var(--text-muted);min-width:130px;">{{ $log->created_at->timezone('Asia/Colombo')->format('d M Y, H:i') }}</span>
        <span>@if($log->from_status)<span class="badge badge-{{ $log->from_status }}">{{ ucfirst($log->from_status) }}</span> →@endif <span class="badge badge-{{ $log->to_status }}">{{ ucfirst($log->to_status) }}</span></span>
    </div>
    @endforeach
</div>
</div>

{{-- Actions panel --}}
<div style="background:#fff;border:1px solid var(--border);border-radius:8px;padding:1.25rem;position:sticky;top:80px;">
    <h3 style="font-size:.95rem;font-weight:700;margin-bottom:1rem;">Actions</h3>

    @if($order->canAdvance())
    <form method="POST" action="{{ route('admin.orders.advance', $order) }}">
        @csrf
        <button type="submit" class="btn btn-primary" style="width:100%;margin-bottom:.75rem;">
            Advance to {{ ucfirst($order->nextStatus()) }} →
        </button>
    </form>
    @else
    <button class="btn btn-primary" style="width:100%;margin-bottom:.75rem;" disabled>No further advance</button>
    @endif

    @if($order->canCancel())
    <form method="POST" action="{{ route('admin.orders.cancel', $order) }}"
          onsubmit="return confirm('Cancel this order? Stock will be restored.')">
        @csrf
        <button type="submit" class="btn btn-danger" style="width:100%;">Cancel order (restore stock)</button>
    </form>
    @else
    <button class="btn btn-danger" style="width:100%;" disabled>Cannot cancel</button>
    @endif
</div>
</div>
@endsection
