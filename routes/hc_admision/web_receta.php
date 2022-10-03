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
  
//consulta máster



Route::get('historiaclinica/receta2/{hcid}', 'hc_admision\RecetaController@receta')->name('receta.receta');
Route::post('historiaclinica/receta3/', 'hc_admision\RecetaController@guardarpaciente')->name('receta.paciente');
Route::post('historiaclinica/receta4/', 'hc_admision\RecetaController@guardar2')->name('receta.guardar2');

Route::post('historiaclinica/buscar_nombre/', 'hc_admision\RecetaController@buscar_nombre')->name('receta.buscar_nombre');
Route::post('historiaclinica/buscar_nombre2/', 'hc_admision\RecetaController@buscar_nombre2')->name('receta.buscar_nombre2');

//ingreso de receta
Route::get('historiaclinica/receta/{ag}', 'hc_admision\RecetaController@mostrar')->name('receta.mostrar');
Route::post('receta/crea_actualiza/', 'hc_admision\RecetaController@crear_actualizar')->name('receta.update_crea');
Route::get('historiaclinica/receta/imprime/{id}/{tipo}', 'hc_admision\RecetaController@imprime')->name('hc_receta.imprime');

Route::match(['get', 'post'],'medicina/{ag}/search', 'hc_admision\MedicinaController@search')->name('medicina.search');
//Route::resource('medicina', 'hc_admision\MedicinaController');
Route::get('medicina/{ag}','hc_admision\MedicinaController@index')->name('medicina.index');
Route::get('medicina/{ag}/create','hc_admision\MedicinaController@create')->name('medicina.create');
Route::get('medicina/{ag}/{id}/edit','hc_admision\MedicinaController@edit')->name('medicina.edit');
Route::post('medicina/store','hc_admision\MedicinaController@store')->name('medicina.store');
Route::match(['put', 'patch'],'medicina/{id}/update', 'hc_admision\MedicinaController@update')->name('medicina.update');

Route::match(['get', 'post'],'generico/{ag}/search', 'hc_admision\GenericoController@search')->name('generico.search');
//Route::resource('generico', 'hc_admision\GenericoController');
Route::get('generico/{ag}','hc_admision\GenericoController@index')->name('generico.index');
Route::get('generico/{ag}/create','hc_admision\GenericoController@create')->name('generico.create');
Route::get('generico/{ag}/{id}/edit','hc_admision\GenericoController@edit')->name('generico.edit');
Route::post('generico/store','hc_admision\GenericoController@store')->name('generico.store');
Route::match(['put', 'patch'],'generico/{id}/update', 'hc_admision\GenericoController@update')->name('generico.update');


Route::get('generico/admision/find', 'hc_admision\GenericoController@find')->name('generico.find');
Route::get('receta_detalle/crear_detalle/{receta}/{medicina}/{pac}', 'hc_admision\RecetaController@crear_detalle')->name('receta.crear_detalle');
Route::get('receta_detalle/index_detalle/{receta}', 'hc_admision\RecetaController@index_detalle')->name('receta.index_detalle');
Route::match(['get', 'post'],'receta_detalle/editar_detalle/{receta}/{id}', 'hc_admision\RecetaController@editar_detalle')->name('receta.editar_detalle');
Route::get('receta_detalle/eliminar_detalle/{receta}/{id}', 'hc_admision\RecetaController@eliminar_detalle')->name('receta.eliminar_detalle');
Route::match(['get', 'post'], 'receta_modificada/modificar_detalle/', 'hc_admision\RecetaController@update_receta_2')->name('receta.update_receta_2');

Route::get('medicina2/{ag}/{ruta}/create','hc_admision\MedicinaController@create2')->name('medicina2.create2');
Route::post('medicina2/store','hc_admision\MedicinaController@store2')->name('medicina2.store2');

Route::post('generico2/store','hc_admision\GenericoController@store2')->name('generico2.store2');

Route::get('reporte/medicinas','hc_admision\MedicinaController@reporte')->name('medicina.reporte');

//Nuevas Rutas Receta

//Ruta para Historial de Recetas
Route::get('paciente/historial/recetas/{id_paciente}', 'hc_admision\RecetaController@historial_recetas')->name('paciente.historial_recetas');

//Ruta para actualizar recetas
Route::get('historiaclinica/actualiza/recetas', 'hc_admision\RecetaController@editar_receta')->name('historial.actualiza_receta');
Route::get('historiaclinica/actualiza/recetas/{id}/{idpaciente}', 'hc_admision\RecetaController@editar_receta');

//Ruta para volver a la vista historial recetas
Route::post('historiaclinica/retorna/vista/receta', 'hc_admision\RecetaController@retorna_vista_historial_recetas')->name('historiaclin_paciente.receta_act');

//Ruta para crea una nueva receta al paciente
Route::get('paciente/nueva/receta/{id_paciente}', 'hc_admision\RecetaController@agregar_nueva_receta')->name('historiaclinica_paciente_nueva.receta');

//para actualizar los campos rp y prescripcion
Route::post('historiaclinicahc4/modificar_detalle/recetas', 'hc_admision\RecetaController@updatehc4_receta_2')->name('update_receta_2.recetahc4');

//Para actualizar fecha_doctor
Route::post('actualiza/fecha/doctor', 'hc_admision\RecetaController@update_fech_doct')->name('update_fecha.iddoctor');


