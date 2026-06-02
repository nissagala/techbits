<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CartController;
use App\Mail\LoginOtpMail;
use App\Mail\RegistrationOtpMail;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    public function showRegistration(Request $request)
    {
        $userId = $request->session()->get('pending_user_id');
        if (! $userId) {
            return redirect()->route('register');
        }
        $user = User::find($userId);
        return view('auth.otp-registration', ['maskedEmail' => $this->maskEmail($user?->email ?? '')]);
    }

    public function verifyRegistration(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $userId = $request->session()->get('pending_user_id');
        $user   = $userId ? User::find($userId) : null;

        if (! $user) {
            return redirect()->route('register');
        }

        $otp = $user->otps()
            ->where('purpose', 'registration')
            ->whereNull('invalidated_at')
            ->latest('created_at')
            ->first();

        if (! $otp || ! $otp->isValid()) {
            return back()->withErrors(['otp' => 'Invalid or expired code.']);
        }

        if (hash('sha256', $request->otp) !== $otp->code) {
            $otp->incrementAttempts();
            if (! $otp->isValid()) {
                return back()->withErrors(['otp' => 'Too many attempts. Please request a new code.']);
            }
            return back()->withErrors(['otp' => 'Invalid or expired code.']);
        }

        $otp->invalidate();
        $user->update(['status' => 'active']);

        $request->session()->forget(['pending_user_id', 'otp_purpose']);

        Auth::login($user, false);
        $request->session()->regenerate();

        $capped = CartController::mergeGuestCart();

        $redirect = $request->session()->pull('redirect_after_login', route('account.dashboard'));

        if ($capped) {
            return redirect($redirect)->with('cart_capped', true);
        }

        return redirect($redirect)->with('success', 'Account verified successfully. Welcome!');
    }

    public function resendRegistration(Request $request)
    {
        $userId = $request->session()->get('pending_user_id');
        $user   = $userId ? User::find($userId) : null;

        if (! $user) {
            return redirect()->route('register');
        }

        $lastOtp = $user->otps()->where('purpose', 'registration')->latest('created_at')->first();

        if ($lastOtp && ! $lastOtp->canResend()) {
            return back()->withErrors(['otp' => 'Please wait before requesting a new code.']);
        }

        $user->otps()->where('purpose', 'registration')->whereNull('invalidated_at')
            ->update(['invalidated_at' => now()]);

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Otp::create([
            'user_id'    => $user->id,
            'code'       => hash('sha256', $code),
            'purpose'    => 'registration',
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
        ]);

        Mail::to($user->email)->send(new RegistrationOtpMail($code, $user->email));

        return back()->with('success', 'A new code has been sent to your email.');
    }

    public function showLogin(Request $request)
    {
        $userId = $request->session()->get('pending_user_id');
        if (! $userId || $request->session()->get('otp_purpose') !== 'login') {
            return redirect()->route('login');
        }
        $user = User::find($userId);
        return view('auth.otp-login', ['maskedEmail' => $this->maskEmail($user?->email ?? '')]);
    }

    public function verifyLogin(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $userId  = $request->session()->get('pending_user_id');
        $user    = $userId ? User::find($userId) : null;
        $remember = (bool) $request->session()->get('otp_remember', false);

        if (! $user || $request->session()->get('otp_purpose') !== 'login') {
            return redirect()->route('login');
        }

        $otp = $user->otps()
            ->where('purpose', 'login')
            ->whereNull('invalidated_at')
            ->latest('created_at')
            ->first();

        if (! $otp || ! $otp->isValid()) {
            return back()->withErrors(['otp' => 'Invalid or expired code.']);
        }

        if (hash('sha256', $request->otp) !== $otp->code) {
            $otp->incrementAttempts();
            if (! $otp->isValid()) {
                return back()->withErrors(['otp' => 'Too many attempts. Please request a new code.']);
            }
            return back()->withErrors(['otp' => 'Invalid or expired code.']);
        }

        $otp->invalidate();

        $redirect = $request->session()->pull('redirect_after_login', route('account.dashboard'));
        $request->session()->forget(['pending_user_id', 'otp_purpose', 'otp_remember']);

        Auth::login($user, $remember);
        $request->session()->regenerate();

        $capped = CartController::mergeGuestCart();

        if ($capped) {
            return redirect($redirect)->with('cart_capped', true);
        }

        return redirect($redirect)->with('success', 'Logged in successfully.');
    }

    public function resendLogin(Request $request)
    {
        $userId = $request->session()->get('pending_user_id');
        $user   = $userId ? User::find($userId) : null;

        if (! $user || $request->session()->get('otp_purpose') !== 'login') {
            return redirect()->route('login');
        }

        $lastOtp = $user->otps()->where('purpose', 'login')->latest('created_at')->first();

        if ($lastOtp && ! $lastOtp->canResend()) {
            return back()->withErrors(['otp' => 'Please wait before requesting a new code.']);
        }

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

        return back()->with('success', 'A new code has been sent to your email.');
    }

    private function maskEmail(string $email): string
    {
        if (! str_contains($email, '@')) {
            return $email;
        }
        [$local, $domain] = explode('@', $email, 2);
        return substr($local, 0, 1) . '•••@' . $domain;
    }
}
