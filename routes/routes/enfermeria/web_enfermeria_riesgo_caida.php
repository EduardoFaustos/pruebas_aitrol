<?php
//Riesgo de Caidas 26/1/2020
Route::match(['get', 'post'], 'gestion/camilla/index', 'enfermeria\Control_CaidaController@index')->name('camilla.index');
Route::match(['get', 'post'], 'gestion/camilla/buscar', 'enfermeria\Control_CaidaController@search')->name('riesgo.search');
Route::match(['get', 'post'], 'gestion/camilla/modal_riesgo/{id}', 'enfermeria\Control_CaidaController@modal_riesgo_caida')->name('riesgo_caida.modal');
Route::get('gestion/camilla/verificar', 'enfermeria\Control_CaidaController@verificar')->name('riesgo.verificar');
Route::get('gestion/camilla/calc_edad', 'enfermeria\Control_CaidaController@calc_edad')->name('riesgo.calc_edad');
Route::get('gestion/camilla/form_mayor/{id}/{id_camilla}/{id_agenda}', 'enfermeria\Control_CaidaController@form_mayor')->name('riesgo.form_mayor');
Route::get('gestion/camilla/form_menor/{id}/{id_camilla}/{id_agenda}', 'enfermeria\Control_CaidaController@form_menor')->name('riesgo.form_menor');
Route::post('gestion/camilla/form_menor/guardar_datos', 'enfermeria\Control_CaidaController@guardar_datos')->name('riesgo.guardar_datos');
Route::match(['get', 'post'], 'gestion/camilla/modal_cambio/{id}', 'enfermeria\Control_CaidaController@modal_cambio')->name('riesgo_cambio.modal');
Route::get('gestion/camilla/cambio_estado', 'enfermeria\Control_CaidaController@cambio_estado')->name('riesgo.cambio_estado');
Route::get('gestion/camilla/buscar_estado', 'enfermeria\Control_CaidaController@buscar_estado')->name('riesgo.buscar_estado');
Route::post('gestion/camilla/form_menor/guardar_datos_menor', 'enfermeria\Control_CaidaController@guardar_datos_menor')->name('guardar_datos_menor');
Route::match(['get', 'post'], 'gestion/camilla/modal_estado/{id}', 'enfermeria\Control_CaidaController@modal_estado')->name('riesgo_cambio.modal_estado');
Route::get('gestion/camilla/cambio_estado_uno', 'enfermeria\Control_CaidaController@cambio_estado_uno')->name('cambio_estado_uno');
Route::get('gestion/camilla/gaurdar_estados/{id}/{id_camilla}/{id_agenda}', 'enfermeria\Control_CaidaController@gaurdar_estados')->name('riesgo.gaurdar_estados');
Route::get('gestion/camilla/camas_estado', 'enfermeria\Control_CaidaController@camas_estado')->name('camas_estado');
Route::get('gestion/camilla/camas_estado_hab', 'enfermeria\Control_CaidaController@camas_estado_hab')->name('camas_estado_hab');
Route::get('gestion/camilla/background_estado/{id_cama}', 'enfermeria\Control_CaidaController@background_estado')->name('background_estado');
Route::match(['get', 'post'],'gestion/camillas/actualizar_estado', 'enfermeria\Control_CaidaController@actualizar_estado')->name('actualizar_estado');
Route::get('gestion/camilla/registro', 'enfermeria\Control_CaidaController@registro')->name('registro_camas');
Route::get('gestion/camilla/tabla', 'enfermeria\Control_CaidaController@tabla')->name('riesgo.tabla');
Route::get('gestion/camilla/pdf/{id_agenda}', 'enfermeria\Control_CaidaController@pdf_mayor_edad')->name('riesgo.pdf');
Route::get('gestion/camilla/pdf_menor/{id_agenda}', 'enfermeria\Control_CaidaController@pdf_menor_edad')->name('riesgo_menor.pdf');
//guardar paciente sin riesgo
Route::get('gestion/camilla/guardar_sinriesgo/{id}/{id_camilla}/{id_agenda}', 'enfermeria\Control_CaidaController@guardar_sinriesgo')->name('camilla.guardar_sinriesgo');
//ocupar sala sin camilla cominezo
Route::get('gestion/camilla/buscar_estado_sin_cama', 'enfermeria\Control_CaidaController@buscar_estado_sin_cama')->name('buscar_estado_sin_cama');
//Actualizar update
Route::match(['get', 'post'], 'gestion/camilla/actualizar_masivo', 'enfermeria\Control_CaidaController@actualizar_masivo')->name('camilla.actualizar_masivo');
Route::match(['get', 'post'], 'gestion/camilla/comprobar_sesion', 'enfermeria\Control_CaidaController@comprobar_sesion')->name('camilla.comprobar_sesion');

Route::get('gestion/camilla/form_mayor_sincama/{id}/{id_agenda}', 'enfermeria\Control_CaidaController@mayor_sincama')->name('riesgo.mayorsincama');
Route::get('gestion/camilla/form_menor_sincama/{id}/{id_agenda}', 'enfermeria\Control_CaidaController@menor_sincama')->name('riesgo.menorsincama');