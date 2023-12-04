<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNonStockRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('non_stock_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursals');
            $table->foreignId('subSucursal_id')->nullable()->constrained('sucursal_sub_almacens');
            $table->foreignId('registerUser_id')->constrained('users');//usuario que registra la solicitud
            $table->string('registerUser_name');//nombre del usuario que registra la solicitud
            $table->date('date_request');//fecha de solicitud
            $table->string('gestion', 10);
            $table->string('nro_request'); //numero de solicitud
            $table->integer('direction_id'); //id de la direccion
            $table->string('direction_name')->nullable(); //nombre de la direccion
            $table->integer('unit_id'); //id de la unidad|
            $table->string('unit_name')->nullable(); //nombre de la unidad
            $table->string('job')->nullable(); //trabajo actual del usuario
            $table->enum('status',['pendiente','enviado','entregado','aprobado','rechazado','eliminado'])->default('pendiente'); //estado
            $table->date('date_status')->nullable();//fecha de actualizacion estado
            $table->foreignId('statusUser_id')->nullable()->constrained('users');//usuario que actualiza el estado
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('non_stock_requests');
    }
}
