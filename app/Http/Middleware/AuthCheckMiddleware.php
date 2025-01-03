<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthCheckMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            $message = 'Veuillez vous connecter ou créer un compte pour accéder à cette page.';
            return redirect()->route('home')->with('error', $message);
        }
    
        return $next($request);
    }
    
    
}
