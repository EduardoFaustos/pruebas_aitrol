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

//ORDENES DE PROCEDIMIENTO ENDOSCOPICO

//Para el historial de las Ordenes de Procedimientos Endoscopicos
Route::get('historial_hc4/orden/endoscopicos/{id_paciente}', 'hc4\ordenes\Orden_Proc_EndosController@index')->name('paciente.orden_proc_endoscopico');

//Para crear una Orden de Procedimiento Endoscopico
Route::get('orden_hc4/procedimiento/endoscopico/{tipo}/{paciente}', 'hc4\ordenes\Orden_Proc_EndosController@crear_orden_endoscopico')->name('hc4_orden_proc_endoscopico');

//Para guardar Orden de Procedimiento Endoscopico
Route::post('orden_hc4/guardar/endoscopico','hc4\ordenes\Orden_Proc_EndosController@guardar_orden_endoscopico')->name('guarda.ordenhc4_proendoscopica');

//Editar Orden de Procedimiento Endoscopico
route::get('orden_hc4/ingresar/historial_hc4/', 'hc4\ordenes\Orden_Proc_EndosController@editar_orden')->name('editar.orden_procedimiento_endoscopico');
route::get('orden_hc4/ingresar/historial_hc4/{id_orden}/{id_paciente}', 'hc4\ordenes\Orden_Proc_EndosController@editar_orden');


//Para actualizar Orden de Procedimiento Endoscopico
Route::post('orden_hc4/actualiza/endoscopico','hc4\ordenes\Orden_Proc_EndosController@actualiza_orden_endoscopico')->name('actualiza.ordenhc4_proendoscopica');

//Para Imprimir la Orden de Procedimiento Endoscopico
Route::get('imprimir/orden_hc4/endoscopico/{id_orden}','hc4\ordenes\Orden_Proc_EndosController@imprimir_orden_endoscopica')->name('imprimir.ordenhc4_endoscopica');

//Para Imprimir la Orden de Procedimiento Endoscopico CIR
Route::get('imprimir/orden_hc4/endoscopico/cir/{id_orden}','hc4\ordenes\Orden_Proc_EndosController@imprimir_orden_endoscopica_cir')->name('imprimir.ordenhc4_endoscopica_cir');

//ORDENES DE PROCEDIMIENTO FUNCIONALES

//Para el historial de las Ordenes de Procedimientos Funcionales
Route::get('historial_hc4/orden/funcional/{id_paciente}', 'hc4\ordenes\Orden_Proc_FunController@index')->name('paciente.orden_proc_funcional');

//Para crear una Orden de Procedimiento Funcional
Route::get('orden_hc4/procedimiento/funcional/{tipo}/{paciente}', 'hc4\ordenes\Orden_Proc_FunController@crear_orden_funcional')->name('hc4_orden_proc_funcional');

//Para guardar Orden de Procedimiento Funcional
/*Route::post('orden_hc4/guardar/funcional','hc4\ordenes\Orden_Proc_FunController@guardar_orden_funcional')->name('guarda.ordenhc4_profuncional');*/

//Editar Orden de Procedimiento Funcional
route::get('edita_orden/funcional/historial_hc4/', 'hc4\ordenes\Orden_Proc_FunController@editar_orden_funcional')->name('editar.orden_procedimiento_funcional');
route::get('edita_orden/funcional/historial_hc4/{id_orden}/{id_paciente}', 'hc4\ordenes\Orden_Proc_FunController@editar_orden_funcional');

//Para actualizar Orden de Procedimiento Funcional
Route::post('orden_hc4/actualiza/funcional','hc4\ordenes\Orden_Proc_FunController@actualiza_orden_funcional')->name('actualiza.ordenhc4_profuncional');


//Para Imprimir la Orden de Procedimiento Funcional
Route::get('imprimir/orden_hc4/funcional/{id_orden}','hc4\ordenes\Orden_Proc_FunController@imprimir_orden_funcional')->name('imprimir.ordenhc4_funcional');


//ORDENES DE IMAGENES
//Muestra el historial de las Imagenes
Route::get('historial_hc4/orden/imagenes/{id_paciente}', 'hc4\ordenes\Orden_Proc_ImagenesController@index')->name('paciente.orden_proc_imagenes');

//Para crear una Orden de Procedimiento Imagenes
Route::get('orden_hc4/procedimiento/imagenes/{tipo}/{paciente}', 'hc4\ordenes\Orden_Proc_ImagenesController@crear_orden_imagenes')->name('hc4_orden_proc_imagenes');

//Para guardar Orden de Imagenes
/*Route::post('orden_hc4/guardar/imagenes','hc4\ordenes\Orden_Proc_ImagenesController@guardar_orden_imagenes')->name('guarda.ordenhc4_proimagenes');*/

//Editar Orden de Procedimiento Imagenes
route::get('edita_orden/imagenes/historial_hc4/', 'hc4\ordenes\Orden_Proc_ImagenesController@editar_orden_imagenes')->name('editar.orden_procedimiento_imagenes');
route::get('edita_orden/imagenes/historial_hc4/{id_orden}/{id_paciente}', 'hc4\ordenes\Orden_Proc_ImagenesController@editar_orden_imagenes');

//Para actualizar Orden de Procedimiento Imagenes
Route::post('orden_hc4/actualiza/imagenes','hc4\ordenes\Orden_Proc_ImagenesController@actualiza_orden_imagenes')->name('actualiza.ordenhc4_procedimagenes');

//Para Imprimir la Orden de Procedimiento Imagenes
Route::get('imprimir/orden_hc4/imagenes/{id_orden}','hc4\ordenes\Orden_Proc_ImagenesController@imprimir_orden_imagenes')->name('imprimir.ordenhc4_imagenes');


//Busca Evolucion Llena
route::get('busca_evolucion_imagenes/imagenes', 'hc4\ordenes\Orden_Proc_ImagenesController@buscar_evolucion_imagenes')->name('busca.evolucion_imag');
route::get('busca_evolucion_imagenes/imagenes/{id_paciente}', 'hc4\ordenes\Orden_Proc_ImagenesController@buscar_evolucion_imagenes');

//ordenes de laboratorio
route::get('hc4/orden/laboratorio/doctor/index/{paciente}', 'hc4\ordenes\Orden_LaboratorioController@index')->name('hc4_orden_lab.index');
route::get('hc4/orden/laboratorio/doctor/index2/{paciente}', 'hc4\ordenes\Orden_LaboratorioController@index2')->name('hc4_orden_lab.index2');
route::get('hc4/orden/laboratorio/doctor/crear/{paciente}', 'hc4\ordenes\Orden_LaboratorioController@crear')->name('hc4_orden_lab.crear');
route::get('hc4/orden/laboratorio/doctor/editar/{paciente}', 'hc4\ordenes\Orden_LaboratorioController@editar')->name('hc4_orden_lab.editar');
Route::post('hc4/orden/laboratorio/doctor/buscar/{orden}','hc4\ordenes\Orden_LaboratorioController@buscar')->name('hc4_orden_lab.buscar');











