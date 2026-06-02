@extends('layouts.admin')
@section('title', 'Messages — TechBits Admin')
@section('content')
<h1>Contact Messages</h1>
<table class="data-table">
    <thead><tr><th>From</th><th>Email</th><th>Subject</th><th>Date</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @forelse($messages as $m)
    <tr class="{{ !$m->is_read ? 'unread' : '' }}" style="cursor:pointer;" onclick="location.href='{{ route('admin.messages.show', $m) }}'">
        <td>{{ $m->sender_name }}</td>
        <td>{{ $m->sender_email }}</td>
        <td>{{ Str::limit($m->subject, 40) }}</td>
        <td>{{ $m->created_at->timezone('Asia/Colombo')->format('d M Y, H:i') }}</td>
        <td>@if(!$m->is_read)<span class="badge badge-processing">Unread</span>@else<span style="color:var(--text-muted);font-size:.82rem;">Read</span>@endif</td>
        <td><a href="{{ route('admin.messages.show', $m) }}" class="btn btn-secondary btn-sm" onclick="event.stopPropagation()">View</a></td>
    </tr>
    @empty
    <tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:2rem;">No messages yet.</td></tr>
    @endforelse
    </tbody>
</table>
<div style="margin-top:1rem;">{{ $messages->links() }}</div>
@endsection
