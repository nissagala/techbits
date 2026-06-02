<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\RegistrationOtpMail;
use App\Models\Otp;
use App\Models\User;
use App\Rules\SriLankanPhone;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function submit(RegisterRequest $request)
    {
        $email = strtolower($request->email);

        // Handle re-registration of expired Unverified account
        $existing = User::where('email', $email)->where('role', 'customer')->first();
        if ($existing) {
            if ($existing->status === 'unverified') {
                $hasValidOtp = $existing->otps()
                    ->where('purpose', 'registration')
                    ->whereNull('invalidated_at')
                    ->where('expires_at', '>', now())
                    ->exists();

                if ($hasValidOtp) {
                    return back()->withErrors(['email' => 'Email already in use.'])->withInput();
                }
                $existing->delete();
            } else {
                return back()->withErrors(['email' => 'Email already in use.'])->withInput();
            }
        }

        $normalizedPhone = SriLankanPhone::normalize($request->phone);

        $user = User::create([
            'name'   => $request->name,
            'email'  => $email,
            'phone'  => $normalizedPhone,
            'password' => $request->password,
            'role'   => 'customer',
            'status' => 'unverified',
        ]);

        $this->sendOtp($user);

        session(['pending_user_id' => $user->id, 'otp_purpose' => 'registration']);

        return redirect()->route('register.verify');
    }

    private function sendOtp(User $user): void
    {
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
    }
}
