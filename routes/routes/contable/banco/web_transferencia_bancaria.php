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
Route::match(['get', 'post'], 'contable/Banco/transferenciabancaria', 'contable\TransferenciaBancariaController@index')->name('transferenciabancaria.index');

Route::get('contable/Banco/transferenciabancaria/create/', 'contable\TransferenciaBancariaController@create')->name('transferenciabancaria.create');
Route::post('contable/Banco/transferenciabancaria/store', 'contable\TransferenciaBancariaController@store')->name('transferenciabancaria.store');
Route::match(['get', 'post'],'contable/Banco/transferenciabancaria/buscar', 'contable\TransferenciaBancariaController@buscar')->name('transferenciabancaria.buscar');
Route::get('contable/Banco/transferenciabancaria/anular', 'contable\TransferenciaBancariaController@anular')->name('transferenciabancaria.anular');
Route::get('contable/Banco/transferenciabancaria/show/{id}', 'contable\TransferenciaBancariaController@show')->name('transferenciabancaria.show');
Route::get('contable/Banco/transferenciabancaria/imprimir/{id}', 'contable\TransferenciaBancariaController@imprimir')->name('transferenciabancaria.imprimir');

Route::match(['get', 'post'], 'contable/Banco/transferenciabancaria/devuelvefloat', 'contable\TransferenciaBancariaController@devuelvefloat')->name('transferenciabancaria.devuelvefloat');

Route::get('contable/Banco/transferenciabancaria/exportar_excel', 'contable\TransferenciaBancariaController@exportar_excel')->name('transferenciabancaria.exportar_excel');

Route::get('contable/Banco/transferenciabancaria/buscar/cuenta_destino', 'contable\TransferenciaBancariaController@buscar_cuenta_destino')->name('transferenciabancaria.buscar_cuenta_destino');