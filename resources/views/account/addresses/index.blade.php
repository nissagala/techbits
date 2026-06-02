@extends('layouts.storefront')
@section('title', 'My Addresses — TechBits')
@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;">
    <h1 style="font-size:1.3rem;font-weight:700;">My Addresses</h1>
    <a href="{{ route('account.addresses.create') }}" class="btn btn-primary btn-sm">+ Add new address</a>
</div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif

@if($addresses->count())
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1rem;">
@foreach($addresses as $addr)
<div style="border:2px solid {{ $addr->is_default ? 'var(--primary)' : 'var(--border)' }};border-radius:8px;padding:1.1rem;position:relative;">
    @if($addr->is_default)<span class="badge badge-active" style="position:absolute;top:.75rem;right:.75rem;">Default</span>@endif
    @if($addr->label)<div style="font-weight:700;margin-bottom:.25rem;">{{ $addr->label }}</div>@endif
    <div style="font-size:.9rem;line-height:1.7;">
        <strong>{{ $addr->recipient }}</strong><br>
        {{ $addr->line1 }}<br>
        @if($addr->line2){{ $addr->line2 }}<br>@endif
        {{ $addr->city }}, {{ $addr->district }} {{ $addr->postal_code }}<br>
        {{ $addr->phone }}
    </div>
    <div style="display:flex;gap:.5rem;margin-top:.9rem;flex-wrap:wrap;">
        <a href="{{ route('account.addresses.edit', $addr) }}" class="btn btn-secondary btn-sm">Edit</a>
        @if(!$addr->is_default)
        <form method="POST" action="{{ route('account.addresses.default', $addr) }}" style="display:inline;">@csrf
            <button type="submit" class="btn btn-secondary btn-sm">Set default</button>
        </form>
        @endif
        <form method="POST" action="{{ route('account.addresses.destroy', $addr) }}" id="del-{{ $addr->id }}">@csrf @method('DELETE')
            <button type="button" class="btn btn-sm" style="color:var(--error);border:1px solid var(--error);"
                    onclick="if(confirm('Delete this address?')) document.getElementById('del-{{ $addr->id }}').submit()">Delete</button>
        </form>
    </div>
</div>
@endforeach
</div>
@else
<div class="empty-state">
    <div class="empty-icon">📍</div>
    <p>You don't have any saved addresses yet.</p>
    <a href="{{ route('account.addresses.create') }}" class="btn btn-primary">Add your first address</a>
</div>
@endif
@endsection
