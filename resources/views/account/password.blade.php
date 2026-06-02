@extends('layouts.storefront')
@section('title', 'Change Password — TechBits')
@section('content')
<h1 style="font-size:1.3rem;font-weight:700;margin-bottom:1.25rem;">Change Password</h1>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
<div style="max-width:420px;">
<form method="POST" action="{{ route('account.password.update') }}">
    @csrf
    <div class="form-group">
        <label>Current Password</label>
        <input class="form-control" type="password" name="current_password" required>
        @error('current_password')<div class="field-error">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <label>New Password</label>
        <input class="form-control" type="password" name="password" minlength="8" maxlength="64" required>
        <div class="field-hint">8–64 characters with at least one letter and one number.</div>
        @error('password')<div class="field-error">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <label>Confirm New Password</label>
        <input class="form-control" type="password" name="password_confirmation" required>
    </div>
    <button type="submit" class="btn btn-primary">Update password</button>
    <a href="{{ route('account.dashboard') }}" style="margin-left:1rem;font-size:.88rem;">Cancel</a>
</form>
</div>
@endsection
