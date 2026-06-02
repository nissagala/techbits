@extends('layouts.storefront')
@section('title', 'Set New Password — TechBits')
@section('content')
<div class="auth-card">
    <h1>Set a new password</h1>

    @if($errors->has('token'))
        <div class="alert alert-error">
            {{ $errors->first('token') }}
            <br><a href="{{ route('password.request') }}">Request a new reset link</a>
        </div>
    @endif
    @if($errors->any() && !$errors->has('token'))
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">
        <div class="form-group">
            <label>New Password</label>
            <input class="form-control" type="password" name="password" minlength="8" maxlength="64" required autofocus>
            <div class="field-hint">8–64 characters with at least one letter and one number.</div>
        </div>
        <div class="form-group">
            <label>Confirm New Password</label>
            <input class="form-control" type="password" name="password_confirmation" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;">Set password</button>
    </form>
</div>
@endsection
