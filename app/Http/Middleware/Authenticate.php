<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{

    public function handle($request, Closure $next, ...$guards)
    {
        if (!$request->expectsJson()) {
            return redirect()->route('login');
        }
        dd($request->headers);
        return $next($request);
    }
}
