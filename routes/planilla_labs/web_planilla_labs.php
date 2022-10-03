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

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'orden/plantilla/labs/{id_orden}', 'plantilla_labs\PlantillaControlLabsController@orden_plantilla')->name('plantilla.orden_plantilla');
Route::match(['get', 'post'], 'orden/plantilla/labs/buscar/producto', 'plantilla_labs\PlantillaControlLabsController@buscarProducto')->name('plantilla.buscarProducto');
Route::match(['get', 'post'], 'orden/plantilla/cargar/plantilla', 'plantilla_labs\PlantillaControlLabsController@cargarPanltilla')->name('plantilla.cargarPanltilla');


Route::match(['get', 'post'], 'orden/plantilla/labs/{id}', 'plantilla_labs\PlantillaControlLabsController@orden_plantilla')->name('plantilla.orden_plantilla');
Route::get('labs/plantilla_labs/index','plantilla_labs\PlantillaControlLabsController@index')->name('plantillacontrollabs.index');
Route::get('labs/plantilla_labs/crear', 'plantilla_labs\PlantillaControlLabsController@create')->name('plantillacontrollabs.crear');
Route::post('labs/plantilla_labs/save', 'plantilla_labs\PlantillaControlLabsController@save')->name('plantillacontrollabs.save');
Route::get('labs/plantilla_labs/editar/{id}', 'plantilla_labs\PlantillaControlLabsController@edit')->name('plantillacontrollabs.edit');
Route::post('labs/plantilla_labs/actualizar/', 'plantilla_labs\PlantillaControlLabsController@update')->name('plantillacontrollabs.update');
Route::get('labs/plantilla_labs/item_lista/{id}', 'plantilla_labs\PlantillaControlLabsController@item_lista')->name('plantillacontrollabs.item_lista');
Route::match(['get', 'post'],'labs/plantilla_labs/item_lista/buscar', 'plantilla_labs\PlantillaControlLabsController@buscar')->name('plantillacontrollabs.buscar');

Route::match(['get', 'post'],'labs/plantilla_labs/comprobar', 'plantilla_labs\PlantillaControlLabsController@comprobar')->name('plantillacontrollabs.comprobar');

Route::match(['get', 'post'],'labs/plantilla/labs/comparativo/{id}', 'plantilla_labs\PlantillaControlLabsController@comparativo')->name('laboratorio.plantilla.comparativo');

Route::match(['get', 'post'],'labs/plantilla/kardex', 'plantilla_labs\PlantillaControlLabsController@storePlanilla')->name('laboratorio.plantilla.storePlanilla');

Route::match(['get', 'post'],'labs/plantilla_labs/busca_examen', 'plantilla_labs\PlantillaControlLabsController@busca_examen')->name('plantillacontrollabs.busca_examen');

Route::match(['get', 'post'],'labs/plantilla_labs/guardar_derivado', 'plantilla_labs\PlantillaControlLabsController@guardar_derivado')->name('plantillacontrollabs.guardar_derivado');

Route::get('labs/plantilla_labs/eliminar_det/{id}', 'plantilla_labs\PlantillaControlLabsController@eliminar_det')->name('plantillacontrollabs.eliminar_det');

//Mantenimiento Examenes Derivados
Route::get('labs/mantenimiento_examenes_derivados/index','plantilla_labs\MantenimientoExamDerivadosController@index')->name('mantenimiento.examderivados.index');


