<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            //validamos si la respuesta es un json al loguearme
            if($request->wantsJson()){
                return response()->json([
                    'message' => 'Ya estás Autenticado!',
                    'data' => $request->user()
                ]);
            }
            return redirect(RouteServiceProvider::HOME);
        }

        return $next($request);
    }
}
