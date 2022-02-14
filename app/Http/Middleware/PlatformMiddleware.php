<?php

namespace App\Http\Middleware;

use Closure;

class PlatformMiddleware
{
    /**
     * Verifica que el usuario que solicita el servicio sea interno de la compañia.
     * @author Edwin David Sanchez Balbin
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check() && auth()->user()->is_internal) {
            return $next($request);
        }
        return response()->json(['status' => 'Not authorized']);
    }
}
