<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Auth;

class CheckProfile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,  ...$profiles_id): Response
    {   
        if (!Auth::check() || !in_array(Auth::user()->profile_id, $profiles_id)) {
            // Redirige o responde con un error si el usuario no tiene el rol adecuado
            return redirect()->route('dashboard');
        }
        return $next($request);
    }
}
