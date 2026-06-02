<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactSubmissionMail;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function submit(ContactRequest $request)
    {
        $message = ContactMessage::create($request->only([
            'sender_name', 'sender_email', 'subject', 'message',
        ]));

        Mail::to(config('mail.admin_address'))->send(new ContactSubmissionMail($message));

        return back()->with('success', 'Message sent. We\'ll get back to you soon.');
    }
}
