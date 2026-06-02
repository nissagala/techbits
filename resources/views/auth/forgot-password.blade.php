@extends('layouts.storefront')
@section('title', 'Forgot Password — TechBits')
@section('content')
<div class="auth-card">
    <h1>Forgot your password?</h1>
    <p style="color:var(--text-muted);margin-bottom:1.25rem;">Enter your email and we'll send a reset link if your account exists.</p>
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-group">
            <label>Email Address</label>
            <input class="form-control" type="email" name="email" required autofocus>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;margin-bottom:.75rem;">Send reset link</button>
    </form>
    <p style="text-align:center;font-size:.88rem;"><a href="{{ route('login') }}">← Back to login</a></p>
</div>
@endsection
