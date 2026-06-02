<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TechBits Admin')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

<div class="admin-wrap">
    {{-- Sidebar --}}
    <aside class="admin-sidebar">
        <div class="brand">TechBits Admin</div>
        <nav>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">Products</a>
            <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">Categories</a>
            <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">Orders</a>
            <a href="{{ route('admin.customers.index') }}" class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">Customers</a>
            <a href="{{ route('admin.messages.index') }}" class="{{ request()->routeIs('admin.messages.*') ? 'active' : '' }}">Messages</a>
        </nav>
        <div style="padding:1rem 1.25rem; border-top:1px solid rgba(255,255,255,.08);">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" style="background:none;border:none;color:#94a3b8;cursor:pointer;font-size:.88rem;padding:0;">Logout</button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="admin-body">
        <div class="admin-topbar">
            <span>Logged in as: <strong>{{ Auth::user()?->name ?? 'Admin' }}</strong></span>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-secondary">Logout</button>
            </form>
        </div>

        <main class="admin-content">
            @if(session('success'))
                <div class="alert alert-success" style="margin-bottom:1rem;">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error" style="margin-bottom:1rem;">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error" style="margin-bottom:1rem;">
                    @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
