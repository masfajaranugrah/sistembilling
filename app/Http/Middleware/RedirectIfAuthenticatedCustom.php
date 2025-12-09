<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticatedCustom
{
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            // User sudah login, redirect sesuai role
            $role = Auth::user()->role ?? null;

            if ($role === 'admin') {
                return redirect('/admin/dashboard');
            } elseif ($role === 'client') {
                return redirect('/client/dashboard');
            } else {
                return redirect('/');
            }
        }

        return $next($request);
    }
}
