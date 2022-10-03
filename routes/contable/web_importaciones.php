<?php

Route::get('contable/importacion/gastos/index', 'contable\GastosImportacionController@index')->name('gastosimportacion.index');
Route::get('contable/importacion/gastos/create', 'contable\GastosImportacionController@create')->name('gastosimportacion.create');
Route::post('contable/importacion/gastos/store', 'contable\GastosImportacionController@store')->name('gastosimportacion.store');
Route::get('contable/importacion/gastos/edit/{id}', 'contable\GastosImportacionController@edit')->name('gastosimportacion.edit');
Route::post('contable/importacion/gastos/update/{id}', 'contable\GastosImportacionController@update')->name('gastosimportacion.update');
Route::get('contable/importacion/gastos/eliminar/{id}', 'contable\GastosImportacionController@eliminar')->name('gastosimportacion.eliminar');

/*iMPORTACIONES */
Route::match(['get','post'],'contable/importaciones/index', 'contable\ImportacionesController@index')->name('importaciones.index');
Route::match(['get','post'],'contable/importaciones/create', 'contable\ImportacionesController@create')->name('importaciones.create');
Route::match(['get','post'],'contable/importaciones/store', 'contable\ImportacionesController@store')->name('importaciones.store');
Route::match(['get','post'],'contable/importaciones/edit/{id}', 'contable\ImportacionesController@edit')->name('importaciones.edit');
Route::match(['get','post'],'contable/importaciones/update/{id}', 'contable\ImportacionesController@update')->name('importaciones.update');


Route::match(['get','post'],'contable/importaciones/view/{id}', 'contable\ImportacionesController@viewImportaciones')->name('importaciones.view');


Route::match(['get','post'],'contable/importaciones/create_recibo/{id}', 'contable\ImportacionesController@create_recibo')->name('importaciones.create_recibo');
Route::match(['get','post'],'contable/importaciones/store_recibo', 'contable\ImportacionesController@store_recibo')->name('importaciones.store_recibo');

Route::match(['get','post'],'contable/importaciones/create_orden/{id}', 'contable\ImportacionesController@create_orden')->name('importaciones.create_orden');
Route::match(['get','post'],'contable/importaciones/store_orden', 'contable\ImportacionesController@store_orden')->name('importaciones.store_orden');

Route::match(['get','post'],'contable/importaciones/pdf_importaciones/{id}', 'contable\ImportacionesController@pdf_importaciones')->name('importaciones.pdf_importaciones');
Route::get('importaciones/documento/{id}', 'ImportacionesController@index')->name('index_importaciones');
Route::match(['get','post'],'importaciones/save', 'ImportacionesController@save')->name('save_importaciones');

// GASTOS FACTURA

Route::get('contable/importacion/ingreso_factura/{id}','contable\GastosImportacionController@ingreso_factura')->name('gastosimportacion.ingreso_factura');

Route::match(['get','post'],'contable/importacion/edit_factura/{id_factura}','contable\GastosImportacionController@edit_factura')->name('gastosimportacion.edit_factura');
Route::post('contable/importacion/store_factura','contable\GastosImportacionController@store_factura')->name('gastosimportacion.store_factura');
//excel
Route::get('importaciones/excel/{id}', 'ImportacionesController@excel')->name('index_excel');

//LIQUIDACION 

Route::get('contable/importaciones/liquidacion/{id}','contable\ImportacionesController@liquidacion')->name('importaciones.liquidacion');
Route::match(['get','post'],'contable/importaciones/store_liquidacion', 'contable\ImportacionesController@store_liquidacion')->name('importaciones.store_liquidacion');
Route::match(['get','post'],'contable/importaciones/store_pais', 'contable\ImportacionesController@store_pais')->name('importaciones.store_pais');


Route::get('contable/importaciones/crear_agrupada','contable\ImportacionesController@crear_agrupada')->name('importaciones.crear_agrupada');
Route::get('contable/importaciones/store_agrupada','contable\ImportacionesController@store_agrupada')->name('importaciones.store_agrupada');

Route::match(['get','post'],'contable/importaciones/store/compras/kardex', 'contable\ImportacionesController@store_kardex')->name('importaciones.store_compras_importacion');


Route::match(['get','post'],'contable/ingreso/arreglar/secuencia', 'contable\ImportacionesController@arreglarSecuencia')->name('importaciones.arreglar_secuencia');

// subir archivos
Route::get('contable/importaciones/subir_archivo/{id}', 'contable\GastosImportacionController@subir_archivo')->name('gastosimportacion.subir_archivo');
Route::post('contable/importaciones/guardar_archivo', 'contable\GastosImportacionController@guardar_archivo')->name('gastosimportacion.guardar_archivo');
Route::get('contable/importaciones/archivo_descarga/{name}', 'contable\GastosImportacionController@archivo_descarga')->name('gastosimportacion.archivo_descarga');
Route::get('contable/importaciones/eliminar_archivo/{id}', 'contable\GastosImportacionController@eliminar_archivo')->name('gastosimportacion.eliminar_archivo');

Route::get('contable/importaciones/ver/anteprima', 'contable\GastosImportacionController@anteprima')->name('gastosimportacion.anteprima');

Route::match(['get', 'post'], 'contable/importaciones/buscar/proveedor', 'contable\ImportacionesController@buscarProveedor' )->name('importaciones.proveedores');
Route::match(['get', 'post'], 'contable/importaciones/buscar/productos', 'contable\ImportacionesController@buscarProductos')->name('importaciones.productos');
Route::match(['get', 'post'], 'contable/importaciones/buscar/direccion', 'contable\ImportacionesController@buscarDireccion')->name('importaciones.direccion');
Route::match(['get', 'post'], 'contable/importaciones/buscar/pais', 'contable\ImportacionesController@buscarPais')->name('importaciones.pais');
//resumen faustito 
//Route::get('importaciones/documento', 'ImportacionesController@index')->name('index_importaciones');

Route::match(['get', 'post'], 'contable/importaciones/busca_precio_bodega', 'contable\ImportacionesController@buscarPrecioBodega')->name('importaciones.buscarPrecioBodega');

//Pre orden
Route::get('contable/importaciones/pre_orden','contable\ImportacionesController@pre_orden')->name('contable.importaciones.pre_orden');
Route::match(['get', 'post'], 'contable/importaciones/pre_orden/store','contable\ImportacionesController@preOrdenStore')->name('importaciones.preOrdenStore');
Route::match(['get', 'post'], 'contable/importaciones/pre_orden/mostrar','contable\ImportacionesController@preOrdenMostrar')->name('importaciones.preOrdenMostrar');


Route::match(['get', 'post'], 'contable/precio/producto/aprobado','contable\PrecioProductoAprobadoController@index')->name('importaciones.PrecioProductoAprobado.index'); 
Route::match(['get', 'post'], 'contable/precio/producto/aprobado/create','contable\PrecioProductoAprobadoController@create')->name('importaciones.PrecioProductoAprobado.create');
Route::match(['get', 'post'], 'contable/precio/producto/aprobado/store','contable\PrecioProductoAprobadoController@store')->name('importaciones.PrecioProductoAprobado.store');
Route::match(['get', 'post'], 'contable/precio/producto/aprobado/buscarTabla','contable\PrecioProductoAprobadoController@buscarTabla')->name('importaciones.PrecioProductoAprobado.buscarTabla');

Route::post('contable/precio/producto/aprobado/delete','contable\PrecioProductoAprobadoController@delete')->name('importaciones.PrecioProductoAprobado.delete');


Route::match(['get', 'post'], 'contable/precio/producto/aprobado/mostrar','contable\ImportacionesController@mostrar')->name('importaciones.Imprtaciones.mostar');







