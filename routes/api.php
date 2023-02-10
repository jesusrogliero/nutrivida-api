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


# rutas para usuarios autenticados
Route::group(['middleware' => ['auth:api']], function () {

	Route::resource('users', 'UsersController');

	Route::get('get_session', 'AuthController@getSession');
	Route::get('get_user', 'AuthController@getUser');
	
	Route::resource('employes', 'EmployesController');
	Route::get('get_provinces', 'EmployesController@show_provinces');
	Route::get('get_positions', 'EmployesController@show_positions');
	Route::get('get_cities_of_provinces/{province_id}', 'EmployesController@show_cities_of_provinces');

	Route::resource('militiamen', 'MilitiamenController');

	Route::resource('machineries_consumables', 'MachineriesConsumablesController');
	Route::resource('cleaning_Supplies', 'CleaningSuppliesController');
	Route::get('presentations', 'CleaningSuppliesController@show_presentations');
	Route::resource('cleaning_tools', 'CleaningToolsController');

	Route::get('primaries_products_histories', 'PrimariesProductsHistories@index');
	
	Route::get('transactions', 'TransactionsController@index');

	Route::resource("users", "UsersController");
	
	Route::resource("types_identities", "TypesIdentitiesController");

//---------------------------------------------------------------------------------------------------------------

	// Administracion
	Route::resource('providers', 'ProvidersController');

	// Almacen
	Route::resource('products_finals', 'ProductsFinalsController')->only(['index', 'store', 'show']);

	Route::resource('nonconforming_products', 'NonconformingProductsController')->only(['index', 'store', 'show']);

	Route::resource('primaries_products', 'PrimariesProductsController');
	Route::get('get_all_primaries_products', 'PrimariesProductsController@get_all_primaries_products');

	Route::resource('supplies_minors', 'SuppliesMinorsController');
	Route::resource('supplies_minors_noconform', 'SuppliesMinorsNoconformController')->only(['index', 'store', 'show']);

	Route::resource('purchases_orders', 'PurchasesOrderController');
	Route::get('approve_purchase/{id}', 'PurchasesOrderController@approve');
	Route::post('set_observation/{id}', 'PurchasesOrderController@set_observation');

	Route::get('get_purchases_orders_items/{id}', 'PurchasesOrdersItemsController@index');
	Route::resource('purchases_orders_items', 'PurchasesOrdersItemsController')->except('index');


	// Produccion
	Route::resource('formulas', 'FormulasController');
	Route::get('get_formula_items/{formula_id}', 'FormulasItemsController@get_items');
	Route::resource('formulas_items', 'FormulasItemsController')->except('index');

	Route::resource('lines_productions', 'LinesController');

	Route::resource('productions_orders', 'ProductionsOrdersController');
	Route::get('get_formula_with_production_order/{production_order_id}', 'ProductionsOrdersController@get_formula_with_production_order');

	Route::resource('productions_consumptions', 'ProductionsConsumptionsController')->only(['store', 'show']);
	Route::resource('productions_consumptions_items','ProductionsConsumptionsItemsController')->only(['show', 'update']);
	Route::get('get_consumption_items/{consumption_id}', 'ProductionsConsumptionsItemsController@get_consumption_items');

	Route::resource('consumptions_supplies_minors', 'ConsumptionsSuppliesMinorsController')->only(['index', 'store', 'show']);

	Route::resource('loss_productions', 'LossProductionsController')->except('delete');
	Route::resource('loss_productions_items', 'LossProductionsItemsController')->only(['show', 'update']);
	Route::get('loss_production_items/{loss_production_id}', 'LossProductionsItemsController@get_items' );

	
});
