<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class HideAvatarFromUsersReadBread extends Migration
{
    public function up()
    {
        // Ocultar avatar de la vista "Ver usuario" (read): salía como imagen rota
        // y la foto ya se muestra en el campo Funcionario (funcionario-browse).
        DB::table('data_rows')
            ->where('data_type_id', function ($q) {
                $q->select('id')->from('data_types')->where('slug', 'users');
            })
            ->where('field', 'avatar')
            ->update(['read' => 0]);
    }

    public function down()
    {
        DB::table('data_rows')
            ->where('data_type_id', function ($q) {
                $q->select('id')->from('data_types')->where('slug', 'users');
            })
            ->where('field', 'avatar')
            ->update(['read' => 1]);
    }
}
