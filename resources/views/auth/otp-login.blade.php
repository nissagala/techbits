@extends('layouts.storefront')
@section('title', 'Confirm Login — TechBits')

@section('content')
<div class="auth-card">
    <h1>Confirm it's you</h1>
    <p style="color:var(--text-muted);margin-bottom:1.25rem;">We sent a login code to <strong>{{ $maskedEmail }}</strong>. Enter it to continue.</p>
    <p style="font-size:.8rem;color:var(--text-muted);margin-bottom:1rem;">If you close this page your login attempt will be cancelled.</p>

    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('login.verify.submit') }}">
        @csrf
        <div class="form-group">
            <label>Login Code</label>
            <input class="form-control otp-input" type="text" name="otp" inputmode="numeric" maxlength="6" pattern="\d{6}" placeholder="000000" autofocus required>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;margin-bottom:.75rem;">Verify</button>
    </form>
    <div style="text-align:center;">
        <form method="POST" action="{{ route('login.resend') }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-secondary btn-sm">Resend code</button>
        </form>
        <p style="font-size:.78rem;color:var(--text-muted);margin-top:.5rem;">Code expires in 10 minutes.</p>
    </div>
</div>
@endsection
