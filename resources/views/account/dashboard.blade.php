@extends('layouts.storefront')
@section('title', 'My Account — TechBits')
@section('content')
<h1 style="font-size:1.4rem;font-weight:700;margin-bottom:1.5rem;">Welcome, {{ $user->name }}</h1>
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;">
    <a href="{{ route('account.profile') }}" style="border:1px solid var(--border);border-radius:8px;padding:1.5rem;text-align:center;color:var(--text);display:block;">
        <div style="font-size:2rem;margin-bottom:.5rem;">👤</div>
        <div style="font-weight:600;">My Profile</div>
        <div style="font-size:.82rem;color:var(--text-muted);">Edit name &amp; contact</div>
    </a>
    <a href="{{ route('account.addresses.index') }}" style="border:1px solid var(--border);border-radius:8px;padding:1.5rem;text-align:center;color:var(--text);display:block;">
        <div style="font-size:2rem;margin-bottom:.5rem;">📍</div>
        <div style="font-weight:600;">My Addresses</div>
        <div style="font-size:.82rem;color:var(--text-muted);">Manage shipping addresses</div>
    </a>
    <a href="{{ route('account.orders.index') }}" style="border:1px solid var(--border);border-radius:8px;padding:1.5rem;text-align:center;color:var(--text);display:block;">
        <div style="font-size:2rem;margin-bottom:.5rem;">📦</div>
        <div style="font-weight:600;">My Orders</div>
        <div style="font-size:.82rem;color:var(--text-muted);">View order history</div>
    </a>
    <a href="{{ route('account.password') }}" style="border:1px solid var(--border);border-radius:8px;padding:1.5rem;text-align:center;color:var(--text);display:block;">
        <div style="font-size:2rem;margin-bottom:.5rem;">🔒</div>
        <div style="font-weight:600;">Change Password</div>
        <div style="font-size:.82rem;color:var(--text-muted);">Update your password</div>
    </a>
    <form method="POST" action="{{ route('logout') }}" style="border:1px solid var(--border);border-radius:8px;padding:1.5rem;text-align:center;">
        @csrf
        <button type="submit" style="background:none;border:none;cursor:pointer;width:100%;">
            <div style="font-size:2rem;margin-bottom:.5rem;">🚪</div>
            <div style="font-weight:600;color:var(--error);">Logout</div>
        </button>
    </form>
</div>
@endsection
