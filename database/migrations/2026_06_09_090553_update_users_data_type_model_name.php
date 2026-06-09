<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateUsersDataTypeModelName extends Migration
{
    public function up()
    {
        DB::table('data_types')
            ->where('slug', 'users')
            ->update(['model_name' => 'App\\Models\\User']);
    }

    public function down()
    {
        DB::table('data_types')
            ->where('slug', 'users')
            ->update(['model_name' => 'TCG\\Voyager\\Models\\User']);
    }
}
