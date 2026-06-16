<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsurePasswordChanged
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && !$user->status) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('voyager.login')
                ->with([
                    'message' => 'Su usuario se encuentra inactivo. Contacte al administrador.',
                    'alert-type' => 'error',
                ]);
        }

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
