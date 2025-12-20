<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\IsAdmin;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \Illuminate\Http\Middleware\TrustProxies::class,
            \App\Http\Middleware\LastSeenMiddleware::class,
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\SessionTimeout::class,
        ]);

        $middleware->alias([
            'admin' => IsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Do not report certain exceptions
        $exceptions->dontReport([
            \Illuminate\Auth\AuthenticationException::class,
            \Illuminate\Auth\Access\AuthorizationException::class,
            \Symfony\Component\HttpKernel\Exception\HttpException::class,
            \Illuminate\Database\Eloquent\ModelNotFoundException::class,
            \Illuminate\Session\TokenMismatchException::class,
            \Illuminate\Validation\ValidationException::class,
        ]);

        // Render specific exceptions with custom responses
        $exceptions->render(function (\Exception $e, $request) {
            if ($request->is('api/*')) {
                // Always log the full error details server-side
                \Log::error($e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'request' => $request->all()
                ]);

                $data = [
                    'error' => 'Something went wrong. Please try again later.',
                ];

                // Only include the actual error message in non-production environments
                if (!app()->environment('production')) {
                    $data['message'] = $e->getMessage();
                }

                return response()->json($data, 500);
            }

            // For non-API requests, return a generic error page in production
            if (app()->environment('production')) {
                \Log::error($e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'request' => $request->all()
                ]);

                return response()->view('errors.500', [], 500);
            }
        });
    })->create();
