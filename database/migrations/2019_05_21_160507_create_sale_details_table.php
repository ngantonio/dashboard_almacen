<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_details', function (Blueprint $table) {
            $table->increments('id');

            // Venta asociada a estos detalles
            $table->integer('sale_id')->unsigned();
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');

            //Articulos asociados a esa venta, los articulos forman parte de los detalles
            $table->integer('article_id')->unsigned();
            $table->foreign('article_id')->references('id')->on('articles');

            $table->integer('quantity');        #cantidad de productos seleccionados
            $table->decimal('price',11,2);      #precio
            $table->decimal('discount',11,2);   #Posible descuento realizado a una venta
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_details');
    }
}
