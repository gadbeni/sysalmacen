<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ReorderFuncionarioColumnUsersBread extends Migration
{
    public function up()
    {
        // funcionario_id: primero visible, usar custom view que muestra email debajo
        DB::table('data_rows')->where('id', 148)->update([
            'order'   => 2,
            'details' => json_encode(['view' => 'almacenes.users.funcionario-browse']),
        ]);

        // ocultar email de browse (ya se muestra dentro del cell de funcionario)
        DB::table('data_rows')->where('id', 3)->update(['browse' => 0]);
    }

    public function down()
    {
        DB::table('data_rows')->where('id', 148)->update([
            'order'   => 8,
            'details' => '{}',
        ]);
        DB::table('data_rows')->where('id', 3)->update(['browse' => 1]);
    }
}
