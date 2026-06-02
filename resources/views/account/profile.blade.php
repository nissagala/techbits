@extends('layouts.storefront')
@section('title', 'My Profile — TechBits')
@section('content')
<h1 style="font-size:1.3rem;font-weight:700;margin-bottom:1.25rem;">My Profile</h1>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
<div style="max-width:480px;">
<form method="POST" action="{{ route('account.profile.update') }}">
    @csrf
    <div class="form-group">
        <label>Full Name</label>
        <input class="form-control" type="text" name="name" value="{{ old('name', $user->name) }}" maxlength="100" required>
        @error('name')<div class="field-error">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <label>Email Address</label>
        <input class="form-control" type="email" value="{{ $user->email }}" readonly style="background:var(--surface);cursor:not-allowed;">
        <div class="field-hint">Email cannot be changed.</div>
    </div>
    <div class="form-group">
        <label>Contact Number</label>
        <input class="form-control" type="text" name="phone" value="{{ old('phone', $user->phone) }}" required>
        @error('phone')<div class="field-error">{{ $message }}</div>@enderror
    </div>
    <button type="submit" class="btn btn-primary">Save changes</button>
    <a href="{{ route('account.dashboard') }}" style="margin-left:1rem;font-size:.88rem;">Cancel</a>
</form>
</div>
@endsection
