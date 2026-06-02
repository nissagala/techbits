@extends('layouts.storefront')
@section('title', 'Page Not Found — TechBits')
@section('content')
<div class="empty-state" style="padding:5rem 1rem;">
    <div class="empty-icon">🔍</div>
    <h1 style="font-size:1.5rem;font-weight:700;margin-bottom:.5rem;">{{ $message ?? 'Page not found' }}</h1>
    <p style="margin-bottom:1.5rem;">The page you're looking for doesn't exist or has been moved.</p>
    <a href="{{ route('home') }}" class="btn btn-primary">Back to home</a>
</div>
@endsection
