<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Partida;
use App\Models\Provider;
use App\Models\Article;
use App\Models\Modality;
use App\Models\Sucursal;
use App\Models\SolicitudCompra;
use App\Models\Factura;
use App\Models\DetalleFactura;
use App\Models\User;
use DataTables;
use DateTime;
use Doctrine\DBAL\Exception\RetryableException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\SucursalUser;
use App\Models\InventarioAlmacen;
use App\Models\SucursalSubAlmacen;
use Luecano\NumeroALetras\NumeroALetras; // Para convertir numeros a su equivalente en palabras

class IncomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {      

        // $data = SucursalUser::where('deleted_at', null)->where('condicion', 1)->get();
        // // return $data;
        // for($i =0; $i<count($data); $i++)
        // {
        //     User::where('id', $data[$i]->user_id)->update(['sucursal_id' => $data[$i]->sucursal_id]);
        // }



        // return Auth::user()->id;
        // if(setting('configuracion.maintenance') && !auth()->user()->hasRole('admin') && !auth()->user()->hasRole('almacen_admin'))
        // {
        //     Auth::logout();
        //     return redirect()->route('maintenance');
        // }


        // $sucursal = SucursalUser::where('user_id', Auth::user()->id)->where('condicion', 1)->where('deleted_at', null)->first();

        $user =Auth::user();

        $sucursal = $user->sucursal_id;
        
        // return $sucursal;
        if(!$sucursal && !auth()->user()->hasRole('admin'))
        {
            return view('errors.error');
        }


        $gestion = InventarioAlmacen::where('status', 1)->where('sucursal_id', $sucursal)->where('deleted_at', null)->first();//para ver si hay gestion activa o cerrada

        // return $gestion;
        

        return view('almacenes.income.browse', compact('gestion'));
    }

    public function list($type, $search = null) {
        /**
         * Esta funcion genera el listado de registros de la vista de ingresos "almacenes.income.list"
         * Y los filtra segun el tipo o la busqueda
         * 
         * @param String $type Describe el tipo, puede ser: "todo","constock","sinstock"
         * @param String $search Es un parametro de busqueda para filtrar los datos por nombre,monto,numero de factura,nit ,etc
         * @return view Devuelve los datos a la vista "almacenes.income.list" que a su vez esta en almacenes.income.browse
         */

        $user = Auth::user();
        $sucursal = $user->sucursal_id;
        $gestion = InventarioAlmacen::where('status', 1)->where('sucursal_id', $sucursal)->where('deleted_at', null)->first();
    
        $query_filter = 'sucursal_id = '.$sucursal.' and subSucursal_id = '. $user->subSucursal_id;
    
        if (Auth::user()->hasRole('admin')) {
            $query_filter = 1;
        }
    
        $paginate = request('paginate') ?? 10;

        $data = SolicitudCompra::with(['factura.proveedor', 'modality', 'unidad', 'direccion'])
        ->where(function($query) use ($search){
            $this->addSearchConditions($query,$search);
        })
        ->where('deleted_at', NULL)
        ->whereRaw($query_filter)
        ->orderBy('id', 'DESC');
        
        /* Condicion Extra para filtrar si el usuario quiere ver "Con stock" o "Sin stock"*/
        if ($type == 'constock'){
            $data->where('stock', 1);
        }
        else if ($type =='sinstock'){
            $data->where('stock', 0);
        }

        $data = $data->paginate($paginate);
    
        return view('almacenes.income.list', compact('data', 'gestion'));
    }

    private function addSearchConditions($query, $search) {
        /**
         * Funcion auxiliar para hacer las consultas, de la funcion "list"
         * 
         * @param String $query Es la consulta
         * @param String $search Es un parametro de busqueda
         */
        if ($search) {
            $query->orWhereHas('modality', function ($query) use ($search) {
                $query->where('nombre', 'like', "%$search%");
            })
            ->orWhereHas('factura', function ($query) use ($search) {
                $query->where('montofactura', 'like', "%$search%")
                    ->orWhere('nrofactura', 'like', "%$search%");
            })
            ->orWhereHas('factura.proveedor', function ($query) use ($search) {
                $query->where('razonsocial', 'like', "%$search%")
                    ->orWhere('nit', 'like', "%$search%");
            })
            ->orWhere('gestion','like',"%$search%")
            ->orWhere('nrosolicitud','like',"%$search%");
        }
    }

    public function create()
    {
        $user= Auth::user();
        $sucursal =$user->sucursal_id;
        $sub = $user->subSucursal_id;

        if(!$sucursal || !$sub)
        {
            return "Contactese con el administrador";
        }

        $gestion = InventarioAlmacen::where('status', 1)->where('sucursal_id', $sucursal)->where('deleted_at', null)->first();//para ver si hay gestion activa o cerrada


        // $da = $this->direccionSucursal($sucursal->first()->sucursal_id);

        $da = $this->direccionSucursal($sucursal);

        $proveedor = Provider::where('condicion',1)->where('sucursal_id',$sucursal)->get();
        // $partida = Partida::where('codigo','like','3%')->get();
        $partida = Partida::all();
        $modalidad = Modality::all();
        $sucursal = Sucursal::where('id', $sucursal)->first();
        $sub = SucursalSubAlmacen::where('id', $sub)->first();
        // return $partida;

        return view('almacenes.income.add', compact('sucursal', 'sub', 'da', 'proveedor', 'partida', 'modalidad', 'gestion'));

    }

    // para imprimir el ingreso de cada almacen
    protected function view_ingreso($id)
    {
        // $sol = SolicitudCompra::find($id);
        $sol = SolicitudCompra::with(['sucursal'])
            ->where('id', $id)->first();
        
        $factura = Factura::where('solicitudcompra_id', $sol->id)->where('deleted_at', null)->first();

        $sucursal = Sucursal::find($sol->sucursal_id);

        $modalidad = Modality::find($sol->modality_id);

       
        $unidad = DB::connection('mamore')->table('unidades')
                ->select('nombre')
                ->where('id', $sol->unidadadministrativa)
                ->get();

        $detalle = DB::table('detalle_facturas as df')
                    ->join('articles as a', 'a.id', 'df.article_id')
                    ->join('partidas as p', 'p.id', 'a.partida_id')
                    ->select('p.codigo as partida', 'a.nombre as articulo','a.presentacion','a.id as codigo', 'df.cantsolicitada as cantidad', 'df.precio as precio')
                    ->where('df.hist', 0)
                    ->where('df.deleted_at', null)
                    // ->where('df.condicion', 1)
                    ->where('df.factura_id', $factura->id)
                    ->get();
        
        $proveedor = Provider::find($factura->provider_id);

        $formatter = new NumeroALetras();
        $formatter->apocope = true;
        // para convertir letras a numeros
        
        
        return view('almacenes.income.report',compact('sol','factura', 'detalle', 'sucursal', 'modalidad', 'unidad', 'proveedor','formatter'));
        // return view('income.view', compact('sol','factura', 'detalle', 'sucursal', 'modalidad', 'unidad'));
    }

    // Para ver el Stock de cada almacen
    protected function view_ingreso_stock($id)
    {
        $sol = SolicitudCompra::find($id); 
        
        $factura = Factura::where('solicitudcompra_id', $sol->id)->get();

        $sucursal = Sucursal::find($sol->sucursal_id);

        $modalidad = Modality::find($sol->modality_id);

       
        $unidad = DB::connection('mamore')->table('unidades')
                ->select('nombre')
                ->where('id', $sol->unidadadministrativa)
                ->get();

        $detalle = DB::table('detalle_facturas as df')
                    ->join('articles as a', 'a.id', 'df.article_id')
                    ->join('partidas as p', 'p.id', 'a.partida_id')
                    ->select('p.codigo as partida', 'a.nombre as articulo','a.presentacion','a.id as codigo', 'df.cantsolicitada as cantidad', 'df.cantrestante')
                    ->where('df.hist', 0)
                    ->where('df.deleted_at', null)
                    // ->where('df.condicion', 1)
                    ->where('df.factura_id', $factura[0]->id)
                    ->get();
        
        $proveedor = Provider::find($factura[0]->provider_id);

        
        return view('almacenes.income.reportstock',compact('sol','factura', 'detalle', 'sucursal', 'modalidad', 'unidad', 'proveedor'));
        // return view('income.view', compact('sol','factura', 'detalle', 'sucursal', 'modalidad', 'unidad'));
    }


    // funcion para guardar los registro de un ingreso en general
    public function store(Request $request)
    {      
        // return $request;
        $user = Auth::user();
        DB::beginTransaction();
        try {

            if($request->amount==0)
            {
                return redirect()->route('income.create')->with(['message' => 'Lista de detalle vacia..', 'alert-type' => 'error']);
            }

                $unidad = DB::connection('mamore')->table('unidades')
                        ->select('sigla')
                        ->where('id',$request->unidadadministrativa)
                        ->get();
                // return $unidad;

                $aux = SolicitudCompra::where('unidadadministrativa',$request->unidadadministrativa)
                    ->where('deleted_at', null)
                    ->get();

                    $length = 4;
                    $char = 0;
                    $type = 'd';
                    $format = "%{$char}{$length}{$type}"; // or "$010d";
                    


                $request->merge(['nrosolicitud' => strtoupper($unidad[0]->sigla).'-'.sprintf($format, count($aux)+1)]);
// return $request;
            
                // $gestion = Carbon::parse($request->fechaingreso)->format('Y');

                $solicitud = SolicitudCompra::create([
                        'sucursal_id'       => $request->branchoffice_id,
                        'subSucursal_id' => $request->subSucursal_id,
                        'direccionadministrativa' => $request->direccionadministrativa,
                        'unidadadministrativa'     => $request->unidadadministrativa,
                        'modality_id'           => $request->modality_id,
                        'registeruser_id'       => $user->id,
                        'nrosolicitud'          => $request->nrosolicitud,
                        'fechaingreso'          => $request->fechaingreso,
                        'gestion'               => $request->gestion,
                        'inventarioAlmacen_id'  => $request->inventarioAlmacen_id
                ]);
                
                // return $request;
            
                $factura = Factura::create([
                        'solicitudcompra_id'   => $solicitud->id,
                        'provider_id'           => $request->provider_id,
                        'registeruser_id'       => $user->id,
                        'tipofactura'           => $request->tipofactura,
                        'fechafactura'          => $request->fechafactura,
                        'montofactura'          => $request->amount,
                        'nrofactura'            => $request->nrofactura,
                        'nroautorizacion'       => $request->nroautorizacion,
                        'nrocontrol'            => $request->nrocontrol,
                        'fechaingreso'          => $request->fechaingreso, 
                        'gestion'               => $request->gestion,
                        'sucursal_id'       => $request->branchoffice_id
                ]);
                // return $request;
                // return $factura;
                
                $cont = 0;
        
                while($cont < count($request->article_id))
                {
                    DetalleFactura::create([
                        'factura_id'            => $factura->id,
                        'registeruser_id'       => $user->id,
                        'article_id'            => $request->article_id[$cont],
                        'cantsolicitada'        => $request->cantidad[$cont],
                        'precio'                => $request->precio[$cont],
                        'totalbs'               => $request->subT[$cont],
                        'cantrestante'          => $request->cantidad[$cont],
                        'fechaingreso'          => $request->fechaingreso,
                        'gestion'               => $request->gestion,
                        'sucursal_id'           => $request->branchoffice_id
                    ]);
                    $cont++;
                }
                // return 1;
                DB::commit();
                return redirect()->route('income.index')->with(['message' => 'Registrado exitosamente.', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            return 0;
            return redirect()->route('income.index')->with(['message' => 'Ocurrio un error.', 'alert-type' => 'error']);            
        }
    }

 
    public function edit($id)
    {
        if(setting('configuracion.maintenance')&& !auth()->user()->hasRole('admin') && !auth()->user()->hasRole('almacen_admin'))
        {
            Auth::logout();
            return redirect()->route('maintenance');
        }

        $sucursal = SucursalUser::where('user_id', Auth::user()->id)->where('condicion', 1)->where('deleted_at', null)->get();

        if(count($sucursal) > 1 && count($sucursal) < 1)
        {
            return "Contactese con el administrador";
        }

        // $da = $this->getDireccion();     
        $da = $this->direccionSucursal($sucursal->first()->sucursal_id);


        $solicitud = SolicitudCompra::find($id);


        // $user = Auth::user();
        // $sucursales = Sucursal::join('sucursal_users as u','u.sucursal_id', 'sucursals.id')
        //             ->select('sucursals.id','sucursals.nombre','u.condicion')
        //             ->where('u.condicion',1)
        //             ->where('u.user_id', $user->id)
        //             ->get();


        $sol = SolicitudCompra::find($id);
        // $facturaux = Factura::where('solicitudcompra_id', $id)->first();

        // $factura = Factura::find($facturaux->id);

        $factura = Factura::where('solicitudcompra_id', $id)->first();
  
        
        $detalle = DB::table('detalle_facturas as df')
                ->join('articles as a', 'a.id', 'df.article_id')
                ->join('partidas as p', 'p.id', 'a.partida_id')
                ->select('df.id as de','a.id as articulo_id', 'a.nombre as articulo', 'a.presentacion', 'p.codigo', 'p.nombre as partida', 'df.cantsolicitada', 'df.precio', 'df.totalbs')
                ->where('df.factura_id', $factura->id)
                ->where('df.deleted_at', null)
                ->where('df.hist', 0)
                ->get();

        $proveedorselect = Provider::find($factura->provider_id);



        // $proveedor = Provider::all();
        $proveedor = Provider::where('condicion',1)->where('sucursal_id',$sucursal->first()->sucursal_id)->get();
        $partida = Partida::all();

        $modalidad = Modality::all();

        return view('almacenes.income.edit', compact('da', 'solicitud', 'sucursal', 'detalle', 'partida', 'proveedor', 'sol', 'factura', 'proveedorselect','modalidad'));

    }



    public function update(Request $request)
    {  
        if(setting('configuracion.maintenance')&& !auth()->user()->hasRole('admin') && !auth()->user()->hasRole('almacen_admin'))
        {
            Auth::logout();
            return redirect()->route('maintenance');
        }

        $user = Auth::user();
        $gestion = Carbon::parse($request->fechaingreso)->format('Y');
        DB::beginTransaction();
        try {
            if(floatval($request->total) === floatval($request->montofactura))
            {
                //para agregar un numero de solicitud si en caso se cambia la unidad administrativa
                $aux = SolicitudCompra::find($request->id);            
                if($aux->unidadadministrativa != $request->unidadadministrativa)
                {
                    $unidad = DB::connection('mamore')->table('unidades')
                        ->select('sigla')
                        ->where('id',$request->unidadadministrativa)
                        ->first();

                    $aux = SolicitudCompra::where('unidadadministrativa',$request->unidadadministrativa)
                        ->where('deleted_at', null)
                        ->get();

                    $length = 4;
                    $char = 0;
                    $type = 'd';
                    $format = "%{$char}{$length}{$type}"; // or "$010d";

                    $request->merge(['nrosolicitud' => strtoupper($unidad->sigla).'-'.sprintf($format, count($aux)+1)]);
                }
                else
                {
                    $request->merge(['nrosolicitud' => $aux->nrosolicitud]);
                }

                SolicitudCompra::where('id',$request->id)->update([
                        'sucursal_id'       => $request->branchoffice_id,
                        'direccionadministrativa' => $request->direccionadministrativa,
                        'unidadadministrativa'     => $request->unidadadministrativa,
                        'modality_id'           => $request->modality_id,
                        'registeruser_id'       => $user->id,
                        'nrosolicitud'          => $request->nrosolicitud,
                        'fechaingreso'          => $request->fechaingreso,
                        'gestion'               => $gestion
                ]);
    
            
                Factura::where('solicitudcompra_id',$request->id)->update([
                        'provider_id'           => $request->provider_id,
                        'registeruser_id'       => $user->id,
                        'tipofactura'           => $request->tipofactura,
                        'fechafactura'          => $request->fechafactura,
                        'montofactura'          => $request->montofactura,
                        'nrofactura'            => $request->nrofactura,
                        'nroautorizacion'       => $request->nroautorizacion,
                        'nrocontrol'            => $request->nrocontrol,
                        'fechaingreso'          => $request->fechaingreso, 
                        'gestion'               => $gestion
                ]);

                $factura = Factura::where('solicitudcompra_id',$request->id)->first();
                // return $factura;
                
                DetalleFactura::where('factura_id', $factura->id)->update(['deleted_at' => Carbon::now()]);
                // DetalleFactura::where('factura_id', $factura->id)->delete();
                // return 1;
                $cont = 0;

                // return 1;
        
                while($cont < count($request->article_id))
                {
                    DetalleFactura::create([
                        'factura_id'            => $factura->id,
                        'registeruser_id'       => $user->id,
                        'article_id'            => $request->article_id[$cont],
                        'cantsolicitada'        => $request->cantidad[$cont],
                        'precio'                => $request->precio[$cont],
                        'totalbs'               => $request->totalbs[$cont],
                        'cantrestante'          => $request->cantidad[$cont],
                        'fechaingreso'          => $request->fechaingreso,
                        'gestion'               => $gestion
                    ]);
                    $cont++;
                }

                DB::commit();
                // return 1;
                return redirect()->route('income.index')->with(['message' => 'Actualizado exitosamente.', 'alert-type' => 'success']);
            }
            else
            {
                return redirect()->route('income.edit', $request->id)->with(['message' => 'El monto de la factura no coincide con el total de la solicitud.', 'alert-type' => 'error']);
            }
        } catch (\Throwable $th) {
            
            DB::rollback();
            // return 0;
            return redirect()->route('income.index')->with(['message' => 'Ocurrio un error.', 'alert-type' => 'error']);
            
        }
        // return redirect()->route('income.index');
        // return redirect('admin/income');
    }

    public function salida($id)
    {
        if(setting('configuracion.maintenance')&& !auth()->user()->hasRole('admin') && !auth()->user()->hasRole('almacen_admin'))
        {
            Auth::logout();
            return redirect()->route('maintenance');
        }


        // return $id;
        // $factura = Factura::where('solicitudcompra_id', $id)->first();
        // $detalle = DetalleFactura::where('factura_id', $factura->id)->where('deleted_at', null)->get();

        $detalle = DB::table('solicitud_egresos as se')
                    ->join('detalle_egresos as de', 'de.solicitudegreso_id', 'se.id')
                    ->join('detalle_facturas as df', 'df.id', 'de.detallefactura_id')
                    ->join('facturas as f', 'f.id', 'df.factura_id')

                    ->where('se.deleted_at', null)
                    ->where('de.deleted_at', null)
                    ->where('df.deleted_at', null)//opcional
                    ->where('df.hist', 0)

                    ->where('f.deleted_at', null)
                    ->where('f.solicitudcompra_id', $id)
                    // ->select('se.id', 'se.nropedido', 'se.fechasolicitud', 'se.fechaegreso')
                    // ->orderBy('se.nropedido')->get();

                    ->select('se.id', 'se.nropedido', 'se.fechasolicitud', 'se.fechaegreso')
                    ->groupBy('se.id', 'se.nropedido', 'se.fechasolicitud', 'se.fechaegreso')
                    ->orderBy('se.nropedido')->get();


                    // return $detalle;

        return view('almacenes.income.salida', compact('detalle'));
    }














    public function destroy(Request $request)
    {    
        if(setting('configuracion.maintenance')&& !auth()->user()->hasRole('admin') && !auth()->user()->hasRole('almacen_admin'))
        {
            Auth::logout();
            return redirect()->route('maintenance');
        }
        $user =Auth::user();
        DB::beginTransaction();
        try{

            $sol = SolicitudCompra::find($request->id);
            SolicitudCompra::where('id', $sol->id)->update(['deleteuser_id'=>$user->id, 'deleted_at' => Carbon::now()]);
    
            $fac = Factura::where('solicitudcompra_id',$sol->id)->get();
            Factura::where('solicitudcompra_id', $sol->id)->update(['deleteuser_id'=>$user->id, 'deleted_at' => Carbon::now()]);

            DetalleFactura::where('factura_id', $fac[0]->id)->where('hist',0)->update(['deleteuser_id'=>$user->id,'deleted_at' => Carbon::now()]);

            DB::commit();
            return redirect()->route('income.index')->with(['message' => 'Ingreso Eliminado Exitosamente.', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('income.index')->with(['message' => 'Ocurrio un error.', 'alert-type' => 'error']);
        }
            

    }





    protected function ajax_unidad_administrativa($id)//para la view de ingreso y egreso
    {
        
        return DB::connection('mamore')->table('unidades')
                        ->where('estado', 1)
                        ->where('deleted_at', null)
                        ->where('direccion_id',$id)
                        ->select('*')
                        ->orderBy('nombre', 'asc')
                        ->get();
    }







    // protected function ajax_unidad_solicitante($id)//para la view de ingreso y egreso
    // {
    //     return RequestingUnit::where('executingunit_id', $id)->get();
    // }
    protected function ajax_article($id)
    {
        // $sucursal = SucursalUser::where('user_id', Auth::user()->id)->where('condicion', 1)->where('deleted_at', null)->first();
        return Article::where('partida_id', $id)->where('condicion', 1)->get();
    }
    protected function ajax_presentacion($id)
    {
        return Article::find($id);
    }
}
