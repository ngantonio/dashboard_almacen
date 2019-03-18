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

Route::get('/', function () {
    return view('content/main');
});

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

// Personas (Clientes)

Route::get('/client', 'ClientController@index');
Route::post('/client/new', 'ClientController@store');
Route::put('/client/update', 'ClientController@update');

// Personas (Proveedores)

Route::get('/provider', 'ProviderController@index');
Route::post('/provider/new', 'ProviderController@store');
Route::put('/provider/update', 'ClientController@update');

// Roles
Route::get('/role', 'RoleController@index');