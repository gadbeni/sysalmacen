<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePasswordChanged
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->must_change_password) {
            return $next($request);
        }

        if ($request->routeIs(
            'sessions',
            'change_password',
            'delete_session',
            'voyager.logout',
            'voyager.login',
            'voyager.postlogin'
        )) {
            return $next($request);
        }

        return redirect()
            ->route('sessions')
            ->with([
                'message' => 'Debe actualizar su contrasena antes de continuar.',
                'alert-type' => 'warning',
            ]);
    }
}
