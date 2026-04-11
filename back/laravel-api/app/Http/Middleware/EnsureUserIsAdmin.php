<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || $request->user()->rol !== 'admin') {
            return response()->json([
                'error' => 'Accés denegat. Només els administradors poden accedir a aquesta funcionalitat.'
            ], 403);
        }

        return $next($request);
    }
}
