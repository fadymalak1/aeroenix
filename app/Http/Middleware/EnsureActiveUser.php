<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && ! $user->isActive()) {
            abort(Response::HTTP_FORBIDDEN, 'Account is inactive.');
        }

        return $next($request);
    }
}
