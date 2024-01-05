<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportSolicitudCompras extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_solicitud_compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursals');
            $table->foreignId('inventarioAlmacen_id')->nullable()->constrained('inventario_almacens');

            $table->integer('direccionadministrativa')->nullable();
            $table->integer('unidadadministrativa')->nullable();
            $table->foreignId('modality_id')->nullable()->constrained('modalities');
            $table->foreignId('registeruser_id')->nullable()->constrained('users');

            $table->date('fechaingreso');
            $table->string('gestion', 10);
            $table->boolean('condicion')->default(1);

            $table->smallInteger('stock')->default(1);

            $table->timestamps();

            $table->unsignedBigInteger('deleteuser_id')->nullable(); 

            $table->foreign('deleteuser_id')->references('id')->on('users');
            $table->boolean('import')->default(0);

            
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
        Schema::dropIfExists('import_solicitud_compras');
    }
}
