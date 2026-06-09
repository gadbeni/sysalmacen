<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MergeDireccionUnidadAvatarUsersBread extends Migration
{
    public function up()
    {
        // direccionAdministrativa_id: custom view que muestra dirección + unidad juntas
        DB::table('data_rows')->where('id', 222)->update([
            'display_name' => 'Dirección / Unidad',
            'details' => json_encode(['view' => 'almacenes.users.direccion-browse']),
        ]);

        // unidadAdministrativa_id: ocultar (se muestra en el cell de dirección)
        DB::table('data_rows')->where('id', 229)->update(['browse' => 0]);

        // avatar: ocultar (se muestra en el cell de funcionario)
        DB::table('data_rows')->where('id', 8)->update(['browse' => 0]);
    }

    public function down()
    {
        DB::table('data_rows')->where('id', 222)->update([
            'display_name' => 'Direccion',
            'details' => '{}',
        ]);
        DB::table('data_rows')->where('id', 229)->update(['browse' => 1]);
        DB::table('data_rows')->where('id', 8)->update(['browse' => 1]);
    }
}
