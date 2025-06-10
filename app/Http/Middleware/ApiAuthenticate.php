<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class ApiAuthenticate extends Middleware
{
    /**
     * Disable redirect to login for unauthenticated users.
     */
    protected function redirectTo($request): ?string
    {
        return null;
    }

    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, ['sanctum']);

        return $next($request);
    }
}
