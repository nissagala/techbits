@extends('layouts.admin')
@section('title', 'Customers — TechBits Admin')
@section('content')
<h1>Customers</h1>
<form method="GET" action="{{ route('admin.customers.index') }}" style="display:flex;gap:.75rem;margin-bottom:1rem;flex-wrap:wrap;">
    <input class="form-control" style="width:220px;padding:.4rem .75rem;" type="text" name="search" value="{{ request('search') }}" placeholder="Name or email…">
    <select class="form-control" style="width:auto;padding:.4rem .75rem;" name="status" onchange="this.form.submit()">
        <option value="">All statuses</option>
        <option value="unverified" {{ request('status')==='unverified' ? 'selected' : '' }}>Unverified</option>
        <option value="active" {{ request('status')==='active' ? 'selected' : '' }}>Active</option>
        <option value="inactive" {{ request('status')==='inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
</form>
<table class="data-table">
    <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Registered</th><th>Status</th><th>Action</th></tr></thead>
    <tbody>
    @forelse($customers as $c)
    <tr>
        <td>{{ $c->name }}</td>
        <td>{{ $c->email }}</td>
        <td>{{ $c->phone }}</td>
        <td>{{ $c->created_at->timezone('Asia/Colombo')->format('d M Y') }}</td>
        <td><span class="badge badge-{{ $c->status }}">{{ ucfirst($c->status) }}</span></td>
        <td>
            <form method="POST" action="{{ route('admin.customers.toggle', $c) }}"
                  onsubmit="return {{ $c->status==='active' ? "confirm('Deactivate this customer?')" : 'true' }}">
                @csrf
                <button type="submit" class="btn btn-sm {{ $c->status==='active' ? 'btn-danger' : 'btn-secondary' }}">
                    {{ $c->status === 'active' ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
        </td>
    </tr>
    @empty
    <tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:2rem;">No customers found.</td></tr>
    @endforelse
    </tbody>
</table>
<div style="margin-top:1rem;">{{ $customers->links() }}</div>
@endsection
