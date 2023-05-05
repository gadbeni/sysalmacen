<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DataTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('data_types')->delete();
        
        \DB::table('data_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'users',
                'slug' => 'users',
                'display_name_singular' => 'User',
                'display_name_plural' => 'Users',
                'icon' => 'voyager-person',
                'model_name' => 'TCG\\Voyager\\Models\\User',
                'policy_name' => 'TCG\\Voyager\\Policies\\UserPolicy',
                'controller' => 'TCG\\Voyager\\Http\\Controllers\\VoyagerUserController',
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"desc","default_search_key":null,"scope":null}',
                'created_at' => '2021-06-02 17:55:30',
                'updated_at' => '2023-05-05 12:12:17',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'menus',
                'slug' => 'menus',
                'display_name_singular' => 'Menu',
                'display_name_plural' => 'Menus',
                'icon' => 'voyager-list',
                'model_name' => 'TCG\\Voyager\\Models\\Menu',
                'policy_name' => NULL,
                'controller' => '',
                'description' => '',
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => NULL,
                'created_at' => '2021-06-02 17:55:30',
                'updated_at' => '2021-06-02 17:55:30',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'roles',
                'slug' => 'roles',
                'display_name_singular' => 'Role',
                'display_name_plural' => 'Roles',
                'icon' => 'voyager-lock',
                'model_name' => 'TCG\\Voyager\\Models\\Role',
                'policy_name' => NULL,
                'controller' => 'TCG\\Voyager\\Http\\Controllers\\VoyagerRoleController',
                'description' => '',
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => NULL,
                'created_at' => '2021-06-02 17:55:31',
                'updated_at' => '2021-06-02 17:55:31',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'partidas',
                'slug' => 'partidas',
                'display_name_singular' => 'Partida',
                'display_name_plural' => 'Partidas',
                'icon' => NULL,
                'model_name' => 'App\\Models\\Partida',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null,"scope":null}',
                'created_at' => '2022-01-31 10:10:52',
                'updated_at' => '2022-12-15 12:46:36',
            ),
            4 => 
            array (
                'id' => 14,
                'name' => 'providers',
                'slug' => 'providers',
                'display_name_singular' => 'Proveedor',
                'display_name_plural' => 'Proveedores',
                'icon' => 'voyager-milestone',
                'model_name' => 'App\\Models\\Provider',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null,"scope":null}',
                'created_at' => '2022-01-31 12:48:27',
                'updated_at' => '2022-12-13 15:10:36',
            ),
            5 => 
            array (
                'id' => 15,
                'name' => 'units',
                'slug' => 'units',
                'display_name_singular' => 'Unidad Solicitante',
                'display_name_plural' => 'Unidades Solicitantes',
                'icon' => 'voyager-group',
                'model_name' => 'App\\Models\\Unit',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null,"scope":null}',
                'created_at' => '2022-01-31 13:21:43',
                'updated_at' => '2022-01-31 13:25:49',
            ),
            6 => 
            array (
                'id' => 16,
                'name' => 'articles',
                'slug' => 'articles',
                'display_name_singular' => 'Artículo',
                'display_name_plural' => 'Artículos',
                'icon' => 'voyager-basket',
                'model_name' => 'App\\Models\\Article',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null,"scope":null}',
                'created_at' => '2022-02-03 16:13:34',
                'updated_at' => '2023-04-10 00:54:30',
            ),
            7 => 
            array (
                'id' => 22,
                'name' => 'modalities',
                'slug' => 'modalities',
                'display_name_singular' => 'Modalidad de Compra',
                'display_name_plural' => 'Modalidades de Compras',
                'icon' => 'voyager-file-text',
                'model_name' => 'App\\Models\\Modality',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null}',
                'created_at' => '2022-02-04 14:34:33',
                'updated_at' => '2022-02-04 14:34:33',
            ),
            8 => 
            array (
                'id' => 23,
                'name' => 'sucursals',
                'slug' => 'sucursals',
                'display_name_singular' => 'Sucursal',
                'display_name_plural' => 'Sucursales',
                'icon' => 'voyager-basket',
                'model_name' => 'App\\Models\\Sucursal',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null,"scope":null}',
                'created_at' => '2022-02-07 14:28:20',
                'updated_at' => '2022-02-07 15:05:34',
            ),
            9 => 
            array (
                'id' => 25,
                'name' => 'donacion_categorias',
                'slug' => 'donacion-categorias',
                'display_name_singular' => 'Categoria',
                'display_name_plural' => 'Categorias',
                'icon' => 'voyager-categories',
                'model_name' => 'App\\Models\\DonacionCategoria',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null,"scope":null}',
                'created_at' => '2022-02-15 21:07:50',
                'updated_at' => '2022-02-15 21:12:40',
            ),
            10 => 
            array (
                'id' => 26,
                'name' => 'donacion_articulos',
                'slug' => 'donacion-articulos',
                'display_name_singular' => 'Articulo',
                'display_name_plural' => 'Articulos',
                'icon' => 'voyager-basket',
                'model_name' => 'App\\Models\\DonacionArticulo',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null,"scope":null}',
                'created_at' => '2022-02-15 21:14:50',
                'updated_at' => '2022-02-21 00:05:53',
            ),
            11 => 
            array (
                'id' => 27,
                'name' => 'centro_categorias',
                'slug' => 'centro-categorias',
                'display_name_singular' => 'Categoria',
                'display_name_plural' => 'Categorias',
                'icon' => 'voyager-categories',
                'model_name' => 'App\\Models\\CentroCategoria',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null,"scope":null}',
                'created_at' => '2022-02-15 22:23:09',
                'updated_at' => '2022-02-15 22:24:20',
            ),
            12 => 
            array (
                'id' => 28,
                'name' => 'centros',
                'slug' => 'centros',
                'display_name_singular' => 'Centro de Establecimiento',
                'display_name_plural' => 'Centros de Establecimientos',
                'icon' => 'voyager-home',
                'model_name' => 'App\\Models\\Centro',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null,"scope":null}',
                'created_at' => '2022-02-15 22:49:51',
                'updated_at' => '2022-02-21 00:01:14',
            ),
            13 => 
            array (
                'id' => 29,
                'name' => 'donador_personas',
                'slug' => 'donador-personas',
                'display_name_singular' => 'Persona',
                'display_name_plural' => 'Personas',
                'icon' => 'voyager-person',
                'model_name' => 'App\\Models\\DonadorPersona',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null,"scope":null}',
                'created_at' => '2022-02-15 23:30:09',
                'updated_at' => '2022-02-15 23:32:10',
            ),
            14 => 
            array (
                'id' => 30,
                'name' => 'donador_empresas',
                'slug' => 'donador-empresas',
                'display_name_singular' => 'Empresa / ONG',
                'display_name_plural' => 'Empresas / ONG',
                'icon' => 'voyager-people',
                'model_name' => 'App\\Models\\DonadorEmpresa',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null,"scope":null}',
                'created_at' => '2022-02-15 23:54:51',
                'updated_at' => '2022-02-16 00:01:50',
            ),
            15 => 
            array (
                'id' => 33,
                'name' => 'permissions',
                'slug' => 'permissions',
                'display_name_singular' => 'Permission',
                'display_name_plural' => 'Permissions',
                'icon' => 'voyager-key',
                'model_name' => 'App\\Models\\Permission',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => NULL,
                'generate_permissions' => 1,
                'server_side' => 0,
                'details' => '{"order_column":null,"order_display_column":null,"order_direction":"asc","default_search_key":null}',
                'created_at' => '2022-03-14 12:01:33',
                'updated_at' => '2022-03-14 12:01:33',
            ),
        ));
        
        
    }
}