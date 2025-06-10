<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\ApiAuthenticate;
use App\Http\Middleware\ForceJsonResponse;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ✅ Alias custom middleware
        $middleware->alias([
            'auth.api' => ApiAuthenticate::class,
        ]);

        // ✅ Force JSON on all API routes
        $middleware->appendToGroup('api', ForceJsonResponse::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ✅ 401 Unauthenticated
        $exceptions->renderable(function (AuthenticationException $e, $request) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        });

        // ✅ 403 Forbidden
        $exceptions->renderable(function (AuthorizationException $e, $request) {
            return response()->json(['message' => 'Access denied.'], 403);
        });

        // ✅ 404 Not Found
        $exceptions->renderable(function (NotFoundHttpException $e, $request) {
            return response()->json(['message' => 'Route not found.'], 404);
        });

        // ✅ 405 Method Not Allowed
        $exceptions->renderable(function (MethodNotAllowedHttpException $e, $request) {
            return response()->json(['message' => 'Method not allowed.'], 405);
        });
    })
    ->create();
