@extends('layouts.admin')
@section('title', 'Dashboard — TechBits Admin')
@section('content')
<h1>Dashboard</h1>

{{-- Counter cards --}}
<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:1rem;margin-bottom:2rem;">
    @foreach([
        ['label'=>'Products','value'=>$totalProducts,'icon'=>'📦'],
        ['label'=>'Total Orders','value'=>$totalOrders,'icon'=>'🛒'],
        ['label'=>'Customers','value'=>$totalCustomers,'icon'=>'👥'],
        ['label'=>'Unread Messages','value'=>$unreadMessages,'icon'=>'✉️'],
    ] as $card)
    <div style="background:#fff;border:1px solid var(--border);border-radius:8px;padding:1.25rem;text-align:center;">
        <div style="font-size:1.8rem;">{{ $card['icon'] }}</div>
        <div style="font-size:1.5rem;font-weight:800;margin:.25rem 0;">{{ $card['value'] }}</div>
        <div style="font-size:.82rem;color:var(--text-muted);">{{ $card['label'] }}</div>
    </div>
    @endforeach
    <div style="background:#fff;border:1px solid var(--border);border-radius:8px;padding:1.25rem;">
        <div style="font-size:.82rem;font-weight:600;margin-bottom:.5rem;color:var(--text-muted);">Orders by Status</div>
        @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
        <div style="display:flex;justify-content:space-between;font-size:.83rem;margin-bottom:.2rem;">
            <span class="badge badge-{{ $s }}">{{ ucfirst($s) }}</span>
            <strong>{{ $ordersByStatus[$s] ?? 0 }}</strong>
        </div>
        @endforeach
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
{{-- Recent orders --}}
<div>
    <h2 style="font-size:1rem;font-weight:700;margin-bottom:.75rem;">Recent Orders</h2>
    <table class="data-table">
        <thead><tr><th>Order #</th><th>Customer</th><th>Date</th><th>Status</th></tr></thead>
        <tbody>
        @foreach($recentOrders as $o)
        <tr onclick="location.href='{{ route('admin.orders.show', $o) }}'" style="cursor:pointer;">
            <td>{{ $o->order_number }}</td>
            <td>{{ $o->user->name }}</td>
            <td>{{ $o->placed_at->timezone('Asia/Colombo')->format('d M Y') }}</td>
            <td><span class="badge badge-{{ $o->status }}">{{ ucfirst($o->status) }}</span></td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
{{-- Recent messages --}}
<div>
    <h2 style="font-size:1rem;font-weight:700;margin-bottom:.75rem;">Recent Messages</h2>
    <table class="data-table">
        <thead><tr><th>From</th><th>Subject</th><th>Date</th></tr></thead>
        <tbody>
        @foreach($recentMessages as $m)
        <tr onclick="location.href='{{ route('admin.messages.show', $m) }}'" style="cursor:pointer;{{ !$m->is_read ? 'font-weight:600;' : '' }}">
            <td>{{ $m->sender_name }}</td>
            <td>{{ Str::limit($m->subject, 30) }}</td>
            <td>{{ $m->created_at->timezone('Asia/Colombo')->format('d M Y') }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
</div>
@endsection
