<?php

namespace App\Http\Middleware;

use App\Models\Resident;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthenticatedResident
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() instanceof Resident) {
            abort(403);
        }

        return $next($request);
    }
}
