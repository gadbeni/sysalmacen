<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\SolicitudCompra;
use App\Models\Article;
use App\Models\Factura;
use App\Models\DetalleFactura;
use App\Models\InventarioAlmacen;
use Faker\Provider\ar_JO\Company;
use Illuminate\Support\Facades\Auth;
use App\Models\Sucursal;
use App\Models\SucursalUser;
use App\Models\Direction;
use App\Models\Provider;
use Maatwebsite\Excel\Facades\Excel;

use App\Exports\AnualPartidaExport;
use App\Exports\AnualDaExport;
use App\Exports\AnualDetalleExport;
use App\Exports\ArticleStockExport;
use App\Exports\ProviderListExport;
use App\Exports\ArticleListExport;
use App\Exports\ArticleIncomeOfficeExport;
use App\Exports\ArticleEgressOfficeExport;
use App\Models\DetalleEgreso;
use App\Models\Partida;
use App\Models\SolicitudEgreso;
use App\Models\Unit;
use Illuminate\Support\Arr;
use Luecano\NumeroALetras\NumeroALetras; // Para convertir numeros a su equivalente en palabras

class ReportAlmacenController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    //para los reportes mediantes direciones admistrativa Income y Egress en Bolivianos  saldo
    public function directionIncomeSalida()
    {

        $user = Auth::user();
        $query_filter = 'user_id ='.Auth::user()->id;
        
        if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('almacen_admin'))
        {
            $query_filter = 1;
        }

        $sucursal = SucursalUser::where('condicion', 1)
                        ->where('deleted_at', null)
                        ->whereRaw($query_filter)
                        ->GroupBy('sucursal_id')
                        ->get();       

        return view('almacenes/report/inventarioAnual/direccionAdministrativa/report', compact('sucursal'));
    }
    public function directionIncomeSalidaList(Request $request)
    {
        $gestion = $request->gestion;
        $sucursal = Sucursal::find($request->sucursal_id);


        // para obtener todas las direciones de cada almacen
        $direction = DB::connection('mamore')->table('direcciones as d')
            ->join('sysalmacen.sucursal_direccions as s', 's.direccionAdministrativa_id', 'd.id')
            ->where('s.deleted_at', null)
            ->where('s.sucursal_id', $request->sucursal_id)

            ->select('d.id as direcion_id', 'd.nombre')
            ->orderBy('d.id', 'ASC')

            ->get();

        
        //Para obtener los saldos de cada almacen de las GESTIONES anteriores 
        $saldos = DB::table('solicitud_compras as sc')
                ->join('facturas as f', 'f.solicitudcompra_id', 'sc.id')       
                ->join('detalle_facturas as df', 'df.factura_id', 'f.id')  

                ->where('sc.deleted_at', null)
                ->where('sc.sucursal_id', $request->sucursal_id)

                ->where('f.deleted_at', null)

                ->where('df.hist', 1)
                ->where('df.gestion', $gestion)
                ->where('df.deleted_at', null)
                

                ->select('sc.direccionadministrativa as id', DB::raw("SUM(df.cantrestante * df.precio) as saldo"))
                ->groupBy('sc.direccionadministrativa')
                ->orderBy('sc.direccionadministrativa', 'ASC')
                ->get();


        // Para obtener los ingresos de la gestion actual de cada almacen
        $data = DB::table('solicitud_compras as sc')
                ->join('facturas as f', 'f.solicitudcompra_id', 'sc.id')       
                ->join('detalle_facturas as df', 'df.factura_id', 'f.id')   

                ->where('sc.deleted_at', null)
                ->where('sc.sucursal_id', $request->sucursal_id)
                ->where('sc.gestion', $gestion)

                ->where('f.deleted_at', null)

                ->where('df.hist', 0)
                ->where('df.gestion', $gestion)
                ->where('df.deleted_at', null)                    

                ->select('sc.direccionadministrativa as id', DB::raw("SUM(df.cantsolicitada * df.precio) as ingreso"))
                    // ->select('sc.direccionadministrativa as id',DB::raw("SUM(f.montofactura) as ingreso"))
                ->groupBy('sc.direccionadministrativa')
                ->get();

                 
        // Para obtener las salidas de la gestion  actual de cada almacen
        $salida = DB::table('solicitud_egresos as se')
                ->join('detalle_egresos as de', 'de.solicitudegreso_id', 'se.id')

                ->where('se.gestion', $gestion)
                ->where('se.sucursal_id', $request->sucursal_id)
                ->where('se.deleted_at', null)

                ->where('de.deleted_at', null)
                        // ->where('d.direcciones_tipo_id', 1)
                ->select('se.direccionadministrativa as id', DB::raw("SUM(de.cantsolicitada * de.precio) as salida"))
                        // ->select('d.id',DB::raw("SUM(de.totalbs) as salida"))

                ->groupBy('se.direccionadministrativa')
                ->get();

        
            foreach($direction as $item)
            {
                $item->inicio="0.0";

                foreach($saldos as $sitem)
                {
                    if($item->direcion_id == $sitem->id)
                    {
                        $item->inicio = $sitem->saldo;
                    }
                }


                $item->ingreso="0.0";
                foreach($data as $ditem)
                {
                    if($item->direcion_id == $ditem->id)
                    {
                        $item->ingreso = $ditem->ingreso;
                    }
                }

                $item->salida="0.0";
                foreach($salida as $eitem)
                {
                    if($item->direcion_id == $eitem->id)
                    {
                        $item->salida = $eitem->salida;
                    }
                }
            }



        if($request->print==1)
        {
            return view('almacenes/report/inventarioAnual/direccionAdministrativa/print', compact('direction', 'gestion', 'sucursal'));
        }
        if($request->print==2)
        {
            return Excel::download(new AnualDaExport($direction), $sucursal->nombre.' - DA Anual '.$gestion.'.xlsx');
        }
        if($request->print ==NULL)
        {            
            return view('almacenes/report/inventarioAnual/direccionAdministrativa/list', compact('direction'));
        }
    }

    // ################################################################################
    // para ver el inventario por partida anual
    public function inventarioPartida()
    { 
        $user = Auth::user();
        $query_filter = 'user_id ='.Auth::user()->id;
        
        if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('almacen_admin'))
        {
            $query_filter = 1;
        }

        $sucursal = SucursalUser::where('condicion', 1)
                        ->where('deleted_at', null)
                        ->whereRaw($query_filter)
                        ->GroupBy('sucursal_id')
                        ->get();  

        return view('almacenes/report/inventarioAnual/partidaGeneral/report', compact('sucursal'));
    }

    public function inventarioPartidaList(Request $request)
    {
        // dd($request);
        $gestion = $request->gestion;
        // return $gestion;
        $date = Carbon::now();
        $sucursal = Sucursal::find($request->sucursal_id);
        // esta fi esta funcionando
            $partida = Partida::all();

            foreach($partida as $item)
            {
                $item->cantidadinicial="0.0";
                $item->totalinicial="0.0";
                $item->cantfinal="0.0";
                $item->totalfinal="0.0";
            }
            

            $saldo = DB::table('solicitud_compras as sc')
                        ->join('facturas as f', 'f.solicitudcompra_id', 'sc.id')       
                        ->join('detalle_facturas as df', 'df.factura_id', 'f.id')   

                        ->join('articles as a', 'a.id', 'df.article_id')
                        ->join('partidas as p', 'p.id', 'a.partida_id')

                        ->where('sc.deleted_at', null)
                        ->where('sc.sucursal_id', $request->sucursal_id)

                        ->where('f.deleted_at', null)

                        ->where('df.hist', 1)
                        // ->where('df.condicion', 1)
                        ->where('df.gestion', $gestion)
                        ->where('df.deleted_at', null)                    

                        ->select('p.id', 'p.nombre', DB::raw("SUM(df.cantrestante * df.precio) as s_inicialbs"), DB::raw("SUM(df.cantrestante) as s_inicialc"))
                        ->groupBy('p.id')
                        ->get();

            foreach($partida as $x)
            {
                foreach($saldo as $y)
                {
                    if($x->id == $y->id)
                    {
                        $x->cantidadinicial=$x->cantidadinicial + $y->s_inicialc;
                        $x->totalinicial=$x->totalinicial + $y->s_inicialbs;
                    }
                }
            }

            $data = DB::table('solicitud_compras as sc')
                        ->join('facturas as f', 'f.solicitudcompra_id', 'sc.id')
                        ->join('detalle_facturas as df', 'df.factura_id', 'f.id')
                        ->join('articles as a', 'a.id', 'df.article_id')
                        ->join('partidas as p', 'p.id', 'a.partida_id')
                        // ->leftJoin('detalle_egresos as de', 'de.detallefactura_id', 'df.id')
                        ->where('sc.deleted_at', null)
                        ->where('sc.gestion', $gestion)

                        ->where('f.deleted_at', null)
                        ->where('df.deleted_at', null)
                        ->where('df.hist', 0)
                        // ->where('df.histgestion', $gestion)
                        ->where('sc.sucursal_id', $request->sucursal_id)
                        
                        // ->where('de.deleted_at', null)
                        ->select('p.id', 'p.codigo', 'p.nombre',DB::raw("SUM(df.cantsolicitada) as cantidadinicial"), DB::raw("SUM(df.cantsolicitada * df.precio) as totalinicial")
                                )
                        ->groupBy('p.id')
                        ->get();
            foreach($partida as $x)
            {
                foreach($data as $y)
                {
                    if($x->id == $y->id)
                    {
                        $x->cantidadinicial=$x->cantidadinicial + $y->cantidadinicial;
                        $x->totalinicial=$x->totalinicial + $y->totalinicial;
                    }
                }
            }

            $salida = DB::table('solicitud_egresos as se')
                        ->join('detalle_egresos as de', 'de.solicitudegreso_id', 'se.id')
                        ->join('detalle_facturas as df', 'df.id', 'de.detallefactura_id')

                        ->join('articles as a', 'a.id', 'df.article_id')
                        ->join('partidas as p', 'p.id', 'a.partida_id')

                        ->where('se.gestion', $gestion)
                        ->where('se.sucursal_id', $request->sucursal_id)
                        ->where('se.deleted_at', null)

                        ->where('de.deleted_at', null)
                        // ->where('df.gestion', $gestion)

                                // ->where('d.direcciones_tipo_id', 1)
                        ->select('p.id', 'p.codigo', 'p.nombre', DB::raw("SUM(de.cantsolicitada) as cantfinal"), DB::raw("SUM(de.cantsolicitada * de.precio) as salida"))
                                // ->select('d.id',DB::raw("SUM(de.totalbs) as salida"))

                        ->groupBy('p.id')
                        ->get();
                

            foreach($partida as $x)
            {
                foreach($salida as $y)
                {
                    if($x->id == $y->id)
                    {
                        $x->cantfinal=$x->cantidadinicial - $y->cantfinal;
                        $x->totalfinal=$x->totalinicial - $y->salida;
                    }
                }
            }
        
        
        if($request->print==1)
        {
            return view('almacenes/report/inventarioAnual/partidaGeneral/print', compact('partida', 'gestion', 'sucursal'));
        }
        if($request->print==2)
        {
            return Excel::download(new AnualPartidaExport($partida, $gestion), $sucursal->nombre.' - Partida Anual '.$gestion.'.xlsx');
        }
        if($request->print ==NULL)
        {            
            return view('almacenes/report/inventarioAnual/partidaGeneral/list', compact('partida', 'gestion', 'sucursal'));
        }
    }
    // ################################################################################

    //para el inventario anual Detallado por ITEM
    public function inventarioDetalle()
    {
        $user = Auth::user();
        $query_filter = 'user_id ='.Auth::user()->id;
                        
        if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('almacen_admin'))
        {
            $query_filter = 1;
        }
                
        $sucursal = SucursalUser::where('condicion', 1)
                ->where('deleted_at', null)
                ->whereRaw($query_filter)
                ->GroupBy('sucursal_id')
                ->get();        

        return view('almacenes/report/inventarioAnual/detalleGeneral/report', compact('sucursal'));
    }

    public function inventarioDetalleList(Request $request)
    {
        $gestion = $request->gestion;

        $date = Carbon::now();
        $sucursal = Sucursal::find($request->sucursal_id);

 
        $collection = collect([
        ]);

        $saldo = DB::table('solicitud_compras as sc')
                        ->join('facturas as f', 'f.solicitudcompra_id', 'sc.id')       
                        ->join('detalle_facturas as df', 'df.factura_id', 'f.id')   

                        ->join('articles as a', 'a.id', 'df.article_id')

                        ->where('sc.deleted_at', null)
                        ->where('sc.sucursal_id', $request->sucursal_id)

                        ->where('f.deleted_at', null)

                        ->where('df.hist', 1)
                        // ->where('df.condicion', 1)
                        ->where('df.gestion', $gestion)
                        ->where('df.deleted_at', null)                    

                        ->select('a.id', 'a.nombre', 'a.presentacion', 'df.precio', DB::raw("SUM(df.cantrestante * df.precio) as bssaldo"), DB::raw("SUM(df.cantrestante) as saldo"))
                        ->groupBy('a.id')
                        ->groupBy('df.precio')
                        
                        ->get();

        foreach($saldo as $item)
        {
            $collection->add(["id"=>$item->id, "nombre"=>$item->nombre, "presentacion"=>$item->presentacion,"precio"=>$item->precio, "saldo"=> $item->saldo, "entrada"=>0.0, "salida"=>0.0, "final"=>0.0, "bssaldo"=>$item->bssaldo, "bsentrada"=>0.0, "bssalida"=>0.0, "bsfinal"=>0.0]);
        }

        $data = DB::table('solicitud_compras as sc')
            ->join('facturas as f', 'f.solicitudcompra_id', 'sc.id')
            ->join('detalle_facturas as df', 'df.factura_id', 'f.id')
            ->join('articles as a', 'a.id', 'df.article_id')

            ->where('sc.gestion', $gestion)
            ->where('sc.deleted_at', null)
            ->where('f.deleted_at', null)
            ->where('df.deleted_at', null)
            ->where('df.hist', 0)

            ->where('sc.sucursal_id', $request->sucursal_id)
            ->select('a.id', 'a.presentacion', 'a.nombre', 'df.precio',
                DB::raw("SUM(df.cantsolicitada) as entrada"), 

                DB::raw("SUM(df.cantsolicitada * df.precio) as bsentrada"), 
            )
            ->groupBy('a.id')
            ->groupBy('df.precio')
            ->get();

        $salidas = DB::table('solicitud_egresos as se')
            ->join('detalle_egresos as de', 'de.solicitudegreso_id', 'se.id')
            ->join('detalle_facturas as df', 'df.id', 'de.detallefactura_id')

            ->join('articles as a', 'a.id', 'df.article_id')

            ->where('se.gestion', $gestion)
            ->where('se.sucursal_id', $request->sucursal_id)
            ->where('se.deleted_at', null)

            ->where('de.deleted_at', null)

            ->select('a.id', 'a.presentacion', 'a.nombre', 'de.precio', 
                DB::raw("SUM(de.cantsolicitada) as salida"), 
                DB::raw("SUM(de.cantsolicitada * de.precio) as bssalida"))

            ->groupBy('a.id', 'de.precio')
            ->get();
        

        if(count($saldo))
        {
            $i=0;
            $ok=false;
            $y = count($collection);

            foreach($data as $item)
            {                
                $ok=false;
                $id = null;
                $nombre = null;
                $precio = null;
                $presentacion = null;
                $saldo = null;
                $bssaldo = null;
                // $salida=null;
                // $bssalida=null;
                for($j=0; $j < $y; $j++)
                {
                    if($item->precio == $collection[$j]['precio'] && $item->id == $collection[$j]['id'])
                    {
                        $i=$j;
                        $ok=true;
                        $id = $collection[$j]['id'];
                        $nombre = $collection[$j]['nombre'];
                        $precio = $collection[$j]['precio'];
                        $presentacion = $collection[$j]['presentacion'];
                        $saldo = $collection[$j]['saldo'];
                        $bssaldo = $collection[$j]['bssaldo'];
                        // $salida = $collection[$j]['salida'];
                        // $bssalida = $collection[$j]['bssalida'];
                    }
                }
                if($ok)
                {
                    $collection[$i]=["id"=>$id, "nombre"=>$nombre, "presentacion"=>$presentacion,"precio"=>$precio, "saldo"=> $saldo, "entrada"=>$item->entrada, "salida"=>0.0, "final"=>0.0, "bssaldo"=>$bssaldo, "bsentrada"=>$item->bsentrada, "bssalida"=>0.0, "bsfinal"=>0.0];
                }
                else
                {
                    $collection->add(["id"=>$item->id, "nombre"=>$item->nombre, "presentacion"=>$item->presentacion,"precio"=>$item->precio, "saldo"=> 0.0, "entrada"=>$item->entrada, "salida"=>0.0, "final"=>0.0, "bssaldo"=>0.0, "bsentrada"=>$item->bsentrada, "bssalida"=>0.0, "bsfinal"=>0.0]);

                }
            }
        }
        else
        {
            // dump($salidas->sum('bssalida'));

            foreach($data as $item)
            {
                $collection->add(["id"=>$item->id, "nombre"=>$item->nombre, "presentacion"=>$item->presentacion,"precio"=>$item->precio, "saldo"=> 0.0, "entrada"=>$item->entrada, "salida"=>0.0, "final"=>0.0, "bssaldo"=>0.0, "bsentrada"=>$item->bsentrada, "bssalida"=>0.0, "bsfinal"=>0.0]);
            }
        }

        // __________________________________________________________________________________________________________

        $i=0;
        $ok=false;
        $y = count($collection);

        foreach($salidas as $item)
        {                
            $ok=false;
            $id = null;
            $nombre = null;
            $precio = null;
            $presentacion = null;
            $saldo = null;
            $bssaldo = null;
            $entrada = null;
            $bsentrada = null;
            $salida=null;
            $bssalida=null;
            for($j=0; $j < $y; $j++)
            {
                if($item->precio == $collection[$j]['precio'] && $item->id == $collection[$j]['id'])
                {
                    $i=$j;
                    $ok=true;
                    $id = $collection[$j]['id'];
                    $nombre = $collection[$j]['nombre'];
                    $precio = $collection[$j]['precio'];
                    $presentacion = $collection[$j]['presentacion'];

                    $saldo = $collection[$j]['saldo'];
                    $bssaldo = $collection[$j]['bssaldo'];  

                    $entrada = $collection[$j]['entrada'];
                    $bsentrada = $collection[$j]['bsentrada'];

                    $salida = $item->salida;
                    $bssalida = $item->bssalida;
                }
            }
            if($ok)
            {
                $collection[$i]=["id"=>$id, "nombre"=>$nombre, "presentacion"=>$presentacion,"precio"=>$precio, "saldo"=> $saldo, "entrada"=>$entrada, "salida"=>$salida, "final"=>0.0, "bssaldo"=>$bssaldo, "bsentrada"=>$bsentrada, "bssalida"=>$bssalida, "bsfinal"=>0.0];
            }
        }

        $id = null;
        $nombre = null;
        $precio = null;
        $presentacion = null;

        $saldo = null;
        $bssaldo = null;

        $entrada = null;
        $bsentrada = null;

        $salida=null;
        $bssalida=null;
        
        for($j=0; $j < $y; $j++)
        {
            $id = $collection[$j]['id'];
            $nombre = $collection[$j]['nombre'];
            $precio = $collection[$j]['precio'];
            $presentacion = $collection[$j]['presentacion'];

            $saldo = $collection[$j]['saldo'];
            $bssaldo = $collection[$j]['bssaldo'];  

            $entrada = $collection[$j]['entrada'];
            $bsentrada = $collection[$j]['bsentrada'];

            $salida = $collection[$j]['salida'];
            $bssalida = $collection[$j]['bssalida'];
                
            
            $collection[$j]=["id"=>$id, "nombre"=>$nombre, "presentacion"=>$presentacion,"precio"=>$precio, "saldo"=> $saldo, "entrada"=>$entrada, "salida"=>$salida, "final"=>($saldo+$entrada)-$salida, "bssaldo"=>$bssaldo, "bsentrada"=>$bsentrada, "bssalida"=>$bssalida, "bsfinal"=>($bssaldo+$bsentrada)-$bssalida];
        }
        // dump($collection);

        if($request->print==1){
            return view('almacenes/report/inventarioAnual/detalleGeneral/print', compact('collection', 'gestion', 'sucursal'));
        }
        if($request->print==2)
        {
            return Excel::download(new AnualDetalleExport($collection, $gestion), $sucursal->nombre.' - Detalle Anual '.$gestion.'.xlsx');
        }

        if($request->print==NULL)
        {            
            return view('almacenes/report/inventarioAnual/detalleGeneral/list', compact('collection'));
        }
    }


    // #####################################################################################################################################################################################################################
    // ################################                 ARTCLE                 ###########################################################################################################
    // #####################################################################################################################################################################################################################
    //para ver el stock de articulo disponible en el almacen
    public function articleStock()
    {
        $user = Auth::user();
        
        $query_filter = 'id ='.$user->sucursal_id;
        
        if(Auth::user()->hasRole('admin'))
        {
            $query_filter = 1;
        }

        $sucursal = Sucursal::whereRaw($query_filter)->get();      

        return view('almacenes/report/article/stock/report', compact('sucursal'));
    }

    public function articleStockList(Request $request)
    {
        
        $date = Carbon::now();
        $sucursal = Sucursal::find($request->sucursal_id);
        // para convertir letras a numeros
        $formatter = new NumeroALetras();
        $formatter->apocope = true;
        // para convertir letras a numeros
        $query_type = 1;
        
        if($request->type_id != 'TODO')
        {
            $query_type = 'sc.subSucursal_id = '. $request->type_id;
        }
        
        $data = DB::table('solicitud_compras as sc')
                    ->join('facturas as f', 'f.solicitudcompra_id', 'sc.id')
                    ->join('detalle_facturas as df', 'df.factura_id', 'f.id')
                    ->join('articles as a', 'a.id', 'df.article_id')
                    ->join('modalities as m', 'm.id', 'sc.modality_id')
                    ->join('providers as p', 'p.id', 'f.provider_id')

                    ->where('sc.deleted_at', null)
                    ->where('sc.sucursal_id', $request->sucursal_id)
                    ->whereRaw($query_type)

                    ->where('f.deleted_at', null)


                    ->where('df.cantrestante', '>', 0)
                    ->where('df.hist', 0)
                    ->where('df.deleted_at', null)

                    ->select('df.fechaingreso', 'm.nombre as modalidad', 'sc.nrosolicitud', 'p.razonsocial as proveedor',
                            'f.tipofactura', 'f.nrofactura', 'a.id as article_id', 'a.nombre as articulo', 'a.presentacion', 'df.cantsolicitada', 'df.precio',
                            'df.cantrestante', 'df.totalbs', 'sc.id')
                    ->orderBy('df.fechaingreso', 'ASC')
                    ->orderBy('sc.id', 'ASC')
                    ->get();

      
        if($request->print==1){
            return view('almacenes.report.article.stock.print', compact('data', 'sucursal','formatter'));
        }
        if($request->print==2)
        {
            return Excel::download(new ArticleStockExport($data), $sucursal->nombre.'_'.$date.'.xlsx');
        }
        if($request->print==NULL)
        {            
            return view('almacenes.report.article.stock.list', compact('data'));
        }
    }
    // _________________________________________________
    public function articleList()
    {
        $user = Auth::user();
        
        $query_filter = 'id ='.$user->sucursal_id;
        
        if(Auth::user()->hasRole('admin'))
        {
            $query_filter = 1;
        }

        $sucursal = Sucursal::whereRaw($query_filter)->get(); 
                         

        return view('almacenes.report.article.list.report', compact('sucursal'));
    }

    public function articleListList(Request $request)
    {
        $date = Carbon::now();
        $sucursal = Sucursal::find($request->sucursal_id);

        $data = DB::table('partidas as p')
                    ->join('articles as a', 'a.partida_id', 'p.id')
                    ->where('a.condicion', 1)
                    ->where('a.deleted_at', null)
                    ->select('p.nombre as partida', 'p.codigo', 'a.nombre', 'a.presentacion')
                    ->orderBy('a.nombre', 'ASC')
                    ->get();
        // dd($data);

        if($request->print==1){
            return view('almacenes.report.article.list.print', compact('data', 'sucursal'));
        }
        if($request->print==2)
        {
            return Excel::download(new ArticleListExport($data), $sucursal->nombre.'-Lista de articulos'.'_'.$date.'.xlsx');
        }
        if($request->print==NULL)
        {            
            return view('almacenes.report.article.list.list', compact('data'));
        }
    }
    
    //  ___________________________________________

    public function incomeOffice()
    {
        $partida = Partida::where('deleted_at', null)->get();        
        $user = Auth::user();
        
        $query_filter = 'id ='.$user->sucursal_id;
        
        if(Auth::user()->hasRole('admin'))
        {
            $query_filter = 1;
        }

        $sucursal = Sucursal::whereRaw($query_filter)->get();    

        return view('almacenes.report.article.incomeOffice.report', compact('sucursal', 'partida'));
    }

    public function incomeOfficeList(Request $request)
    {
        // dump($request);
        $finish = $request->finish;
        $start = $request->start;
        $date = Carbon::now();
        $sucursal = Sucursal::find($request->sucursal_id);

        $message = '';
        $messagePartida ='';

        $query_direccion ='';
        $query_partida ='';

        $query_type = 1;

        // para convertir letras a numeros
        $formatter = new NumeroALetras();
        $formatter->apocope = true;
        // para convertir letras a numeros
        if($request->type_id != 'TODO')
        {
            $query_type = 'cp.subSucursal_id = '. $request->type_id;
        }


        if($request->direccion_id == 'TODO')
        {
            $message = 'Todas la Direcciones';
            $query_direccion = 1;
        }
        else
        {
            if($request->unidad_id == 'TODO')
            {
                $message = 'Dirección Administrativa - '.$this->getDireccion($request->direccion_id)->nombre;
                $query_direccion = 'cp.direccionadministrativa = '. $request->direccion_id;
            }
            else      
            {
                $message = 'Unidad - '.$this->getUnidad($request->unidad_id)->nombre;
                $query_direccion = 'cp.direccionadministrativa = '. $request->direccion_id.' and cp.unidadadministrativa = '. $request->unidad_id;
            }
        }

        


        
        if($request->partida_id == 'TODOp')
        {
            $messagePartida = 'Partidas - Todas las Partidas';
            $query_partida = 1;
        }
        else      
        {
            $messagePartida = 'Partidas - '.Partida::where('id', $request->partida_id)->first()->codigo.' '.Partida::where('id', $request->partida_id)->first()->nombre;
            $query_partida = 'a.partida_id = '. $request->partida_id;
        }


        $data = DB::table('solicitud_compras as cp')
                        ->join('facturas as f', 'f.solicitudcompra_id', 'cp.id')
                        ->join('detalle_facturas as df', 'df.factura_id', 'f.id')
                        ->join('articles as a', 'df.article_id', 'a.id')
                        ->join('partidas as p', 'p.id', 'a.partida_id')
                        ->where('df.deleted_at', null)
                        ->where('df.hist', 0)
                        ->where('f.deleted_at', null)
                        ->where('cp.deleted_at', null)
                        ->whereRaw($query_direccion)
                        ->whereRaw($query_partida)
                        ->whereRaw($query_type)
                        ->where('cp.fechaingreso', '>=', $request->start)
                        ->where('cp.fechaingreso', '<=', $request->finish)
                        ->where('cp.sucursal_id', $request->sucursal_id)

                        ->select('cp.unidadadministrativa as unidad','cp.fechaingreso',  'a.nombre as articulo', 'p.nombre as partida', 'nrosolicitud', 'a.presentacion', 'df.precio', 'df.cantsolicitada', 'df.totalbs')
                        // ->orderBy('u.id')
                        ->orderBy('cp.fechaingreso')
                        ->get();

        foreach($data as $item)
        {
            $item->unidad = Unit::find($item->unidad)->nombre;
        }

        if($request->print==1){
            return view('almacenes.report.article.incomeOffice.print', compact('data', 'sucursal',  'message', 'messagePartida', 'finish', 'start','formatter'));
        }
        if($request->print==2)
        {
            return Excel::download(new ArticleIncomeOfficeExport($data), $sucursal->nombre.' - Ingreso Artículo '.'_'.$date.'.xlsx');
        }
        if($request->print==NULL)
        {            
            return view('almacenes.report.article.incomeOffice.list', compact('data'));
        }
    }

    public function ajax_incomeOffice_direccion($id)
    {
        return $this->direccionSucursal($id);
    }

    public function ajax_incomeOffice_unidad($id)
    {
        return $this->getUnidades($id);
    }

    // _________________________________________________________
    //  para los egresos por todas las oficina de una direcion o por oficinas.......
    public function egressOffice()
    {
        $partida = Partida::where('deleted_at', null)->get();

        $user = Auth::user();
        
        $query_filter = 'id ='.$user->sucursal_id;
        
        if(Auth::user()->hasRole('admin'))
        {
            $query_filter = 1;
        }

        $sucursal = Sucursal::whereRaw($query_filter)->get();    

        return view('almacenes.report.article.egressOffice.report', compact('sucursal', 'partida'));
    }

    public function egressOfficeList(Request $request)
    {
            // dd($request);
        $finish = $request->finish;
        $start = $request->start;
        $date = Carbon::now();
        $sucursal = Sucursal::find($request->sucursal_id);
        $message = '';
        $messagePartida ='';

        $query_direccion ='';
        $query_partida ='';

        $query_type = 1;
        // numeros a letras
        $formatter = new NumeroALetras();
        $formatter->apocope = true;
        // numeros a letras
        if($request->type_id != 'TODO')
        {
            $query_type = 'se.subSucursal_id = '. $request->type_id;
        }

        if($request->direccion_id == 'TODO')
        {
            $message = 'Todas la Direcciones';
            $query_direccion = 1;
        }
        else
        {
            if($request->unidad_id == 'TODO')
            {
                $message = 'Dirección Administrativa - '.$this->getDireccion($request->direccion_id)->nombre;
                $query_direccion = 'se.direccionadministrativa = '. $request->direccion_id;
            }
            else      
            {
                $message = 'Unidad - '.$this->getUnidad($request->unidad_id)->nombre;
                $query_direccion = 'se.direccionadministrativa = '. $request->direccion_id.' and se.unidadadministrativa = '. $request->unidad_id;
            }
        }

        if($request->partida_id == 'TODOp')
        {
            $messagePartida = 'Partidas - Todas las Partidas';
            $query_partida = 1;
        }
        else      
        {
            $messagePartida = 'Partidas - '.Partida::where('id', $request->partida_id)->first()->codigo.' '.Partida::where('id', $request->partida_id)->first()->nombre;
            $query_partida = 'a.partida_id = '. $request->partida_id;
        }



        $data = DB::table('solicitud_egresos as se')
                        ->join('detalle_egresos as de', 'de.solicitudegreso_id', 'se.id')
                        ->join('detalle_facturas as df', 'df.id', 'de.detallefactura_id')
                        ->join('articles as a', 'df.article_id', 'a.id')
                        ->join('partidas as p', 'p.id', 'a.partida_id')

                        ->whereRaw($query_direccion)
                        ->whereRaw($query_partida)
                        ->whereRaw($query_type)

                        ->where('se.deleted_at', null)
                        // ->where('se.direccionadministrativa', $request->direccion_id)
                        ->where('se.fechaegreso', '>=', $request->start)
                        ->where('se.fechaegreso', '<=', $request->finish)
                        ->where('se.sucursal_id', $request->sucursal_id)

                        ->where('de.deleted_at', null)                        

                        ->select('se.unidadadministrativa as unidad', 'se.fechaegreso', 'a.nombre as articulo', 'p.nombre as partida', 'se.nropedido', 'a.presentacion', 'de.precio', 'de.cantsolicitada', 'de.totalbs')
                        ->orderBy('se.fechaegreso')
                        ->get();

        foreach($data as $item)
        {
            $item->unidad = Unit::find($item->unidad)->nombre;
        }

        if($request->print==1){
            return view('almacenes.report.article.egressOffice.print', compact('data', 'sucursal', 'start', 'finish', 'message', 'messagePartida','formatter'));
        }
        if($request->print==2)
        {
            return Excel::download(new ArticleEgressOfficeExport($data), $sucursal->nombre.' - Egreso Artículo '.'_'.$date.'.xlsx');
        }
        if($request->print==NULL)
        {            
            return view('almacenes.report.article.egressOffice.list', compact('data'));
        }
    }

    // #####################################################################################################################################################################################################################
    // ################################                 PARTIDA                 ###########################################################################################################
    // #####################################################################################################################################################################################################################
    //para ver todos los articulos que se ingresaron en la gestion por partida detallando que factura es
    public function incomePartidaArticle()
    {
        $partida = Partida::where('deleted_at', null)->get();

        $user = Auth::user();
        
        $query_filter = 'id ='.$user->sucursal_id;
        
        if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('almacen_admin'))
        {
            $query_filter = 1;
        }

        $sucursal = Sucursal::whereRaw($query_filter)->get(); 

        return view('almacenes.report.partida.incomearticle.report', compact('sucursal', 'partida'));
    }

    public function incomePartidaArticleList(Request $request)
    {
        $date = Carbon::now();
        $sucursal = Sucursal::find($request->sucursal_id);
        $query_type = 1;

        // para convertir letras a numeros
        $formatter = new NumeroALetras();
        $formatter->apocope = true;
        // para convertir letras a numeros

        if($request->type_id != 'TODO')
        {
            $query_type = 'sc.subSucursal_id = '. $request->type_id;
        }
        $finish = $request->finish;
        $start = $request->start;
        $partida = Partida::where('id', $request->partida_id)->first();
        $data = DB::table('solicitud_compras as sc')
                    ->join('facturas as f', 'f.solicitudcompra_id', 'sc.id')
                    ->join('detalle_facturas as df', 'df.factura_id', 'f.id')
                    ->join('articles as a', 'a.id', 'df.article_id')
                    ->join('partidas as p', 'p.id', 'a.partida_id')

                    ->where('sc.deleted_at', null)
                    ->where('sc.sucursal_id', $request->sucursal_id)
                    ->where('sc.fechaingreso', '>=', $request->start)
                    ->where('sc.fechaingreso', '<=', $request->finish)

                    ->where('f.deleted_at', null)

                    ->where('df.hist', 0)
                    ->where('df.deleted_at', null)

                    ->where('p.id', $request->partida_id)
                    ->whereRaw($query_type)



                    ->select('df.fechaingreso', 'p.codigo', 'p.nombre', 'sc.nrosolicitud', 
                            'f.tipofactura', 'f.nrofactura', 'a.id as article_id', 'a.nombre as articulo', 'a.presentacion', 'df.cantsolicitada', 'df.precio',
                            'df.cantrestante', 'df.totalbs')
                    ->orderBy('df.fechaingreso', 'ASC')
                    ->get();

        // dd($data->sum('totalbs'));
      
        if($request->print==1){
            return view('almacenes.report.partida.incomearticle.print', compact('data', 'partida', 'finish', 'start','formatter'));
        }
        if($request->print==2)
        {
            return Excel::download(new ArticleStockExport($data), $sucursal->nombre.'_'.$date.'.xlsx');
        }
        if($request->print==NULL)
        {            
            return view('almacenes.report.partida.incomearticle.list', compact('data', 'partida'));
        }
    }



    // #######################################################################
    // #######################################################################
    // #######################################################################
    // para los proveedores
    public function provider()
    {
        $user = Auth::user();
        $query_filter = 'user_id ='.Auth::user()->id;
        
        if(Auth::user()->hasRole('admin'))
        {
            $query_filter = 1;
        }

        $sucursal = SucursalUser::where('condicion', 1)
                        ->where('deleted_at', null)
                        ->whereRaw($query_filter)
                        ->GroupBy('sucursal_id')
                        ->get();
                        
        $direction = $this->getDirecciones();        

        return view('almacenes.report.provider.list.report', compact('sucursal', 'direction'));
    }

    public function providerList(Request $request)
    {
        $date = Carbon::now();
        $sucursal = Sucursal::find($request->sucursal_id);

        $data = Provider::where('sucursal_id', $request->sucursal_id)->where('condicion', 1)->get();
        // dd($request);
 
        if($request->print==1){
            return view('almacenes.report.provider.list.print', compact('data', 'sucursal'));
        }
        if($request->print==2)
        {
            return Excel::download(new ProviderListExport($data), $sucursal->nombre.'_Lista Proveedores '.$date.'xlsx');
        }
        if($request->print==NULL)
        {            
            return view('almacenes.report.provider.list.list', compact('data'));
        }
    }





    //################################################              REPORTE ADITIONAL           ###################################

    // para los usuarios
    public function user()
    {
        $user = Auth::user();
        $query_filter = 'user_id ='.Auth::user()->id;
        
        if(Auth::user()->hasRole('admin'))
        {
            $query_filter = 1;
        }

        $sucursal = SucursalUser::where('condicion', 1)
                        ->where('deleted_at', null)
                        ->whereRaw($query_filter)
                        ->GroupBy('sucursal_id')
                        ->get();
                        
        $direction = $this->getDirecciones();        

        // return view('almacenes.report.aditional.list.report', compact('sucursal', 'direction'));
        return view('almacenes.report.aditional.user.report', compact('sucursal', 'direction'));

    }

    public function userList(Request $request)
    {
        $data = DB::table('sucursals as s')
                    ->leftJoin('solicitud_compras as sc', 's.id', 'sc.sucursal_id')
                    ->leftJoin('facturas as f', 'f.solicitudcompra_id', 'sc.id')
                    ->leftJoin('detalle_facturas as df', 'df.factura_id', 'f.id')
                    ->leftJoin('articles as a', 'a.id', 'df.article_id')
                    

                    // ->where('su.condicion', 1)

                    ->where('sc.deleted_at', null)
                    ->where('f.deleted_at', null)
                    ->where('df.deleted_at', null)
                    ->where('df.hist', 0)

                    // ->whereDate('sc.fechaingreso', '>=', date('Y-m-d', strtotime($request->start)))
                    // ->whereDate('sc.fechaingreso', '<=', date('Y-m-d', strtotime($request->finish)))
                    ->select('s.nombre as almacen', 's.id as sucursal_id',

                            // DB::raw("SUM(df.cantsolicitada) as cEntrada"), DB::raw("SUM(df.cantsolicitada - df.cantrestante) as cSalida"), DB::raw("SUM(df.cantrestante) as cFinal"),

                            // DB::raw("SUM(df.totalbs) as vEntrada"), DB::raw("SUM((df.cantsolicitada - df.cantrestante) * df.precio) as vSalida"), DB::raw("SUM(df.cantrestante * df.precio) as vFinal")
                            
                            DB::raw("SUM(df.cantsolicitada) as cEntrada"),

                            DB::raw("SUM(df.totalbs) as vEntrada")   
                            )
                    ->groupBy('s.id')
                    ->get();
        // dd($data);


        if($request->print){
            return view('almacenes.report.aditional.user.print', compact('data'));
        }
        else
        {            
            return view('almacenes.report.aditional.user.list', compact('data'));
        }
    }









































    // para los articlos en general SAldo inicial, entrada, salida, saldo final,.....expresado en boliviano y en cantidades
    public function articleInventory()
    {
        $user = Auth::user();
        $sucursal = SucursalUser::where('user_id', $user->id)->get();

        return view('almacenes/report/article/inventory/report', compact('sucursal'));
    }

    public function articleInventoryList(Request $request)
    {
        $start = $request->start;
        $finish = $request->finish;


        // $data = DB::table('solicitud_compras as sp')
        //             ->join('facturas as f', 'f.solicitudcompra_id', 'sp.id')
        //             ->join('detalle_facturas as df', 'df.factura_id', 'f.id')
        //             ->join('article as a')

        if($request->print){
            return view('almacenes/report/article/inventory/print', compact('start', 'finish'));
        }else
        {            
            return view('almacenes/report/article/inventory/list');
        }
    }







    


    //REPORTES PARA OBTENER LAS UNIDADES QUE AN PARTICIPADO EN ESE ARTICULO
    public function articleUnidades()
    {
        $article = Article::where('deleted_at', null)->where('condicion', 1)->orderBy('nombre')->get();
        return view('almacenes/report/article/unidades/report', compact('article'));
    }
    
    public function articleUnidadesList(Request $request)
    {
        $type = $request->type;
        if($request->type == 1)
        {
            $data = DB::connection('mamore')->table('unidades as u')
                    ->join('sysalmacen.solicitud_compras as sc', 'sc.unidadadministrativa', 'u.id')
                    ->join('sysalmacen.facturas as f', 'f.solicitudcompra_id', 'sc.id')
                    ->join('sysalmacen.detalle_facturas as df', 'df.factura_id', 'f.id')
                    ->join('sysalmacen.articles as a', 'a.id', 'df.article_id')
                    ->where('df.article_id', $request->article_id)
                    ->where('df.deleted_at', null)
                    ->where('f.deleted_at', null)
                    ->where('sc.deleted_at', null)
                    ->where('sc.fechaingreso', '>=', $request->start)
                    ->where('sc.fechaingreso', '<=', $request->finish)
                    ->select('u.nombre', 'sc.nrosolicitud')
                    ->orderBy('sc.fechaingreso')
                    ->get();
                    
        }
        else
        {
            $data = DB::connection('mamore')->table('unidades as u')
                    ->join('sysalmacen.solicitud_egresos as se', 'se.unidadadministrativa', 'u.id')
                    ->join('sysalmacen.detalle_egresos as de', 'de.solicitudegreso_id', 'se.id')
                    ->join('sysalmacen.detalle_facturas as df', 'df.id', 'de.detallefactura_id')
                    ->join('sysalmacen.articles as a', 'a.id', 'df.article_id')
                    ->where('df.article_id', $request->article_id)
                    ->where('se.deleted_at', null)
                    ->where('de.deleted_at', null)
                    ->where('se.fechaegreso', '>=', $request->start)
                    ->where('se.fechaegreso', '<=', $request->finish)
                    ->select('*')
                    ->orderBy('se.fechaegreso')
                    ->get();

        }
        // dd($data);
        return view('almacenes/report/article/unidades/list', compact('data', 'type'));
    }
}