<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $user = Auth::user();

        switch ($role) {
            case 'farmer':
                if (!$user->isFarmer()) {
                    return redirect()->route('dashboard')->with('error', 'Access denied. Farmer role required.');
                }
                break;
            case 'buyer':
                if (!$user->isBuyer()) {
                    return redirect()->route('dashboard')->with('error', 'Access denied. Buyer role required.');
                }
                break;
            case 'admin':
                if (!$user->isAdmin()) {
                    return redirect()->route('dashboard')->with('error', 'Access denied. Admin role required.');
                }
                break;
        }

        return $next($request);
    }
}
