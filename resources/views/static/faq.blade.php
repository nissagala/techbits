@extends('layouts.storefront')
@section('title', 'FAQ — TechBits')
@section('content')
<div style="max-width:760px;margin:0 auto;">
<h1 style="font-size:1.5rem;font-weight:700;margin-bottom:1.5rem;">Frequently Asked Questions</h1>
@php $faqs = [
    ['Q' => 'How do I place an order?', 'A' => 'Browse our product catalog, add items to your cart, and proceed to checkout. You will need a registered account to complete a purchase.'],
    ['Q' => 'Can I browse without an account?', 'A' => 'Yes. You can browse all products and add them to your cart without registering. An account is only required when you are ready to check out.'],
    ['Q' => 'What payment methods do you accept?', 'A' => 'TechBits accepts card payments at checkout. Note: all payments on this platform are simulated — no real card transactions are processed.'],
    ['Q' => 'How much does shipping cost?', 'A' => 'A flat shipping fee of LKR 500 applies to every order, regardless of order size or delivery location within Sri Lanka.'],
    ['Q' => 'How long does delivery take?', 'A' => 'Orders are typically delivered within 3–7 working days from the date of placement.'],
    ['Q' => 'Can I cancel my order?', 'A' => 'Orders may be cancelled by the store administrator. Contact us via the Contact Us form if you need to request a cancellation.'],
    ['Q' => 'How do I track my order?', 'A' => 'Log in to your account and go to My Orders. You can view the current status (Pending, Processing, Shipped, Delivered) of each order.'],
    ['Q' => 'What if my product arrives damaged?', 'A' => 'Please contact us immediately via the Contact Us form with your order number and a description of the issue.'],
    ['Q' => 'How do I reset my password?', 'A' => 'Click "Forgot password?" on the login page and enter your email address. If your account is active, you will receive a password reset link.'],
    ['Q' => 'Who do I contact for support?', 'A' => 'Use the Contact Us form linked in the footer. We aim to respond within 1 business day.'],
]; @endphp

@foreach($faqs as $i => $faq)
<details style="border:1px solid var(--border);border-radius:6px;padding:.85rem 1rem;margin-bottom:.6rem;" {{ $i === 0 ? 'open' : '' }}>
    <summary style="font-weight:600;cursor:pointer;list-style:none;display:flex;justify-content:space-between;align-items:center;">
        {{ $faq['Q'] }}
        <span style="font-size:.8rem;color:var(--text-muted);">▼</span>
    </summary>
    <p style="margin-top:.75rem;color:var(--text-muted);font-size:.92rem;line-height:1.7;">{{ $faq['A'] }}</p>
</details>
@endforeach

<p style="font-size:.82rem;color:var(--text-muted);border-top:1px solid var(--border);padding-top:.75rem;margin-top:1.5rem;"><em>Illustrative academic content. TechBits is a coursework project for a Master of Information Security program.</em></p>
</div>
@endsection
