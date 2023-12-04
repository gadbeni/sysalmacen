<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNonRequestArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('non_request_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('non_request_id')->constrained('non_stock_requests');
            $table->foreignId('non_article_id')->constrained('non_stock_articles');
            $table->foreignId('article_presentation_id')->constrained('article_presentations');//presentacion del articulo
            $table->integer('quantity'); //cantidad
            $table->double('unit_price', 8, 2)->nullable();// precio unitario
            $table->double('reference_price', 8, 2)->nullable();// precio de referencia
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
        Schema::dropIfExists('non_request_articles');
    }
}
