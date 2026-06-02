<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'sender_name'  => 'required|string|min:2|max:100',
            'sender_email' => 'required|email:rfc|max:254',
            'subject'      => 'required|string|min:3|max:150',
            'message'      => 'required|string|min:10|max:2000',
        ];
        // No rate limiting — intentional per SCOPE.md §10.12
    }
}
