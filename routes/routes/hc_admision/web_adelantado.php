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

//consulta 
Route::resource('adelantado', 'AdelantadoController');
Route::get('adelantado/detalle/{id}', 'AdelantadoController@detalle')->name('adelantado.detalle');
Route::get('adelantado/detalle_ag/{id}/{unix}', 'AdelantadoController@detalle_ag')->name('AdelantadoController.detalle_ag');
Route::get('adelantado/detalle/consulta_documentos/{hcid}', 'AdelantadoController@consulta_documentos')->name('adelantado.consulta_documentos');
Route::match(['get', 'post'], 'adelantado/search', 'AdelantadoController@search')->name('adelantado.search');
Route::match(['get', 'post'], 'adelantado/index/reporte', 'AdelantadoController@reporte')->name('adelantado.reporte');
Route::get('adelantado/search', 'AdelantadoController@search')->name('adelantado.search');
Route::match(['get', 'post'], 'adelantado/log_agenda/{id}', 'AdelantadoController@log_agenda')->name('adelantado.log_agenda');


//Reporte de Biopsias
