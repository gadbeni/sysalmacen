<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportFacturas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitudcompra_id')->constrained('import_solicitud_compras');
            $table->foreignId('provider_id')->nullable()->constrained('providers');

            $table->foreignId('registeruser_id')->constrained('users');

            $table->string('tipofactura');
            $table->date('fechafactura')->nullable();
            $table->decimal('montofactura', 11, 2);
            $table->string('nrofactura')->nullable();
            $table->string('nroautorizacion')->nullable();
            $table->string('nrocontrol')->nullable();

            $table->date('fechaingreso');
            $table->string('gestion', 10);
            $table->boolean('condicion')->default(1); 
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursals');

            $table->timestamps();

            $table->unsignedBigInteger('deleteuser_id')->nullable(); 

            $table->foreign('deleteuser_id')->references('id')->on('users');
            
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
        Schema::dropIfExists('import_facturas');
    }
}
