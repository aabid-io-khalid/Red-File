<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $isAdmin = Auth::user()->hasRole('admin'); 
                $redirectTo = $isAdmin ? 'admin.index' : 'home';
                Log::info('RedirectIfAuthenticated triggered', [
                    'user_id' => Auth::id(),
                    'is_admin' => $isAdmin,
                    'redirect_to' => $redirectTo,
                    'requested_url' => $request->fullUrl(),
                ]);
                return redirect()->route($redirectTo);
            }
        }

        return $next($request);
    }
}