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
//BALANCE GENERALs
Route::match(['get', 'post'], 'contable/kardex', 'contable\KardexController@index')->name('kardex.index');
Route::match(['get', 'post'], 'contable/kardex/show', 'contable\KardexController@show')->name('kardex.show');
Route::match(['get', 'post'], 'contable/kardex/exportar', 'contable\KardexController@exportar')->name('kardex.exportar');

Route::match(['get', 'post'], 'contable/kardex/prueba', 'contable\KardexController@kardex')->name('kardex.prueba');

#KARDEX CONTABLE COMPRAS
Route::match(['get', 'post'], 'contable/compras/kardex/', 'contable\KardexContableController@index')->name('contable.compras.kardex.index');
Route::match(['get', 'post'], 'contable/compras/kardex/show', 'contable\KardexContableController@show')->name('contable.compras.kardex.show');

