<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized. Please log in.'], 401);
            }
            return redirect()->route('login')->with('error', 'You must be logged in to access this feature.');
        }

        return $next($request);
    }
}
