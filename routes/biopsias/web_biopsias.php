<?php
Route::match(['get', 'post'], 'biopsias', 'biopsias\BiopsiasController@index')->name('biopsias.index');
Route::match(['get', 'post'], 'biopsias/detalles/{hc_id_procedimiento}', 'biopsias\BiopsiasController@detalles')->name('biopsias.detalles');
Route::match(['get', 'post'], 'biopsias/{id}', 'biopsias\BiopsiasController@edit')->name('biopsias.edit');
Route::match(['get', 'post'], 'biopsias/editar/{id}', 'biopsias\BiopsiasController@editresultado')->name('biopsias.editresultado');
Route::match(['get', 'post'], 'biopsias/registro/{id}', 'biopsias\BiopsiasController@registro')->name('biopsias.registro');
Route::match(['get', 'post'], 'biopsias/registro/guardar/biopsias', 'biopsias\BiopsiasController@registroguardar')->name('biopsias.registroguardar');
Route::match(['get', 'post'], 'biopsias/buscadorporfecha', 'biopsias\BiopsiasController@buscadorporfecha')->name('biopsias.buscadorporfecha');

/*Route::match(['get', 'post'],'hospital/admin/gestionh/buscar','hospital\HospitalAdminController@buscar')->name('hospital_admin.buscar');>
/*Route::match(['get', 'post'],'hospital/gestioncuartos','HospitalController@gcuartos')->name('hospital.gcuartos');
Route::match(['get', 'post'],'hospital/admcuarto','HospitalController@admcuarto')->name('hospital.admcuarto');
Route::match(['get', 'post'],'hospital/farmacia/agregarp','HospitalController@agregarp')->name('hospital.agregarp');
Route::match(['get', 'post'],'hospital/quirofano','HospitalController@quirofano')->name('hospital.quirofano');
Route::match(['get', 'post'],'hospital/modalq','HospitalController@modalq')->name('hospital.modalq');*/
?>