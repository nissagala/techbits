<!DOCTYPE html>
<html><body style="font-family:sans-serif;color:#1f2937;max-width:600px;margin:0 auto;padding:2rem;">
<h2 style="color:#1a6fc4;">Order Confirmed — {{ $order->order_number }}</h2>
<p>Thank you for your order! Here's your summary:</p>
<table style="width:100%;border-collapse:collapse;margin:1.25rem 0;font-size:.9rem;">
    <thead><tr style="background:#f9fafb;"><th style="text-align:left;padding:.5rem;">Product</th><th style="text-align:right;padding:.5rem;">Qty</th><th style="text-align:right;padding:.5rem;">Price</th></tr></thead>
    <tbody>
    @foreach($order->items as $item)
    <tr style="border-bottom:1px solid #e5e7eb;">
        <td style="padding:.5rem;">{{ $item->product_name }}</td>
        <td style="padding:.5rem;text-align:right;">{{ $item->quantity }}</td>
        <td style="padding:.5rem;text-align:right;">LKR {{ number_format($item->line_total) }}</td>
    </tr>
    @endforeach
    <tr><td colspan="2" style="padding:.5rem;text-align:right;">Subtotal</td><td style="padding:.5rem;text-align:right;">LKR {{ number_format($order->subtotal) }}</td></tr>
    <tr><td colspan="2" style="padding:.5rem;text-align:right;">Shipping</td><td style="padding:.5rem;text-align:right;">LKR {{ number_format($order->shipping_fee) }}</td></tr>
    <tr style="font-weight:700;"><td colspan="2" style="padding:.5rem;text-align:right;">Total</td><td style="padding:.5rem;text-align:right;">LKR {{ number_format($order->total) }}</td></tr>
    </tbody>
</table>
@php $a = $order->shipping_address; @endphp
<p><strong>Shipping to:</strong> {{ $a['recipient'] }}, {{ $a['line1'] }}, {{ $a['city'] }}, {{ $a['district'] }}</p>
<p><strong>Payment:</strong> Card ending ****{{ $order->payment_last4 }}</p>
<p>Estimated delivery: 3–7 working days.</p>
<p><a href="{{ url('/account/orders/'.$order->id) }}" style="color:#1a6fc4;">View your order in your account →</a></p>
<hr style="border:none;border-top:1px solid #e5e7eb;margin:1.5rem 0;">
<p style="font-size:.8rem;color:#6b7280;">TechBits — Computer accessories, delivered across Sri Lanka.<br><em>This is an academic project. No real transactions are processed.</em></p>
</body></html>
