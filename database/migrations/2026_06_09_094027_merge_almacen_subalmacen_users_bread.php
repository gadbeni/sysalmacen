<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MergeAlmacenSubalmacenUsersBread extends Migration
{
    public function up()
    {
        // Almacenes relationship: custom view que muestra almacén + sub almacén
        DB::table('data_rows')->where('id', 228)->update([
            'display_name' => 'Almacén / Sub Almacén',
            'details' => json_encode(['view' => 'almacenes.users.almacen-browse']),
        ]);

        // Ocultar: sub almacén relationship, sucursal_id raw, subSucursal_id raw
        DB::table('data_rows')->whereIn('id', [230, 227, 232])->update(['browse' => 0]);
    }

    public function down()
    {
        DB::table('data_rows')->where('id', 228)->update([
            'display_name' => 'Almacenes',
            'details' => '{"model":"App\\\\Models\\\\Sucursal","table":"sucursals","type":"belongsTo","column":"sucursal_id","key":"id","label":"nombre","pivot_table":"archivos","pivot":"0","taggable":"0"}',
        ]);
        DB::table('data_rows')->whereIn('id', [230, 227, 232])->update(['browse' => 1]);
    }
}
