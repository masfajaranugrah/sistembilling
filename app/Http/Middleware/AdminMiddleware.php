<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user login
        if (! Auth::check()) {
            return redirect()->route('login'); // redirect ke halaman login
        }

        // Cek role user
        if (Auth::user()->role !== 'administrator') {
            abort(403, 'Unauthorized'); // atau redirect ke halaman lain
        }

        return $next($request);
    }
}
