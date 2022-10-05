<?php

//ruta para la conexion de las maquinas del laboratorio1
Route::match(['get', 'post'], 'maquina/hemograma/prueba', 'SinLoginController@maquina1');

//ruta para la conexion de las maquinas del laboratorio2
Route::match(['get', 'post'], 'maquina/bioquimica/prueba', 'SinLoginController@maquina2');

Route::match(['get', 'post'], 'maquina/bha200/prueba', 'SinLoginController@bha200');
//ultima maquina
Route::match(['get', 'post'], 'maquina/luminicencia/prueba', 'SinLoginController@maquina3');

Route::match(['get', 'post'], 'examenes/laboratorio/dato', 'PaginaLabsController@examenes');

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

//RUTA PARA DESCARGA DE LA COTIZACION
Route::get('facturacion/descarga/cotizacion/externo/{id}', 'PaginaLabsController@cotizacion_externo')->name('servicios.cotizacion_externo');

Route::get('app/laboratorio/externo/carrito/pagar/{id_orden}', 'SinLoginController@app_carrito_pago')->name('sinlogin.app_carrito_pago');
