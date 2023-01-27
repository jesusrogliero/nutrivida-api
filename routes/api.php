<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Middleware\RolesMiddleware;

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


# rutas para usuarios autenticados
Route::group(['middleware' => ['auth:api']], function () {

	Route::resource('users', 'UsersController');

	Route::get('get_session', 'AuthController@getSession');
	Route::get('get_user', 'AuthController@getUser');
	Route::resource('roles', 'RolesController');
	
	Route::resource('employes', 'EmployesController');
	Route::get('get_provinces', 'EmployesController@show_provinces');
	Route::get('get_cities_of_provinces/{province_id}', 'EmployesController@show_cities_of_provinces');
	Route::resource('militiamen', 'MilitiamenController');

	Route::get('get_positions', 'EmployesController@show_positions');
	Route::resource('machineries_consumables', 'MachineriesConsumablesController');
	Route::resource('cleaning_Supplies', 'CleaningSuppliesController');
	Route::get('presentations', 'CleaningSuppliesController@show_presentations');
	Route::resource('cleaning_tools', 'CleaningToolsController');
	Route::resource('primaries_products', 'PrimariesProductsController');
	Route::get('get_all_primaries_products', 'PrimariesProductsController@get_all_primaries_products');
	Route::get('primaries_products_histories', 'PrimariesProductsHistories@index');
	Route::resource('nonconforming_products', 'NonconformingProductsController');

	Route::resource('providers', 'ProvidersController');

	Route::resource('products_finals', 'ProductsFinalsController');
	Route::resource('supplies_minors', 'SuppliesMinorsController');
	Route::resource('supplies_minors_noconform', 'SuppliesMinorsNoconformController');
	Route::get('transactions', 'TransactionsController@index');


	Route::group(['middleware' => [RolesMiddleware::class]], function() {
		# Orden de Ingreso
		Route::resource('purchases_orders', 'PurchasesOrderController');
		Route::get('get_purchases_orders_items/{id}', 'PurchasesOrdersItemsController@index');
		Route::post('purchases_orders_items', 'PurchasesOrdersItemsController@store');
		Route::get('purchases_orders_items/{id}', 'PurchasesOrdersItemsController@show');
		Route::put('purchases_orders_items/{id}', 'PurchasesOrdersItemsController@update');
		Route::delete('purchases_orders_items/{id}', 'PurchasesOrdersItemsController@destroy');
		Route::get('approve_purchase/{id}', 'PurchasesOrderController@approve');
		Route::post('set_observation/{id}', 'PurchasesOrderController@set_observation');
	});

	Route::resource('formulas', 'FormulasController');
	Route::get('get_formula_items/{formula_id}', 'FormulasItemsController@get_items');
	Route::resource('formulas_items', 'FormulasItemsController');
	Route::resource('lines_productions', 'LinesController');

	Route::resource('productions_orders', 'ProductionsOrdersController');

	Route::resource('productions_consumptions', 'ProductionsConsumptionsController');
	Route::get('get_consumption_items/{consumption_id}', 'ProductionsConsumptionsItemsController@get_consumption_items');
	Route::get('show_consumption_item/{consumption_id}', 'ProductionsConsumptionsItemsController@show');
	Route::put('update_consumption_item/{consumption_id}', 'ProductionsConsumptionsItemsController@update');

	Route::resource("users", "UsersController");

	Route::resource("types_identities", "TypesIdentitiesController");

	
});
