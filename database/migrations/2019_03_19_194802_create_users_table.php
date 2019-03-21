<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Modificar la tabla users, Users es una especializacion de la tabla perso
        //Debemos cambiar el nombre (fecha) de esta tabla para que se
        //se ejecute en el orden correcto, despues de creadas las tablas, person y roles
        Schema::create('users', function (Blueprint $table) {
           
            # El id de un user es el id de un registro de la tabla person
            $table->integer('id')->unsigned();
            $table->foreign('id')->references('id')->on('persons')->onDelete('cascade');

            $table->string('username')->unique();
            $table->string('password');
            $table->boolean('active')->default(true);

            $table->integer('id_role')->unsigned();
            $table->foreign('id_role')->references('id')->on('roles');
            
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
