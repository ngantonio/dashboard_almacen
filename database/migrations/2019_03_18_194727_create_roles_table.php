<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('rolename')->unique();
            $table->string('description')->nullable();
            $table->boolean('active')->default(true);
        });

        # Codigo para insertar un Rol por defecto inmediatamente despues
        # crear la tabla
        DB::table('roles')->insert(array('id'=>'1','rolename'=>'admin','description'=>'Administrador del sistema'));
        DB::table('roles')->insert(array('id'=>'2','rolename'=>'seller','description'=>'Area: Ventas, Vendedor'));
        DB::table('roles')->insert(array('id'=>'3','rolename'=>'warehouser','description'=>'Area: Compras, Almacén'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
