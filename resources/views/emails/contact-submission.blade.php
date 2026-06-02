<!DOCTYPE html>
<html><body style="font-family:sans-serif;color:#1f2937;max-width:560px;margin:0 auto;padding:2rem;">
<h2 style="color:#1a6fc4;">TechBits Contact — {{ $message->subject }}</h2>
<table style="font-size:.9rem;margin-bottom:1.25rem;border-collapse:collapse;">
    <tr><td style="padding:.3rem .75rem .3rem 0;font-weight:600;">From</td><td>{{ $message->sender_name }} &lt;{{ $message->sender_email }}&gt;</td></tr>
    <tr><td style="padding:.3rem .75rem .3rem 0;font-weight:600;">Subject</td><td>{{ $message->subject }}</td></tr>
    <tr><td style="padding:.3rem .75rem .3rem 0;font-weight:600;">Received</td><td>{{ $message->created_at->timezone('Asia/Colombo')->format('d M Y, H:i') }}</td></tr>
</table>
<div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;padding:1rem;white-space:pre-line;font-size:.9rem;">{{ $message->message }}</div>
<hr style="border:none;border-top:1px solid #e5e7eb;margin:1.5rem 0;">
<p style="font-size:.8rem;color:#6b7280;">TechBits Admin — Contact form submission.</p>
</body></html>
