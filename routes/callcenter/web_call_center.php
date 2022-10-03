<?php

Route::get('callcenter/reporter/{fecha}/{fechafin}', 'CallCenterController@index')->name('callcenter.index');
Route::get('callcenter/reporter/')->name('callcenter.buscador');
Route::post('callcenter/reporte/actualizar/', 'CallCenterController@actualizarpaciente')->name('callcenter.actualizarpaciente');
Route::post('callcenter/descarga/reporte/', 'CallCenterController@descargar_reporte')->name('callcenter.descargar_reporte');


	