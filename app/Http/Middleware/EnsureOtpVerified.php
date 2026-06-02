<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOtpVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('pending_user_id')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
