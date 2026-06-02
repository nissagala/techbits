<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function show(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'token'                 => 'required',
            'email'                 => 'required|email',
            'password'              => ['required', 'min:8', 'max:64', 'regex:/[a-zA-Z]/', 'regex:/[0-9]/'],
            'password_confirmation' => 'required|same:password',
        ]);

        $email = strtolower($request->email);

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (! $record
            || ! hash_equals($record->token, hash('sha256', $request->token))
            || now()->diffInSeconds($record->created_at) > 3600
        ) {
            return back()->withErrors(['token' => 'Invalid or expired reset link.'])->withInput();
        }

        $user = User::where('email', $email)->where('role', 'customer')->where('status', 'active')->first();

        if (! $user) {
            return back()->withErrors(['token' => 'Invalid or expired reset link.'])->withInput();
        }

        $user->update(['password' => Hash::make($request->password)]);
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return redirect()->route('login')->with('success', 'Password reset successfully. Please log in.');
    }
}
