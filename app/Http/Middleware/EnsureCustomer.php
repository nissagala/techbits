<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('redirect_after_login', $request->url());
        }

        $user = Auth::user();

        if ($user->role !== 'customer') {
            Auth::logout();
            return redirect()->route('login');
        }

        if ($user->status !== 'active') {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Invalid email or password.']);
        }

        return $next($request);
    }
}
