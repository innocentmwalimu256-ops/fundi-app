<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->validateCsrfTokens(except: [
            'logout',
            'logout/*',
            'webhook/snippe',
            'api/webhook/snippe',
            'snippe/payment/webhook',
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SetLocaleMiddleware::class,
        ]);
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn () => match (auth()->user()?->role) {
            'admin' => route('admin.dashboard'),
            'technician' => route('technician.dashboard'),
            default => route('client.dashboard'),
        });
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'subscription.active' => \App\Http\Middleware\EnsureTechnicianSubscriptionActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function ($response, Throwable $exception, \Illuminate\Http\Request $request) {
            if ($response->getStatusCode() === 419 || $exception instanceof \Illuminate\Session\TokenMismatchException) {
                if ($request->expectsJson() || $request->header('X-Turbo-Request-Id')) {
                    return response()->json([
                        'message' => 'Session expired. Please refresh the page.',
                        'status' => 419,
                        'redirect' => auth()->check() ? url()->current() : route('login'),
                    ], 419);
                }

                if (auth()->check()) {
                    return redirect()->to($request->fullUrl())->with('info', 'Ukurasa umesasishwa.');
                }

                return redirect()->route('login')->with('info', 'Session yako imesasishwa. Tafadhali ingia tena.');
            }

            return $response;
        });
    })->create();
