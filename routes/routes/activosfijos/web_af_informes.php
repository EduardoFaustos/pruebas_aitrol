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

//Documento Factura
Route::resource('afInformes', 'activosfijos\InformesController');
Route::match(['get', 'post'],'activojifos/informe/buscar', 'activosfijos\InformesController@buscar')->name('activosfijos.informe.buscar');
// Route::match(['get', 'post'],'activojifos/documentofactura/anular/{id}', 'activosfijos\DocumentoFacturaController@anular')->name('activosfijos.documentofactura.anular');



Route::match(['get', 'post'],'contable/kardex', 'contable\KardexController@kardex')->name('kardex');