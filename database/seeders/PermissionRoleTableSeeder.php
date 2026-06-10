<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Models\Role;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('permission_role')->delete();
        
        // Root
        $role = Role::where('name', 'admin')->firstOrFail();
        $permissions = Permission::all();
        $role->permissions()->sync($permissions->pluck('id')->all());

         // ALAMACENES CENTRALES 


        //Para el administrador de todos los almacenes
        $role = Role::where('name', 'almacen_admin')->firstOrFail();
        $permissions = Permission::whereRaw('table_name = "admin" or
                                            table_name = "outbox" or
                                            table_name = "inbox" or

                                            table_name = "existingproducts" or                                            
                                            

                                            `key` = "browse_partidas" or
                                            `key` = "read_partidas" or
                                            `key` = "edit_partidas" or
                                            `key` = "add_partidas" or

                                            `key` = "browse_articles" or
                                            `key` = "read_articles" or
                                            `key` = "edit_articles" or
                                            `key` = "add_articles" or

                                            `key` = "browse_sucursals" or

                                            `key` = "browse_inventory" or
                                            

                                            `key` = "browse_modalities" or
                                            `key` = "read_modalities" or
                                            `key` = "edit_modalities" or
                                            `key` = "add_modalities" or

                                            `key` = "browse_providers" or
                                            `key` = "read_providers" or
                                            `key` = "edit_providers" or
                                            `key` = "add_providers" or

                                            table_name = "reports_anual" or
                                            
                                            `key` = "browse_printalmacen-article-list" or
                                            `key` = "browse_printalmacen-article-stock" or
                                            `key` = "browse_printalmacen-article-incomeoffice" or     
                                            `key` = "browse_printalmacen-article-egressoffice" or   
                                            
                                            `key` = "browse_printalmacen-partida-incomearticle" or    
                                            
                                            
                                            
                                            `key` = "browse_printalmacen-provider-list" or

                                            table_name = "inventory" or

                                            `key` = "browse_clear-cache"')->get();
        $role->permissions()->sync($permissions->pluck('id')->all());


        //para los reportes
        $role = Role::where('name', 'almacen_subadmin')->firstOrFail();
        $permissions = Permission::whereRaw('table_name = "admin" or
                                            `key` = "browse_providers" or
                                            `key` = "read_providers" or
                                            `key` = "edit_providers" or
                                            `key` = "add_providers" or

                                            
                                            `key` = "browse_printalmacen-inventarioAnual-da" or
                                            `key` = "browse_printalmacen-inventarioAnual-partida" or
                                            `key` = "browse_printalmacen-inventarioAnual-detalle" or
                                            
                                            `key` = "browse_printalmacen-article-list" or
                                            `key` = "browse_printalmacen-article-stock" or
                                            `key` = "browse_printalmacen-article-incomeoffice" or     
                                            `key` = "browse_printalmacen-article-egressoffice" or    
                                            
                                            
                                            `key` = "browse_printalmacen-partida-incomearticle" or    

                                            
                                            `key` = "browse_printalmacen-provider-list" or

                                            `key` = "browse_clear-cache"')->get();
        $role->permissions()->sync($permissions->pluck('id')->all());


        //responsable del almacen: ingresa producto al almacen y dispensa los egresos o articulos
        $role = Role::where('name', 'almacen_responsable')->firstOrFail();
        $permissions = Permission::whereRaw('table_name = "admin" or
                                            table_name = "income" or
                                            table_name = "egres" or


                                            `key` = "browse_providers" or
                                            `key` = "read_providers" or
                                            `key` = "edit_providers" or
                                            `key` = "add_providers" or

                                            table_name = "reports_anual" or
                                            
                                            `key` = "browse_printalmacen-article-list" or
                                            `key` = "browse_printalmacen-article-stock" or
                                            `key` = "browse_printalmacen-article-incomeoffice" or     
                                            `key` = "browse_printalmacen-article-egressoffice" or  

                                            `key` = "browse_printalmacen-partida-incomearticle" or  
                                            
                                            `key` = "browse_printalmacen-provider-list" or
                                            
                                            `key` = "browse_clear-cache"')->get();
        $role->permissions()->sync($permissions->pluck('id')->all());


        //Para ingreso y egreso y reporte y aprobacion de solicitudes
        $role = Role::where('name', 'almacen_subadmin_responsable_aprobar')->firstOrFail();
        $permissions = Permission::whereRaw('table_name = "admin" or
                                            table_name = "income" or
                                            table_name = "egres" or

                                            `key` = "browse_providers" or
                                            `key` = "read_providers" or
                                            `key` = "edit_providers" or
                                            `key` = "add_providers" or

                                            table_name = "inbox" or

                                            
                                            `key` = "browse_printalmacen-inventarioAnual-da" or
                                            `key` = "browse_printalmacen-inventarioAnual-partida" or
                                            `key` = "browse_printalmacen-inventarioAnual-detalle" or
                                            
                                            `key` = "browse_printalmacen-article-list" or
                                            `key` = "browse_printalmacen-article-stock" or
                                            `key` = "browse_printalmacen-article-incomeoffice" or     
                                            `key` = "browse_printalmacen-article-egressoffice" or  

                                            `key` = "browse_printalmacen-partida-incomearticle" or  
                                            
                                            `key` = "browse_printalmacen-provider-list" or
                                            
                                            `key` = "browse_clear-cache"')->get();
        $role->permissions()->sync($permissions->pluck('id')->all());


        //Para ingreso y egreso y reporte y aprobacion de solicitudes y crear solicitud
        $role = Role::where('name', 'almacen_subadmin_responsable_aprobar_solicitar')->firstOrFail();
        $permissions = Permission::whereRaw('table_name = "admin" or
                                            table_name = "income" or
                                            table_name = "egres" or

                                            `key` = "browse_providers" or
                                            `key` = "read_providers" or
                                            `key` = "edit_providers" or
                                            `key` = "add_providers" or

                                            table_name = "inbox" or
                                            table_name = "outbox" or

                                            table_name = "existingproducts" or 

                                            
                                            `key` = "browse_printalmacen-inventarioAnual-da" or
                                            `key` = "browse_printalmacen-inventarioAnual-partida" or
                                            `key` = "browse_printalmacen-inventarioAnual-detalle" or
                                            
                                            `key` = "browse_printalmacen-article-list" or
                                            `key` = "browse_printalmacen-article-stock" or
                                            `key` = "browse_printalmacen-article-incomeoffice" or     
                                            `key` = "browse_printalmacen-article-egressoffice" or  

                                            `key` = "browse_printalmacen-partida-incomearticle" or  
                                            
                                            `key` = "browse_printalmacen-provider-list" or
                                            
                                            `key` = "browse_clear-cache"')->get();
        $role->permissions()->sync($permissions->pluck('id')->all());


        //Para ingreso y egreso y reporte y crear solicitud
        $role = Role::where('name', 'almacen_subadmin_responsable_solicitar')->firstOrFail();
        $permissions = Permission::whereRaw('table_name = "admin" or
                                            table_name = "income" or
                                            table_name = "egres" or

                                            `key` = "browse_providers" or
                                            `key` = "read_providers" or
                                            `key` = "edit_providers" or
                                            `key` = "add_providers" or

                                            table_name = "outbox" or
                                            table_name = "existingproducts" or 

                                            `key` = "browse_printalmacen-inventarioAnual-da" or
                                            `key` = "browse_printalmacen-inventarioAnual-partida" or
                                            `key` = "browse_printalmacen-inventarioAnual-detalle" or
                                            
                                            `key` = "browse_printalmacen-article-list" or
                                            `key` = "browse_printalmacen-article-stock" or
                                            `key` = "browse_printalmacen-article-incomeoffice" or     
                                            `key` = "browse_printalmacen-article-egressoffice" or  

                                            `key` = "browse_printalmacen-partida-incomearticle" or  
                                            
                                            `key` = "browse_printalmacen-provider-list" or
                                            
                                            `key` = "browse_clear-cache"')->get();
        $role->permissions()->sync($permissions->pluck('id')->all());

    



        //para las solicitudes de pedidos
        $role = Role::where('name', 'almacen_solicitud_pedido')->firstOrFail();
        $permissions = Permission::whereRaw('table_name = "admin" or
                                             table_name = "outbox" or
                                             table_name = "non_stock_requests" or
                                             table_name = "existingproducts" or 

                                            
                                            `key` = "browse_clear-cache"')->get();
        $role->permissions()->sync($permissions->pluck('id')->all());


        //Para aprobar las solicitudes de pedidos   
        $role = Role::where('name', 'almacen_solicitud_aprobar')->firstOrFail();
        $permissions = Permission::whereRaw('table_name = "admin" or
                                             table_name = "inbox" or
                                            
                                            `key` = "browse_clear-cache"')->get();
        $role->permissions()->sync($permissions->pluck('id')->all());

        //Para aprobar las solicitudes y hacer solicitudes 
        $role = Role::where('name', 'almacen_aprobar_solicitar')->firstOrFail();
        $permissions = Permission::whereRaw('table_name = "admin" or
                                             table_name = "outbox" or
                                             table_name = "existingproducts" or 

                                             table_name = "inbox" or
                                            
                                            `key` = "browse_clear-cache"')->get();
        $role->permissions()->sync($permissions->pluck('id')->all());

        //Para ingreso y egreso y reporte y crear solicitud ---- ALMACEN CENTRAL SUBADMIN
        $role = Role::where('name', 'almacen_subadmin_central')->firstOrFail();
        $permissions = Permission::whereRaw('table_name = "admin" or
                                            table_name = "income" or
                                            table_name = "egres" or

                                            `key` = "browse_articles" or
                                            `key` = "read_articles" or
                                            `key` = "edit_articles" or
                                            `key` = "add_articles" or

                                            `key` = "browse_providers" or
                                            `key` = "read_providers" or
                                            `key` = "edit_providers" or
                                            `key` = "add_providers" or

                                            table_name = "outbox" or
                                            table_name = "existingproducts" or
                                            table_name = "no_stock_inbox" or
                                            table_name = "non_stock_requests" or 

                                            table_name = "reports_anual" or
                                            
                                            `key` = "browse_printalmacen-article-list" or
                                            `key` = "browse_printalmacen-article-stock" or
                                            `key` = "browse_printalmacen-article-incomeoffice" or     
                                            `key` = "browse_printalmacen-article-egressoffice" or  

                                            `key` = "browse_printalmacen-partida-incomearticle" or  
                                            
                                            `key` = "browse_printalmacen-provider-list" or
                                            
                                            `key` = "browse_clear-cache"')->get();
        $role->permissions()->sync($permissions->pluck('id')->all());
        //almacen_report_central
        $role = Role::where('name', 'almacen_report_central')->firstOrFail();
        $permissions = Permission::whereRaw('table_name = "admin" or
                                            table_name = "reports_anual" or
                                            
                                            `key` = "browse_printalmacen-article-list" or
                                            `key` = "browse_printalmacen-article-stock" or
                                            `key` = "browse_printalmacen-article-incomeoffice" or     
                                            `key` = "browse_printalmacen-article-egressoffice" or  

                                            `key` = "browse_printalmacen-partida-incomearticle" or  
                                            
                                            `key` = "browse_printalmacen-provider-list" or
                                            
                                            `key` = "browse_clear-cache"')->get();
        $role->permissions()->sync($permissions->pluck('id')->all());
    }
}
