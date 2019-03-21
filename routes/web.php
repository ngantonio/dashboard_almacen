<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/home', 'HomeController@index')->name('home');


//Usuarios que no se han autenticado, acceso a los invitados
Route::group(['middleware'=>['guest']],function(){
    // Enviar la peticion de inicio de sesion del usuario al controllador
    Route::post('/login','Auth\LoginController@login')->name('login');
    Route::get('/','Auth\LoginController@showLoginForm');
    Route::get('/login','Auth\LoginController@showLoginForm');
    
});



// Autenticacion para usuarios autenticados
Route::group(['middleware'=>['auth']],function(){

    //ruta principal
    Route::get('/main', function () {
        return view('content/main');
    })->name('main');

    Route::post('/logout','Auth\LoginController@logout')->name('logout');
    
    // Conjunto de rutas para el warehouser
    Route::group(['middleware'=>['Warehouser']],function(){
         
        // Categorias
        Route::get('/category', 'CategoryController@index');
        Route::post('/category/new', 'CategoryController@store');
        Route::put('/category/update', 'CategoryController@update');
        Route::put('/category/disable', 'CategoryController@desactivar');
        Route::put('/category/enable', 'CategoryController@activar');
        Route::get('/category/active', 'CategoryController@activeCategories');
        
        //Articulos
        Route::get('/article', 'ArticleController@index');
        Route::post('/article/new', 'ArticleController@store');
        Route::put('/article/update', 'ArticleController@update');
        Route::put('/article/disable', 'ArticleController@changeStatus');
        Route::put('/article/enable', 'ArticleController@changeStatus');
    
        // Personas (Proveedores)
        Route::get('/provider', 'ProviderController@index');
        Route::post('/provider/new', 'ProviderController@store');
        Route::put('/provider/update', 'ClientController@update');
    });
   
    Route::group(['middleware'=>['Seller']],function(){

        // Personas (Clientes)
        Route::get('/client', 'ClientController@index');
        Route::post('/client/new', 'ClientController@store');
        Route::put('/client/update', 'ClientController@update');

        //Ventas y consultas por ventas
    
    });

    Route::group(['middleware'=>['Admin']],function(){
        
        // Categorias
        Route::get('/category', 'CategoryController@index');
        Route::post('/category/new', 'CategoryController@store');
        Route::put('/category/update', 'CategoryController@update');
        Route::put('/category/disable', 'CategoryController@desactivar');
        Route::put('/category/enable', 'CategoryController@activar');
        Route::get('/category/active', 'CategoryController@activeCategories');
        
        //Articulos
        Route::get('/article', 'ArticleController@index');
        Route::post('/article/new', 'ArticleController@store');
        Route::put('/article/update', 'ArticleController@update');
        Route::put('/article/disable', 'ArticleController@changeStatus');
        Route::put('/article/enable', 'ArticleController@changeStatus');
    
        // Personas (Proveedores)
        Route::get('/provider', 'ProviderController@index');
        Route::post('/provider/new', 'ProviderController@store');
        Route::put('/provider/update', 'ClientController@update');

        
        // Personas (Clientes)
        Route::get('/client', 'ClientController@index');
        Route::post('/client/new', 'ClientController@store');
        Route::put('/client/update', 'ClientController@update');
    
        //Articulos
        Route::get('/article', 'ArticleController@index');
        Route::post('/article/new', 'ArticleController@store');
        Route::put('/article/update', 'ArticleController@update');
        Route::put('/article/disable', 'ArticleController@changeStatus');
        Route::put('/article/enable', 'ArticleController@changeStatus');
    
        // Personas (Proveedores)
        Route::get('/provider', 'ProviderController@index');
        Route::post('/provider/new', 'ProviderController@store');
        Route::put('/provider/update', 'ClientController@update');
    
        // Roles
        Route::get('/role', 'RoleController@index');
        Route::get('/role/select', 'RoleController@selectRole');

        //Usuarios
        Route::get('/users', 'UserController@index');
        Route::post('/users/new', 'UserController@store');
        Route::put('/users/update', 'UserController@update');
        Route::put('/users/disable', 'UserController@changeStatus');
        Route::put('/users/enable', 'UserController@changeStatus');
    
    });

});

