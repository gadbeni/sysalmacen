<?php

namespace App\Http\Controllers\NonStock;

use App\Http\Controllers\Controller;
use App\Models\NonStock\NonStockRequest;
use App\Models\NonStock\NonStockArticle;
use App\Models\NonStock\ArticlePresentation;
use App\Models\NonStock\NonRequestArticle;
use Illuminate\Http\Request;
use App\Models\Sucursal;
use App\Models\SucursalSubAlmacen;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\SucursalUnidadPrincipal;

use App\Models\InventarioAlmacen;

use function PHPSTORM_META\type;

class NonStockRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // La condicion funciona, pero se detecta como error a pesar que no lo es
        if (!auth()->user()->hasPermission('browse_outbox')) {
            abort('401');
        }
        return view('almacenes.nonstock.browse');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        if (!auth()->user()->hasPermission('browse_outbox')) {
            abort('401');
        }
        $gestion = InventarioAlmacen::where('status', 1)->where('deleted_at', null)->first();//para ver si hay gestion activa o cerrada 
        if($gestion == null){
            return redirect()->route('nonstock.index')->with(['message' => 'No se puede realizar la solicitud de articulos de inexistencia, no hay gestion activa', 'alert-type' => 'error']);
        }
        $user = auth()->user();
        $sucursal = Sucursal::findOrFail($user->sucursal_id);
        $subalmacen = SucursalSubAlmacen::where('sucursal_id', $sucursal->id)->where('deleted_at', null)->get();
        $funcionario = $this->getWorker($user->funcionario_id);
        return view('almacenes.nonstock.create',compact('funcionario','sucursal','subalmacen'));
    }

    //Funcion ajax para obtener los articulos disponible en el almacen y sin stock
    public function ajaxProductNoExists(Request $request)
    {

        $q = $request->search;
        $type = $request->externo;


        $user = Auth::user();


        $mainUnit = SucursalUnidadPrincipal::where('sucursal_id', $user->sucursal_id)->where('status', 1)->where('deleted_at', null)->get();
        // return $mainUnit;
        $query = '';

        if(count($mainUnit)== 1)
        {
            $query = ' or s.unidadadministrativa = '.$mainUnit[0]->unidadAdministrativa_id;
        }

        if(count($mainUnit)== 2)
        {
            $query = ' or s.unidadadministrativa = '.$mainUnit[0]->unidadAdministrativa_id.' or s.unidadadministrativa = '.$mainUnit[1]->unidadAdministrativa_id;
        }

        $unidad = 'null';
        if($user->unidadAdministrativa_id)
        {
            $unidad = $user->unidadAdministrativa_id;
        }

        // articulos con stocks
        $stockArticles = DB::table('solicitud_compras as s')
                ->join('facturas as f', 'f.solicitudcompra_id', 's.id')
                ->join('detalle_facturas as d', 'd.factura_id', 'f.id')
                ->join('articles as a', 'a.id', 'd.article_id')
                ->where('s.sucursal_id', $user->sucursal_id)
                ->where('s.subSucursal_id', $type)
                ->where('s.stock', 1)
                ->where('s.deleted_at', null)      
                // ->whereRaw('(s.unidadadministrativa = '.$funcionario->id_unidad.' or s.unidadadministrativa = 0)')
                ->whereRaw('(s.unidadadministrativa = '.$unidad.''.$query.')')
                // ->whereRaw('(s.unidadadministrativa = '.$funcionario->id_unidad.')')
                ->where('f.deleted_at', null)
                ->where('d.deleted_at', null)
                ->where('d.cantrestante', '>', 0)
                ->where('d.condicion', 1)
                ->where('d.hist', 0)
                ->select('a.id', 'a.nombre as nombre', 'a.image', 'a.presentacion')
                ->whereRaw("(nombre like '%$q%')")
                ->groupBy('id')
                ->orderBy('nombre')
                ->get();
        // todos los articulos
        $allArticles = DB::table('articles as a')
        ->select('id', 'nombre', 'image', 'presentacion')
        ->whereRaw("(nombre like '%$q%')")
        ->orderBy('nombre')
        ->get();

        $dataIds = $stockArticles->pluck('id')->toArray();
        $allArticleIds = $allArticles->pluck('id')->toArray();

        $articleIdsWithoutStock = array_diff($allArticleIds, $dataIds);
        //todos los articulos  menos los articulos con stok
        // articulos sin stock
        if (!$type) {
            return response()->json([]);
        }
        $data = DB::table('articles as a')
            ->join('partidas as p', 'p.id', '=', 'a.partida_id')
            ->select('a.id', 'a.nombre', 'a.image', 'a.presentacion','p.nombre as nombre_partida')
            ->whereIn('a.id', $articleIdsWithoutStock)
            ->orderBy('a.nombre')
            ->get();


        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //El usuario admin no puede almacernar articulos de inexistencia por que no tiene unidad administrativa
        DB::beginTransaction(); //Start transaction!
        try{
            $user = auth()->user();
            $funcionario = $this->getWorker($user->funcionario_id);
            $sucursal = Sucursal::where('id', $user->sucursal_id)->first();
            if($sucursal == null){
                return redirect()->route('nonstock.index')->with('error','No se puede realizar la solicitud de articulos de inexistencia, no se ha encontrado la sucursal');
            }
            //para ver si hay gestion activa o cerrada
            $gestion = InventarioAlmacen::where('status', 1)->where('sucursal_id', $sucursal->id)->where('deleted_at', null)->first();
            if($gestion == null){
                return redirect()->route('nonstock.index')->with('error','No se puede realizar la solicitud de articulos de inexistencia, no hay gestion activa');
            }
            $unidad = $user->unit;
            $nro_request = NonStockRequest::where('gestion', $gestion->gestion)->where('unit_id',$unidad->id)->where('deleted_at', null)->count()+1;
            // $format = "%{$}{$length}{$type}";
            $nro_request_final = "FI-".$unidad->sigla."-".sprintf("%04d", $nro_request)."/".$gestion->gestion;
            //----------- NonStockRequest ----------------
            $nonStockRequest = new NonStockRequest();
            $nonStockRequest->sucursal_id = $user->sucursal_id;
            $nonStockRequest->subSucursal_id = $request->input('subSucursal_id');
            $nonStockRequest->registerUser_id = $user->id;
            $nonStockRequest->registerUser_name = $funcionario->first_name.' '.$funcionario->last_name;

            $nonStockRequest->date_request = Carbon::now();
            $nonStockRequest->gestion = $gestion->gestion;
            $nonStockRequest->nro_request = $nro_request_final;
            $nonStockRequest->people_id = $funcionario->people_id;
            $nonStockRequest->job = $funcionario->cargo;
            $nonStockRequest->direction_id = $user->direccionAdministrativa_id;
            $nonStockRequest->direction_name = $user->direction->nombre;
            $nonStockRequest->unit_id = $user->unidadAdministrativa_id;
            $nonStockRequest->unit_name = $user->unit->nombre;

            $nonStockRequest->date_status = Carbon::now();

            $nonStockRequest->save();
        //-----------  Articulos existentes en almacen pero sin stok ----------------
            $articlesIds = $request->input('article_id'); // articulos sin stock pero existentes
            $noStockArticles = $request->input('article_name'); // articulos no existentes
            $quantities =  $request->input('cantidad');
            if ($articlesIds == null && $noStockArticles == null) {
                DB::rollback();
                return redirect()->route('nonstock.index')->with(['message' => 'No se ha registrado la solicitud de articulos de inexistencia, no se ha seleccionado ningun articulo', 'alert-type' => 'error']);
            }
            if ($articlesIds != null) {
                for($i = 0; $i < count($articlesIds); $i++){
                    $nonRequestArticle = new NonRequestArticle();
                    $nonRequestArticle->non_request_id = $nonStockRequest->id;
                    $nonRequestArticle->sucursal_id = $user->sucursal_id;
                    $nonRequestArticle->gestion = $gestion->gestion;
                    $nonRequestArticle->article_id = $articlesIds[$i];
                    $nonRequestArticle->quantity = $quantities[$i];
                    $nonRequestArticle->save();
                }
            }

            // En caso de no haber articulos manuales ya no se procede
            if ($noStockArticles == null) {
                DB::commit(); //Commit to DataBase
                return redirect()->route('nonstock.index')->with(['message' => 'Se ha registrado la solicitud de articulos de inexistencia con exito', 'alert-type' => 'success']);
            }

        //----------- ArticlePresentation ----------------
            $articlePresentationsIds = [];
            $presentations = $request->input('unit_presentation');
            
            foreach($presentations as $presentation){
                $articlePresentation = ArticlePresentation::firstOrCreate(['name_presentation' => $presentation]);
                array_push($articlePresentationsIds, $articlePresentation->id);
            }
            // ----------- NonStockArticle ---------------
            $nonStockArticlesIds = [];
            foreach($noStockArticles as $article){
                $nonStockArticle = NonStockArticle::firstOrCreate(
                    ['name_description' => $article],
                    ['registerUser_id' => $user->id]
                );
                array_push($nonStockArticlesIds, $nonStockArticle->id);
            }
            // ----------- NonRequestArticle -------------
            $quantities =  $request->input('quantity');
            // $prices = $request->input('price');
            // $price_refs = $request->input('price_ref');
            for($i = 0; $i < count($noStockArticles); $i++){
                $nonRequestArticle = new NonRequestArticle();
                $nonRequestArticle->non_request_id = $nonStockRequest->id;
                $nonRequestArticle->sucursal_id = $user->sucursal_id;
                $nonRequestArticle->gestion = $gestion->gestion;
                $nonRequestArticle->non_article_id = $nonStockArticlesIds[$i];
                $nonRequestArticle->article_presentation_id = $articlePresentationsIds[$i];
                $nonRequestArticle->quantity = $quantities[$i];
                // $nonRequestArticle->unit_price = $prices[$i];
                // $nonRequestArticle->reference_price = $price_refs[$i];
                $nonRequestArticle->save();
            }
            DB::commit(); //Commit to DataBase       
            return redirect()->route('nonstock.index')->with(['message' => 'Se ha registrado la solicitud de articulos de inexistencia con exito', 'alert-type' => 'success']);


        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('nonstock.index')->withwith(['message' => 'No se ha registrado la solicitud de articulos de inexistencia, ha ocurrido un error', 'alert-type' => 'error']);
        }
        
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $nonStockRequest = NonStockRequest::findOrFail($id);
        $nonRequestArticles = NonRequestArticle::where('non_request_id', $nonStockRequest->id)->get();
        return view('almacenes.nonstock.report', compact('nonStockRequest', 'nonRequestArticles'));
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

    public function getArticlesNames(Request $request)
    {   
        /**
         * @var NonStockArticle $articles
         * @return \Illuminate\Http\JsonResponse
         * 
         * Esta funcion retorna los nombres de los articulos inexistencia non_stock
         */

        $query = $request->input('query');
        $articles = NonStockArticle::where('name_description', 'LIKE', "%$query%")->get();
        $articlesNames = $articles->map(function($article){
            return [
                'id' => $article->id,
                'name' => $article->name_description,
            ];
        });
        return response()->json($articlesNames);
    }
    public function getPresentationNames(Request $request)
    {
        /**
         * @var ArticlePresentation $presentations
         * @return \Illuminate\Http\JsonResponse
         * 
         * Esta funcion retorna los nombres de las presentaciones de los articulos inexistencia non_stock
         */
        $query = $request->input('query');
        $presentations = ArticlePresentation::where('name_presentation', 'LIKE', "%$query%")->get();
        $presentationsNames = $presentations->map(function($presentation){
            return [
                'id' => $presentation->id,
                'name' => $presentation->name_presentation,
            ];
        });
        return response()->json($presentationsNames);
    }
    public function getTableList()
    {
        /**
         * 
         */
        $search = request('search') ?? null;
        $type = request('type') ?? null;
        $paginate = request('paginate') ?? 10;

        $user = auth()->user();
        $gestion = InventarioAlmacen::where('status', 1)->where('sucursal_id', $user->sucursal_id)->where('deleted_at', null)->first();//para ver si hay gestion activa o cerrada

        $query_filter = 'registerUser_id = '.$user->id;

        if(auth()->user()->hasRole('admin'))
        {
            $query_filter =1;
        }

        //data
        $data = NonStockRequest::where(function($query) use ($search){
            if ($search) {
                $query->where('gestion', 'like', '%' . $search . '%')
                    ->orWhere('nro_request', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%')
                    ->orWhere('unit_name', 'like', '%' . $search . '%')
                    ->orWhere('direction_name', 'like', '%' . $search . '%');
            }
        })->whereNull('deleted_at');

        //filter
        switch($type){
            case 'pendiente':
                $data = $data->where('status', 'pendiente');
                break;
            case 'enviado':
                $data = $data->where('status', 'enviado');
                break;
            case 'aprobado':
                $data = $data->where('status', 'aprobado');
                break;
            case 'rechazado':
                $data = $data->where('status', 'rechazado');
                break;
        }
        $data = $data->whereRaw($query_filter)->orderBy('id', 'DESC')->paginate($paginate);
        // $data = $data->orderBy('id', 'DESC')->paginate($paginate);
        return view('almacenes.nonstock.tablelist', compact('data', 'gestion'));
        
    }
    //--------------------- status ---------------------
    public function sendNonStock(Request $request)
    {
        /**
         * @var NonStockRequest $nonStockRequest
         * @return \Illuminate\Http\RedirectResponse
         * 
         * Esta funcion envia la solicitud de articulos de inexistencia non_stock
         */
        $nonStockRequest = NonStockRequest::findOrFail($request->input('id'));
        $nonStockRequest->status = 'enviado';
        $nonStockRequest->save();
        return redirect()->route('nonstock.index')->with('success','Se ha enviado la solicitud de articulos de inexistencia con exito');

    }
    public function deleteNonStock(Request $request)
    {
        /**
         * @var NonStockRequest $nonStockRequest
         * @return \Illuminate\Http\RedirectResponse
         * 
         * Esta funcion elimina la solicitud de articulos de inexistencia non_stock 
         * Siempre y cuando la solicitud no haya sido aprobada o rechazada
         */
        $nonStockRequest = NonStockRequest::findOrFail($request->input('id'));
        if($nonStockRequest->status == 'aprobado' || $nonStockRequest->status == 'rechazado'){
            return redirect()->route('nonstock.index')->with('message','No se puede eliminar la solicitud de articulos de inexistencia, ya ha sido aprobada o rechazada');
        }
        $nonStockRequest->status = 'eliminado';
        $nonStockRequest->deletedUser_Id = auth()->user()->id;
        $nonStockRequest->deleted_at = Carbon::now();
        $nonStockRequest->save();
        return redirect()->route('nonstock.index')->with('success','Se ha eliminado la solicitud de articulos de inexistencia con exito');
    }
    public function approveNonStock(Request $request)
    {
        /**
         * @var NonStockRequest $nonStockRequest
         * @return \Illuminate\Http\RedirectResponse
         * 
         * Esta funcion aprueba la solicitud de articulos de inexistencia non_stock
         */
        if (!auth()->user()->hasPermission('approve_noninbox')) {
            abort('401');
        }
        $nonStockRequest = NonStockRequest::findOrFail($request->input('id'));
        $nonStockRequest->status = 'aprobado';
        $nonStockRequest->date_status = Carbon::now();
        $nonStockRequest->statusUser_id = auth()->user()->id;
        $nonStockRequest->save();
        return redirect()->route('nonstock.inbox')->with('success','Se ha aprobado la solicitud de articulos de inexistencia con exito');
    }
    public function rejectNonStock(Request $request)
    {
        /**
         * @var NonStockRequest $nonStockRequest
         * @return \Illuminate\Http\RedirectResponse
         * 
         * Esta funcion rechaza la solicitud de articulos de inexistencia non_stock
         */
        if (!auth()->user()->hasPermission('reject_noninbox')) {
            abort('401');
        }

        $nonStockRequest = NonStockRequest::findOrFail($request->input('id'));
        $nonStockRequest->status = 'rechazado';
        $nonStockRequest->date_status = Carbon::now();
        $nonStockRequest->statusUser_id = auth()->user()->id;
        $nonStockRequest->save();
        return redirect()->route('nonstock.inbox')->with('success','Se ha rechazado la solicitud de articulos de inexistencia con exito');
    }

    //-------------------- admin inboxes --------------------
    public function inboxIndex(Request $request){
        if (!auth()->user()->hasPermission('browse_noninbox')) {
            abort('401');
        }
        return view('almacenes.nonstock.nonstock-inbox.browse');
    }
    public function getInboxList(){
        /**
         * 
         */
        $search = request('search') ?? null;
        $type = request('type') ?? null;
        $paginate = request('paginate') ?? 10;

        $user = auth()->user();
        $gestion = InventarioAlmacen::where('status', 1)->where('sucursal_id', $user->sucursal_id)->where('deleted_at', null)->first();//para ver si hay gestion activa o cerrada
        
        $query_filter = 'sucursal_id = '.$user->sucursal_id;

        if(auth()->user()->hasRole('admin'))
        {
            $query_filter =1;
        }

        //data
        $data = NonStockRequest::where(function($query) use ($search){
            if ($search) {
                $query->where('gestion', 'like', '%' . $search . '%')
                    ->orWhere('nro_request', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%')
                    ->orWhere('unit_name', 'like', '%' . $search . '%')
                    ->orWhere('direction_name', 'like', '%' . $search . '%');
            }
        })->whereNull('deleted_at');

        //filter
        switch($type){
            case 'todo':
                $data = $data->where('status', '!=', 'eliminado')
                    ->where('status', '!=', 'pendiente');
                break;
            case 'pendiente':
                $data = $data->where('status', 'enviado');
                break;
            case 'aprobado':
                $data = $data->where('status', 'aprobado');
                break;
            case 'rechazado':
                $data = $data->where('status', 'rechazado');
                break;
        }
        $data = $data->whereRaw($query_filter)->orderBy('id', 'DESC')->paginate($paginate);
        return view('almacenes.nonstock.nonstock-inbox.list', compact('data', 'gestion'));
    }

    public function inboxShow($id){
        /**
         * 
         */
        if (!auth()->user()->hasPermission('read_noninbox')) {
            abort('401');
        }
        $nonStockRequest = NonStockRequest::findOrFail($id);
        $nonRequestArticles = NonRequestArticle::where('non_request_id', $nonStockRequest->id)->get();
        return view('almacenes.nonstock.nonstock-inbox.read', compact('nonStockRequest', 'nonRequestArticles'));
    }
}
