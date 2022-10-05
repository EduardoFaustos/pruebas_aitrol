<?php

Route::match(['get', 'post'], 'comercial/proforma/index', 'comercial\ProformaController@index')->name('proforma.index');
Route::match(['get', 'post'], 'comercial/proforma/busar/paciente', 'comercial\ProformaController@buscarPaciente')->name('proforma.buscarPaciente');
Route::match(['get', 'post'], 'comercial/proforma/crear/paciente', 'comercial\ProformaController@crearPaciente')->name('proforma.crearPaciente');
Route::match(['get', 'post'], 'comercial/proforma/store', 'comercial\ProformaController@store')->name('comercial.proforma.store');
Route::match(['get', 'post'], 'comercial/proforma/editar/{id}', 'comercial\ProformaController@editar')->name('comercial.proforma.editar');
Route::match(['get', 'post'], 'comercial/proforma/detalles/{id}', 'comercial\ProformaController@detalles')->name('comercial.proforma.detalles');
Route::match(['get', 'post'], 'comercial/proforma/actualizar/producto', 'comercial\ProformaController@actualizar_producto')->name('comercial.proforma.actualizar_producto');
//Route::post('nuevo_recibo_de_cobro/actualizar/producto', 'contable\NuevoReciboCobroController@actualizar_producto')->name('comercial.proforma.detalles');
Route::post('comercial/proforma/eliminar/producto', 'comercial\ProformaController@eliminar_producto')->name('comercial.proforma.eliminar_producto');
Route::match(['get', 'post'], 'comercial/proforma/update/Cabecera', 'comercial\ProformaController@updateCabecera')->name('comercial.proforma.updateCabecera');
Route::get('comercial/proforma/modal/{id_paciente}', 'comercial\ProformaController@proformaModal')->name('comercial.proforma.proformaModal');
Route::match(['get', 'post'], 'comercial/proforma/actualizar/paciente', 'comercial\ProformaController@updatePaciente')->name('comercial.proforma.updatePaciente');
Route::match(['get', 'post'], 'comercial/proforma/nivel', 'comercial\ProformaController@nivel')->name('comercial.proforma.nivel');
Route::match(['get', 'post'], 'comercial/proforma/actualizar/precio/nivel', 'comercial\ProformaController@actualizarPrecio')->name('comercial.proforma.actualizarNivel');
Route::match(['get', 'post'], 'comercial/proforma/pasarNuevoRecibo', 'comercial\ProformaController@pasarNuevoRecibo')->name('comercial.proforma.pasarNuevoRecibo');
Route::match(['get', 'post'], 'comercial/proforma/proformaLista', 'comercial\ProformaController@proformaLista')->name('comercial.proforma.proformaLista');


Route::match(['get', 'post'], 'comercial/proforma/guardar_producto', 'comercial\ProformaController@guardar_producto')->name('comercial.proforma.guardar_producto');
Route::get('comercial/producto_tarifario/index', 'comercial\ProdTarifarioController@index')->name('prodtarifario.index');
Route::match(['get', 'post'], 'comercial/buscar/productos', 'comercial\ProdTarifarioController@buscarproductos')->name('prodtarifario.productos');
Route::match(['get', 'post'], 'comercial/buscar', 'comercial\ProdTarifarioController@buscar')->name('prodtarifario.buscar');
Route::match(['get', 'post'], 'comercial/index_tarifario/{id_producto}', 'comercial\ProdTarifarioController@index_tarifario')->name('prodtarifario.index_tarifario');
Route::get('comercial/producto_tarifario/crear_tarifario/{id_producto}', 'comercial\ProdTarifarioController@crear_tarifario')->name('prodtarifario.crear_tarifario');
Route::match(['get', 'post'], 'comercial/producto_tarifario/guardar_tarifario', 'comercial\ProdTarifarioController@guardar_tarifario')->name('prodtarifario.guardar_tarifario');
Route::get('comercial/producto_tarifario/eliminar_tarifario/{id}', 'comercial\ProdTarifarioController@eliminar_tarifario')->name('prodtarifario.eliminar_tarifario');
Route::match(['get', 'post'], 'comercial/producto_tarifario/edit_tarifario/{id}', 'comercial\ProdTarifarioController@edit_tarifario')->name('prodtarifario.edit_tarifario');
Route::match(['get', 'post'], 'comercial/producto_tarifario/update_tarifario', 'comercial\ProdTarifarioController@update_tarifario')->name('prodtarifario.update_tarifario');
Route::match(['get', 'post'], 'comercial/producto_tarifario/edit_particular/{id}', 'comercial\ProdTarifarioController@edit_particular')->name('prodtarifario.edit_particular');
Route::match(['get', 'post'], 'comercial/producto_tarifario/update_particular', 'comercial\ProdTarifarioController@update_particular')->name('prodtarifario.update_particular');
Route::match(['get', 'post'], 'comercial/proforma/index_proforma', 'comercial\ProformaController@index_proforma')->name('comercial.proforma.index_proforma');

//CAMBIAR ESTA RUTA DE AQUI NUEVO RECIBO DE COBRO 22226
Route::get('nuevo_recibo_de_cobro/{id_agenda}', 'contable\NuevoReciboCobroController@crear')->name('nuevorecibocobro.crear');
Route::get('nuevo_recibo_de_cobro/editar/{id}', 'contable\NuevoReciboCobroController@editar')->name('nuevorecibocobro.editar');
Route::get('nuevo_recibo_de_cobro/detalles/{id}', 'contable\NuevoReciboCobroController@detalles')->name('nuevorecibocobro.detalles');
Route::post('nuevo_recibo_de_cobro/guardar_producto', 'contable\NuevoReciboCobroController@guardar_producto')->name('nuevorecibocobro.guardar_producto');
Route::post('nuevo_recibo_de_cobro/actualizar/producto', 'contable\NuevoReciboCobroController@actualizar_producto')->name('nuevorecibocobro.actualizar_producto');
Route::post('nuevo_recibo_de_cobro/actualizar/producto/descripcion', 'contable\NuevoReciboCobroController@actualizar_descripcion')->name('nuevorecibocobro.actualizar_descripcion');
Route::post('nuevo_recibo_de_cobro/eliminar/producto', 'contable\NuevoReciboCobroController@eliminar_producto')->name('nuevorecibocobro.eliminar_producto');
Route::post('nuevo_rc/cabecera', 'contable\NuevoReciboCobroController@actualizar_cabecera')->name('nuevorecibocobro.actualizar_cabecera');
Route::get('nuevo_rc/formas_pago/{id}', 'contable\NuevoReciboCobroController@formas_pago')->name('nuevorecibocobro.formas_pago');
Route::post('nuevo_rc/guardar_formapago', 'contable\NuevoReciboCobroController@guardar_formapago')->name('nuevorecibocobro.guardar_formapago');
Route::post('nuevo_rc/eliminar/forma_pago', 'contable\NuevoReciboCobroController@eliminar_pago')->name('nuevorecibocobro.eliminar_pago');

//Excel
Route::match(['get', 'post'], 'comercial/proforma/excel', 'comercial\ProformaController@excel_proforma')->name('comercial.excel_proforma');
//EXCEL
Route::get('comercial/producto_tarifario/excel', 'comercial\ProdTarifarioController@excel')->name('prodtarifario.excel');
Route::match(['get', 'post'],'proforma.pdf/{id_orden}', 'comercial\ProformaController@pdf_proforma')->name('comercial.proforma.pdf_proforma');



