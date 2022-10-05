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
Route::post('contable/activofijo/buscar_proveedor', 'activosfijos\DocumentoFacturaController@buscar_proveedor')->name('documentofactura.buscar_proveedor');
Route::resource('afDocumentoFactura', 'activosfijos\DocumentoFacturaController');
Route::match(['get', 'post'],'activojifos/documentofactura/buscar', 'activosfijos\DocumentoFacturaController@search')->name('activosfijos.documentofactura.search');
Route::match(['get', 'post'],'activojifos/documentofactura/anular/{id}', 'activosfijos\DocumentoFacturaController@anular')->name('activosfijos.documentofactura.anular');

Route::match(['get', 'post'],'activosfijos/documentofactura/guardar_color/{id}','activosfijos\DocumentoFacturaController@guardar_color')->name('documentofactura.guardar_color');
Route::match(['get', 'post'],'activosfijos/documentofactura/guardar_serie/{id}','activosfijos\DocumentoFacturaController@guardar_serie')->name('documentofactura.guardar_serie');
Route::match(['get', 'post'],'activosfijos/documentofactura/guardar_marca/{id}','activosfijos\DocumentoFacturaController@guardar_marca')->name('documentofactura.guardar_marca');
Route::match(['get', 'post'],'activosfijos/documentofactura/guardar_responsable/{id}','activosfijos\DocumentoFacturaController@guardar_responsable')->name('documentofactura.guardar_responsable');

//nueva factura

Route::get('af/nueva/factura','activosfijos\DocumentoFacturaController@new_factura')->name('documentofactura.new_factura');
Route::get('af/nueva/edit_new_factura/{id}','activosfijos\DocumentoFacturaController@edit_new_factura')->name('documentofactura.edit_new_factura');
Route::get('af/new/modal_activo','activosfijos\DocumentoFacturaController@modal_activo')->name('documentofactura.modal_activo');
//
Route::get('af/new/seach/active','activosfijos\DocumentoFacturaController@search_acive')->name('documentofactura.search_acive');

// subir archivos
Route::get('contable/activofijo/subir_archivo/{id}', 'activosfijos\DocumentoFacturaController@subir_archivo')->name('documentofactura.subir_archivo');
Route::post('contable/activofijo/guardar_archivo', 'activosfijos\DocumentoFacturaController@guardar_archivo')->name('documentofactura.guardar_archivo');
Route::get('contable/activofijo/archivo_descarga/{name}', 'activosfijos\DocumentoFacturaController@archivo_descarga')->name('documentofactura.archivo_descarga');
Route::get('contable/activofijo/eliminar_archivo/{id}', 'activosfijos\DocumentoFacturaController@eliminar_archivo')->name('documentofactura.eliminar_archivo');
//ver anteprima
Route::get('contable/activofijo/ver/anteprima', 'activosfijos\DocumentoFacturaController@ver_anteprima')->name('documentofactura.ver_anteprima');

Route::match(['get', 'post'],'contable/activofijo/buscar_categoria','activosfijos\DocumentoFacturaController@buscar_categoria')->name('documentofactura.buscar_categoria');

//masivo observaciones en factura af y compra
Route::match(['get', 'post'],'contable/activofijo/masivo_observacion','activosfijos\DocumentoFacturaController@masivo_observacion')->name('documentofactura.masivo_observacion');

Route::match(['get', 'post'],'contable/activofijo/anular_fc/{id}','activosfijos\DocumentoFacturaController@anular_fc')->name('documentofactura.anular_fc');
