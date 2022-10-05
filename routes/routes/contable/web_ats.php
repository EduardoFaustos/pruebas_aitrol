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
Route::match(['get', 'post'], 'contable/contabilidad/ats', 'contable\AtsController@index')->name('ats.index');

Route::match(['get', 'post'], 'contable/contabilidad/ats/show', 'contable\AtsController@show')->name('ats.show');

Route::match(['get', 'post'], 'contable/contabilidad/ats/store', 'contable\AtsController@store')->name('ats.store');
