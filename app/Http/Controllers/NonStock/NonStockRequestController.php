<?php

namespace App\Http\Controllers\NonStock;

use App\Http\Controllers\Controller;
use App\Models\NonStock\NonStockArticle;
use App\Models\NonStock\ArticlePresentation;
use Illuminate\Http\Request;
use App\Models\Sucursal;
use App\Models\SucursalSubAlmacen;
use App\Models\NonStock\NonStockRequest;

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
        //
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
}
