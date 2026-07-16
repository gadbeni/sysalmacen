<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class HideNameColumnUsersBread extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('data_rows')
            ->where('data_type_id', 1)
            ->where('field', 'name')
            ->update(['browse' => 0]);
    }

    public function down()
    {
        DB::table('data_rows')
            ->where('data_type_id', 1)
            ->where('field', 'name')
            ->update(['browse' => 1]);
    }
}
