<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Persons es una tabla padre que da origen a las tablas users y clients
        Schema::create('persons', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->string("document_type",20)->nullable();
            $table->string('document_number')->unique();  //dni, CI, passport
            $table->string('address')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->unique();
            
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
        Schema::dropIfExists('persons');
    }
}
