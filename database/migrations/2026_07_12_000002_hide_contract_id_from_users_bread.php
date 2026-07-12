<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class HideContractIdFromUsersBread extends Migration
{
    public function up()
    {
        // Ocultar contract_id del listado (browse) y de "Ver usuario" (read):
        // es un ID interno sin valor para el operador.
        DB::table('data_rows')
            ->where('data_type_id', function ($q) {
                $q->select('id')->from('data_types')->where('slug', 'users');
            })
            ->where('field', 'contract_id')
            ->update(['browse' => 0, 'read' => 0]);
    }

    public function down()
    {
        DB::table('data_rows')
            ->where('data_type_id', function ($q) {
                $q->select('id')->from('data_types')->where('slug', 'users');
            })
            ->where('field', 'contract_id')
            ->update(['browse' => 1, 'read' => 1]);
    }
}
