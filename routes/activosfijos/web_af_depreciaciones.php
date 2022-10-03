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
Route::resource('afDepreciacion', 'activosfijos\DepreciacionController');
Route::match(['get', 'post'],'activojifos/depreciacion/buscar', 'activosfijos\DepreciacionController@buscar')->name('activosfijos.depreciacion.buscar');
Route::match(['get','post'],'activofijo/depreciacion/pdf_depreciacionesmen', 'activosfijos\DepreciacionController@pdf_depreciacionesmen')->name('activofjo.depreciacion.pdf_depreciacionesmen');