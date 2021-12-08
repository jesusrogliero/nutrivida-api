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

	Route::resource('roles', 'RolesController');
	Route::resource('employes', 'EmployesController');
	Route::resource('MachineriesConsumables', 'MachineriesConsumablesController');

	Route::resource("users", "UsersController");

	
});
