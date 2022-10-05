<?php
//MANTENIEMTO PRESTAMOS
Route::get('mantenimiento_prestamos/index', 'contable\MantenimientoPrestamosController@index_mantenimiento')->name('mantenimientoprestamos.index');
Route::get('mantenimiento_prestamos/crear','contable\MantenimientoPrestamosController@crear_mantenimiento')->name('mantenimientoprestamos.crear');
Route::post('mantenimiento_prestamos/guardar','contable\MantenimientoPrestamosController@guardar_mantenimiento')->name('mantenimientoprestamos.guardar');