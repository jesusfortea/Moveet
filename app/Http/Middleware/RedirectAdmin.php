<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_admin) {
            // Si es admin intentando entrar a zona de usuario, lo mandamos a su dashboard
            return redirect()->route('admin.dashboard')->with('error', 'Los administradores no pueden acceder a la sección de usuarios.');
        }

        return $next($request);
    }
}
