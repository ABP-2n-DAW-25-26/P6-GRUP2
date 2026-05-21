<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class isSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, ['admin', 'superadmin'], true)) {
            Inertia::flash([
                'unauthorized' => true,
                'title' => 'Accés Denegat',
                'message' => 'No tens permisos per accedir a aquesta pàgina.',
            ]);

            return to_route('home');
        }

        return $next($request);
    }
}
