<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Rules\SriLankanPhone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        return view('account.profile', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'  => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL\s.\-]+$/u'],
            'phone' => ['required', new SriLankanPhone],
        ]);

        $phone = SriLankanPhone::normalize($request->phone);

        Auth::user()->update(['name' => $request->name, 'phone' => $phone]);

        return back()->with('success', 'Profile updated.');
    }
}
