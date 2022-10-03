<?php

Route::get('reportesubir/subir/','ReporteSubirController@index')->name('reportesubir.index');
Route::match(['get', 'post'],'reportesubir/subir/leer/{id}', 'ReporteSubirController@detalle')->name('reportesubir.detalle');
Route::post('reportesubir/subir/actualizar','ReporteSubirController@vistasubida')->name('reportesubir.vistasubida');
Route::get('reportesubir/subir/vistareporte','ReporteSubirController@vistareporte')->name('reportesubir.vistareporte');
Route::match(['get', 'post'],'reportesubir/subir/correo/{id}/{id_detalle}', 'ReporteSubirController@correo')->name('reportesubir.correo');
Route::match(['get', 'post'],'reportesubir/enviar/correo/{id}/{id_agenda}', 'ReporteSubirController@correotodos')->name('reportesubir.correotodos');
Route::match(['get', 'post'],'reportesubir/reagendar/{id_agenda}', 'ReporteSubirController@reagendar')->name('reportesubir.reagendar');