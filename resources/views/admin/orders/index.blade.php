@extends('layouts.admin')
@section('title', 'Orders — TechBits Admin')
@section('content')
<h1>Orders</h1>
<form method="GET" action="{{ route('admin.orders.index') }}" style="display:flex;gap:.75rem;margin-bottom:1rem;flex-wrap:wrap;">
    <select class="form-control" style="width:auto;padding:.4rem .75rem;" name="status">
        <option value="">All statuses</option>
        @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
            <option value="{{ $s }}" {{ request('status', 'pending') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
    <input class="form-control" style="width:200px;padding:.4rem .75rem;" type="text" name="search" value="{{ request('search') }}" placeholder="Order # or customer name…">
    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
</form>
<table class="data-table">
    <thead><tr><th>Order #</th><th>Customer</th><th>Date</th><th>Total</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @forelse($orders as $o)
    <tr>
        <td><strong>{{ $o->order_number }}</strong></td>
        <td>{{ $o->user->name }}</td>
        <td>{{ $o->placed_at->timezone('Asia/Colombo')->format('d M Y') }}</td>
        <td>@lkr($o->total)</td>
        <td><span class="badge badge-{{ $o->status }}">{{ ucfirst($o->status) }}</span></td>
        <td><a href="{{ route('admin.orders.show', $o) }}" class="btn btn-secondary btn-sm">View</a></td>
    </tr>
    @empty
    <tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:2rem;">No orders found.</td></tr>
    @endforelse
    </tbody>
</table>
<div style="margin-top:1rem;">{{ $orders->links() }}</div>
@endsection
