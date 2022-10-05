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
Route::match(['get', 'post'], 'contable/Banco/conciliacionbancaria', 'contable\ConciliacionBancariaController@index')->name('conciliacionbancaria.index');
Route::match(['get', 'post'], 'contable/Banco/conciliacionbancaria/actualizar', 'contable\ConciliacionBancariaController@actualizar')->name('conciliacionbancaria.actualizar');
Route::match(['get', 'post'], 'contable/Banco/conciliacionbancaria/actualizarmasivo', 'contable\ConciliacionBancariaController@actualizarmasivo')->name('conciliacionbancaria.actualizarmasivo');
Route::get('contable/Banco/conciliacionbancaria/exportar_excel', 'contable\ConciliacionBancariaController@exportar_excel')->name('conciliacionbancaria.exportar_excel');
Route::get('contable/envio_correo/proveedor/{id}', 'contable\DebitoBancarioController@envioCorreo')->name('debitoBancario.envioCorreo');
// Route::get('contable/Banco/transferenciabancaria/create/', 'contable\TransferenciaBancariaController@create')->name('transferenciabancaria.create');
// Route::post('contable/Banco/transferenciabancaria/store', 'contable\TransferenciaBancariaController@store')->name('transferenciabancaria.store');
// Route::match(['get', 'post'],'contable/Banco/transferenciabancaria/buscar', 'contable\TransferenciaBancariaController@buscar')->name('transferenciabancaria.buscar');
// Route::get('contable/Banco/transferenciabancaria/anular/{id}', 'contable\TransferenciaBancariaController@anular')->name('transferenciabancaria.anular');
// Route::get('contable/Banco/transferenciabancaria/show/{id}', 'contable\TransferenciaBancariaController@show')->name('transferenciabancaria.show');
// Route::get('contable/Banco/transferenciabancaria/imprimir/{id}', 'contable\TransferenciaBancariaController@imprimir')->name('transferenciabancaria.imprimir');

// vista saldo en banco 

Route::match(['get', 'post'], 'contable/banco/conciliacionbancaria/saldo_bancos','contable\ConciliacionBancariaController@saldo_bancos')->name('conciliacionbancaria.saldo_bancos');
Route::match(['get', 'post'], 'contable/banco/conciliacion/pendientes','contable\ConciliacionBancariaController@pendientes')->name('conciliacionbancaria.pendientes');
Route::post('contable/banco/conciliacion/guardar_mes','contable\ConciliacionBancariaController@guardar_mes')->name('conciliacionbancaria.guardar_mes');
Route::match(['get', 'post'], 'contable/banco/conciliacion/saldo_ant', 'contable\ConciliacionBancariaController@saldo_ant')->name('conciliacionbancaria.saldo_ant');