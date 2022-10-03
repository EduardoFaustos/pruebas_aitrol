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

//bodegas

Route::resource('bodega', 'Insumos\BodegaController');
Route::match(['get', 'post'], 'bodega/search', 'Insumos\BodegaController@search')->name('bodega.search');

//proveedores
Route::resource('proveedor', 'Insumos\ProveedorController');
Route::match(['get', 'post'], 'proveedores/search', 'Insumos\ProveedorController@search')->name('proveedor.search');
Route::get('proveedor/logo/{name}', 'Insumos\ProveedorController@load');
Route::post('proveedor/subir_logo', 'Insumos\ProveedorController@subir_logo')->name('proveedor.subir_logo');
Route::post('proveedor/selected', 'Insumos\ProveedorController@query_cuentas')->name('proveedor.query_cuentas');
//tipo de proveedores
Route::resource('tipo_proveedor', 'Insumos\TipoProveedorController');
Route::match(['get', 'post'], 'tipo_proveedores/search', 'Insumos\TipoProveedorController@search')->name('tipoproveedor.search');

//marca
Route::resource('marca', 'Insumos\MarcaController');
Route::match(['get', 'post'], 'marca/search', 'Insumos\MarcaController@search')->name('marca.search');

//marca
Route::resource('tipo', 'Insumos\TipoController');
Route::match(['get', 'post'], 'tipo/search', 'Insumos\TipoController@search')->name('tipo.search');

//productos
Route::resource('producto', 'Insumos\ProductoController');
//Route::match(['get', 'post'],'producto/dar_baja', 'Insumos\ProductoController@bajar_producto')->name('producto.dar_baja');
Route::match(['get', 'post'], 'producto_search/search', 'Insumos\ProductoController@search')->name('producto.search');
Route::match(['get', 'post'], 'producto/codigo_datos/', 'Insumos\ProductoController@codigo')->name('producto.codigo');
Route::match(['get', 'post'], 'producto_activo/listar/activos', 'Insumos\ProductoController@listar')->name('producto.listar');
Route::match(['get', 'post'], 'producto/buscadorcodigo', 'Insumos\ProductoController@codigo2')->name('producto.codigo2');
Route::match(['get', 'post'], 'producto/buscar/{nombre}', 'Insumos\ProductoController@nombre');
Route::match(['get', 'post'], 'producto/buscar/nombre', 'Insumos\ProductoController@nombre')->name('producto.nombre');
Route::match(['get', 'post'], 'producto/buscador/nombre', 'Insumos\ProductoController@nombre2')->name('producto.nombre2');
Route::get('producto/seguimiento/{i}', 'Insumos\ProductoController@seguimiento')->name('producto.seguimiento');
Route::get('producto/revisar/pedido/{id_pedido}', 'Insumos\ProductoController@revisar_pedido')->name('producto.revisar_pedido');
Route::resource('despacho', 'Insumos\DespachoController');

//productos
Route::resource('ingreso_producto', 'Insumos\IngresoController');
Route::match(['get', 'post'], 'ingreso/search', 'Insumos\IngresoController@search')->name('ingreso_producto.search');
Route::post('producto/ingresodato', 'Insumos\IngresoController@formulario')->name('ingreso.formulario');
Route::post('producto/ingresar/informacion/', 'Insumos\IngresoController@guardar')->name('ingreso.guardar');

//codigo de barras
Route::match(['get', 'post'], 'codigo/barras/', 'Insumos\ProductoController@codigo')->name('codigo.barra');
Route::match(['get', 'post'], 'codigo/barras/imprimir/{id}', 'Insumos\ProductoController@imprimirbarras')->name('barra.generar');
Route::match(['get', 'post'], 'codigo/barras/pedido/{id}', 'Insumos\ProductoController@pedido')->name('pedido.seguimiento');

//productos en transito
Route::resource('transito', 'Insumos\TransitoController');
Route::match(['get', 'post'], 'producto/transito/nombre_encargado/', 'Insumos\TransitoController@nombre')->name('transito.nombre');
Route::match(['get', 'post'], 'producto/transito/nombre_encargado2/', 'Insumos\TransitoController@nombre2')->name('transito.nombre2');
Route::match(['get', 'post'], 'producto/transito/searching/', 'Insumos\TransitoController@search')->name('transito.searching');
Route::get('transito/insumo/pedido/{id}', 'Insumos\TransitoController@modal')->name('transito.modals');
Route::match(['get', 'post'], 'producto/transito/codigo/', 'Insumos\TransitoController@codigo')->name('transito.codigo');

//excel transito 1/04/2021
Route::get('transito/descarga/excel', 'contable\TipoTarjetaController@descarga_excel')->name('transito_excel');

//descagar reporte en excel
Route::get('producto/descarga/archivo', 'Insumos\ProductoController@reporte')->name('producto.reporte');
Route::get('producto/dar/baja/', 'Insumos\ProductoController@bajar_producto')->name('producto_dar_baja');

Route::match(['get', 'post'], 'producto/dar_baja/codigo/', 'Insumos\ProductoController@codigo_baja')->name('producto.codigo_baja');
Route::post('producto/dar/baja/guardar/', 'Insumos\ProductoController@guardar_bajar_producto')->name('producto_dar_baja_guardar');

Route::post('producto/uso/paciente/buscar', 'Insumos\TransitoController@serie_enfermero')->name('transito.serie_enfermero');
Route::post('producto/uso/paciente_equipo/buscar', 'Insumos\TransitoController@serie_enfermero_equipo')->name('transito.serie_enfermero_equipo');
Route::get('producto/uso/paciente_equipo/eliminar/{id}', 'Insumos\TransitoController@eliminar_equipo')->name('transito.eliminar_equipo');

Route::match(['get', 'post'], 'producto/dar/baja/descontar/{cant}/{tipo}/{id_prod}/{serie}/{bodega}/{pedido}/{f_vencimiento}/{lote}', 'Insumos\ProductoController@descontar')->name('producto.descontar');
Route::post('producto/borrar', 'Insumos\ProductoController@borrar')->name('producto.borrar');

Route::resource('equipo', 'Insumos\EquipoController');
Route::match(['get', 'post'], 'equipo/search', 'Insumos\EquipoController@search')->name('equipo.search');

Route::match(['get', 'post'], 'producto/reporte/bodega', 'Insumos\ReportesController@reporte_bodega')->name('reporte.reporte_bodega');

Route::match(['get', 'post'], 'producto/buscador/master', 'Insumos\ReportesController@buscador_master')->name('reporte.buscador_master');

Route::match(['get', 'post'], 'producto/buscador/descargar/master', 'Insumos\ReportesController@reporte_master')->name('reporte.reporte_master');

Route::match(['get', 'post'], 'producto/reporte/caducado', 'Insumos\ReportesController@reporte_caducado')->name('reporte.reporte_caducado');

Route::match(['get', 'post'], 'producto/descarga/reporte/producto', 'Insumos\ReportesController@reporte_producto_bodega')->name('reporte.reporte_producto_bodega');

Route::match(['get', 'post'], 'producto/descarga/reporte/caducado', 'Insumos\ReportesController@reporte_producto_caducado')->name('reporte.reporte_producto_caducado');

Route::match(['get', 'post'], 'insumos/reporte/caducado', 'Insumos\ReportesController@buscar_caducado')->name('reporte.buscar_caducado');

Route::get('producto/eliminar/pedido/{id}', 'Insumos\IngresoController@eliminar_pedido')->name('ingreso.eliminar_pedido');
Route::post('producto/eliminar/ingreso_clave/', 'Insumos\IngresoController@eliminar_clave')->name('ingreso.eliminar_clave');

Route::match(['get', 'post'], 'reporte/producto/buscador_usos', 'Insumos\ReportesController@buscador_usos')->name('reporte.buscador_usos');
Route::match(['get', 'post'], 'reporte/equipo/buscador_usos', 'Insumos\ReportesController@buscador_usos_equipo')->name('reporte.buscador_usos_equipo');

Route::match(['get', 'post'], 'reporte/producto/buscador_usos_excel', 'Insumos\ReportesController@buscador_usos_excel')->name('reporte.buscador_usos_excel');

Route::match(['get', 'post'], 'reporte/equipo/buscador_usos_excel', 'Insumos\ReportesController@buscador_equipo_excel')->name('reporte.buscador_equipo_excel');

Route::get('producto/editar/pedido/{id}', 'Insumos\IngresoController@editar_pedido')->name('ingreso.editar_pedido');
Route::post('producto/ingresar/actualizar/', 'Insumos\IngresoController@actualizar_pedido')->name('ingreso.actualizar_pedido');
Route::post('producto/ingresar/create/Fact', 'Insumos\IngresoController@generar_factura')->name('ingreso.generar_facturas');
Route::post('producto/ingresar/store/pedidos', 'Insumos\IngresoController@store_new')->name('ingreso.store_new');
Route::get('producto/unico/imprimir/barra/{id}', 'Insumos\ProductoController@imprimir_barra')->name('imprimir.barras_unico');
Route::get('producto/equipo/imprimir/barra/{id}', 'Insumos\EquipoController@imprimir_barra')->name('imprimir.barras_unico_equipo');
Route::get('producto/buscador/usos/modal/{id}', 'Insumos\ReportesController@modal_detalle')->name('reporte.modal_detalle');

Route::match(['get', 'post'], 'proveedor/validar/ruc', 'Insumos\ProveedorController@validar_ruc')->name('proveedor.validar_ruc');

//plantillas insumos 
Route::match(['get', 'post'], 'insumos/plantillas', 'Insumos\PlantillaController@index')->name('plantilla.index');
Route::get('insumos/plantillas/crear', 'Insumos\PlantillaController@crear_plantilla')->name('plantilla.crear');
Route::post('insumo/items', 'Insumos\PlantillaController@buscar_item')->name('planilla.find_item');
Route::post('insumo/id_item', 'Insumos\PlantillaController@agregar_id_item')->name('planilla.find_id_item');
Route::post('insumos/plantillas/guardar', 'Insumos\PlantillaController@guardar')->name('insumo_planilla.guardar');
Route::get('insumos/plantillas/editar/{id}', 'Insumos\PlantillaController@edit')->name('plantilla.edit');
Route::post('insumos/plantillas/actualizar/{id}', 'Insumos\PlantillaController@update')->name('plantilla.update');
Route::get('insumos/plantillas/item_lista/{id}', 'Insumos\PlantillaController@item_lista')->name('plantilla.item_lista');
Route::get('insumos/plantillas/item_lista2/{id}', 'Insumos\PlantillaController@item_lista2')->name('plantilla.item_lista2');
Route::match(['get', 'post'],'insumos/plantillas/buscar', 'Insumos\PlantillaController@buscar')->name('plantilla.buscar');

//masivo consecion
Route::match(['get', 'post'],'insumos/consecion_detalle', 'Insumos\IngresoController@consecion_detalle')->name('ingreso.consecion_detalle');

Route::get('transito/datails/{id_producto}', 'Insumos\TransitoController@getData')->name('trans.getData');
//guardar_item_plantilla 1/04/2021
Route::get('enfermeria/insumos/guardar_productos', 'Insumos\PlantillaController@guardar_productos')->name('guardar_productos_nuevo');

Route::get('producto/ingreso/conglomerada', 'Insumos\IngresoController@conglomerada')->name('ingreso.conglomerada');
Route::get('producto/ingreso/details', 'Insumos\IngresoController@details')->name('ingreso.details');

