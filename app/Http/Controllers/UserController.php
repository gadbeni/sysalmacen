<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use app\models\User;
use App\Models\SucursalUser;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Contract;
use App\Models\SucursalSubAlmacen;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data = SucursalUser::where('deleted_at', null)->where('condicion', 1)->get();

        for ($i = 0; $i < count($data); $i++) {
            User::where('id', $data[$i]->id)->update(['sucursal_id' => $data[$i]->sucursal_id]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $request;
        // $ok = SucursalUser::where('sucursal_id', $request->branchoffice_id)->where('user_id', $request->user_id)->where('condicion',1)->first();
        // $ok = SucursalUser::where('sucursal_id', $request->branchoffice_id)->where('condicion',1)->first();
        // if($ok)
        // {
        //     return redirect('admin/users')->with(['message' => 'La sucursal ya se encuentra asignada a una persona.', 'alert-type' => 'error']);
        // }

        $ok = SucursalUser::where('user_id', $request->user_id)->where('condicion', 1)->first();
        if ($ok) {
            return redirect('admin/users')->with(['message' => 'La persona se encuentra asignada a una sucursal activa.', 'alert-type' => 'error']);
        }
        SucursalUser::create(['sucursal_id' => $request->branchoffice_id, 'user_id' => $request->user_id]);
        return redirect()->route('admin/users')->with(['message' => 'Sucursal asignada exitosamente', 'alert-type' => 'success']);

        // return redirect('admin/users/'.$request->user_id.'/edit');
    }


    // public function desactivar(Request $request)
    // {
    //     return $request;
    //     SucursalUser::where('id',$request->id)->update(['condicion' =>0]);
    //     return redirect('admin/users/'.$request->user_id.'/edit');
    // }
    // protected function activar(Request $request)
    // {
    //     return $request;
    //     SucursalUser::where('id',$request->id)->update(['condicion' =>1]);
    //     return redirect('admin/users/'.$request->user_id.'/edit');
    // }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }





    // para obrtener las personas interna o externas
    public function getFuncionario(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $type = (int) $request->input('type', 1);
        $likeSearch = '%' . $search . '%';

        if ($type == 1) {
            $personas = DB::connection('mamore')->table('people as p')
                ->join('contracts as c', 'c.person_id', 'p.id')
                ->select('p.id', 'p.first_name as nombre', DB::raw("CONCAT(COALESCE(p.paternal_surname, ''), ' ', COALESCE(p.maternal_surname, '')) as apellido"), 'p.ci', DB::raw("CONCAT(p.first_name, ' ',  COALESCE(p.paternal_surname, ''), ' ', COALESCE(p.maternal_surname, '')) as nombre_completo"))
                ->distinct()
                ->where('c.status', 'firmado')
                ->where('p.deleted_at', null)
                ->where('c.deleted_at', null)
                ->where(function ($query) use ($likeSearch) {
                    $query->where('p.ci', 'like', $likeSearch)
                        ->orWhereRaw("CONCAT(COALESCE(p.first_name, ''), ' ', COALESCE(p.paternal_surname, ''), ' ', COALESCE(p.maternal_surname, '')) like ?", [$likeSearch]);
                })
                ->limit(20)
                ->get();
            $response = array();
            foreach ($personas as $persona) {

                $response[] = array(
                    "id" => $persona->id,
                    "text" => $persona->nombre_completo,
                    "nombre" => $persona->nombre,
                    "apellido" => $persona->apellido,
                    // "ap_materno" => $persona->apellido,
                    "ci" => $persona->ci,
                    // "alfanum" => $persona->alfanu,
                    // "departamento_id" => $persona->Expedido
                );
            }
        } else {
            $personas = DB::table('sysalmacen.people_exts as s')
                ->join('sysadmin.people as m', 'm.id', '=', 's.people_id')
                ->select(
                    'm.id',
                    DB::raw("CONCAT(m.first_name, ' ', COALESCE(m.paternal_surname, ''), ' ', COALESCE(m.maternal_surname, '')) as text"),
                    'm.first_name as nombre',
                    DB::raw("CONCAT_WS(' ', m.paternal_surname, m.maternal_surname) as apellido"),
                    'm.ci',
                )
                ->where(function ($query) use ($likeSearch) {
                    $query->where('m.ci', 'like', $likeSearch)
                        ->orWhereRaw("CONCAT(COALESCE(m.first_name, ''), ' ', COALESCE(m.paternal_surname, ''), ' ', COALESCE(m.maternal_surname, '')) like ?", [$likeSearch]);
                })
                ->where('s.status', 1)
                ->where('s.deleted_at', null)
                ->limit(20)
                ->get();

            $response = array();
            foreach ($personas as $persona) {

                $response[] = array(
                    "id" => $persona->id,
                    "text" => $persona->text,
                    "nombre" => $persona->nombre,
                    "apellido" => $persona->apellido,
                    "ci" => $persona->ci,
                );
            }
        }
        return response()->json($response);
    }


    public function create_user(Request $request)
    {
        // return $request;


        $ok = User::where('funcionario_id', $request->funcionario_id)->first();
        if ($ok) {
            return redirect()->route('voyager.users.index')->with(['message' => 'El Funcionario ya cuenta con usuario.', 'alert-type' => 'error']);
        }

        $ok = User::where('email', $request->email)->first();
        if ($ok) {
            return redirect()->route('voyager.users.index')->with(['message' => 'Elija otro correo por favor.', 'alert-type' => 'error']);
        }

        DB::beginTransaction();
        try {

            $user = User::create([
                'name' =>  $request->name,
                'funcionario_id' => $request->funcionario_id,
                'role_id' => $request->role_id,
                'email' => $request->email,
                'sucursal_id' => $request->sucursal_id,
                'subSucursal_id' => $request->subSucursal_id,
                'unidadAdministrativa_id' => $request->unit_id,
                'direccionAdministrativa_id' => $request->direction_id,
                'avatar' => 'users/default.png',
                'password' => bcrypt($request->password),
                'must_change_password' => true,
                'status' => $request->input('status', 1),
            ]);

            // return 1;

            if ($request->user_belongstomany_role_relationship <> '') {
                $user->roles()->attach($request->user_belongstomany_role_relationship);
            }

            // return $request;
            if ($request->sucursal_id) {
                SucursalUser::create(['sucursal_id' => $request->sucursal_id, 'user_id' => $user->id]);
            }

            // return 1;

            DB::commit();
            return redirect()->route('voyager.users.index')->with(['message' => "El usuario, se registro con exito.", 'alert-type' => 'success']);
        } catch (\Exception $e) {
            DB::rollback();

            // return 0;
            return redirect()->route('voyager.users.index')->with(['message' => 'Ocurrio un error.', 'alert-type' => 'error']);
        }
    }



    public function update_user(Request $request, User $user)
    {
        // Destino de redireccion: el listado si el usuario tiene permiso de verlo,
        // en caso contrario el dashboard (/admin) para evitar caer en una pagina sin acceso.
        $redirectRoute = $request->user()->hasPermission('browse_users')
            ? 'voyager.users.index'
            : 'voyager.dashboard';

        $ok = User::where('funcionario_id', $request->funcionario_id)->where('id', '!=', $user->id)->first();
        // return $ok;
        if ($ok) {
            return redirect()->route($redirectRoute)->with(['message' => 'El Funcionario ya cuenta con usuario.', 'alert-type' => 'error']);
        }

        $ok = User::where('email', $request->email)->where('id', '!=', $user->id)->first();
        if ($ok) {
            return redirect()->route($redirectRoute)->with(['message' => 'Elija otro correo por favor.', 'alert-type' => 'error']);
        }

        $status = $request->has('status') ? (bool) $request->input('status', 1) : $user->status;

        if ((int) $request->user()->id === (int) $user->id) {
            $status = true;
        }

        // return $request;
        DB::beginTransaction();
        try {
            $user->update([
                // 'role_id' => $request->role_id,
                'email' => $request->email,
                'sucursal_id' => $request->sucursal_id,
                'subSucursal_id' => $request->subSucursal_id,
                'unidadAdministrativa_id' => $request->unit_id,
                'direccionAdministrativa_id' => $request->direction_id,
                'status' => $status,
            ]);

            if (!$user->status) {
                DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->delete();
            }

            if ($request->password != '') {
                $user->password = bcrypt($request->password);
                $user->must_change_password = ($request->user()->id !== $user->id);
                $user->save();

                //Eliminar las sesiones del usuario por cambio de contraseña
                DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->delete();
            }
            if ($request->role_id) {
                // return 1;
                $user->role_id = $request->role_id;
                $user->save();
            }
            // return 2;

            if ($request->funcionario_id != '') {
                $user->update([
                    'funcionario_id' => $request->funcionario_id,
                    'name' => $request->name,
                ]);
            }
            // $contract = Contract::with(['unidad'])->where('person_id', $user->funcionario_id)->where('deleted_at', null)->where('status', 'firmado')->first();

            // if($contract)
            // {
            //     if(!$contract->unidad_administrativa_id)
            //     {
            //         $contract->update(['unidad_administrativa_id' => $request->unidad_administrativa_id]);
            //     }
            // }


            SucursalUser::where('deleted_at', null)->where('user_id', $user->id)->where('condicion', 1)->update(['condicion' => 0]);

            if ($request->sucursal_id) {
                SucursalUser::create(['sucursal_id' => $request->sucursal_id, 'user_id' => $user->id]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }

        if ($request->user_belongstomany_role_relationship <> '') {
            $user->roles()->sync($request->user_belongstomany_role_relationship);
        }
        return redirect()->route($redirectRoute)->with([
            'message' => "El usuario, se actualizo con exito.",
            'alert-type' => 'success'
        ]);
    }

    public function toggle_status(User $user)
    {
        if ((int) auth()->id() === (int) $user->id) {
            return redirect()
                ->route('voyager.users.index')
                ->with([
                    'message' => 'No puede desactivar su propio usuario.',
                    'alert-type' => 'error',
                ]);
        }

        $user->status = !$user->status;
        $user->save();

        if (!$user->status) {
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();
        }

        return redirect()
            ->route('voyager.users.index')
            ->with([
                'message' => $user->status
                    ? 'El usuario fue activado.'
                    : 'El usuario fue desactivado.',
                'alert-type' => 'success',
            ]);
    }

    public function getSubSucursal($id)
    {
        return SucursalSubAlmacen::where('sucursal_id', $id)
            ->where('deleted_at', null)->get();
    }

    //<- sessions
    public function showSessions()
    {
        $sessions = DB::table('sessions')
            ->where('user_id', auth()->id())
            ->orderBy('last_activity', 'DESC')
            ->get();

        return view('sessions', ['sessions' => $sessions]);
    }

    public function updatePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ], [
            'photo.required' => 'Seleccione una imagen.',
            'photo.image'    => 'El archivo debe ser una imagen.',
            'photo.mimes'    => 'Formatos permitidos: jpg, jpeg, png, webp.',
            'photo.max'      => 'La imagen no debe superar los 4MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('sessions')
                ->with(['message' => $validator->errors()->first(), 'alert-type' => 'error']);
        }

        $user = User::find(Auth::id());
        $disk = config('voyager.storage.disk');

        try {
            // Subir nueva foto al disco configurado (s3 en produccion, prefijo AWS_ROOT)
            $path = $request->file('photo')->store('avatars', $disk);

            // Eliminar la foto anterior si era personalizada (no la default ni una URL externa).
            // delete() es idempotente en S3/Spaces; no usar exists() (lanza error en Spaces si falta).
            $old = $user->avatar;
            if ($old && $old !== 'users/default.png' && !filter_var($old, FILTER_VALIDATE_URL)) {
                Storage::disk($disk)->delete($old);
            }

            $user->avatar = $path;
            $user->save();

            return redirect()->route('sessions')
                ->with(['message' => 'Foto actualizada con éxito.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            return redirect()->route('sessions')
                ->with(['message' => 'Error al subir la foto: ' . $e->getMessage(), 'alert-type' => 'error']);
        }
    }

    public function removePhoto()
    {
        $user = User::find(Auth::id());
        $disk = config('voyager.storage.disk');

        try {
            $old = $user->avatar;

            // Borrar la foto personalizada (no la default ni una URL externa).
            // delete() es idempotente; no usar exists() (lanza error en Spaces si falta).
            if ($old && $old !== 'users/default.png' && !filter_var($old, FILTER_VALIDATE_URL)) {
                Storage::disk($disk)->delete($old);
            }

            $user->avatar = 'users/default.png';
            $user->save();

            return redirect()->route('sessions')
                ->with(['message' => 'Foto eliminada. Se restauró la imagen por defecto.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            return redirect()->route('sessions')
                ->with(['message' => 'Error al quitar la foto: ' . $e->getMessage(), 'alert-type' => 'error']);
        }
    }

    public function deleteSession(Request $request)
    {
        $sessionId = $request->id;
        $currentSessionId = session()->getId();

        if ($sessionId != $currentSessionId) {
            DB::table('sessions')
                ->where('id', $sessionId)
                ->where('user_id', auth()->id())
                ->delete();
        }

        return redirect()->route('sessions');
    }
    //sessions->
    //<- cambio de contraseña
    public function changePassword(Request $request, User $user)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        //octener session actual
        $currentSessionId = session()->getId();
        // elimina las sesiones excepto la actual.
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $currentSessionId)
            ->delete();

        $user->update([
            'password' => bcrypt($request->input('password')),
            'must_change_password' => false,
        ]);

        // Listado de usuarios si tiene permiso, en caso contrario el dashboard (/admin).
        $redirectRoute = $user->hasPermission('browse_users')
            ? 'voyager.users.index'
            : 'voyager.dashboard';

        return redirect()->route($redirectRoute)->with([
            'message' => 'Su contrasena se actualizo con exito.',
            'alert-type' => 'success',
        ]);
    }
    // cambio de contraseña ->
}
