<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminLoginOtpMail;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // OTP screen-close detection: if admin navigated back to login with a pending OTP, invalidate it.
        if ($request->session()->has('pending_admin_id')) {
            $userId = $request->session()->get('pending_admin_id');
            Otp::where('user_id', $userId)
                ->where('purpose', 'admin_login')
                ->whereNull('invalidated_at')
                ->update(['invalidated_at' => now()]);
            $request->session()->forget('pending_admin_id');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', strtolower($request->email))
            ->where('role', 'admin')
            ->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

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

        // Invalidate any existing pending admin OTP for this user
        $user->otps()
            ->where('purpose', 'admin_login')
            ->whereNull('invalidated_at')
            ->update(['invalidated_at' => now()]);

        // Generate and dispatch OTP
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Otp::create([
            'user_id'    => $user->id,
            'code'       => hash('sha256', $code),
            'purpose'    => 'admin_login',
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
        ]);

        Mail::to($user->email)->send(new AdminLoginOtpMail($code, $user->email));

        $request->session()->put('pending_admin_id', $user->id);

        return redirect()->route('admin.login.otp');
    }

    public function showOtp(Request $request)
    {
        if (! $request->session()->has('pending_admin_id')) {
            return redirect()->route('admin.login');
        }

        return view('admin.auth.otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $userId = $request->session()->get('pending_admin_id');
        $user   = $userId ? User::find($userId) : null;

        if (! $user) {
            return redirect()->route('admin.login');
        }

        $otp = $user->otps()
            ->where('purpose', 'admin_login')
            ->whereNull('invalidated_at')
            ->latest('created_at')
            ->first();

        if (! $otp || ! $otp->isValid()) {
            return back()->withErrors(['otp' => 'Invalid or expired code.']);
        }

        if (hash('sha256', $request->otp) !== $otp->code) {
            $otp->incrementAttempts();
            if (! $otp->isValid()) {
                $request->session()->forget('pending_admin_id');
                return redirect()->route('admin.login')
                    ->withErrors(['otp' => 'Invalid or expired code.']);
            }
            return back()->withErrors(['otp' => 'Invalid or expired code.']);
        }

        $otp->invalidate();
        $request->session()->forget('pending_admin_id');

        Auth::login($user, false);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function resendOtp(Request $request)
    {
        $userId = $request->session()->get('pending_admin_id');
        $user   = $userId ? User::find($userId) : null;

        if (! $user) {
            return redirect()->route('admin.login');
        }

        $lastOtp = $user->otps()
            ->where('purpose', 'admin_login')
            ->latest('created_at')
            ->first();

        if ($lastOtp && ! $lastOtp->canResend()) {
            return back()->withErrors(['otp' => 'Please wait before requesting a new code.']);
        }

        $user->otps()
            ->where('purpose', 'admin_login')
            ->whereNull('invalidated_at')
            ->update(['invalidated_at' => now()]);

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Otp::create([
            'user_id'    => $user->id,
            'code'       => hash('sha256', $code),
            'purpose'    => 'admin_login',
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
        ]);

        Mail::to($user->email)->send(new AdminLoginOtpMail($code, $user->email));

        return back()->with('success', 'A new code has been sent to your email.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
