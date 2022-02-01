<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

# Grupo de rutas para la autenticacion
Route::group( ['prefix' => "auth"], function() {
	Route::post('login', 'AuthController@login');
	Route::post('signup', 'AuthController@signup');
});

Route::get('hola', function() {
	return 'hola';
});


# rutas para usuarios autenticados
Route::group(['middleware' => ['auth:api']], function () {

	
	Route::get('get_session', 'AuthController@getSession');
	Route::resource('roles', 'RolesController');
	Route::resource('employes', 'EmployesController');
	Route::get('get_positions', 'EmployesController@show_positions');
	Route::resource('machineries_consumables', 'MachineriesConsumablesController');
	Route::resource('cleaning_Supplies', 'CleaningSuppliesController');
	Route::get('presentations', 'CleaningSuppliesController@show_presentations');
	Route::resource('cleaning_tools', 'CleaningToolsController');
	Route::resource('primaries_products', 'PrimariesProductsController');
	Route::get('primaries_products_histories', 'PrimariesProductsHistories@index');
	Route::resource('nonconforming_products', 'NonconformingProductsController');

	Route::resource('products_finals', 'ProductsFinalsController');

	Route::resource("users", "UsersController");

	
});
