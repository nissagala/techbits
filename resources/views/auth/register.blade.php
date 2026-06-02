@extends('layouts.storefront')
@section('title', 'Create Account — TechBits')

@section('content')
<div class="auth-card">
    <h1>Create an account</h1>

    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('register.submit') }}">
        @csrf
        <div class="form-group">
            <label>Full Name</label>
            <input class="form-control" type="text" name="name" value="{{ old('name') }}" maxlength="100" required>
            @error('name')<div class="field-error">{{ $message }}</div>@enderror
            <div class="field-hint">2–100 characters. Letters, spaces, dots, hyphens only.</div>
        </div>
        <div class="form-group">
            <label>Email Address</label>
            <input class="form-control" type="email" name="email" value="{{ old('email') }}" maxlength="254" required>
            @error('email')<div class="field-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label>Contact Number</label>
            <input class="form-control" type="text" name="phone" value="{{ old('phone') }}" placeholder="0771234567" required>
            @error('phone')<div class="field-error">{{ $message }}</div>@enderror
            <div class="field-hint">Sri Lankan format: 0771234567 or +94771234567</div>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input class="form-control" type="password" name="password" minlength="8" maxlength="64" required>
            @error('password')<div class="field-error">{{ $message }}</div>@enderror
            <div class="field-hint">8–64 characters. Must include at least one letter and one number.</div>
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input class="form-control" type="password" name="password_confirmation" required>
            @error('password_confirmation')<div class="field-error">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;">Create account</button>
    </form>
    <p style="text-align:center;margin-top:1rem;font-size:.88rem;">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
</div>
@endsection
