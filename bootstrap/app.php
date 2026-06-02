<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            \Illuminate\Support\Facades\Route::middleware('web')
                ->prefix('admin')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'ensure.customer'        => \App\Http\Middleware\EnsureCustomer::class,
            'ensure.admin'           => \App\Http\Middleware\EnsureAdmin::class,
            'ensure.otp.verified'    => \App\Http\Middleware\EnsureOtpVerified::class,
            'redirect.if.auth'       => \App\Http\Middleware\RedirectIfAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // Render S30 for 404s on the storefront
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if (! $request->is('admin/*') && ! $request->is('api/*')) {
                return response()->view('error', ['message' => 'Page not found'], 404);
            }
        });
    })->create();
