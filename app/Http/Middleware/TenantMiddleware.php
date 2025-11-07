<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Case-insensitive + trimmed check
        if (strtolower(trim(Auth::user()->usertype)) !== 'tenant') {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
