@extends('layouts.storefront')
@section('title', isset($address) ? 'Edit Address — TechBits' : 'Add Address — TechBits')
@section('content')
<h1 style="font-size:1.3rem;font-weight:700;margin-bottom:1.25rem;">{{ isset($address) ? 'Edit Address' : 'Add New Address' }}</h1>
@if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
<div style="max-width:520px;">
<form method="POST" action="{{ isset($address) ? route('account.addresses.update', $address) : route('account.addresses.store') }}">
    @csrf
    @if(isset($address)) @method('PUT') @endif
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
        <div class="form-group" style="grid-column:1/-1">
            <label>Label (optional)</label>
            <input class="form-control" type="text" name="label" value="{{ old('label', $address->label ?? '') }}" maxlength="30" placeholder="Home, Office…">
        </div>
        <div class="form-group" style="grid-column:1/-1">
            <label>Recipient Name *</label>
            <input class="form-control" type="text" name="recipient" value="{{ old('recipient', $address->recipient ?? '') }}" maxlength="100" required>
        </div>
        <div class="form-group" style="grid-column:1/-1">
            <label>Address Line 1 *</label>
            <input class="form-control" type="text" name="line1" value="{{ old('line1', $address->line1 ?? '') }}" maxlength="200" required>
        </div>
        <div class="form-group" style="grid-column:1/-1">
            <label>Address Line 2</label>
            <input class="form-control" type="text" name="line2" value="{{ old('line2', $address->line2 ?? '') }}" maxlength="200">
        </div>
        <div class="form-group">
            <label>City *</label>
            <input class="form-control" type="text" name="city" value="{{ old('city', $address->city ?? '') }}" maxlength="50" required>
        </div>
        <div class="form-group">
            <label>District *</label>
            <select class="form-control" name="district" required>
                <option value="">Select district…</option>
                @foreach(config('districts') as $d)
                    <option value="{{ $d }}" {{ old('district', $address->district ?? '') === $d ? 'selected' : '' }}>{{ $d }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Postal Code *</label>
            <input class="form-control" type="text" name="postal_code" value="{{ old('postal_code', $address->postal_code ?? '') }}" maxlength="5" pattern="\d{5}" required>
        </div>
        <div class="form-group">
            <label>Contact Number *</label>
            <input class="form-control" type="text" name="phone" value="{{ old('phone', $address->phone ?? '') }}" required>
        </div>
        <div style="grid-column:1/-1;margin-bottom:1rem;">
            <label style="display:flex;align-items:center;gap:.4rem;font-weight:400;cursor:pointer;">
                <input type="checkbox" name="is_default" value="1" {{ old('is_default', $address->is_default ?? false) ? 'checked' : '' }}>
                Set as default address
            </label>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Save address</button>
    <a href="{{ route('account.addresses.index') }}" style="margin-left:1rem;font-size:.88rem;">Cancel</a>
</form>
</div>
@endsection
