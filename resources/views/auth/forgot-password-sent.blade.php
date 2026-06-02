@extends('layouts.storefront')
@section('title', 'Reset Link Sent — TechBits')
@section('content')
<div class="auth-card" style="text-align:center;">
    <div style="font-size:2.5rem;margin-bottom:.75rem;">📧</div>
    <h1>Check your email</h1>
    <p style="color:var(--text-muted);margin:1rem 0;">If this email is associated with an active account, a reset link has been sent.</p>
    <a href="{{ route('login') }}" class="btn btn-primary">← Back to login</a>
</div>
@endsection
