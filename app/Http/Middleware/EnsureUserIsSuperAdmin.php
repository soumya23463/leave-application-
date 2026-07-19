<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! isSuperAdmin()) {
            abort(403, 'This area is restricted to super administrators.');
        }

        return $next($request);
    }
}
