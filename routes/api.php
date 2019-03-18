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
    Route::get('categories/{category}/getservice', 'CategoryController@getServices');
    Route::get('categories/{category}/get/empleados', 'CategoryController@getEmpleados');
    //GESTION DE SERVICIOS
    Route::apiResource('services', 'ServiceController');
    //GESTION DE USUARIOS
    Route::apiResource('users', 'UserController');
    Route::get('users/authenticated/user/get/{api_token}', 'UserController@getAuthenticatedUser');
    Route::get('users/password/{identificacion}/{newpassword}/change', 'UserController@passwordChange');
    //CAJA
    Route::apiResource('caja', 'CajaController');
    Route::get('caja/dinero/caja', 'CajaController@getDineroCaja');
    Route::get('caja/obtener/caja/abierta', 'CajaController@getOpenCaja');
    Route::get('caja/apertura/primeravez/validar', 'CajaController@validarPrimeraVez');
    Route::post('caja/apertura/primeravez/abrirPrimeraVez', 'CajaController@abrirPrimeraVez');
    Route::post('caja/apertura/abrir', 'CajaController@abrirCaja');
    Route::post('caja/cierre/cerrar', 'CajaController@cerrarCaja');
    Route::post('caja/transacciones/ingreso', 'CajaController@ingreso');
    Route::post('caja/transacciones/egreso', 'CajaController@egreso');
    Route::post('caja/historialcaja/transacciones/inconsistencia', 'CajaController@setInconsistencia');
    //GESTION DE EMPLEADOS
    Route::apiResource('empleados', 'EmpleadoController');
    Route::apiResource('movimientosempleado', 'MovimientosempleadoController');
    //ESTADISTICAS
    Route::get('estadistica/consulta/numerica/caja', 'EstadisticasController@Datosnumericoscaja');
    //VENTAS
    Route::apiResource('venta', 'VentaController');
});
