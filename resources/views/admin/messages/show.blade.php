@extends('layouts.admin')
@section('title', 'Message — TechBits Admin')
@section('content')
<a href="{{ route('admin.messages.index') }}" style="font-size:.88rem;display:inline-block;margin-bottom:1rem;">← Messages</a>
<div style="background:#fff;border:1px solid var(--border);border-radius:8px;padding:1.5rem;max-width:680px;">
    <table style="font-size:.9rem;margin-bottom:1.25rem;border-collapse:collapse;">
        <tr><td style="padding:.3rem .75rem .3rem 0;font-weight:600;color:var(--text-muted);">From</td><td>{{ $message->sender_name }} &lt;{{ $message->sender_email }}&gt;</td></tr>
        <tr><td style="padding:.3rem .75rem .3rem 0;font-weight:600;color:var(--text-muted);">Subject</td><td>{{ $message->subject }}</td></tr>
        <tr><td style="padding:.3rem .75rem .3rem 0;font-weight:600;color:var(--text-muted);">Received</td><td>{{ $message->created_at->timezone('Asia/Colombo')->format('d M Y, H:i') }}</td></tr>
    </table>
    <div style="background:var(--surface);border-radius:6px;padding:1rem;font-size:.9rem;line-height:1.8;white-space:pre-line;margin-bottom:1.5rem;">{{ $message->message }}</div>
    <div style="display:flex;gap:.75rem;align-items:center;">
        <form method="POST" action="{{ route('admin.messages.unread', $message) }}">
            @csrf
            <button type="submit" class="btn btn-secondary btn-sm">Mark as unread</button>
        </form>
        <span style="font-size:.82rem;color:var(--text-muted);">Reply via your email client to {{ $message->sender_email }}</span>
    </div>
</div>
@endsection
