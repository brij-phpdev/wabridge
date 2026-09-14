<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlatformAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isPlatformAdmin()) {
            abort(403, 'This area is restricted to BrijRaj.Tech administrators.');
        }

        return $next($request);
    }
}
