<?php

namespace App\Http\Middleware;

use App\Models\Zone;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureZoneAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $zoneParam = $request->route('zone');

        if (! $user || ! $zoneParam) {
            abort(403, 'No autorizado.');
        }

        $zoneId = $zoneParam instanceof Zone ? $zoneParam->id : (int) $zoneParam;

        if (! $user->hasZoneAccess($zoneId)) {
            abort(403, 'No tienes acceso a esta zona.');
        }

        return $next($request);
    }
}
