<?php

namespace App\Http\Middleware;

use App\Models\AuthSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the better-auth session cookie (set by the Node auth server) against
 * the shared `session`/`user` tables and attaches the user to the request.
 * Runs on every API request but never blocks — routes that require a logged-in
 * user enforce that themselves via the `auth.required` middleware.
 */
class BetterAuthSession
{
    public function handle(Request $request, Closure $next): Response
    {
        // PHP mangles dots in cookie *names* to underscores when populating
        // $_COOKIE, so better-auth's "better-auth.session_token" cookie only
        // ever arrives here as "better-auth_session_token".
        $cookie = $request->cookie('better-auth_session_token');

        if ($cookie) {
            $dot = strrpos($cookie, '.');
            $token = $dot !== false ? substr($cookie, 0, $dot) : $cookie;

            $session = AuthSession::with('user')->where('token', $token)->first();

            if ($session && $session->user && $session->expiresAt->isFuture()) {
                $request->attributes->set('authUser', $session->user);
            }
        }

        return $next($request);
    }
}
