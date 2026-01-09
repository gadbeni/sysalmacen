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
            $table->foreignId('registerUser_id')->nullable()->constrained('users');//usuario que registra la solicitud
            $table->string('registerUser_name')->nullable();//nombre del usuario que registra la solicitud
            $table->date('date_request');//fecha de solicitud
            $table->string('gestion', 10);
            $table->string('nro_request')->nullable(); //numero de solicitud

            $table->integer('people_id')->nullable(); //id de la persona
            $table->string('job')->nullable(); //trabajo actual del usuario

            $table->integer('direction_id'); //id de la direccion
            $table->string('direction_name')->nullable(); //nombre de la direccion
            $table->integer('unit_id'); //id de la unidad|
            $table->string('unit_name')->nullable(); //nombre de la unidad
            $table->dateTime('visto')->nullable(); //fecha de visto
            
            $table->enum('status',['pendiente','enviado','entregado','aprobado','rechazado','eliminado'])->default('pendiente'); //estado
            $table->text('observation')->nullable();
            $table->date('date_status')->nullable();//fecha de actualizacion estado
            $table->foreignId('statusUser_id')->nullable()->constrained('users');//usuario que actualiza el estado
            $table->timestamps();

            $table->softDeletes();
            $table->foreignId('deletedUser_Id')->nullable()->constrained('users');
            $table->text('deletedObservation')->nullable();
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
