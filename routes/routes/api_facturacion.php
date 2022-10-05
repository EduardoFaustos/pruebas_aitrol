<?php

//rutas para la conexion con el AS400

Route::match(['get', 'post'], '/api/facturacion/electronica', 'ApiFacturacionController@envio')->name('apifacturacion.envio');

Route::get('/importar/ventas/{nombre}', 'ImportarController@ventas');

Route::get('/pagina/resultados/externos/imprimir/{id}', 'PaginaLabsController@resultados_externos')->name('pagina_labs.resultados');

Route::post('/api/sucursales/factura/crear', 'ApiFacturacionSucursales@envio');
