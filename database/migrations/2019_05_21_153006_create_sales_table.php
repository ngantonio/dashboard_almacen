<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->increments('id');

            // Cliente involucrado en una venta
            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('persons');

            // Usuario del sistema que realizó o registró la venta
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

            $table->string('ticket_type',20);                # Tipo de comprobante
            $table->string('ticket_serie',7)->nullable();    # serie de comprobante
            $table->string('ticket_number',10);              # Numero de comprobante

            $table->dateTime('date');
            $table->decimal('tax',4,2);                     # Impuesto a pagar
            $table->decimal('total',11,2);                  # Total a pagar

            $table->string('status',20); #estado de la venta (cancelada, anulada, procesada)
    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
