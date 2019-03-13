<?php

use Illuminate\Http\Request;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
//AUTENTICACION
Route::post('users/access/authenticate', 'UserController@authenticate');
Route::get('users/access/logout', 'UserController@logout');
Route::get('users/apitoken/get/{dentificacion}', 'UserController@getApitoken');

Route::middleware('auth:api')->group(function () {
    //GESTION DE CATEGORIAS
    Route::apiResource('categories', 'CategoryController');
    //GESTION DE SERVICIOS
    Route::apiResource('services', 'ServiceController');
    //GESTION DE USUARIOS
    Route::apiResource('users', 'UserController');
    Route::get('users/authenticated/user/get/{api_token}', 'UserController@getAuthenticatedUser');
    Route::get('users/password/{identificacion}/{newpassword}/change', 'UserController@passwordChange');
    //CAJA
    Route::apiResource('caja', 'CajaController');
    //GESTION DE EMPLEADOS
    Route::apiResource('empleados', 'EmpleadoController');
});
