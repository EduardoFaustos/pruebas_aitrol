<?php
 use Sis_medico\ControlSintoma;
Route::get('control/sintomas', 'control_sintoma\ControlSintomasController@index')->name('index_control');
Route::get('control/sintomas/create', function () {
    return view('control_sintoma/create');
})->name('create_control');
Route::match(['get', 'post'],'control/sintomas/save', 'control_sintoma\ControlSintomasController@save')->name('save_control');
Route::match(['get', 'post'],'control/sintomas/edit/{id}',function(ControlSintoma $id){
    return view('control_sintoma/edit',['user'=>$id]);
})->name('edit_control');

Route::match(['get', 'post'],'control/sintomas/editar', 'control_sintoma\ControlSintomasController@editar')->name('editar_control');


Route::match(['get', 'post'],'control/sintomas/buscar', 'control_sintoma\ControlSintomasController@buscar')->name('buscar_control');
Route::get('control/sintomas', 'control_sintoma\ControlSintomasController@index')->name('index_control');
Route::match(['get', 'post'],'control/sintomas/buscarusuario', 'control_sintoma\ControlSintomasController@usuarios')->name('buscarusuario_control');

//excel Mario
Route::get('excel/importaciones', 'control_sintoma\ControlSintomasController@excel')->name('excel_importaciones');



