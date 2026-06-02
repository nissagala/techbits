@extends('layouts.storefront')
@section('title', 'Checkout — Payment — TechBits')
@section('content')
<div class="steps">
    <div class="step">1. Shipping</div>
    <div class="step active">2. Payment</div>
    <div class="step">3. Review</div>
</div>
<div class="payment-notice">⚠️ Simulated payment — do not enter real card details. No real payment is processed.</div>
@if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
<div style="max-width:460px;">
<form method="POST" action="{{ route('checkout.payment.save') }}">
    @csrf
    <div class="form-group">
        <label>Cardholder Name *</label>
        <input class="form-control" type="text" name="cardholder" value="{{ old('cardholder') }}" maxlength="100" required placeholder="As shown on card">
        @error('cardholder')<div class="field-error">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <label>Card Number *</label>
        <input class="form-control" type="text" name="card_number" value="{{ old('card_number') }}" maxlength="19" placeholder="1234 5678 9012 3456" required>
        @error('card_number')<div class="field-error">{{ $message }}</div>@enderror
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
        <div class="form-group">
            <label>Expiry *</label>
            <input class="form-control" type="text" name="expiry" value="{{ old('expiry') }}" placeholder="MM/YY" maxlength="5" required>
            @error('expiry')<div class="field-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label>CVV *</label>
            <input class="form-control" type="text" name="cvv" maxlength="4" placeholder="3–4 digits" required>
            @error('cvv')<div class="field-error">{{ $message }}</div>@enderror
        </div>
    </div>
    <div style="display:flex;gap:.75rem;">
        <button type="submit" class="btn btn-primary">Continue to review →</button>
        <a href="{{ route('checkout.shipping') }}" class="btn btn-secondary">← Back</a>
    </div>
</form>
</div>
@endsection
