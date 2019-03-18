<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('providers', function (Blueprint $table) {
            
            // El Id de un proveedor es el mismo Id de una persona
            // Al insertar un registro en esta table, se debe insertar tambien
            // en la tabla Persons
            $table->integer('id')->unsigned();
            $table->foreign('id')->references('id')->on('persons')->onDelete('cascade');
            
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('providers');
    }
}
