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
    Route::get('/dashboard','DashboardController');
    // Ruta para obtener las notificaiones 
    Route::post('notification/get','NotificationController@get');

    Route::group(['middleware'=>['Admin']],function(){
        
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
            Route::get('/article/searchArticle', 'ArticleController@searchArticle');
            //Trae un Articulo disponible para la venta
            Route::get('/article/searchArticleStock', 'ArticleController@searchArticleStock');
            // Todos los articulos disponibles para la venta getArticlesToSale
            Route::get('/article/getArticlesToSale', 'ArticleController@getArticlesToSale'); 
            //Ruta par generar el pdf de articulos
            Route::get('/article/pdf', 'ArticleController@listPDF')->name('articulos.pdf');
        
            // Personas (Proveedores)
            Route::get('/provider', 'ProviderController@index');
            Route::post('/provider/new', 'ProviderController@store');
            Route::put('/provider/update', 'ClientController@update');
            Route::get('/provider/getProviders', 'ProviderController@getProviders');
        });

        Route::group(['middleware'=>['Seller']],function(){
            // Personas (Clientes)
            Route::get('/client', 'ClientController@index');
            Route::post('/client/new', 'ClientController@store');
            Route::put('/client/update', 'ClientController@update');
            Route::get('/client/getClient', 'ClientController@getClient'); #Obtiene un cliente dado su numero de documento
        
            // Ventas
            Route::get('/sale', 'SalesController@index');
            Route::get('/sale/getHeader', 'SalesController@getHeader');
            Route::get('/sale/getDetailsSale', 'SalesController@getDetailsSale');
            Route::post('/sale/store', 'SalesController@store');
            Route::put('/sale/disable', 'SalesController@changeStatus');
            Route::get('/sale/pdf/{id}', 'SalesController@generatePDF');

        });
    
        // Roles
        Route::get('/role', 'RoleController@index');
        Route::get('/role/select', 'RoleController@selectRole');

        //Usuarios
        Route::get('/users', 'UserController@index');
        Route::post('/users/new', 'UserController@store');
        Route::put('/users/update', 'UserController@update');
        Route::put('/users/disable', 'UserController@changeStatus');
        Route::put('/users/enable', 'UserController@changeStatus');
    
        # Ingresos (para almacenero tambien)
        Route::get('/incomes', 'IncomeController@index');
        Route::post('/incomes/store', 'IncomeController@store');
        Route::put('/incomes/disable', 'IncomeController@changeStatus');
        Route::get('/incomes/getHeader', 'IncomeController@getHeader');
        Route::get('/incomes/getDetailsIncome', 'IncomeController@getDetailsIncome');

    });
});

