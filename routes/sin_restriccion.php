<?php

//ruta para la conexion de las maquinas del laboratorio1
Route::match(['get', 'post'], 'maquina/hemograma/prueba', 'SinLoginController@maquina1');

//ruta para la conexion de las maquinas del laboratorio2
Route::match(['get', 'post'], 'maquina/bioquimica/prueba', 'SinLoginController@maquina2');

Route::match(['get', 'post'], 'maquina/bha200/prueba', 'SinLoginController@bha200');
//ultima maquina
Route::match(['get', 'post'], 'maquina/luminicencia/prueba', 'SinLoginController@maquina3');

Route::match(['get', 'post'], 'examenes/laboratorio/dato', 'PaginaLabsController@examenes');

// maquina nueva 

Route::match(['get', 'post'], 'maquina/lum/prueba_demonio', 'SinLoginController@maquina4');

//ruta
Route::post('carrito/paciente/guardar/orden', 'SinLoginController@carrito_guardar_orden')->name('carrito.guardar_orden');
//POST PROCESO PAGO EN LINEA
Route::match(['get', 'post'], 'laboratorio/externo/web_postproceso_pago', 'SinLoginController@web_postproceso_pago');
Route::get('laboratorio/externo/web_retorno_pago', 'SinLoginController@web_retorno_pago');
Route::get('laboratorio/externo/web_cancelacion_pago', 'SinLoginController@web_cancelacion_pago');
Route::get('laboratorio/externo/carrito/pagar/{id_orden}', 'SinLoginController@carrito_pago')->name('sinlogin.carrito_pago');
Route::post('paquete/paciente/guardar/orden', 'SinLoginController@paquete_guardar_orden')->name('sinlogin.paquete_guardar_orden');
Route::match(['get', 'post'], 'laboratorio/externo/web/buscar_clientes/{id}', 'SinLoginController@buscar_clientes')->name('sinlogin.buscar_clientes');

//RUTA PARA DESCARGA DEL COMPROBANTE PDF
Route::get('facturacion/descarga/cliente/externo/{comprobante}', 'PaginaLabsController@comprobante_externo')->name('servicios.comprobante_externo');

Route::post('facturacion/sucursales/aitrolbilling/ride/{recurso}', 'PaginaLabsController@ride_externo');

//RUTA PARA DESCARGA DE LA COTIZACION
Route::match(['get', 'post'], 'facturacion/descarga/cotizacion/externo/{id}', 'PaginaLabsController@cotizacion_externo')->name('servicios.cotizacion_externo');

Route::get('app/laboratorio/externo/carrito/pagar/{id_orden}', 'SinLoginController@app_carrito_pago')->name('sinlogin.app_carrito_pago');


//RUTA PARA FACTURACION ELECTRONICA SUCURSALES
Route::post('facturacion/sucursales/aitrol', 'PaginaLabsController@facturacion_externo')->name('Pagina.fatura');

Route::match(['get', 'post'],'bd/consulta/rc', 'SinLoginController@consulta_rc')->name('sinlogin.consulta_rc');
Route::match(['get', 'post'],'bd/consulta/total_anio', 'BigDataController@total_anio')->name('total_anio');
Route::post('big_data/consulta', 'BigDataController@consulta');
Route::match(['get', 'post'],'bd/consulta_labs/labs_meses', 'BigDataController@labs_meses')->name('labs_meses');

Route::post('app/buscar/usuario', 'BigDataController@buscar_usuario')->name('laboratorio_login');
Route::post('app/buscar/examenes', 'BigDataController@buscar_examenes')->name('bd.buscar_examenes');
Route::post('app/listado/examenes', 'BigDataController@listado_examenes')->name('bd.listado_examenes');
Route::post('app/crear/orden_labs', 'BigDataController@crear_orden_labs')->name('bd.crear_orden_labs');

Route::post('labs_gestion_servicios', 'PaginaLabsController@labs_gestion_servicios')->name('Pagina.labs_gestion_servicios');
Route::post('app/buscar/referido', 'BigDataController@buscar_referido')->name('buscar_referido');