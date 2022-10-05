<?php
Route::match(['get', 'post'],'vacunas/empleados', 'vacunas\VacunaController@vacunas_empleados')->name('vacunas.empleados');

Route::match(['get', 'post'],'vacunas/buscar_empleados','vacunas\VacunaController@buscar_empleados')->name('vacunas.buscar_empleados');

Route::match(['get', 'post'],'vacunas/empleados/revisar_vacunas/{id}', 'vacunas\VacunaController@revisar_vacunas')->name('vacunas.revisar');

Route::match(['get', 'post'],'vacunas/empleados/crear/{id}', 'vacunas\VacunaController@crear_registro')->name('vacunas.crear_registro');

Route::match(['get', 'post'],'vacunas/guardar/', 'vacunas\VacunaController@guardar')->name('vacunas.guardar');

Route::match(['get', 'post'],'vacunas/empleados/buscar_vacunas', 'vacunas\VacunaController@buscar_vacunas')->name('vacunas.buscar_vacunas');

Route::match(['get', 'post'],'vacunas/empleados/reporte_vacunas', 'vacunas\VacunaController@reporte_vacunas')->name('vacunas.reporte_vacunas');

Route::match(['get', 'post'],'vacunas/empleados/buscar_reporte', 'vacunas\VacunaController@buscar_reporte')->name('vacunas.buscar_reporte');
Route::match(['get', 'post'],'reporte/informe_eep.pdf', 'vacunas\VacunaController@pdf_informe_epp')->name('vacunas.pdf_informe_epp');
Route::match(['get', 'post'],'reporte/informe_013.pdf', 'vacunas\VacunaController@pdf_informe_013')->name('vacunas.pdf_informe_013');