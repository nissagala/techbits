<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function show()
    {
        return view('account.password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password'      => 'required',
            'password'              => ['required', 'min:8', 'max:64', 'regex:/[a-zA-Z]/', 'regex:/[0-9]/'],
            'password_confirmation' => 'required|same:password',
        ]);

        if (! Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        Auth::user()->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password changed successfully.');
    }
}
