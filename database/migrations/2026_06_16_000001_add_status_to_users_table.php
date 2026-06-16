<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddStatusToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('status')->default(true)->after('must_change_password');
        });

        $usersDataTypeId = DB::table('data_types')->where('slug', 'users')->value('id');

        if ($usersDataTypeId && !DB::table('data_rows')->where('data_type_id', $usersDataTypeId)->where('field', 'status')->exists()) {
            DB::table('data_rows')->insert([
                'data_type_id' => $usersDataTypeId,
                'field' => 'status',
                'type' => 'checkbox',
                'display_name' => 'Estado',
                'required' => 1,
                'browse' => 1,
                'read' => 1,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'details' => json_encode([
                    'on' => 'Activo',
                    'off' => 'Inactivo',
                    'checked' => true,
                    'view' => 'almacenes.users.status-browse',
                ]),
                'order' => DB::table('data_rows')->where('data_type_id', $usersDataTypeId)->max('order') + 1,
            ]);
        }
    }

    public function down()
    {
        $usersDataTypeId = DB::table('data_types')->where('slug', 'users')->value('id');

        if ($usersDataTypeId) {
            DB::table('data_rows')
                ->where('data_type_id', $usersDataTypeId)
                ->where('field', 'status')
                ->delete();
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
