<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserHistorialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_historials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();       // usuario modificado
            $table->unsignedBigInteger('changed_by')->nullable(); // quien realizo el cambio
            $table->string('accion', 30);                         // creado / actualizado / activado / desactivado
            $table->text('antes')->nullable();                    // snapshot JSON del estado anterior
            $table->text('despues')->nullable();                  // snapshot JSON del estado posterior
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_historials');
    }
}
