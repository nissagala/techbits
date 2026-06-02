<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TechBits — Computer Accessories Sri Lanka')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('head')
</head>
<body>

{{-- Header --}}
<header class="site-header">
    <div class="header-top">
        <a href="{{ route('home') }}" class="logo">TechBits</a>

        <form class="search-form" action="{{ route('search') }}" method="GET">
            <input type="text" name="q" placeholder="Search products…" value="{{ request('q') }}" maxlength="100">
            <button type="submit">Search</button>
        </form>

        <div class="header-actions">
            <a href="{{ route('cart.show') }}" class="cart-link">
                🛒 Cart
                @php $cartCount = \App\Http\Controllers\CartController::cartCount(); @endphp
                @if($cartCount > 0)
                    <span class="cart-badge">{{ $cartCount }}</span>
                @endif
            </a>

            @if(Auth::check() && Auth::user()->role === 'customer')
                <span style="font-size:.9rem">{{ Auth::user()->name }}</span>
                <a href="{{ route('account.dashboard') }}">Account</a>
                <a href="{{ route('account.orders.index') }}">My Orders</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-secondary">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}" class="btn btn-sm btn-primary">Register</a>
            @endif
        </div>
    </div>

    <nav class="header-nav">
        <div class="nav-inner">
            @foreach(\App\Models\Category::orderBy('name')->limit(10)->get() as $cat)
                <a href="{{ route('category.show', $cat) }}">{{ $cat->name }}</a>
            @endforeach
        </div>
    </nav>
</header>

{{-- Flash messages --}}
<div class="container" style="margin-top:.75rem;">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    @if(session('cart_capped'))
        <div class="alert alert-warning">Some cart quantities were capped to available stock during merge.</div>
    @endif
</div>

{{-- Main content --}}
<main style="padding:1.5rem 1rem; max-width:var(--max-w); margin:0 auto;">
    @yield('content')
</main>

{{-- Footer --}}
<footer class="site-footer">
    <div class="footer-grid">
        <div>
            <strong style="font-size:1.1rem;">TechBits</strong>
            <p style="color:var(--text-muted);font-size:.88rem;margin-top:.5rem;">Computer accessories, delivered across Sri Lanka.</p>
            <div class="footer-links" style="margin-top:1rem;">
                <a href="{{ route('about') }}">About Us</a>
                <a href="{{ route('terms') }}">Terms &amp; Conditions</a>
                <a href="{{ route('privacy') }}">Privacy Policy</a>
                <a href="{{ route('shipping') }}">Shipping &amp; Delivery</a>
                <a href="{{ route('faq') }}">FAQ</a>
                <a href="{{ route('contact') }}">Contact Us</a>
            </div>
        </div>
        <div>
            <p class="copyright">&copy; {{ date('Y') }} TechBits. All rights reserved.</p>
        </div>
    </div>
    <p class="disclaimer">TechBits is an academic project. No real transactions are processed.</p>
</footer>

{{-- Toast container --}}
<div class="toast-container" id="toastContainer"></div>

<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
