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

Route::resource('empleados', 'rrhh\EmpleadosController');
Route::get('empleados/documentos/{id}', 'rrhh\EmpleadosController@documentos')->name('empleados.documentos');

Route::get('contable/rol/pago/envio/correo/{id}', 'contable\RolPagoController@rol_pago_envio')->name('rolpago.rol_pago_envio');

//rutas de preparcion de emily
Route::match(['get', 'post'], 'preparaciones/index', 'preparaciones\preparacionesController@index')->name('preparaciones.index');

Route::match(['get', 'post'], 'preparaciones/mostrar/pdf', 'preparaciones\preparacionesController@mostrar_pdf')->name('preparaciones.mostrar_pdf');
Route::get('preparaciones/crear', 'preparaciones\preparacionesController@crear')->name('preparaciones.crear');
Route::post('preparaciones/guardar', 'preparaciones\preparacionesController@guardar_preparaciones')->name('preparaciones.guardar');
