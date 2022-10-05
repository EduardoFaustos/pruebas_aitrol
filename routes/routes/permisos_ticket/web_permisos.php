<?php
Route::get('ticket_permisos/index', 'ticket_permisos\TicketPermisosController@index')->name('ticketpermisos.index');
Route::get('ticket_permisos/create', 'ticket_permisos\TicketPermisosController@create')->name('ticketpermisos.create');
Route::post('ticket_permisos/verificar', 'ticket_permisos\TicketPermisosController@verificar')->name('ticketpermisos.verificar');
Route::post('ticket_permisos/save', 'ticket_permisos\TicketPermisosController@save')->name('ticketpermisos.save');
Route::match(['get','post'],'ticket_permisos/save_sin_dato', 'ticket_permisos\TicketPermisosController@save_sin_dato')->name('ticketpermisos.save_sin_dato');
Route::get('ticket_permisos/editar/{id}', 'ticket_permisos\TicketPermisosController@editar')->name('ticketpermisos.editar');
Route::post('ticket_permisos/editar_datos', 'ticket_permisos\TicketPermisosController@editar_datos')->name('ticketpermisos.editar_datos');
Route::match(['get','post'],'ticket_permisos/buscador', 'ticket_permisos\TicketPermisosController@buscador')->name('ticketpermisos.buscador');
Route::post('ticket_permisos/subir_pdf/permiso', 'ticket_permisos\TicketPermisosController@subir_pdf')->name('ticketpermisos.subir_documento');
Route::get('ticket_permisos/subir_pdf/ver_pdf', 'ticket_permisos\TicketPermisosController@ver_pdf')->name('ticketpermisos.ver_pdf');
Route::post('ticket_permisos/subir_pdf/subir_pdf1', 'ticket_permisos\TicketPermisosController@subir_pdf1')->name('ticketpermisos.subir_pdf1');
Route::post('vh_ticket_permisos/buscar_usuario', 'ticket_permisos\TicketPermisosController@vh_buscar_usuario')->name('ticketpermisos.vh_buscar_usuario');
Route::get('vh_ticket_permisos/buscar_nomina/{id}', 'ticket_permisos\TicketPermisosController@vh_buscar_nomina')->name('ticketpermisos.vh_buscar_nomina');
Route::get('ticket_permisos/permisos_pdf/{id}', 'ticket_permisos\TicketPermisosController@pdf_permiso')->name('ticketpermisos.permisos_pdf');
Route::match(['get','post'],'usuarios_ticket_permisos/index_usuario', 'ticket_permisos\TicketPermisosController@buscador_usuarios')->name('ticketpermisos.index_usuario');
Route::get('usuarios_ticket_permisos/create_usuario', 'ticket_permisos\TicketPermisosController@create_usuario')->name('ticketpermisos.create_usuario');
Route::get('usuarios_ticket_permisos/editar_usuario/{id}', 'ticket_permisos\TicketPermisosController@editar_usuario')->name('ticketpermisos.editar_usuario');
Route::match(['get','post'],'usuarios_ticket_permisos/editar_datos_usuario', 'ticket_permisos\TicketPermisosController@editar_datos_usuarios')->name('ticketpermisos.editar_datos_usuarios');
Route::get('vh_ticket_permisos/mail_permisos/{id}', 'ticket_permisos\TicketPermisosController@mail_permisos')->name('ticketpermisos.mail_permisos');