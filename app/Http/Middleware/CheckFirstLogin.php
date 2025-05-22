<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFirstLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated and it's their first login
        if (auth()->check() && auth()->user()->first_login) {
            // Redirect to password change route based on user role
            if (auth()->user()->role === 'admin') {
                return redirect()->route('admin.password.change');
            } else {
                return redirect()->route('usher.password.change');
            }
        }

        return $next($request);
    }
}
