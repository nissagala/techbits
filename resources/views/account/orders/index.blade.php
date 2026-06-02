@extends('layouts.storefront')
@section('title', 'My Orders — TechBits')
@section('content')
<h1 style="font-size:1.3rem;font-weight:700;margin-bottom:1.25rem;">My Orders</h1>
@if($orders->count())
<table class="data-table">
    <thead>
        <tr><th>Order #</th><th>Date</th><th>Total</th><th>Status</th><th></th></tr>
    </thead>
    <tbody>
    @foreach($orders as $order)
    <tr style="cursor:pointer;" onclick="location.href='{{ route('account.orders.show', $order) }}'">
        <td><strong>{{ $order->order_number }}</strong></td>
        <td>{{ $order->placed_at->timezone('Asia/Colombo')->format('d M Y') }}</td>
        <td>@lkr($order->total)</td>
        <td><span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
        <td><a href="{{ route('account.orders.show', $order) }}">View</a></td>
    </tr>
    @endforeach
    </tbody>
</table>
<div style="margin-top:1.25rem;">{{ $orders->links() }}</div>
@else
<div class="empty-state">
    <div class="empty-icon">📦</div>
    <p>You haven't placed any orders yet.</p>
    <a href="{{ route('home') }}" class="btn btn-primary">Start shopping</a>
</div>
@endif
@endsection
