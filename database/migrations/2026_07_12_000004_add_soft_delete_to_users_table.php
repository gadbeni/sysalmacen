<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeleteToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminación lógica: el borrado físico rompe FKs (ej. solicitud_pedido_detalles.registerUser_id)
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedBigInteger('deleteuser_id')->nullable(); // quién eliminó (convención del proyecto)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['deleted_at', 'deleteuser_id']);
        });
    }
}
