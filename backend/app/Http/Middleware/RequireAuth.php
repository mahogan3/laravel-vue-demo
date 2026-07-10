<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->attributes->has('authUser')) {
            abort(401, 'Unauthenticated.');
        }

        return $next($request);
    }
}
