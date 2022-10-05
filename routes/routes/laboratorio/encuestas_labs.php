<?php
//encuestas laboratorio
Route::get('formato_encuestalabs', 'SinLoginController@formato_encuestalabs')->name('formato.encuestalabs');
Route::post('laboratorio_encuesta/guardar/datos', 'SinLoginController@encuestalabsguardar')->name('laboratorio_encuesta.guardar');
Route::get('laboratorio/resultados_labs', 'laboratorio\Preguntas_LabsController@resultados_labs')->name('laboratorio.resultados_labs');
Route::match(['get','post'],'laboratorio/estadisticalabs', 'laboratorio\Preguntas_LabsController@estadisticalabs')->name('laboratorio.estadisticalabs');
Route::post('laboratorio/detalle/mes_labs', 'laboratorio\Preguntas_LabsController@detalle_mes_labs')->name('laboratorio.detalle_mes_labs');
Route::resource('pregunta_labs', 'laboratorio\Preguntas_LabsController');
//preguntas para la encuensta
//Route::match(['get', 'post'], 'preguntas/resultados/search', 'laboratorio\Preguntas_LabsController@search')->name('preguntas.search');
///Route::resource('preguntas', 'laboratorio\Preguntas_LabsController');
//Route::resource('grupopreguntaslabs', 'laboratorio\GrupoPreguntas_LabsController');