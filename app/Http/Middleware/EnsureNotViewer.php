<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotViewer
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === UserRole::Viewer) {
            abort(Response::HTTP_FORBIDDEN, 'Read-only role cannot modify resources.');
        }

        return $next($request);
    }
}
