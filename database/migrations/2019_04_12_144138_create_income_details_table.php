<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncomeDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        # detalles de un ingreso. contiene asociado uno o mas articulos del ingreso
        # ademas de el ingreso al que pertenece
        Schema::create('income_details', function (Blueprint $table) {
            $table->increments('id');

            # Referencia a un ingreso 
            $table->integer('income_id')->unsigned(); 
            $table->foreign('income_id')->references('id')->on('incomes')->onDelete('cascade');

            # Referencia a los articulos 
            $table->integer('article_id')->unsigned(); 
            $table->foreign('article_id')->references('id')->on('articles');

            $table->integer('quantity');  #cantidad de productos seleccionados
            $table->decimal('price',11,2);#precio
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('income_details');
    }
}
