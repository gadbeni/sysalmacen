<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsuariosDireccionExport;
use App\Models\Sucursal;

class ReportUsuariosDireccionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        

        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('almacen_admin')) {
            $sucursal = Sucursal::where('deleted_at', null)->where('condicion', 1)->get();
        } else {
            $sucursal = Sucursal::where('id', $user->sucursal_id)
                ->where('deleted_at', null)
                ->get();
        }

        return view('almacenes.report.aditional.usuariosDireccion.report', compact('sucursal'));
    }

    public function list(Request $request)
    {
        $sucursal_id = $request->sucursal_id;
        $sucursal    = Sucursal::find($sucursal_id);

        // Todas las direcciones asignadas a este almacén (desde la BD mamore)
        $direcciones = DB::connection('mamore')
            ->table('direcciones as d')
            ->join('sysalmacen.sucursal_direccions as sd', 'sd.direccionAdministrativa_id', 'd.id')
            ->where('sd.sucursal_id', $sucursal_id)
            ->where('sd.deleted_at', null)
            ->select('d.id', 'd.nombre', 'd.sigla')
            ->orderBy('d.nombre', 'asc')
            ->get();

        $data = [];

        foreach ($direcciones as $dir) {
            // Unidades que dependen de esta dirección
            $unidades = DB::connection('mamore')
                ->table('unidades as u')
                ->where('u.direccion_id', $dir->id)
                ->where('u.deleted_at', null)
                ->select('u.id', 'u.nombre', 'u.sigla')
                ->orderBy('u.nombre', 'asc')
                ->get();

            $unidades_data = [];

            foreach ($unidades as $unidad) {
                // Usuarios del almacén asignados a esta unidad
                $usuarios = DB::connection('mamore')
                    ->table('sysalmacen.users as u')
                    ->leftJoin('people as p', 'p.id', 'u.funcionario_id')
                    ->leftJoin('sysalmacen.roles as r', 'r.id', 'u.role_id')
                    ->where('u.sucursal_id', $sucursal_id)
                    ->where('u.unidadAdministrativa_id', $unidad->id)
                    ->select(
                        'u.id',
                        'u.email',
                        'u.last_login_at',
                        'r.display_name as rol',
                        'p.ci',
                        'p.first_name',
                        'p.paternal_surname',
                        'p.maternal_surname',
                        DB::raw("CONCAT_WS(' ', p.first_name, p.middle_name, p.paternal_surname, p.maternal_surname, p.married_surname) as nombre")
                    )
                    ->orderBy('p.paternal_surname', 'asc')
                    ->get();

                $unidades_data[] = [
                    'unidad'   => $unidad,
                    'usuarios' => $usuarios,
                ];
            }

            $data[] = [
                'direccion' => $dir,
                'unidades'  => $unidades_data,
            ];
        }

        if ($request->print == 1) {
            return view('almacenes.report.aditional.usuariosDireccion.print', compact('data', 'sucursal'));
        }

        if ($request->print == 2) {
            return Excel::download(
                new UsuariosDireccionExport($data, $sucursal),
                'Usuarios por Direccion - ' . $sucursal->nombre . '.xlsx'
            );
        }

        return view('almacenes.report.aditional.usuariosDireccion.list', compact('data', 'sucursal'));
    }
}
