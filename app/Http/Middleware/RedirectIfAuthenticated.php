<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        // Jika tidak menggunakan HTTPS, arahkan ke HTTPS
        if (!$request->secure()) {
            return redirect()->secure($request->getRequestUri());
        }

        // Cek apakah pengguna sudah terautentikasi
        if (auth()->check()) {
            return redirect('/home'); // Atau rute yang sesuai untuk pengguna yang sudah login
        }

        return $next($request);
    }
}
