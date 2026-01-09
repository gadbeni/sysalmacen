<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Person;
use App\Models\Direction;
use App\Models\PeopleExt;

class PeopleExtController extends Controller
{
    public function index()
    {
        // return Person::all();
        return view('almacenes.peopleExt.browse');
    }

    public function list()
    {
        $user = Auth::user();
        $search = request('search');
        $paginate = request('paginate') ?? 10;

        if ($search) {
            // Primero buscar en la tabla people (conexión mamore)
            $peopleIds = Person::where(function ($query) use ($search) {
                $query->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('paternal_surname', 'LIKE', "%{$search}%")
                    ->orWhere('maternal_surname', 'LIKE', "%{$search}%")
                    ->orWhereRaw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(paternal_surname, ''), ' ', COALESCE(maternal_surname, '')) LIKE ?", ["%{$search}%"]);
            })->pluck('id')->toArray();

            // Luego filtrar people_ext por esos IDs
            $data = PeopleExt::with(['people', 'direction'])
                ->whereIn('people_id', $peopleIds)
                ->where('deleted_at', NULL)
                ->orderBy('id', 'DESC')
                ->paginate($paginate);
        } else {
            // Sin búsqueda, mostrar todos
            $data = PeopleExt::with(['people', 'direction'])
                ->where('deleted_at', NULL)
                ->orderBy('id', 'DESC')
                ->paginate($paginate);
        }




        return view('almacenes.peopleExt.list', compact('data'));
    }

    public function create()
    {
        $direction = Direction::where('estado', 1)->where('deleted_at', null)->get();
        $people = Person::where('deleted_at', null)->get();
        // return $direction;
        return view('almacenes.peopleExt.add', compact('direction', 'people'));
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            PeopleExt::create([
                'people_id' => $request->people_id,
                'direccionAdministrativa_id' => $request->direccionAdministrativa_id,
                'cargo' => $request->cargo,
                'start' => $request->start,
                'finish' => $request->finish,
                'registerUser_id' => Auth::user()->id
            ]);

            DB::commit();
            return redirect()->route('people_ext.index')->with(['message' => 'Registrado exitosamente.', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('people_ext.index')->with(['message' => 'Ocurrio un error.', 'alert-type' => 'error']);
        }
    }

    public function destroy($people_ext)
    {
        DB::beginTransaction();
        try {
            PeopleExt::where('id', $people_ext)->update(['deleted_at' => Carbon::now()]);

            DB::commit();
            return redirect()->route('people_ext.index')->with(['message' => 'Eliminado exitosamente.', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('people_ext.index')->with(['message' => 'Ocurrio un error.', 'alert-type' => 'error']);
        }
    }

    public function finish($people_ext)
    {
        DB::beginTransaction();
        try {
            PeopleExt::where('id', $people_ext)->update(['status' => 0]);

            DB::commit();
            return redirect()->route('people_ext.index')->with(['message' => 'Finalizado exitosamente.', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('people_ext.index')->with(['message' => 'Ocurrio un error.', 'alert-type' => 'error']);
        }
    }
}
