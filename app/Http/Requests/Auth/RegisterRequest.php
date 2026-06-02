<?php

namespace App\Http\Requests\Auth;

use App\Rules\SriLankanPhone;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL\s.\-]+$/u'],
            'email'                 => 'required|email:rfc|max:254',
            'phone'                 => ['required', new SriLankanPhone],
            'password'              => ['required', 'min:8', 'max:64', 'regex:/[a-zA-Z]/', 'regex:/[0-9]/'],
            'password_confirmation' => 'required|same:password',
        ];
    }

    public function messages(): array
    {
        return [
            'password.regex' => 'Password must contain at least one letter and one number.',
        ];
    }
}
