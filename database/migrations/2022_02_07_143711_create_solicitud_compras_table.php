<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolicitudComprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitud_compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursals');
            // $table->foreignId('requestingunit_id')->constrained('requesting_units');
            $table->integer('unidadadministrativa');
            $table->foreignId('modality_id')->constrained('modalities');
            $table->foreignId('registeruser_id')->constrained('users');


            $table->string('nrosolicitud');
            $table->date('fechaingreso');
            $table->string('gestion', 10);
            $table->boolean('condicion')->default(1);  // para cuando la solicitud no tiene moviemto de egreso tendra 1
                                                       // y cuando tenga algun egreso sera 0
            $table->timestamps();


            $table->unsignedBigInteger('deleteuser_id')->nullable(); 

            $table->foreign('deleteuser_id')->references('id')->on('users');

            // $table->foreignId('deleteuser_id')->constrained('users')->nullable();
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
        Schema::dropIfExists('solicitud_compras');
    }
}