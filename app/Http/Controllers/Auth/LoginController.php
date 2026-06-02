<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\LoginOtpMail;
use App\Mail\RegistrationOtpMail;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    public function show(Request $request)
    {
        // OTP screen-close invalidation: if pending_user_id is in session for 'login',
        // the user navigated back to login — invalidate the dangling login OTP.
        if ($request->session()->has('pending_user_id') && $request->session()->get('otp_purpose') === 'login') {
            $userId = $request->session()->get('pending_user_id');
            Otp::where('user_id', $userId)
                ->where('purpose', 'login')
                ->whereNull('invalidated_at')
                ->update(['invalidated_at' => now()]);
            $request->session()->forget(['pending_user_id', 'otp_purpose', 'otp_remember', 'redirect_after_login']);
        }

        return view('auth.login');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $email = strtolower($request->email);
        $user  = User::where('email', $email)->where('role', 'customer')->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        // Check lockout
        if ($user->isLocked()) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        if (! Hash::check($request->password, $user->password)) {
            $user->increment('failed_login_attempts');
            if ($user->failed_login_attempts >= 5) {
                $user->update(['locked_until' => now()->addMinutes(15)]);
            }
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        // Credentials correct — reset lockout counter
        $user->update(['failed_login_attempts' => 0, 'locked_until' => null]);

        // Unverified account: J8.1 divert
        if ($user->status === 'unverified') {
            $user->otps()->whereNull('invalidated_at')->update(['invalidated_at' => now()]);
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            Otp::create([
                'user_id'    => $user->id,
                'code'       => hash('sha256', $code),
                'purpose'    => 'registration',
                'expires_at' => now()->addMinutes(10),
                'created_at' => now(),
            ]);
            Mail::to($user->email)->send(new RegistrationOtpMail($code, $user->email));
            session(['pending_user_id' => $user->id, 'otp_purpose' => 'registration']);
            return redirect()->route('register.verify');
        }

        if ($user->status !== 'active') {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        // Active: send login OTP
        $user->otps()->where('purpose', 'login')->whereNull('invalidated_at')
            ->update(['invalidated_at' => now()]);

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Otp::create([
            'user_id'    => $user->id,
            'code'       => hash('sha256', $code),
            'purpose'    => 'login',
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
        ]);

        Mail::to($user->email)->send(new LoginOtpMail($code, $user->email));

        $redirectUrl = session('redirect_after_login') ?? route('account.dashboard');

        session([
            'pending_user_id'      => $user->id,
            'otp_purpose'          => 'login',
            'otp_remember'         => $request->boolean('remember'),
            'redirect_after_login' => $redirectUrl,
        ]);

        return redirect()->route('login.verify');
    }
}
