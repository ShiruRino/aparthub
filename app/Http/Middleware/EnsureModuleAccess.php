<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string $module, string $action): Response
    {
        if (! Auth::check()) {
            return $request->expectsJson()
                ? abort(401)
                : redirect()->route('login');
        }

        if (! $request->user()->canAccessModule($module, $action)) {
            abort(403);
        }

        return $next($request);
    }
}
