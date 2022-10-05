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

Route::get('/prueba/iliana', 'prueba1\IlianaController@index')->name('iliana');

Route::get('/prueba/prueba12/nombres', 'prueba1\IlianaController@nombres')->name('nombres');

Route::get('/prueba/prueba12/apellidos', 'prueba1\IlianaController@apellidos')->name('apellidos');

//prueba

Route::get('pruebap/index','prueba_emily\pruebaController@indexpro')->name('indexpro');
Route::get ('pruebap/crear','prueba_emily\pruebaController@crearpro')->name('crearpro');
Route::get('pruebap/editar/{id}', 'prueba_emily\pruebaController@editarpro')->name('editarpro');
Route::match(['get','post'], 'pruebap/update/pro', 'prueba_emily\pruebaController@update_pro');
Route::post('pruebap/guardar','prueba_emily\pruebaController@guardarpro') -> name ('guardarpro');

//Leer excel 
//Route::get('prueba/excel','prueba_emily\pruebaController@leer_excel')->name('importar.leer_excel');


///Manuel 
Route::get('productos_manuel/index','prueba_manuel\prueba_manuel@index_productos')->name('index.manuel');
Route::get('productos_manuel/crear','prueba_manuel\prueba_manuel@crear_productos')->name('crear.manuel');
Route::post('productos_manuel/guardar','prueba_manuel\prueba_manuel@guardar_productos')->name('guardar.manuel'); 
Route::get('productos_manuel/editar/{id}','prueba_manuel\prueba_manuel@editar_productos')->name('editar.manuel');
Route::match(['get','post'],'productos/actualizar','prueba_manuel\prueba_manuel@actualizar_productos')->name('actualizar.manuel');