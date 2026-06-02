@extends('layouts.storefront')
@section('title', 'Log In — TechBits')

@section('content')
<div class="auth-card">
    <h1>Log in</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}">
        @csrf
        <div class="form-group">
            <label>Email Address</label>
            <input class="form-control" type="email" name="email" value="{{ old('email') }}" maxlength="254" required autofocus>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input class="form-control" type="password" name="password" required>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <label style="display:flex;align-items:center;gap:.4rem;font-size:.88rem;font-weight:400;">
                <input type="checkbox" name="remember" value="1"> Remember me
            </label>
            <a href="{{ route('password.request') }}" style="font-size:.88rem;">Forgot password?</a>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;">Log in</button>
    </form>
    <p style="text-align:center;margin-top:1rem;font-size:.88rem;">Don't have an account? <a href="{{ route('register') }}">Register</a></p>
</div>
@endsection
