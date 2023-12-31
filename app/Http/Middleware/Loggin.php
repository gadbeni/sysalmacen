<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Sucursal;
use App\Models\SucursalUser;
use App\Http\Controllers\Controller;

class Loggin
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
        try {
            $obj = new Controller();
            $user  = Auth::user();
            // dd($user);
            if(setting('configuracion.maintenance') && !auth()->user()->hasRole('admin')){
                return redirect()->route('maintenance');
            }

            // $sucursal = SucursalUser::where('user_id', Auth::user()->id)->where('condicion', 1)->where('deleted_at', null)->first();
            $sucursal = Auth::user()->sucursal_id;
        
            if(!$sucursal && !auth()->user()->hasRole('admin') && !auth()->user()->hasRole('almacen_admin'))
            {
                return redirect()->route('error');
            }


            //si el usuario no tiene direcion y unidad  no accedera al sistema
            if((!$user->unidadAdministrativa_id || !$user->direccionAdministrativa_id) && !auth()->user()->hasRole('admin'))
            {
                return redirect()->route('contact');
            }

            // dd($obj->getWorker($user->funcionario_id));
            //Para ver si no existe la personas con contrato no accedera al sistema
            if(!$obj->getWorker($user->funcionario_id) && !auth()->user()->hasRole('admin'))
            {
                return redirect()->route('notpeople');
            }

            // if($obj->getWorker(Auth::user()->funcionario_id) == "null" && !auth()->user()->hasRole('admin'))
            // {
            //     return redirect()->route('notpeople');
            // }



        } catch (\Throwable $th) {}

        if (!str_contains(request()->url(), 'admin/compass')) {
            try {
                $data = [
                    'user_id' => Auth::user()->id,
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'url' => request()->url(),
                    'method' => request()->method(),
                    'input' => request()->except(['password', '_token', '_method']),
                ];
                Log::channel('requests')->info('Petición HTTP al sistema.', $data);
            } catch (\Throwable $th) {
                $data = [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'url' => request()->url(),
                    'method' => request()->method(),
                    'input' => request()->except(['password', '_token', '_method']),
                ];
                Log::channel('requests')->info('Petición HTTP al sistema.', $data);
            }
        }

        return $next($request);
    }
}
