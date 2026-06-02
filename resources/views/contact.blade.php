@extends('layouts.storefront')
@section('title', 'Contact Us — TechBits')
@section('content')
<div style="max-width:560px;margin:0 auto;">
<h1 style="font-size:1.4rem;font-weight:700;margin-bottom:1.25rem;">Contact Us</h1>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('contact.submit') }}">
    @csrf
    <div class="form-group">
        <label>Your Name *</label>
        @if(Auth::check() && Auth::user()->isCustomer())
            <input class="form-control" type="text" value="{{ Auth::user()->name }}" readonly style="background:var(--surface);">
            <input type="hidden" name="sender_name" value="{{ Auth::user()->name }}">
        @else
            <input class="form-control" type="text" name="sender_name" value="{{ old('sender_name') }}" maxlength="100" required>
        @endif
    </div>
    <div class="form-group">
        <label>Email Address *</label>
        @if(Auth::check() && Auth::user()->isCustomer())
            <input class="form-control" type="email" value="{{ Auth::user()->email }}" readonly style="background:var(--surface);">
            <input type="hidden" name="sender_email" value="{{ Auth::user()->email }}">
        @else
            <input class="form-control" type="email" name="sender_email" value="{{ old('sender_email') }}" maxlength="254" required>
        @endif
    </div>
    <div class="form-group">
        <label>Subject *</label>
        <input class="form-control" type="text" name="subject" value="{{ old('subject') }}" maxlength="150" required>
        <div class="field-hint">3–150 characters.</div>
    </div>
    <div class="form-group">
        <label>Message *</label>
        <textarea class="form-control" name="message" rows="6" maxlength="2000" required>{{ old('message') }}</textarea>
        <div class="field-hint">10–2000 characters.</div>
    </div>
    <button type="submit" class="btn btn-primary">Send message</button>
</form>
</div>
@endsection
