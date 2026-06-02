<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;

class MessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::orderByRaw('is_read ASC, created_at DESC')
            ->paginate(20);

        return view('admin.messages.index', compact('messages'));
    }

    public function show(ContactMessage $message)
    {
        $message->update(['is_read' => true]);
        return view('admin.messages.show', compact('message'));
    }

    public function markUnread(ContactMessage $message)
    {
        $message->update(['is_read' => false]);
        return redirect()->route('admin.messages.index')
            ->with('success', 'Message marked as unread.');
    }
}
