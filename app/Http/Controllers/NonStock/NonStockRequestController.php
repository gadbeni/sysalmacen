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

use App\Models\InventarioAlmacen;

class NonStockRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction(); //Start transaction!
        try{
            $user = auth()->user();
            $funcionario = $this->getWorker($user->funcionario_id);
            $gestion = InventarioAlmacen::where('status', 1)->where('deleted_at', null)->first();//para ver si hay gestion activa o cerrada
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

            $nonStockRequest->date_request = Carbon::now();
            $nonStockRequest->gestion = $gestion->gestion;
            $nonStockRequest->nro_request = $nro_request_final; 
            $nonStockRequest->job = $funcionario->cargo;
            $nonStockRequest->direction_id = $user->direccionAdministrativa_id;
            $nonStockRequest->direction_name = $user->direction->nombre;
            $nonStockRequest->unit_id = $user->unidadAdministrativa_id;
            $nonStockRequest->unit_name = $user->unit->nombre;

            $nonStockRequest->date_status = Carbon::now();

            $nonStockRequest->save();
        //----------- ArticlePresentation ----------------
            $articlePresentationsIds = [];
            $presentations = $request->input('unit_presentation');
            
            foreach($presentations as $presentation){
                $articlePresentation = ArticlePresentation::firstOrCreate(['name_presentation' => $presentation]);
                array_push($articlePresentationsIds, $articlePresentation->id);
            }
            // ----------- NonStockArticle ---------------
            $nonStockArticlesIds = [];
            $articles = $request->input('article_name');
            foreach($articles as $article){
                $nonStockArticle = NonStockArticle::firstOrCreate(
                    ['name_description' => $article],
                    ['registerUser_id' => $user->id]
                );
                array_push($nonStockArticlesIds, $nonStockArticle->id);
            }
            // ----------- NonRequestArticle -------------
            $quantities =  $request->input('quantity');
            $prices = $request->input('price');
            $price_refs = $request->input('price_ref');
            for($i = 0; $i < count($articles); $i++){
                $nonRequestArticle = new NonRequestArticle();
                $nonRequestArticle->non_request_id = $nonStockRequest->id;
                $nonRequestArticle->non_article_id = $nonStockArticlesIds[$i];
                $nonRequestArticle->article_presentation_id = $articlePresentationsIds[$i];
                $nonRequestArticle->quantity = $quantities[$i];
                $nonRequestArticle->unit_price = $prices[$i];
                $nonRequestArticle->reference_price = $price_refs[$i];
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
         */
        $nonStockRequest = NonStockRequest::findOrFail($request->input('id'));
        $nonStockRequest->status = 'eliminado';
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
        $nonStockRequest = NonStockRequest::findOrFail($request->input('id'));
        $nonStockRequest->status = 'rechazado';
        $nonStockRequest->date_status = Carbon::now();
        $nonStockRequest->statusUser_id = auth()->user()->id;
        $nonStockRequest->save();
        return redirect()->route('nonstock.inbox')->with('success','Se ha rechazado la solicitud de articulos de inexistencia con exito');
    }

    //-------------------- admin inboxes --------------------
    public function inboxIndex(Request $request){
        if (!auth()->user()->hasPermission('browse_inbox')) {
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
        $nonStockRequest = NonStockRequest::findOrFail($id);
        $nonRequestArticles = NonRequestArticle::where('non_request_id', $nonStockRequest->id)->get();
        return view('almacenes.nonstock.nonstock-inbox.read', compact('nonStockRequest', 'nonRequestArticles'));
    }
}
