<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function show()
    {
        return view('auth.forgot-password');
    }

    public function submit(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = strtolower($request->email);

        $user = User::where('email', $email)->where('role', 'customer')->where('status', 'active')->first();

        if ($user) {
            $token = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                ['token' => hash('sha256', $token), 'created_at' => now()]
            );

            $resetUrl = route('password.reset', ['token' => $token, 'email' => $email]);
            Mail::to($email)->send(new PasswordResetMail($resetUrl));
        }

        return redirect()->route('password.sent');
    }

    public function sent()
    {
        return view('auth.forgot-password-sent');
    }
}
