<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SessionCheck
{
    public function handle($request, Closure $next)
    {
        // 1. Jika user sudah login dan akses halaman login → redirect dashboard
        if ($request->routeIs('login') && Auth::check()) {
            // pastikan ini bukan route dashboard untuk menghindari loop
            return redirect('/dashboard/admin/pelanggan');
        }

        // 2. Jika user akses dashboard tetapi belum login → redirect login
        if ($request->is('dashboard/*') && ! Auth::check()) {
            return redirect()->route('login');
        }

        // 3. Kalau user login dan akses dashboard → biarkan
        return $next($request);
    }
}
