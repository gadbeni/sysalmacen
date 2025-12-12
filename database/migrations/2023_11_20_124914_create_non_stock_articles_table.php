<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNonStockArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //table article_presentations
        Schema::create('article_presentations', function (Blueprint $table) {
            $table->id();
            $table->string('name_presentation');
            $table->timestamps();
        });

        //table non_stock_articles
        Schema::create('non_stock_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registerUser_id')->constrained('users');//usuario que registra el articulo
            $table->string('name_description');
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
        Schema::dropIfExists('non_stock_articles');
        Schema::dropIfExists('article_presentations');
    }
}
