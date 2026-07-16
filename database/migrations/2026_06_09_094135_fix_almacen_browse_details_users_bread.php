<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixAlmacenBrowseDetailsUsersBread extends Migration
{
    public function up()
    {
        // Mantener relationship config original + agregar view para custom display
        DB::table('data_rows')->where('id', 228)->update([
            'details' => json_encode([
                'view'        => 'almacenes.users.almacen-browse',
                'model'       => 'App\\Models\\Sucursal',
                'table'       => 'sucursals',
                'type'        => 'belongsTo',
                'column'      => 'sucursal_id',
                'key'         => 'id',
                'label'       => 'nombre',
                'pivot_table' => 'archivos',
                'pivot'       => '0',
                'taggable'    => '0',
            ]),
        ]);
    }

    public function down()
    {
        DB::table('data_rows')->where('id', 228)->update([
            'details' => json_encode([
                'model'       => 'App\\Models\\Sucursal',
                'table'       => 'sucursals',
                'type'        => 'belongsTo',
                'column'      => 'sucursal_id',
                'key'         => 'id',
                'label'       => 'nombre',
                'pivot_table' => 'archivos',
                'pivot'       => '0',
                'taggable'    => '0',
            ]),
        ]);
    }
}
