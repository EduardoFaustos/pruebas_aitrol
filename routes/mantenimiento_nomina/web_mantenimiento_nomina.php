<?php
Route::match(['get', 'post'], 'area_rh/index', 'mantenimiento_nomina\Ct_Rh_AreaController@index')->name('area_rh.index');
Route::match(['get', 'post'], 'area_rh/create', 'mantenimiento_nomina\Ct_Rh_AreaController@create')->name('area_rh.create');
Route::match(['get', 'post'], 'area_rh/store', 'mantenimiento_nomina\Ct_Rh_AreaController@store')->name('area_rh.store');
Route::match(['get', 'post'], 'area_rh/edit/{id}', 'mantenimiento_nomina\Ct_Rh_AreaController@edit')->name('area_rh.edit');
Route::match(['get', 'post'], 'area_rh/update', 'mantenimiento_nomina\Ct_Rh_AreaController@update')->name('area_rh.update');
//estado civil
Route::match(['get', 'post'], 'estado_civil/index', 'mantenimiento_nomina\Ct_Rh_Estado_CivilController@index')->name('estado_civil.index');
Route::match(['get', 'post'], 'estado_civil/create', 'mantenimiento_nomina\Ct_Rh_Estado_CivilController@create')->name('estado_civil.create');
Route::match(['get', 'post'], 'estado_civil/store', 'mantenimiento_nomina\Ct_Rh_Estado_CivilController@store')->name('estado_civil.store');
Route::match(['get', 'post'], 'estado_civil/edit/{id}', 'mantenimiento_nomina\Ct_Rh_Estado_CivilController@edit')->name('estado_civil.edit');
Route::match(['get', 'post'], 'estado_civil/update', 'mantenimiento_nomina\Ct_Rh_Estado_CivilController@update')->name('estado_civil.update');
//Horario
Route::match(['get', 'post'], 'mantenimiento/horario/index', 'mantenimiento_nomina\HorarioController@index')->name('mantenimiento.horario.index');
Route::match(['get', 'post'], 'mantenimiento/horario/create', 'mantenimiento_nomina\HorarioController@create')->name('mantenimiento.horario.create');
Route::match(['get', 'post'], 'mantenimiento/horario/store', 'mantenimiento_nomina\HorarioController@store')->name('mantenimiento.horario.store');
Route::match(['get', 'post'], 'mantenimiento/horario/edit/{id}', 'mantenimiento_nomina\HorarioController@edit')->name('mantenimiento.horario.edit');
Route::match(['get', 'post'], 'mantenimiento/horario/update', 'mantenimiento_nomina\HorarioController@update')->name('mantenimiento.horario.update');

//Route::match(['get', 'post'], 'mantenimiento_nomina/area_rh', 'mantenimiento_nomina\AreaController@index')->name('area_rh.index');

//Nivel academico
Route::match(['get', 'post'], 'nivel_academico/index', 'mantenimiento_nomina\NivelAcademicoController@index')->name('nivel_academico.index');
Route::match(['get', 'post'], 'nivel_academico/create', 'mantenimiento_nomina\NivelAcademicoController@create')->name('nivel_academico.create');
Route::match(['get', 'post'], 'nivel_academico/store', 'mantenimiento_nomina\NivelAcademicoController@store')->name('nivel_academico.store');
Route::match(['get', 'post'], 'nivel_academico/edit/{id}', 'mantenimiento_nomina\NivelAcademicoController@edit')->name('nivel_academico.edit');
Route::match(['get', 'post'], 'nivel_academico/update', 'mantenimiento_nomina\NivelAcademicoController@update')->name('nivel_academico.update');

//pago beneficio
Route::get('nomina_pago_beneficio/index', 'mantenimiento_nomina\PagoBeneficioController@index')->name('pagobeneficio.index');
Route::get('nomina_pago_beneficio/crear','mantenimiento_nomina\PagoBeneficioController@crear')->name('pagobeneficio.crear');
Route::post('nomina_pago_beneficio/store', 'mantenimiento_nomina\PagoBeneficioController@store')->name('pagobeneficio.store');
Route::get('nomina_pago_beneficio/editar/{id}', 'mantenimiento_nomina\PagoBeneficioController@edit')->name('pagobeneficio.edit');
Route::post('nomina_pago_beneficio/update', 'mantenimiento_nomina\PagoBeneficioController@update')->name('pagobeneficio.update');

//Tipo Aporte
Route::get('nomina_tipo_aporte/index', 'mantenimiento_nomina\Tipo_AporteController@index')->name('tipo_aporte.index');
Route::get('nomina_tipo_aporte/crear','mantenimiento_nomina\Tipo_AporteController@crear')->name('tipo_aporte.crear');
Route::post('nomina_tipo_aporte/store', 'mantenimiento_nomina\Tipo_AporteController@store')->name('tipo_aporte.store');
Route::get('nomina_tipo_aporte/editar/{id}', 'mantenimiento_nomina\Tipo_AporteController@edit')->name('tipo_aporte.edit');
Route::post('nomina_tipo_aporte/update', 'mantenimiento_nomina\Tipo_AporteController@update')->name('tipo_aporte.update');

//Tipo rol
Route::get('nomina_tipo_rol/index', 'mantenimiento_nomina\Tipo_RolController@index')->name('tipo_rol.index');
Route::get('nomina_tipo_rol/crear','mantenimiento_nomina\Tipo_RolController@crear')->name('tipo_rol.crear');
Route::post('nomina_tipo_rol/store', 'mantenimiento_nomina\Tipo_RolController@store')->name('tipo_rol.store');
Route::get('nomina_tipo_rol/editar/{id}', 'mantenimiento_nomina\Tipo_RolController@edit')->name('tipo_rol.edit');
Route::post('nomina_tipo_rol/update', 'mantenimiento_nomina\Tipo_RolController@update')->name('tipo_rol.update');


//mantenimiento titulo profesional

Route::get('mantenimiento/titulo/index', 'Titulo_ProfesionalController@index')->name('tituloprofesional.index');
//Tipo Pago
Route::get('mantenimiento_nomina/tipo_pago/index', 'mantenimiento_nomina\Ct_Rh_Tipo_PagoController@index')->name('tipo_pago.index');
Route::get('mantenimiento_nomina/tipo_pago/crear', 'mantenimiento_nomina\Ct_Rh_Tipo_PagoController@crear')->name('tipo_pago.crear');
Route::post('mantenimiento_nomina/tipo_pago/store', 'mantenimiento_nomina\Ct_Rh_Tipo_PagoController@store')->name('tipo_pago.store');
Route::get('mantenimiento_nomina/tipo_pago/editar/{id}', 'mantenimiento_nomina\Ct_Rh_Tipo_PagoController@editar')->name('tipo_pago.editar');
Route::post('mantenimiento_nomina/tipo_pago/update', 'mantenimiento_nomina\Ct_Rh_Tipo_PagoController@update')->name('tipo_pago.update');

// titulo profesionar para empresas 
Route::get('mantenimiento/titulo/create', 'Titulo_ProfesionalController@create')->name('tituloprofesional.create');
Route::post('mantenimiento/titulo/store', 'Titulo_ProfesionalController@store')->name('tituloprofesional.store');
Route::get('mantenimiento/titulo/edit/{id}', 'Titulo_ProfesionalController@edit')->name('tituloprofesional.edit');
Route::post('mantenimiento/titulo/update', 'Titulo_ProfesionalController@update')->name('tituloprofesional.update');
Route::match(['get', 'post'],'mantenimiento/titulo/delete/{id}', 'Titulo_ProfesionalController@delete')->name('tituloprofesional.delete');


//mantenimiento membresia

Route::get('mantenimiento/membresia/index', 'membresia\membresiaController@index')->name('membresia.index');
Route::get('mantenimiento/membresia/create', 'membresia\membresiaController@create')->name('membresia.create');
Route::post('mantenimiento/membresia/store', 'membresia\membresiaController@store')->name('membresia.store');
Route::get('mantenimiento/membresia/edit/{id}', 'membresia\membresiaController@edit')->name('membresia.edit');
Route::post('mantenimiento/membresia/update', 'membresia\membresiaController@update')->name('membresia.update');
Route::match(['get', 'post'],'mantenimiento/membresia/delete/{id}', 'membresia\membresiaController@delete')->name('membresia.delete');

//mantenimiento user membresia

Route::get('mantenimiento/user_membresia/index', 'membresia\user_membresiaController@index')->name('usermembresia.index');
Route::get('mantenimiento/user_membresia/create', 'membresia\user_membresiaController@create')->name('usermembresia.create');
Route::post('mantenimiento/user_membresia/store', 'membresia\user_membresiaController@store')->name('usermembresia.store');
Route::get('mantenimiento/user_membresia/edit/{id}', 'membresia\user_membresiaController@edit')->name('usermembresia.edit');
Route::post('mantenimiento/user_membresia/update', 'membresia\user_membresiaController@update')->name('usermembresia.update');
Route::match(['get', 'post'],'mantenimiento/user_membresia/delete/{id}', 'membresia\user_membresiaController@delete')->name('usermembresia.delete');

// Mantenimiento Nivel - Labs
Route::get('mantenimiento/labs/nivel/index','mantenimientos_botones_labs\NivelController@index')->name('nivel.index');
Route::get('mantenimiento/labs/nivel/crear','mantenimientos_botones_labs\NivelController@crear')->name('nivel.crear');
Route::post('mantenimiento/labs/nivel/store','mantenimientos_botones_labs\NivelController@store')->name('nivel.store');
Route::get('mantenimiento/labs/nivel/editar/{id}','mantenimientos_botones_labs\NivelController@editar')->name('nivel.editar');
Route::post('mantenimiento/labs/nivel/update','mantenimientos_botones_labs\NivelController@update')->name('nivel.update');

//Mantenimiento Protocolo - Labs
Route::get('mantenimiento/labs/protocolo/index','mantenimientos_botones_labs\ProtocoloController@index')->name('protocolo.index');
Route::get('mantenimiento/labs/protocolo/crear','mantenimientos_botones_labs\ProtocoloController@crear')->name('protocolo.crear');
Route::post('mantenimiento/labs/protocolo/store','mantenimientos_botones_labs\ProtocoloController@store')->name('protocolo.store');
Route::get('mantenimiento/labs/protocolo/editar/{id}','mantenimientos_botones_labs\ProtocoloController@editar')->name('protocolo.editar');
Route::post('mantenimiento/labs/protocolo/update','mantenimientos_botones_labs\ProtocoloController@update')->name('protocolo.update');
Route::get('mantenimiento/labs/protocolo/cargarExamenes{id}','mantenimientos_botones_labs\ProtocoloController@cargarExamenes')->name('protocolo.cargarExamenes');
