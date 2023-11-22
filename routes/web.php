<?php
use Illuminate\Http\Request;

use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\EgressController;
use App\Http\Controllers\UserController;
// use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\IncomeSolicitudController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NonStock\NonStockRequestController;
use App\Http\Controllers\IncomeDonorController;
use App\Http\Controllers\EgressDonorController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DonacionSolicitudController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\ProviderController;

use App\Http\Controllers\MaintenanceController;

use App\Http\Controllers\DonationStockController;
use App\Http\Controllers\ExistingProductController;
use App\Http\Controllers\InventarioAlmacenController;
use App\Http\Controllers\PeopleExtController;
use App\Http\Controllers\ReportAlmacenController;
use App\Http\Controllers\SolicitudBandejaController;
use App\Http\Controllers\SolicitudPedidoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('/', function () {
    return redirect('admin');
});
 
// Route::middleware('auth')->group(function() {
//     Route::post('/delete-session', function(Request $request) {
//         DB::table('sessions')
//             ->where('id', $request->id)
//             ->where('user_id', auth()->id())
//             ->delete();
//     });
// });

Route::get('login', 'Auth\LoginController@showLoginForm')->name('logins');

Route::get('/maintenance', [MaintenanceController::class , 'maintenance'])->name('maintenance');
Route::get('/notpeople', [MaintenanceController::class , 'notpeople'])->name('notpeople');
Route::get('/error', [MaintenanceController::class , 'error'])->name('error');
Route::get('/contact', [MaintenanceController::class , 'contact'])->name('contact');



Route::group(['prefix' => 'admin', 'middleware' => 'loggin'], function () {
    Voyager::routes();

    //<- sesiones
    Route::get('/usuario/seguridad',[UserController::class,'showSessions'])->name('sessions')->middleware('auth');
    Route::delete('/usuario/sesion/',[UserController::class,'deleteSession'])->name('delete_session');
    //sesiones ->
    Route::put('/usario/change-password/',[UserController::class,'changePassword'])->name('change_password');

    Route::resource('usuario', UserController::class);
    Route::post('usuarios/desactivar', [UserController::class, 'desactivar'])->name('almacen_desactivar');
    Route::post('usuarios/activar', [UserController::class, 'activar'])->name('almacen_activar');



    // ::::::::::::::::::::::::::Para los productos en existencia ::::::::::::::::::::::::::::::::::::::
    Route::get('existingproducts', [ExistingProductController::class, 'index'])->name('existingproducts.index');
    Route::post('existingproducts/list', [ExistingProductController::class, 'articleExistingList'])->name('existingproducts.list');

    // :::::::::::::::::::::::::::::     PARA LAS SOLICITUDES  DE LOS PRODECTOS O ARTICULOS       ::::::::::::::::::::::::::::::::::::::::::
    Route::resource('outbox',SolicitudPedidoController::class);
    Route::get('outbox/ajax/list', [SolicitudPedidoController::class, 'list']);
    Route::get('outbox/article/stock/ajax', [SolicitudPedidoController::class, 'ajaxProductExists']);//para poder obtener los particulos o productos disponible para hacer la solicitud
    Route::post('outbox/delete', [SolicitudPedidoController::class, 'deletePedido'])->name('outbox.deletepedido');
    Route::post('outbox/enviar', [SolicitudPedidoController::class, 'solicitudEnviada'])->name('outbox.enviar');
    Route::post('outbox/delete/confirmar', [SolicitudPedidoController::class, 'confirmarEliminacion'])->name('outbox-delete.confirmar');//Paar que confirme la anulacion del pedido y vuelva el detalle al almacen
    Route::post('outbox/delete/cancelar', [SolicitudPedidoController::class, 'cancelarEliminacion'])->name('outbox-delete.cancelar'); //Para cancelar la anulacion de pedido

    Route::resource('inbox', SolicitudBandejaController::class);
    Route::get('inbox/ajax/list/{type}/{search?}', [SolicitudBandejaController::class, 'list']);
    Route::post('inbox/rechazar', [SolicitudBandejaController::class, 'rechazarSolicitud'])->name('inbox.rechazar');
    Route::post('inbox/aprobar', [SolicitudBandejaController::class, 'aprobarSolicitud'])->name('inbox.aprobar');

    //:::::::::::::::::::::::::::::: PRODUCTOS INEXISTENCIA Non-stock
    Route::resource('nonstock', NonStockRequestController::class);
    Route::get('get-articles-nonstock/ajax/list', [NonStockRequestController::class, 'getArticlesNames'])->name('get-articlesnames-nonstock.list');
    Route::get('get-presentation-nonstock/ajax/list', [NonStockRequestController::class, 'getPresentationNames'])->name('get-presentations-nonstock.list');


    
    //........................  INCOME
    Route::resource('income', IncomeController::class);
    Route::get('income/ajax/list/{type}/{search?}', [IncomeController::class, 'list']);
    Route::get('income/provider/list/ajax', [ProviderController::class, 'list']);
    Route::get('income/article/list/ajax', [ArticleController::class, 'getArticle']);//para poder obtener todos los articulos disponible
    Route::get('incomes/browse/view/{id?}', [IncomeController::class, 'view_ingreso'])->name('income_view');
    Route::get('incomes/browse/view/stock/{id?}', [IncomeController::class, 'view_ingreso_stock'])->name('income_view_stock');
    Route::delete('incomes/browse/delete', [IncomeController::class, 'destroy'])->name('income_delete');
    Route::post('incomes/update', [IncomeController::class, 'update'])->name('income_update');
    Route::get('incomes/browse/{income?}/salida', [IncomeController::class, 'salida'])->name('incomes-browse.salida');

    // Route::get('incomes/browse/edit/{id?}', [IncomeController::class, 'edit'])->name('edit_income');


    // Route::get('incomes/selectproveedor', [IncomeController::class, 'select_proveedor'])->name('select_proveedor');
    // Route::get('incomes/selectproveedorsearch', [IncomeController::class, 'ajax_proveedor']);

   

   //........................  EGRES
    Route::resource('egres', EgressController::class);
    Route::get('egres/ajax/list/{type}/{search?}', [EgressController::class, 'list']);
    Route::post('egres/update', [EgressController::class, 'update'])->name('egres_update');
    Route::post('egres/delete', [EgressController::class, 'destroy'])->name('egres_delete');//Para eliminar los egreso de manera normal
    Route::post('egres/solicitud/delete', [EgressController::class, 'destroySolicitud'])->name('egres-solicitud.delete');//Para eliminar los egreso de manera mediante solicitud
    Route::post('egres/delete/cancelar', [EgressController::class, 'cancelarEliminacion'])->name('egres-delete.cancelar'); //Para cancelar la anulacion de pedido


    Route::get('egres/solicitud/{solicitud?}/show', [EgressController::class, 'showSolicitud'])->name('egres-solicitud.show');
    Route::post('egres/rechazar', [EgressController::class, 'rechazarSolicitud'])->name('egres.rechazar');
    Route::post('egres/solicitud/entregar', [EgressController::class, 'entregarSolicitud'])->name('egres-solicitud.entregar');

    // para obtener los articulos de la unidad y articulo en especifico
    Route::get('egres/ajax/articleunidad/{unidad?}/{article?}', [EgressController::class, 'ajax_unidad'])->name('egres-ajax.articleunidad');
    Route::get('egres/ajax/articlealmacen/{article?}/{unidad_id?}/{unidad1?}/{unidad2?}', [EgressController::class, 'ajax_almacen'])->name('egres-ajax.articlealmacen');
    Route::post('egres/detalle/store', [EgressController::class, 'detailsDetalle'])->name('egres-ajax.detalle.store');
    Route::post('egres/entregarsolicitud/store', [EgressController::class, 'egresEntregarSolicitud'])->name('egres-entregarsolicitud.store');

    




    // Route::get('egres/view/{id?}', [EgressController::class, 'view_egreso'])->name('egreso_view_entregado');// en mantenimiento sin funcionamiento 


    // SUCURSALES Y DIRECION ADMINISTRATIVA Y UNIUDAD ADMINISTRATIVA COMO ALMACEN PRINCIPAL
    Route::get('sucursals', [SucursalController::class, 'index'])->name('voyager.sucursals.index');
    Route::get('sucursals/{sucursal?}/da/index', [SucursalController::class, 'indexDireccion'])->name('sucursal-da.index');
    Route::post('sucursals/da/store', [SucursalController::class, 'storeDireccion'])->name('sucursal-da.store');
    Route::delete('sucursals/da/delete', [SucursalController::class, 'destroyDireccion'])->name('sucursal-da.destroy');
    Route::post('sucursals/da/habilitar', [SucursalController::class, 'habilitarDireccion'])->name('sucursal-da.habilitar');
    Route::post('sucursals/da/inhabilitar', [SucursalController::class, 'inhabilitarDireccion'])->name('sucursal-da.inhabilitar');

    Route::post('sucursals/unidad/store', [SucursalController::class, 'storeUnidad'])->name('sucursal-unidad.store'); //para poder agregar una unidad administrativa como almacen principal
    Route::delete('sucursals/unidad/delete', [SucursalController::class, 'destroyUnidad'])->name('sucursal-unidad.destroy');

    Route::post('sucursals/subalmacen/store', [SucursalController::class, 'storeSubAlmacen'])->name('sucursal-subalmacen.store'); //para poder agregar un subalmacen al almacen
    Route::delete('sucursals/subalmacen/delete', [SucursalController::class, 'destroySubAlmacen'])->name('sucursal-subalmacen.destroy');//Para poder eliminar un sub almacen
    Route::get('sucursals/subalmacen/get/{id?}', [SucursalController::class, 'getSubSucursal'])->name('ajax-sucursal-subalmacen.get');//Para obtener todo los sub almacenes de una sucursal
    Route::get('sucursals/subalmacen/all/{id?}', [SucursalController::class, 'allSubSucursal'])->name('ajax-sucursal-subalmacen.all');//Para obtener todo los sub almacenes de una sucursal



    Route::get('providers', [ProviderController::class, 'index'])->name('voyager.providers.index');

    Route::get('articles', [ArticleController::class, 'index'])->name('voyager.articles.index');
    Route::get('articles/ajax/list/{search?}', [ArticleController::class, 'list']);



    // para crear personas externas en el sistemas
    Route::resource('people_ext', PeopleExtController::class);
    Route::get('people_ext/ajax/list/{search?}', [PeopleExtController::class, 'list']);
    Route::get('people_ext/{people_ext}/baja', [PeopleExtController::class, 'finish'])->name('people_ext.baja');


    // Para registrar los usuarios
    Route::post('register-users', [UserController::class, 'create_user'])->name('store.users');
    Route::put('update-user/{user}' ,[UserController::class ,'update_user'])->name('update.users');
    Route::get('search', [UserController::class, 'getFuncionario'])->name('user.getFuncionario');




    // Inventario Para cada Almacen
    // Route::resource('inventory', InventarioAlmacenController::class);
    Route::get('inventory/{id?}', [InventarioAlmacenController::class, 'index'])->name('inventory.index');
    Route::post('inventory/start', [InventarioAlmacenController::class, 'start'])->name('inventory.start');
    Route::post('inventory/finish', [InventarioAlmacenController::class, 'finish'])->name('inventory.finish');
    Route::post('inventory/reabrir', [InventarioAlmacenController::class, 'reabrir'])->name('inventory.reabrir');
    Route::get('inventory/{sucursal}/histinvdelete{gestion?}', [InventarioAlmacenController::class, 'indexHistInvDelete'])->name('inventory-histinvdelete.index');




    


    // reporte


    //  Reportes anuales: direcion Administrativa, partidas, detalle de articulos
    Route::get('print/almacen/getGestion/sucursal/{id?}', [Controller::class, 'getGestione'])->name('ajax-sucursal.getGestion');
    Route::get('print/almacen-inventarioAnual-da', [ReportAlmacenController::class, 'directionIncomeSalida'])->name('almacen-inventarioAnual-da.report');
    Route::post('print/almacen-inventarioAnual-da/list', [ReportAlmacenController::class, 'directionIncomeSalidaList'])->name('almacen-direction-income-egress.list');

    Route::get('print/almacen-inventarioAnual-partida', [ReportAlmacenController::class, 'inventarioPartida'])->name('almacen-inventarioAnual-partida.report');
    Route::post('print/almacen-inventarioAnual-partida/list', [ReportAlmacenController::class, 'inventarioPartidaList'])->name('almacen-inventarioAnual-partida.list');

    Route::get('print/almacen-inventarioAnual-detalle', [ReportAlmacenController::class, 'inventarioDetalle'])->name('almacen-inventarioAnual-detalle.report');
    Route::post('print/almacen-inventarioAnual-detalle/list', [ReportAlmacenController::class, 'inventarioDetalleList'])->name('almacen-inventarioAnual-detalle.list');

    // Reporte para generar el stock de ariculos disponible en cada almamacen
    Route::get('print/almacen-article-stock', [ReportAlmacenController::class, 'articleStock'])->name('almacen-article-stock.report');
    Route::post('print/almacen/article/stock/list', [ReportAlmacenController::class, 'articleStockList'])->name('almacen-article-stock.list');

    Route::get('print/almacen-article-list', [ReportAlmacenController::class, 'articleList'])->name('almacen-article-list.report');
    Route::post('print/almacen/article/list/list', [ReportAlmacenController::class, 'articleListList'])->name('almacen-article-list.list');

    Route::get('print/almacen-article-incomeoffice', [ReportAlmacenController::class, 'incomeOffice'])->name('almacen-article-incomeOffice.report');
    Route::post('print/almacen/article/incomeoffice/list', [ReportAlmacenController::class, 'incomeOfficeList'])->name('almacen-article-incomeOffice.list');

    Route::get('ajaxPrint/almacen-article-incomeoffice/direccion/{id?}', [ReportAlmacenController::class, 'ajax_incomeOffice_direccion'])->name('ajax-incomeOffice.direccion');
    Route::get('ajaxPrint/almacen-article-incomeoffice/unidad/{id?}', [ReportAlmacenController::class, 'ajax_incomeOffice_unidad'])->name('ajax-incomeOffice.unidad');

    Route::get('print/almacen-article-egressoffice', [ReportAlmacenController::class, 'egressOffice'])->name('almacen-article-egressOffice.report');
    Route::post('print/almacen/article/egressoffice/list', [ReportAlmacenController::class, 'egressOfficeList'])->name('almacen-article-egressOffice.list');

    //REPORTE PARA LAS PARTIDA PARA VER LAS ENTRADA Y SALIDA DE ARTICULOS POR PARTIDA
    Route::get('print/almacen-partida-incomearticle', [ReportAlmacenController::class, 'incomePartidaArticle'])->name('almacen-partida-incomearticle.report');
    Route::post('print/almacen/partida/incomearticle/list', [ReportAlmacenController::class, 'incomePartidaArticleList'])->name('almacen-partida-incomearticle.list');


    // Proveedores
    Route::get('print/almacen-provider-list', [ReportAlmacenController::class, 'provider'])->name('almacen-provider-list.report');
    Route::post('print/almacen/provider/list/list', [ReportAlmacenController::class, 'providerList'])->name('almacen-provider-list.list');


                            // REPORTE ADICIONAL
    // Usuario
    Route::get('print/almacen-user-list', [ReportAlmacenController::class, 'user'])->name('almacen-user-list.report');
    Route::post('print/almacen/user/list/list', [ReportAlmacenController::class, 'userList'])->name('almacen-user-list.list');



    Route::get('print/almacen-article-inventory', [ReportAlmacenController::class, 'articleInventory'])->name('almacen-article-inventory.report');
    Route::post('print/almacen-article-inventory/list', [ReportAlmacenController::class, 'articleInventoryList'])->name('almacen-article-inventory.list');

   

    Route::get('print/almacen-article-egresado', [ReportAlmacenController::class, 'articleEgresado'])->name('almacen-article-egresado.report');
    Route::post('print/almacen-article-egresado/list', [ReportAlmacenController::class, 'articleEgresadoList'])->name('almacen-article-egresado.list');


    Route::get('print/almacen-article-unidades', [ReportAlmacenController::class, 'articleUnidades'])->name('almacen-article-unidades.report');
    Route::post('print/almacen-article-unidades/list', [ReportAlmacenController::class, 'articleUnidadesList'])->name('almacen-article-unidades.list');





        //bandeja
    // Route::resource('inbox', BandejaController::class);


    // Route::get('egresos/browse/pendiente/view/{id?}', [EgressController::class, 'view_pendiente'])->name('egres_view_pendiente');
    // Route::post('egresos/pendiente/entregar', [EgressController::class, 'store_egreso_pendiente'])->name('egreso_store_egreso_pendiente');


    // Route::delete('egres/browse/delete', [EgressController::class, 'destroy'])->name('egres_delete');// en mantenimiento sin funcionamiento 




//     //bandeja
//     Route::resource('bandeja', BandejaController::class);
//     Route::get('bandeja/pendiente/view/{id?}', [BandejaController::class, 'view'])->name('bandeja_pendiente_view');
//     Route::get('bandeja/aprobada/view/{id?}', [BandejaController::class, 'view_aprobada'])->name('bandeja_aprobada_view');
//     Route::post('bandeja/pendiente/aprobarsoicitud', [BandejaController::class, 'aprobar'])->name('bandeja_aprobar_solicitud');
//     Route::post('bandeja/pendiente/rechazarsolicitud', [BandejaController::class, 'rechazo'])->name('bandeja_rechazar_solicitud');

//     Route::get('bandeja/pendiente/view/editar/{id?}', [BandejaController::class, 'view_editar'])->name('view_editar_aprobar_solicitud');
//     Route::post('bandeja/pendiente/view/editar/aprobar', [BandejaController::class, 'editar_aprobar'])->name('store_editar_aprobar_solicitud');




//   //........................  Solicitud para hacer Egresos a jefe de contratacion

//     Route::resource('incomesolicitud', IncomeSolicitudController::class);

//     Route::resource('solicitud', SolicitudController::class);
//     Route::delete('solicitudes/delete', [SolicitudController::class, 'destroy'])->name('solicitudes_delete');
//     Route::post('solicitudes/derivarsolicitud', [SolicitudController::class, 'derivar_proceso'])->name('derivar_proceso_solicitud');
//     Route::get('solicitudes/browse/view/{id?}', [SolicitudController::class, 'view'])->name('solicitudes_view');



//////////////////////////////////////////////////////////////////// SEDEGES ////////////////////////////////////////////////////////////////////////////////////////////////////

//                                                                      DONACION EN LOS ALMACENES

//INCOME
    Route::resource('incomedonor', IncomeDonorController::class);
    Route::delete('incomedonor/browse/delete', [IncomeDonorController::class, 'destroy'])->name('incomedonor_delete');
    Route::get('incomedonor/browse/view/stock/{id?}', [IncomeDonorController::class, 'show_stock'])->name('incomedonor_view_stock');
    Route::post('incomedonor/update', [IncomeDonorController::class, 'update'])->name('incomedonor_update');
    Route::post('incomedonor/delete/archivos', [IncomeDonorController::class, 'destroy_file'])->name('incomedonor_delete_file');

    // Route::get('donacion/browse/view/stock/disponible', [IncomeDonorController::class, 'show_stock_disponible'])->name('incomedonor_view_stock_disponible');


    // Route::get('incomedonor/browse/view/{id?}', [IncomeController::class, 'view_ingreso'])->name('incomedonor_view');
//STOCK VIEW PARA VER EL SEDEGES
    Route::get('incomedonor/stock/view', [DonationStockController::class, 'stock_view'])->name('donation.stock.view');   


//EGRESS
    Route::resource('egressdonor', EgressDonorController::class);  
    Route::get('egressdonor/browse/view/photo/{id?}', [EgressDonorController::class, 'show_photo'])->name('egressdonor_view_photo');



// VISTA PARA SOLICITUDES DE LAS DESCONCENTRADA
    Route::resource('view_stock_donacion', DonacionSolicitudController::class);   








    










    


    //:::::::::::::::::::::::::::::::::::::::::::::::::::::::Route Ajax::::::::::::::::::::::::::::::
    //INCOME
    Route::get('incomes/unida', [IncomeController::class, 'select_sucursal'])->name('select_sucursal');
    Route::get('incomes/selectunidadejecutora/{id?}', [IncomeController::class, 'ajax_unidad_administrativa'])->name('ajax_unidad_administrativa');

    // Route::get('incomes/selectunidadsolicitante/{id?}', [IncomeController::class, 'ajax_unidad_solicitante'])->name('ajax_unidad_solicitante');
    Route::get('incomes/selectarticle/{id?}', [IncomeController::class, 'ajax_article'])->name('ajax_article');
    Route::get('incomes/selectpresentacion/{id?}', [IncomeController::class, 'ajax_presentacion'])->name('ajax_presentacion');

    //No existe
    // //SOLICITUD DE EGRESO MEDIANTE VISTA
    // Route::get('solicitudes/modalidadcompra/{id?}', [SolicitudController::class, 'ajax_modalidadcompra'])->name('ajax_modalidadcompra'); 
    // Route::get('solicitudes/articulosolicitud/{id?}', [SolicitudController::class, 'ajax_articulo'])->name('ajax_solicitudes_articulo');
    // Route::get('solicitudes/articuloautollenar/{id?}', [SolicitudController::class, 'ajax_autollenar_articulo'])->name('ajax_autollenar_articulo');


    

    //EGRESS
    Route::get('egress/solicitudcompraunidad/{id?}', [EgressController::class, 'ajax_solicitud_compra'])->name('ajax_solicitud_compra');
    Route::get('egress/selectarticle/modalidadcompra/{id?}', [EgressController::class, 'ajax_egres_select_article'])->name('ajax_egres_select_article');
    Route::get('egress/selectarticle/detalle/{id?}', [EgressController::class, 'ajax_egres_select_article_detalle'])->name('ajax_egres_select_article_detalle');


    //###################################################################   Funciona   ######################################################
    Route::get('ajax/get/direccionsucursal/{id?}', [Controller::class, 'getDireccionSucursal'])->name('ajax-get.direccinsucursal'); //Para obtener todas las direciones de una sucursal
    Route::get('ajax/get/subsucursal/{id?}', [UserController::class, 'getSubSucursal'])->name('ajax-get.subsucursal'); //Para obtener todas la sub sucursales de los almacenes
    Route::get('ajax/get/unidadDirection/{id?}', [Controller::class, 'getUnidades'])->name('ajax-get.unidadDirection'); //Para obtener todas las direciones de una sucursal




    //___________________________________________________________________________DONACIONES_______________________________________________________
    Route::get('incomedonor/selectarticle/{id?}', [IncomeDonorController::class, 'ajax_article'])->name('ajax_article_donor');
    Route::get('incomedonor/selectpresentacion/{id?}', [IncomeDonorController::class, 'ajax_presentacion'])->name('ajax_presentacion_donor');
    Route::get('incomedonor/selectcentro/{id?}', [IncomeDonorController::class, 'ajax_centro_acogida'])->name('ajax_centro_acogida');
    Route::get('incomedonor/selectdonante/{id?}', [IncomeDonorController::class, 'ajax_income_donante'])->name('ajax_income_donante');

 //egress
    Route::get('incomedonor/selectarticle/egress/{id?}', [EgressDonorController::class, 'ajax_article'])->name('ajax_disponible_article_donor');
    Route::get('incomedonor/llenar_input/egress/{id?}', [EgressDonorController::class, 'ajax_autollenar_articulo'])->name('ajax_egressdoner_llenarimput');










//NOTIFICACION
    Route::get('notification/donacion/caducidad', [NotificationController::class, 'ajax_notificacion_donacion'])->name('donacion_notificacion');


   



});




// Clear cache
Route::get('/admin/clear-cache', function() {
    Artisan::call('optimize:clear');
    return redirect('/admin')->with(['message' => 'Cache eliminada.', 'alert-type' => 'success']);
})->name('clear.cache');

Auth::routes(['register'=>false]);

Route::get('login', function () {
    return redirect('admin/login');
})->name('login');

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
