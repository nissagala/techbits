<!DOCTYPE html>
<html><body style="font-family:sans-serif;color:#1f2937;max-width:560px;margin:0 auto;padding:2rem;">
<h2 style="color:#1a6fc4;">Your order {{ $order->order_number }} is now {{ ucfirst($newStatus) }}</h2>
@php $messages = [
    'processing' => 'Great news! We\'re preparing your order.',
    'shipped'    => 'Your order is on its way.',
    'delivered'  => 'Your order has been delivered. We hope you enjoy your purchase!',
    'cancelled'  => 'Your order has been cancelled. Stock has been restored.',
]; @endphp
<p>{{ $messages[$newStatus] ?? 'Your order status has been updated.' }}</p>
<p><strong>Order:</strong> {{ $order->order_number }}<br>
<strong>New status:</strong> {{ ucfirst($newStatus) }}</p>
<p><a href="{{ url('/account/orders/'.$order->id) }}" style="color:#1a6fc4;">View order details →</a></p>
<hr style="border:none;border-top:1px solid #e5e7eb;margin:1.5rem 0;">
<p style="font-size:.8rem;color:#6b7280;">TechBits — Computer accessories, delivered across Sri Lanka.</p>
</body></html>
