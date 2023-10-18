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
        
        for($i =0; $i<count($data); $i++)
        {
            User::where('id', $data[$i]->id)->update(['sucursal_id' => $data[$i]->sucursal_id]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

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

        $ok = SucursalUser::where('user_id', $request->user_id)->where('condicion',1)->first();
        if($ok)
        {
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
    public function getFuncionario(Request $request){
        $search = $request->search;
        $type = $request->type;
            if($type==1)
            {
                $personas = DB::connection('mamore')->table('people as p')
                    ->join('contracts as c', 'c.person_id', 'p.id')
                    ->select('p.id', 'p.first_name as nombre', 'p.last_name as apellido', 'p.ci' , DB::raw("CONCAT(p.first_name, ' ', p.last_name) as nombre_completo"))
                    ->where('c.status', 'firmado')
                    ->where('p.deleted_at', null)
                    ->where('c.deleted_at', null)
                    // ->where('p.ci', 'like', '%' .$search . '%')
                    ->whereRaw('(p.ci like "%' .$search . '%" or '.DB::raw("CONCAT(p.first_name, ' ', p.last_name)"). 'like "%' . $search . '%")')
                    ->get();
                    $response = array();
                foreach($personas as $persona){

                    $response[] = array(
                        "id"=>$persona->id,
                        "text"=>$persona->nombre_completo,
                        "nombre" => $persona->nombre,
                        "apellido" => $persona->apellido,
                        // "ap_materno" => $persona->apellido,
                        "ci" => $persona->ci,
                        // "alfanum" => $persona->alfanu,
                        // "departamento_id" => $persona->Expedido
                    );
                }
            }
            else
            {
                $personas = DB::table('sysalmacen.people_exts as s')
                ->join('sysadmin.people as m', 'm.id', '=', 's.people_id')
                ->select(
                    'm.id',
                    DB::raw("CONCAT(m.first_name, ' ', m.last_name) as text"),
                    'm.first_name as nombre', 'm.last_name as apellido',
                    'm.ci',
                )
                ->whereRaw('(m.ci like "%' .$search . '%" or '.DB::raw("CONCAT(m.first_name, ' ', m.last_name)").' like "%' .$search . '%")')
                ->where('s.status',1)
                ->where('s.deleted_at',null)
                ->get();

                $response = array();
                foreach($personas as $persona){

                    $response[] = array(
                        "id"=>$persona->id,
                        "text"=>$persona->text,
                        "nombre" => $persona->nombre,
                        "apellido" => $persona->apellido,
                        "ci" => $persona->ci,
                    );
                }
            }  
        return response()->json($response);
    }


    public function create_user(Request $request){
        // return $request;


        $ok = User::where('funcionario_id', $request->funcionario_id)->first();
        if($ok)
        {
            return redirect()->route('voyager.users.index')->with(['message' => 'El Funcionario ya cuenta con usuario.', 'alert-type' => 'error']);
        }

        $ok = User::where('email', $request->email)->first();
        if($ok)
        {
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
                'avatar' => 'users/default.png',
                'password' => bcrypt($request->password),
            ]);
            
            // return 1;
            
            if ($request->user_belongstomany_role_relationship <> '') {
                $user->roles()->attach($request->user_belongstomany_role_relationship);
            }

            // return $request;
            if($request->sucursal_id)
            {
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



    public function update_user(Request $request, User $user){

        $ok = User::where('funcionario_id', $request->funcionario_id)->where('id', '!=', $user->id)->first();
        // return $ok;
        if($ok)
        {
            return redirect()->route('voyager.users.index')->with(['message' => 'El Funcionario ya cuenta con usuario.', 'alert-type' => 'error']);
        }

        $ok = User::where('email', $request->email)->where('id', '!=', $user->id)->first();
        if($ok)
        {
            return redirect()->route('voyager.users.index')->with(['message' => 'Elija otro correo por favor.', 'alert-type' => 'error']);
        }

        // return $request;
        DB::beginTransaction();
        try {
            $user->update([
                // 'role_id' => $request->role_id,
                'email' => $request->email,
                'sucursal_id'=>$request->sucursal_id,
                'subSucursal_id'=>$request->subSucursal_id,
                'unidadAdministrativa_id' => $request->unit_id,
                'direccionAdministrativa_id'=> $request->direction_id
            ]);
            
            if ($request->password != '') {
                $user->password = bcrypt($request->password);
                $user->save();

                //Eliminar las sesiones del usuario por cambio de contraseña
                DB::table('sessions')
                ->where('user_id',$user->id)
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


            SucursalUser::where('deleted_at', null)->where('user_id', $user->id)->where('condicion',1)->update(['condicion'=>0]);

            if($request->sucursal_id)
            {
                SucursalUser::create(['sucursal_id' => $request->sucursal_id, 'user_id' => $user->id]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }  

        if ($request->user_belongstomany_role_relationship <> '') {
            $user->roles()->sync($request->user_belongstomany_role_relationship);
        }
        return redirect()
        ->route('voyager.users.index')
        ->with([
                'message' => "El usuario, se actualizo con exito.",
                'alert-type' => 'success'
            ]);
    }

    public function getSubSucursal($id)
    {
        return SucursalSubAlmacen::where('sucursal_id', $id)
            ->where('deleted_at', null)->get();
    }

    //<- sessions
    public function showSessions(){
        $sessions = DB::table('sessions')
        ->where('user_id', auth()->id())
        ->orderBy('last_activity', 'DESC')
        ->get();
        
        return view('sessions', ['sessions' => $sessions]);
    }
    public function deleteSession(Request $request){
        DB::table('sessions')
        ->where('id', $request->id)
        ->where('user_id', auth()->id())
        ->delete();

        return redirect()->route('sessions');
    }
    //sessions->
    //<- cambio de contraseña
    public function changePassword(Request $request,User $user){
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8|confirmed', // La regla "confirmed" verifica que la contraseña coincida con la confirmación.
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        //octener session actual
        $currentSessionId= session()->getId();
        // elimina las sesiones excepto la actual.
        DB::table('sessions')
        ->where('user_id',$user->id)
        ->where('id','!=',$currentSessionId)
        ->delete();
        
        $user->update([
            'password' => bcrypt($request->input('password'))
        ]);
        return redirect('/');
    }
    // cambio de contraseña ->
}
