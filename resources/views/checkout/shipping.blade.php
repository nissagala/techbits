@extends('layouts.storefront')
@section('title', 'Checkout — Shipping — TechBits')
@section('content')
<div class="steps">
    <div class="step active">1. Shipping</div>
    <div class="step">2. Payment</div>
    <div class="step">3. Review</div>
</div>
@if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
<h2 style="font-size:1.1rem;font-weight:700;margin-bottom:1rem;">Select a shipping address</h2>
<form method="POST" action="{{ route('checkout.shipping.save') }}">
@csrf
@if($addresses->count())
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1rem;margin-bottom:1.5rem;">
@foreach($addresses as $addr)
<label style="border:2px solid var(--border);border-radius:8px;padding:1rem;cursor:pointer;display:block;">
    <input type="radio" name="address_id" value="{{ $addr->id }}" {{ ($default && $default->id === $addr->id) ? 'checked' : '' }} style="margin-bottom:.4rem;">
    @if($addr->is_default)<span class="badge badge-active" style="margin-left:.4rem;">Default</span>@endif
    <div style="font-size:.88rem;line-height:1.7;margin-top:.3rem;">
        <strong>{{ $addr->recipient }}</strong><br>
        {{ $addr->line1 }}, {{ $addr->city }}<br>
        {{ $addr->district }} {{ $addr->postal_code }}
    </div>
</label>
@endforeach
</div>
@endif

<details style="margin-bottom:1.5rem;border:1px solid var(--border);border-radius:8px;padding:1rem;">
    <summary style="cursor:pointer;font-weight:500;">+ Add a new address</summary>
    <div style="margin-top:1rem;display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
        <div class="form-group" style="grid-column:1/-1"><label>Recipient *</label><input class="form-control" type="text" name="new_address[recipient]" maxlength="100"></div>
        <div class="form-group" style="grid-column:1/-1"><label>Address Line 1 *</label><input class="form-control" type="text" name="new_address[line1]" maxlength="200"></div>
        <div class="form-group" style="grid-column:1/-1"><label>Address Line 2</label><input class="form-control" type="text" name="new_address[line2]" maxlength="200"></div>
        <div class="form-group"><label>City *</label><input class="form-control" type="text" name="new_address[city]" maxlength="50"></div>
        <div class="form-group"><label>District *</label>
            <select class="form-control" name="new_address[district]">
                <option value="">Select…</option>
                @foreach(config('districts') as $d)<option>{{ $d }}</option>@endforeach
            </select>
        </div>
        <div class="form-group"><label>Postal Code *</label><input class="form-control" type="text" name="new_address[postal_code]" maxlength="5" pattern="\d{5}"></div>
        <div class="form-group"><label>Phone *</label><input class="form-control" type="text" name="new_address[phone]"></div>
        <div style="grid-column:1/-1"><label><input type="checkbox" name="new_address[is_default]" value="1"> Set as default</label></div>
    </div>
</details>

<div style="display:flex;gap:.75rem;">
    <button type="submit" class="btn btn-primary">Continue to payment →</button>
    <a href="{{ route('cart.show') }}" class="btn btn-secondary">← Back to cart</a>
</div>
</form>
@endsection
