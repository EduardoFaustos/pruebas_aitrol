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

Route::match(['get', 'post'], 'examen/search', 'laboratorio\ExamenController@search')->name('examen.search');
Route::resource('examen', 'laboratorio\ExamenController');
Route::get('examen/parametro/{examen}', 'laboratorio\ExamenController@parametro')->name('examen.parametro');
Route::get('examen/parametro/{examen}/crear', 'laboratorio\ExamenController@create_parametro')->name('examen.create_parametro');
Route::post('examen/parametro/store_parametro', 'laboratorio\ExamenController@store_parametro')->name('examen.store_parametro');
Route::get('examen/parametro/{id}/edit_parametro', 'laboratorio\ExamenController@edit_parametro')->name('examen.edit_parametro');
Route::post('examen/parametro/update_parametro', 'laboratorio\ExamenController@update_parametro')->name('examen.update_parametro');
Route::get('examen/consulta/reporte', 'laboratorio\ExamenController@reporte')->name('examen.reporte');

Route::resource('protocolo', 'laboratorio\ProtocoloController');
Route::get('protocolo/examen/{id}/{pr}', 'laboratorio\ProtocoloController@examen')->name('protocolo.examen');
Route::get('protocolo/examen/eliminar/{id}/{pr}', 'laboratorio\ProtocoloController@eliminar')->name('protocolo.eliminar');
Route::get('protocolo/busca/examen/{id}', 'laboratorio\ProtocoloController@buscaexamen')->name('protocolo.buscaexamen');
Route::get('protocolo/busca/examen/{id}/{ex}', 'laboratorio\ProtocoloController@buscaexamenid')->name('protocolo.buscaexamenid');

Route::match(['get', 'post'], 'orden/search', 'laboratorio\OrdenController@search')->name('orden.search');
Route::resource('orden', 'laboratorio\OrdenController');
Route::get('orden/eliminar/{id}', 'laboratorio\OrdenController@eliminar')->name('orden.eliminar');
Route::get('orden/descargar/{id}', 'laboratorio\OrdenController@descargar')->name('orden.descargar');
Route::match(['get', 'post'], 'orden/reporte', 'laboratorio\OrdenController@reporte')->name('orden.reporte');

Route::match(['get', 'post'], 'orden/cierre/caja', 'laboratorio\OrdenController@cierreCaja')->name('orden.cierre_caja');

Route::get('orden/detalle/{id}/{dir}', 'laboratorio\OrdenController@detalle')->name('orden.detalle');
Route::match(['get', 'post'], 'orden/reporte/index', 'laboratorio\OrdenController@reporte_index')->name('orden.reporte_index');
Route::match(['get', 'post'], 'orden/cotizaciones/index', 'laboratorio\OrdenController@cotizaciones')->name('orden.cotizaciones');
Route::match(['get', 'post'], 'cotizaciones_orden/reporte_cotizaciones', 'laboratorio\OrdenController@reporte_cotizaciones')->name('orden_cotizaciones.reporte_cot');
Route::match(['get', 'post'], 'orden_detalle/reporte/index', 'laboratorio\OrdenController@reporte_detalle')->name('orden.reporte_detalle');
Route::match(['get', 'post'], 'orden_detalle/reporte/reporte_detalle_covid', 'laboratorio\OrdenController@reporte_detalle_covid')->name('orden.reporte_detalle_covid');
Route::match(['get', 'post'], 'orden/supervision/index', 'laboratorio\OrdenController@index_supervision')->name('orden.index_supervision');
Route::match(['get', 'post'], 'orden_buscar/supervision/index', 'laboratorio\OrdenController@search_supervision')->name('orden.search_supervision');
Route::match(['get', 'post'], 'orden/control/index', 'laboratorio\OrdenController@index_control')->name('orden.index_control');
Route::get('orden_b/control/index_control_b/{id}', 'laboratorio\OrdenController@index_control_b')->name('orden.index_control_b');
Route::match(['get', 'post'], 'orden/control/index/search', 'laboratorio\OrdenController@search_control')->name('orden.search_control');
Route::get('orden/control/edit/{id}/{dir}', 'laboratorio\OrdenController@edit2')->name('orden.edit2');
Route::get('orden_c/control/edit1/{id}', 'laboratorio\OrdenController@edit1_c')->name('orden.edit1_c');

Route::get('detalle/busca/examen/{id_or}/{id_ex}', 'laboratorio\OrdenController@buscaexamendb')->name('detalle.buscaexamendb');

Route::get('admision/{hcid}/{url}', 'laboratorio\OrdenController@create_admin')->name('orden.admision');
Route::post('admision/orden/store', 'laboratorio\OrdenController@store_admin')->name('orden.store_admin');
Route::get('admision/orden/index/{id}', 'laboratorio\OrdenController@index_admin')->name('orden.index_admin');
//vista para los dorctores
Route::get('laboratorio/orden/doctores/{id}/{agenda}', 'laboratorio\OrdenController@index_doctor')->name('orden.index_doctor');
Route::get('menu/laboratorio/orden/', 'laboratorio\OrdenController@index_doctor_menu')->name('orden.index_doctor_menu');
Route::match(['get', 'post'], 'laboratorio/orden/search_doctor', 'laboratorio\OrdenController@search_doctor')->name('orden.search_doctor');
Route::get('laboratorio/orden/revisar_doctor/{id}/', 'laboratorio\OrdenController@ver_doctor')->name('orden.ver_doctor');
Route::get('laboratorio/orden/revisar_convenios/{id}/', 'laboratorio\OrdenController@ver_convenios')->name('orden.ver_convenios');
Route::post('orden/convenios/revision/fecha', 'laboratorio\OrdenController@fecha_convenios')->name('orden.fecha_convenios');

Route::match(['get', 'post'], 'exa_agrupadores/search', 'laboratorio\Exa_agrupadorController@search')->name('exa_agrupadores.search');
Route::resource('exa_agrupadores', 'laboratorio\Exa_agrupadorController');
Route::match(['get', 'post'], 'orden/realizar/{id}/{r}', 'laboratorio\OrdenController@realizar')->name('orden.realizar');

Route::get('control_semaforo', 'laboratorio\SemaforoController@index')->name('semaforo.index');
Route::match(['get', 'post'], 'control_semaforo/search', 'laboratorio\SemaforoController@search')->name('semaforo.search');

//codigo de barras
Route::get('imprimir/codigo_barras/laboratorio/{id}', 'laboratorio\OrdenController@codigo_barras')->name('orden.codigo_barras');

Route::get('pentax_semaforo', 'laboratorio\OrdenController@pentax_semaforo')->name('orden.pentax_semaforo');

Route::post('estad_mes', 'laboratorio\OrdenController@estad_mes')->name('orden.estad_mes');
Route::get('estad_mes/examen/{mes}/{anio}', 'laboratorio\OrdenController@estad_examen')->name('orden.estad_examen');
Route::get('estad_mes/examen/{mes}/{anio}/to_excel', 'laboratorio\OrdenController@to_excel')->name('orden.to_excel');

Route::match(['get', 'post'], 'examen_costo/search', 'laboratorio\ExamenCostoController@search')->name('examen_costo.search');
Route::resource('examen_costo', 'laboratorio\ExamenCostoController');
Route::get('examen_costo/consulta/reporte', 'laboratorio\ExamenCostoController@reporte')->name('examen_costo.reporte');

Route::get('genera_costo', 'laboratorio\OrdenController@genera_costo')->name('detalle.genera_costo');

//ruta de ingreso de resultados
Route::get('orden/realizar_examen/ingreso_actualiza/{id_orden}/{id_parametro}', 'laboratorio\OrdenController@crea_modifica')->name('resultados.crea_actualiza');

//ruta de guarda o actualiza de resultados
Route::post('orden/realizar_examen/ingreso_actualiza_guardar/', 'laboratorio\OrdenController@guarda_actualiza_resultados')->name('resultados.guardar_actualizar_resultados');

Route::get('resultados/imprimir/{id_orden}', 'laboratorio\OrdenController@imprimir_resultado')->name('resultados.imprimir');
Route::get('valida/imprimir/{id_orden}', 'laboratorio\OrdenController@puede_imprimir')->name('resultados.puede_imprimir');
Route::get('resultados2/imprimir2/{id_orden}', 'laboratorio\OrdenController@imprimir_resultado2')->name('resultados.imprimir2');
Route::get('resultados_gastro/imprimir/{id_orden}', 'laboratorio\OrdenController@imprimir_resultado3')->name('resultados.imprimir3');

Route::get('orden_particular/crear', 'laboratorio\OrdenController@crear_particular')->name('orden_particular.crear_particular');
Route::get('orden_particular/crear/{id}', 'laboratorio\OrdenController@crear_particular2')->name('orden_particular.crear_particular2');
Route::post('orden_particular/store', 'laboratorio\OrdenController@store_particular')->name('orden_particular.store_particular');
Route::match(['put', 'patch'], 'orden_particular/update/{id}', 'laboratorio\OrdenController@update_particular')->name('orden_particular.update_particular');

//ruta agendalabs
Route::match(['get', 'post'], 'agendalabs/agenda', 'laboratorio\AgendaLabsController@agenda')->name('agendalabs.agenda');
Route::get('agendalabs/laboratorio/{id}', 'laboratorio\AgendaLabsController@laboratorio')->name('agendalabs.laboratorio');

//convenio
Route::get('convenio_buscar/{seguro}/{empresa}', 'laboratorio\OrdenController@convenio_buscar')->name('convenio.convenio_buscar');
Route::get('convenio_buscar/examen/{nivel}/{examen}', 'laboratorio\OrdenController@convenio_buscar_examen')->name('convenio.convenio_buscar_examen');
Route::get('detalle/valor/{id}', 'laboratorio\OrdenController@detalle_valor')->name('detalle.valor');

Route::get('carga_totales', 'laboratorio\OrdenController@carga_totales')->name('orden.carga_totales');

Route::post('agrupador_labs/buscar', 'laboratorio\OrdenController@agrupador_labs_buscar')->name('agrupador_labs.buscar');
Route::post('agrupador_labs/nivel', 'laboratorio\OrdenController@agrupador_labs_nivel')->name('agrupador_labs.nivel');

Route::post('cotizador/crear', 'laboratorio\OrdenController@cotizador_store')->name('cotizador.store');
Route::post('cotizador/recalcular', 'laboratorio\OrdenController@cotizador_recalcular')->name('cotizador.recalcular');
Route::post('cotizador/crear_cabecera', 'laboratorio\OrdenController@crear_cabecera')->name('cotizador.crear_cabecera');
Route::post('cotizador/cotizador/cabecera', 'laboratorio\OrdenController@cotizador_cabecera')->name('cotizador.cotizador_cabecera');
Route::get('cotizador/editar/{id}', 'laboratorio\OrdenController@cotizador_editar')->name('cotizador.editar');
Route::get('cotizador/update/{cot}/{id}', 'laboratorio\OrdenController@cotizador_update')->name('cotizador.update');
Route::get('cotizador/delete/{cot}/{id}', 'laboratorio\OrdenController@cotizador_delete')->name('cotizador.delete');
Route::get('cotizador/imprimir/{id}', 'laboratorio\OrdenController@cotizador_imprimir')->name('cotizador.imprimir');

//pdf Tiempos
Route::get('tiempos/imprimir/{id}', 'laboratorio\OrdenController@tiempos_imprimir')->name('tiempos.imprimir');

Route::get('cotizador/imprimir_sistema/{id}', 'laboratorio\OrdenController@cotizador_imprimir_sistema')->name('cotizador.imprimir_sistema');
Route::get('cotizador_p/orden/imprimir/{id}', 'laboratorio\OrdenController@cotizador_orden_imprimir')->name('cotizador.imprimir_orden');
Route::get('cotizador_gastro/imprimir/{id}', 'laboratorio\OrdenController@cotizador_imprimir_gastro')->name('cotizador.imprimir_gastro');
Route::get('cotizador/generar/{id}', 'laboratorio\OrdenController@cotizador_generar')->name('cotizador.generar');
//Route::get('laboratorio/agenda/{id}', 'laboratorio\OrdenController@cotizador_generar')->name('cotizador.generar');
//Route::match(['get', 'post'],'laboratorio/agendar_privado/agenda/{id_orden}', 'laboratorio\OrdenController@agendar_privado')->name('agenda.agendar_privado');

Route::get('subresultado/{id_orden}/{id_examen}/crear', 'laboratorio\OrdenController@subresultado_crear')->name('subresultado.crear');
Route::get('subresultado/eliminar/{id}', 'laboratorio\OrdenController@subresultado_eliminar')->name('subresultado.eliminar');

Route::post('subresultado/store', 'laboratorio\OrdenController@subresultado_store')->name('subresultado.store');

Route::get('subresultado/{id_orden}/{id_examen}/listar', 'laboratorio\OrdenController@subresultado_listar')->name('subresultado.listar');

//CONVENIOS PRIVADOS
Route::get('privados/index', 'laboratorio\OrdenController@index_privado')->name('privados.index');
Route::match(['get', 'post'], 'privados/search', 'laboratorio\OrdenController@search_privado')->name('privados.search');
Route::match(['get', 'post'], 'privados/reporte', 'laboratorio\OrdenController@ordenes_rpt')->name('privados.ordenes_rpt');
Route::match(['get', 'post'], 'privados/detalle/reporte', 'laboratorio\OrdenController@detalle_rpt')->name('privados.detalle_rpt');

//certificar
Route::get('certificar/resultado/{orden}/{id}/{n}/{maq}', 'laboratorio\OrdenController@certificar')->name('certificar.resultado');

//estadisticos
Route::get('labs_estadisticos', 'laboratorio\EstadisticoController@anio_mes')->name('labs_estadisticos.anio_mes');
//marca listo
Route::get('marca_listo', 'laboratorio\OrdenController@marca_listo')->name('marca_listo');

Route::get('cotizador/protocolo/{orden}/{protocolo}', 'laboratorio\OrdenController@genera_protocolo_privado')->name('cotizador.protocolo');

Route::get('laboratorio/orden/buscar/{id}', 'laboratorio\OrdenController@buscar_orden')->name('orden_lab.buscar_orden');

//Muestra los datos del Paciente al hacer clip en el button Pago
Route::get('datos_paciente_email/modal_pago/{id_paciente}/{id_exa_orden}', 'laboratorio\OrdenController@modal_pago_paciente')->name('modal.pago_paciente');

//Actualiza Email Paciente y estado_pago examen_orden
Route::post('actualiza/estado_pago/email_paciente', 'laboratorio\OrdenController@update_estado_email_paciente')->name('update.estado_email_pago');

//Reenvio Email Paciente

Route::get('paciente_email/modal_reenvio/{id_paciente}/{id_exa_orden}', 'laboratorio\OrdenController@open_reenvio_email')->name('paciente_reenvio_email');

//DESLIGAR CORREO
Route::get('paciente_email/desligar_correo/{id_paciente}/{correo}', 'laboratorio\OrdenController@desligar_correo')->name('paciente_desligar_correo');

Route::post('reenvio/email_paciente', 'laboratorio\OrdenController@reenviar_email_paciente')->name('reenvio.email');

//Reseteo Clave Paciente

Route::get('paciente_clave/modal_reseteo/{id_paciente}/{id_exa_orden}', 'laboratorio\OrdenController@open_reseteo_clave')->name('paciente_reseteo_clave');

Route::post('reseteo/clave_paciente', 'laboratorio\OrdenController@reseteo_clave_paciente')->name('reseteo.clave');

Route::get('laboratorio/orden/buscar/doctor/{id}', 'laboratorio\OrdenController@buscar_orden_doctor')->name('orden_lab.buscar_orden_doctor');

Route::get('laboratorio/estadistico/mes/doctor/{anio}/{mes}', 'laboratorio\EstadisticoController@estad_doctor_mes')->name('labs_estad.doctor_mes');

Route::get('laboratorio/examenes/humanlabs', 'ImportarController@subir_humanlabs')->name('examenes.subir_humanlabs');

Route::get('orden_iess/confirmar/{id}', 'laboratorio\OrdenController@confirmar_publico')->name('orden_iess.confirmar');

Route::get('guardar/testdealimentos/{id_parametro}/{valor}/{orden}', 'laboratorio\OrdenController@guardar_testdealimentos')->name('testalimentos.guardar');

Route::get('laboratorio/orden/orden_imprimir/{id_parametro}/{valor}/{orden}', 'laboratorio\OrdenController@orden_imprimir')->name('orden_lab.orden_imprimir');

Route::get('laboratorio/estadistico/covid/{anio}/{mes}', 'laboratorio\EstadisticoController@estadistico_covid')->name('estadistico.covid');
//REPORTE DE ENVIADOS AL MAIL
Route::match(['get', 'post'], 'orden/reporte/mail', 'laboratorio\OrdenController@reporte_mail')->name('orden.reporte_mail');
//mail principal
Route::get('laboratorio/mail/principal/{mail}', 'laboratorio\OrdenController@recupera_mail')->name('orden.recupera_mail');
Route::get('laboratorio/mail/principal/recupera/{usuario}', 'laboratorio\OrdenController@recupera_usuario')->name('orden.recupera_usuario');

//Obtener Reporte por Id_Examen "1191"
Route::get('laboratorio/recupera/informacion/factura', 'laboratorio\OrdenController@recupera_info_factura')->name('orden.recupera_inf_factura');

//Cambio Masivo de valores
Route::get('cambio_masivo_valores', 'laboratorio\OrdenController@cambio_masivo_valores')->name('orden.cambio_masivo_valores');
//Cambio Masivo de valores Ordenes Publicos
Route::get('cambio_ordenes_publicos', 'ImportarController@cambio_ordenes_publicos')->name('importar.cambio_ordenes_publicos');
//Buscar agrupador Labs
Route::post('aj_agrupador_labs/buscador', 'laboratorio\Exa_agrupadorController@agrupador_labs_buscar_aj')->name('agrupador_labs.buscar_aj');
//Buscar agrupador Labs
Route::post('aj_examenes/buscador', 'laboratorio\ExamenController@examenes_buscar_aj')->name('examenes.buscar_aj');

//NUEVA VALIDACION DE RANGOS
Route::post('validacion/rangos/maximos/minimos', 'laboratorio\OrdenController@validacion_maximos')->name('resultados.validacion_maximos');

//PAGOS EN LINEA
Route::match(['get', 'post'], 'pagoenlinea/gestionar', 'laboratorio\OrdenController@pagoenlinea_gestionar')->name('orden.pagoenlinea_gestionar');
Route::match(['get', 'post'], 'pagoenlinea/gestionar/js', 'laboratorio\OrdenController@pagoenlinea_gestionar_js')->name('orden.pagoenlinea_gestionar_js');
Route::get('pagoenlinea/gestionar/orden/{id}', 'laboratorio\OrdenController@pagoenlinea_gestionar_orden')->name('orden.pagoenlinea_gestionar_orden');
Route::post('pagoenlinea/gestionar/orden/guardar', 'laboratorio\OrdenController@pagoenlinea_gestionar_guardar')->name('orden.pagoenlinea_gestionar_guardar');
Route::get('cexamenes/correccion/nombreiess', 'ImportarController@importar_nombreiess')->name('importar.importar_nombreiess');

//AGENDA-LABS
Route::get('agenda_labs/agendar/{id_agenda}', 'laboratorio\OrdenController@aglaboratorio_nuevo')->name('orden_labs.aglaboratorio_nuevo');
Route::post('agenda_labs/agendar/calendario', 'laboratorio\OrdenController@aglaboratorio_calendario')->name('orden_labs.ag_laboratorio_calendario');
Route::post('agenda_labs/agendar/calendario/guardar', 'laboratorio\OrdenController@aglaboratorio_store')->name('orden_labs.aglaboratorio_store');

//AGENDA-LABS PRIVADA
Route::get('seguros_privados/agenda_labs/agendar', 'laboratorio\OrdenController@privados_agendar')->name('orden_labs.privados_agendar');
Route::post('seguros_privados/agenda_labs/calendario/guardar', 'laboratorio\OrdenController@privados_store')->name('orden_labs.privados_store');

//RESULTADOS PENDIENTES
Route::post('resultados/pendintes/xls', 'laboratorio\OrdenController@resultados_pendientes')->name('orden_labs.resultados_pendientes');

//CUPONERA CONTROL
Route::get('cuponera/index', 'laboratorio\CuponeraController@index')->name('orden_labs.cuponera_index');
//INGRESO DE PLANTILLAS_CONVENIOS
Route::post('labs_plantillas_convenios/crear_cabecera/{id}', 'laboratorio\Labs_Plantillas_ConveniosController@crear_cabecera')->name('labs_plantillas_convenios.crear_cabecera');
//CARGA COMISIONES LABS
Route::get('comisiones_labs', 'ImportarController@comisiones_labs')->name('comisiones_labs');
//GUARDA FECHA DE TOMA DE MUESTRA
Route::get('examen_orden/toma_muestra/{id}', 'laboratorio\OrdenController@toma_muestra')->name('orden_labs.toma_muestra');
//PIDE DATOS DE INFORMACION DE FACTURA
Route::get('facturacion_labs/info_factura/{id}', 'laboratorio\FacturaLabsController@datos_factura')->name('facturalabs.datos_factura');
//modal forma de pago
Route::get('cotizador/forma_pago/{id_orden}', 'laboratorio\FacturaLabsController@forma_pago')->name('facturalabs.forma_pago');
Route::get('cotizador/forma_pago/ajax/{id_orden}', 'laboratorio\FacturaLabsController@forma_pago_ajax')->name('facturalabs.forma_pago_ajax');
Route::get('cotizador/datos_forma/{id_orden}', 'laboratorio\FacturaLabsController@datos_forma')->name('facturalabs.datos_forma');
Route::get('cotizador/eliminar_forma/{id_orden}/{id_forma}', 'laboratorio\FacturaLabsController@eliminar_forma')->name('facturalabs.eliminar_forma');
Route::get('cotizador/eliminar_forma_gastro/{id_orden}/{id_forma}', 'laboratorio\FacturaLabsController@eliminar_forma_gastro')->name('facturalabs.eliminar_forma_gastro');

Route::post('cotizador/forma_pago/guardar_forma', 'laboratorio\FacturaLabsController@guardar_forma')->name('facturalabs.guardar_forma');
Route::get('cotizador/revisa_forma/{id_orden}', 'laboratorio\FacturaLabsController@revisa_forma')->name('facturalabs.revisa_forma');
Route::get('facturacion_labs/cuadrar/{id}', 'laboratorio\FacturaLabsController@cuadrar')->name('facturalabs.cuadrar');
Route::get('facturacion_labs/humanlabs_enviar_sri/{id}', 'laboratorio\FacturaLabsController@humanlabs_enviar_sri')->name('facturalabs.humanlabs_enviar_sri');
Route::get('facturacion_labs/pagoenlinea_factura/{id}', 'laboratorio\FacturaLabsController@pagoenlinea_factura')->name('facturalabs.pagoenlinea_factura');
//GUARDAR INFORMACION DE FACTURA
Route::post('facturacion_labs/info_factura/guardar', 'laboratorio\FacturaLabsController@guardar_info_factura')->name('facturalabs.guardar_info_factura');
//FUNCION CREA PRODUCTO A PARTIR DE EXAMEN
Route::get('crear_producto_labs_masivo', 'laboratorio\FacturaLabsController@crear_producto_labs_masivo')->name('facturalabs.crear_producto_labs_masivo');
//factura agrupada
Route::get('facturacion_labs/modal_factura_agrupada', 'laboratorio\FacturaLabsController@modal_factura_agrupada')->name('facturalabs.modal_factura_agrupada');

//añadir a la factura x sesion
Route::match(['get', 'post'], 'facturacion_labs/factura_agrup/guardar', 'laboratorio\FacturaLabsController@guardar_agrupada')->name('facturalabs.guardar_agrupada');
Route::match(['get', 'post'], 'facturacion_labs/factura_agrup/guardar/contabilidad', 'laboratorio\FacturaLabsController@guardar_agrupada_contabilidad')->name('facturalabs.guardar_agrupada');
Route::get('facturacion_labs/añadir_factura/{id_orden}', 'laboratorio\FacturaLabsController@añadir_factura')->name('facturalabs.añadir_factura');
Route::get('facturacion_labs/añadir_factura/contabilidad/{id_orden}', 'laboratorio\FacturaLabsController@añadir_factura_contabilidad')->name('facturalabs.añadir_factura');
Route::get('facturacion_labs/leeañadir_factura/{id_orden}', 'laboratorio\FacturaLabsController@leeañadir_factura')->name('facturalabs.leeañadir_factura');
Route::match(['get', 'post'], 'facturacion_labs/factura_agrup/eliminar_sesion', 'laboratorio\FacturaLabsController@eliminar_sesion')->name('facturalabs.eliminar_sesion');
Route::get('facturacion_labs/datos_factura_agrupada', 'laboratorio\FacturaLabsController@datos_factura_agrupada')->name('facturalabs.datos_factura_agrupada');
Route::get('facturacion_labs/agrupada_sri/{id_falsa}', 'laboratorio\FacturaLabsController@agrupada_sri')->name('facturalabs.agrupada_sri');

//Comprobante no tributario
Route::get('laboratorio/index/pdf/{id}', 'laboratorio\ComprobanteNoTributarioController@pdf')->name('pdf_tributario');
//cotizaciones
Route::get('laboratorio/index/pdf_cotizacion/{id}', 'laboratorio\ComprobanteNoTributarioController@pdf_cotizacion')->name('pdf_cotizacion');
//carga masivo facturacion
Route::match(['get', 'post'], 'facturacion/labs/carga/masivo', 'laboratorio\FacturaLabsController@carga_masivo')->name('facturalabs.carga_masivo');
Route::get('facturacion/labs/eliminar/orden/sesion/{id}', 'laboratorio\FacturaLabsController@eliminar_orden_sesion')->name('facturalabs.eliminar_orden_sesion');
Route::get('humanlabs/factura_agrupada/{id_falsa}', 'laboratorio\FacturaLabsController@temporal_factura_agrupada')->name('facturalabs.temporal_factura_agrupada');
//agrupada contabilidad

Route::get('humanlabs/index_factura_agrupada', 'laboratorio\FacturaAgrupadaController@index_factura_agrupada')->name('factura_agrupada.index_factura_agrupada');
Route::get('humanlabs/modal_registro', 'laboratorio\FacturaAgrupadaController@modal_registro')->name('factura_agrupada.modal_registro');
Route::post('humanlabs/factura_agrupada/guardar_datos_agrupada', 'laboratorio\FacturaAgrupadaController@guardar_datos_agrupada_cab')->name('factura_agrupada.guardar_datos_agrupada_cab');

/*Editar las facturas agrupadas*/

Route::post('humanlabs/factura_agrupada/editar_datos_agrupada', 'laboratorio\FacturaAgrupadaController@editar_datos_agrupada_cab')->name('factura_agrupada.editar_datos_agrupada_cab');

/*Fin de edicion*/

Route::get('humanlabs/index_privadas/{id_cab}', 'laboratorio\FacturaAgrupadaController@index_privadas')->name('factura_agrupada.index_privadas');

/********************* Edicion Buscador Facturas Agrupadas **************************/
Route::match(['get', 'post'], 'humanlabs/index_privadas/busqueda/{id_cab}', 'laboratorio\FacturaAgrupadaController@index_privadas_buscador')->name('factura_agrupada.index_privadas_buscador');
Route::match(['get', 'post'], 'humanlabs/editar_privadas/busqueda/{id_cab}', 'laboratorio\FacturaAgrupadaController@editar_privadas_buscador')->name('factura_agrupada.editar_privadas_buscador');
Route::match(['get', 'post'], 'humanlabs/editar_publicas/busqueda/{id_cab}', 'laboratorio\FacturaAgrupadaController@editar_publicas_buscador')->name('factura_agrupada.editar_publicas_buscador');
/**********************************************************************************/

/*********************************Guardar detalle de factura agrupada todo**********************/
Route::post('humanlabs/privadas/guardar_det_todo', 'laboratorio\FacturaAgrupadaController@guardar_det_todo')->name('factura_agrupada.guardar_det_todo');

/*********************************Fin detalle de factura agrupada todo**********************/

Route::match(['get', 'post'], 'humanlabs/privadas/guardar_det/{id_orden}', 'laboratorio\FacturaAgrupadaController@guardar_det')->name('factura_agrupada.guardar_det');
Route::get('humanlabs/editar_privadas/{id_cab}', 'laboratorio\FacturaAgrupadaController@editar_privadas')->name('factura_agrupada.editar_privadas');
Route::get('humanlabs/index_privadas_ajax/{id_cab}', 'laboratorio\FacturaAgrupadaController@index_privadas_ajax')->name('factura_agrupada.index_privadas_ajax');
Route::get('humanlabs/eliminar_orden_privada/{id_orden}/{id_cab}', 'laboratorio\FacturaAgrupadaController@eliminar_orden_privada')->name('factura_agrupada.eliminar_orden_privada');
Route::match(['get', 'post'], 'humanlabs/factura_agrupada/carga_publicas/{id_cab}', 'laboratorio\FacturaAgrupadaController@carga_publicas')->name('factura_agrupada.carga_publicas');
Route::get('humanlabs/editar_publicas/{id_cab}', 'laboratorio\FacturaAgrupadaController@editar_publicas')->name('factura_agrupada.editar_publicas');
Route::match(['get', 'post'], 'humanlabs/factura_agrupada/guardar_agrup_sri/{id_cab}', 'laboratorio\FacturaAgrupadaController@guardar_agrup_sri')->name('factura_agrupada.guardar_agrup_sri');
Route::get('humanlabs/recalcular_agrupada/{id_cab}', 'laboratorio\FacturaAgrupadaController@recalcular_agrupada')->name('factura_agrupada.recalcular_agrupada');
Route::get('humanlabs/excel_detalle_orden/{id_cab}', 'laboratorio\FacturaAgrupadaController@excel_detalle_orden')->name('factura_agrupada.excel_detalle_orden');

//EXAMENES PENDIENTES
Route::get('examenes_pendientes/{id_orden}', 'laboratorio\OrdenController@examenes_pendientes')->name('orden.examenes_pendientes');

//Agrupada labs
Route::get('faturas_pendientes/agregar/{mes}', 'laboratorio\FacturaAgrupadaController@agregarPendientes')->name('orden.agregar_pendientes');

//FACTURACION GASTROCLINICA
Route::get('gastroclinica/facturacion/labs/{id_orden}', 'laboratorio\OrdenController@facturacion_gastroclinica')->name('orden.facturacion_gastroclinica');

//guardar forma pago gastro
Route::post('gastroc/pago/guardar_forma_gastro', 'laboratorio\FacturaLabsController@guardar_forma_gastro')->name('facturalabs.guardar_forma_gastro');
Route::post('gastroc/pago/guardar_oda', 'laboratorio\FacturaLabsController@guardar_oda')->name('facturalabs.guardar_oda');
Route::get('gastroc/forma_gastro/ajax/{id_ordv}', 'laboratorio\FacturaLabsController@forma_gastro_ajax')->name('facturalabs.forma_gastro_ajax');
Route::get('gastroc/informacion/factura/{id}', 'laboratorio\FacturaLabsController@informacion_factura')->name('facturalabs.informacion_factura');
Route::get('gastroc/pago/calculo_oda/{id_ordv}', 'laboratorio\FacturaLabsController@calculo_oda')->name('facturalabs.forma_gastro_ajax');
//edit cliente agrupado
Route::get('humanlabs/modal_registro/edit/{id_cab}', 'laboratorio\FacturaAgrupadaController@modal_edit_cliente')->name('factura_agrupada.modal_edit_cliente');
//pendientes publicas
Route::get('resultados/pendintes/publicas/{id_cab}', 'laboratorio\FacturaAgrupadaController@resultados_pendientes_publicas')->name('factura_agrupada.resultados_pendientes_publicas');

Route::get('resultados/mostrar/pendintes/publicas/{id_cab}', 'laboratorio\FacturaAgrupadaController@mostrar_pendientes_publicas')->name('factura_agrupada.mostrar_pendientes_publicas');

Route::get('resultados/ingresar/pendintes/publicas/individual/{id_cab}/{id_orden} ', 'laboratorio\FacturaAgrupadaController@carga_publicas_ind')->name('factura_agrupada.ingre_pendientes_publicas_indi');

Route::get('estadisticos_labs/{anio}/produccion_vs_facturas_vs_pagos', 'laboratorio\EstadisticoController@produccion_vs_facturas_vs_pagos')->name('labs_estadistico.produccion_vs_facturas_vs_pagos');

Route::get('estadisticos_labs/{anio}/examenes', 'laboratorio\EstadisticoController@estadisticos_examenes')->name('labs_estadistico.estadisticos_examenes');

Route::get('factura/labs/crea_ventas', 'laboratorio\FacturaLabsController@crea_ventas')->name('factura_labs.crea_ventas');

Route::get('facturacion_labs/enviar_sri2/{id}', 'laboratorio\FacturaLabsController@enviar_sri2')->name('facturalabs.enviar_sri2');

Route::match(['get', 'post'], 'labs/estadisticos/contable', 'EstadisticosPlanoController@labs_estadisticos')->name('e.labs_estadisticos');
Route::get('pago_en_linea/ingreso_contabilidad/{id}', 'contable\CierreCajaController@pago_en_linea_contab')->name('cierrecaja.pago_en_linea_contab');

//ESTADISTICO DE COMISIONES
Route::match(['get', 'post'], 'laboratorio/comisiones', 'laboratorio\LabsComisionesController@comisiones')->name('labscomisiones.comisiones');
//DETALLE
Route::get('laboratorio/comisiones/detalles/{ames}/{doctor}', 'laboratorio\LabsComisionesController@detalle_comisiones')->name('labscomisiones.detalle_comisiones');
//DETALLE EXTERNOS
Route::get('laboratorio/comisiones/detalles/externos/{ames}/{codigo}', 'laboratorio\LabsComisionesController@detalle_comisiones_externos')->name('labscomisiones.detalle_comisiones_externos');
//check nuevo
Route::match(['get', 'post'], 'guardar/check', 'laboratorio\OrdenController@guardar_ordenes_check')->name('ordenes.check_nuevo');
//modal f
Route::get('labs_validar_mail/{id_orden}', 'laboratorio\OrdenController@labs_validar_mail')->name('orden.labs_validar_mail');
//Actualiza Email Paciente y estado_pago examen_orden
//Route::post('actualiza/estado_pago/email_paciente', 'laboratorio\OrdenController@update_estado_email_paciente2')->name('update.estado_email_pago2');
Route::get('labs_modal_log_toma_muestras/{id_orden}', 'laboratorio\OrdenController@log_toma_muestras')->name('orden.log_toma_muestras');
//MASIVO FACTURACION DE NOVIEMBRE
Route::get('lmasivo/facturacion/noviembre/', 'laboratorio\FacturaLabsController@masivo_facturacion_noviembre')->name('facturalabs.masivo_facturacion_noviembre');

Route::match(['get', 'post'], 'orden/reporte/anual/index', 'laboratorio\FacturaLabsController@reporte_anual')->name('facturalabs.reporte_anual');
//guardar forma pago gastro
Route::post('cambio_resultado', 'laboratorio\OrdenController@cambio_resultado')->name('orden.cambio_resultado');

Route::post('laboratorio_seguro_convenio', 'laboratorio\OrdenController@obtener_convenio_seguro')->name('orden.obtener_convenio_seguro');

Route::post('nuevo_reporte_cierre_laboratorio', 'contable\CierreCajaController@nr_cierre_laboratorio')->name('cierrecaja.nr_cierre_laboratorio');

Route::post('conglomerada_nuevo_reporte/cierre_laboratorio', 'contable\CierreCajaController@conglomerada_cierre_laboratorio')->name('cierrecaja.conglomerada_cierre_laboratorio');
Route::match(['get', 'post'],'nuevo_reporte_cierre_ct_pdf', 'contable\CierreCajaController@ct_reporte_cierre_pdf')->name('cierrecaja.ct_reporte_cierre_pdf');
Route::match(['get', 'post'],'nuevo_reporte_cierre_ct_excel', 'contable\CierreCajaController@ct_reporte_cierre_excel')->name('cierrecaja.ct_reporte_cierre_excel');

//BUSCADOR FACTURA AGRUPADA
Route::match(['get', 'post'],'humanlabs/index_factura_agrupada/buscador', 'laboratorio\FacturaAgrupadaController@buscador_agrupada')->name('facturalabs.buscador');
Route::match(['get', 'post'],'agrupada/editar_detalle/{id_det}', 'laboratorio\FacturaAgrupadaController@editar_detalle')->name('factura_agrupada.editar_detalle');

//abril 2022 SUBIDA DE PRECIO EXAMENES DE LABORATORIO 5%
Route::get('2022_abril_laboratorio/warning', 'laboratorio\OrdenController@subir_5pct_abril_2022')->name('orden.subir_5pct_abril_2022');

//facturar masivo desde cierre de caja
Route::post('cc/facturar_masivo', 'contable\CierreCajaController@facturar_masivo')->name('cierrecaja.facturar_masivo');

//MEMBRESIAS
Route::get('labs_membresias/{id_paciente}', 'laboratorio\MembresiasLabsController@buscar_membresia')->name('membresiaslabs.buscar_membresia');
Route::get('vt_labs_membresias/{id_paciente}', 'laboratorio\MembresiasLabsController@buscar_membresia')->name('vt_membresiaslabs.buscar_membresia');


//PROFORMAS ******* RECIBO DE COBRO
Route::match(['get', 'post'], 'comercial/proforma/index', 'comercial\ProformaController@index')->name('proforma.index');
Route::match(['get', 'post'], 'comercial/proforma/busar/paciente', 'comercial\ProformaController@buscarPaciente')->name('proforma.buscarPaciente');
Route::match(['get', 'post'], 'comercial/proforma/crear/paciente', 'comercial\ProformaController@crearPaciente')->name('proforma.crearPaciente');
Route::match(['get', 'post'], 'comercial/proforma/store', 'comercial\ProformaController@store')->name('comercial.proforma.store');
Route::match(['get', 'post'], 'comercial/proforma/editar/{id}', 'comercial\ProformaController@editar')->name('comercial.proforma.editar');
Route::match(['get', 'post'], 'comercial/proforma/detalles/{id}', 'comercial\ProformaController@detalles')->name('comercial.proforma.detalles');
Route::match(['get', 'post'], 'comercial/proforma/actualizar/producto', 'comercial\ProformaController@actualizar_producto')->name('comercial.proforma.actualizar_producto');
//Route::post('nuevo_recibo_de_cobro/actualizar/producto', 'contable\NuevoReciboCobroController@actualizar_producto')->name('comercial.proforma.detalles');
Route::post('comercial/proforma/eliminar/producto', 'comercial\ProformaController@eliminar_producto')->name('comercial.proforma.eliminar_producto');
Route::match(['get', 'post'], 'comercial/proforma/update/Cabecera', 'comercial\ProformaController@updateCabecera')->name('comercial.proforma.updateCabecera');
Route::match(['get', 'post'], 'comercial/proforma/eliminar/{id}', 'comercial\ProformaController@eliminar_proforma')->name('comercial.proforma.eliminar_proforma');
Route::get('comercial/proforma/modal/{id_paciente}','comercial\ProformaController@proformaModal')->name('comercial.proforma.proformaModal');
Route::match(['get', 'post'], 'comercial/proforma/actualizar/paciente', 'comercial\ProformaController@updatePaciente')->name('comercial.proforma.updatePaciente');
Route::match(['get', 'post'], 'comercial/proforma/nivel', 'comercial\ProformaController@nivel')->name('comercial.proforma.nivel');
Route::match(['get', 'post'], 'comercial/proforma/actualizar/precio/nivel', 'comercial\ProformaController@actualizarPrecio')->name('comercial.proforma.actualizarNivel');
Route::match(['get', 'post'], 'comercial/proforma/pasarNuevoRecibo', 'comercial\ProformaController@pasarNuevoRecibo')->name('comercial.proforma.pasarNuevoRecibo');
Route::match(['get', 'post'], 'comercial/proforma/proformaLista', 'comercial\ProformaController@proformaLista')->name('comercial.proforma.proformaLista');


Route::match(['get', 'post'], 'comercial/proforma/guardar_producto', 'comercial\ProformaController@guardar_producto')->name('comercial.proforma.guardar_producto');
Route::get('comercial/producto_tarifario/index', 'comercial\ProdTarifarioController@index')->name('prodtarifario.index');
Route::match(['get', 'post'], 'comercial/buscar/productos', 'comercial\ProdTarifarioController@buscarproductos')->name('prodtarifario.productos');
Route::match(['get', 'post'], 'comercial/buscar', 'comercial\ProdTarifarioController@buscar')->name('prodtarifario.buscar');
Route::match(['get', 'post'], 'comercial/index_tarifario/{id_producto}', 'comercial\ProdTarifarioController@index_tarifario')->name('prodtarifario.index_tarifario');
Route::get('comercial/producto_tarifario/crear_tarifario/{id_producto}','comercial\ProdTarifarioController@crear_tarifario')->name('prodtarifario.crear_tarifario');
Route::match(['get', 'post'], 'comercial/producto_tarifario/guardar_tarifario','comercial\ProdTarifarioController@guardar_tarifario')->name('prodtarifario.guardar_tarifario');
Route::get('comercial/producto_tarifario/eliminar_tarifario/{id}','comercial\ProdTarifarioController@eliminar_tarifario')->name('prodtarifario.eliminar_tarifario');
Route::match(['get', 'post'], 'comercial/producto_tarifario/edit_tarifario/{id}','comercial\ProdTarifarioController@edit_tarifario')->name('prodtarifario.edit_tarifario');
Route::match(['get', 'post'],'comercial/producto_tarifario/update_tarifario','comercial\ProdTarifarioController@update_tarifario')->name('prodtarifario.update_tarifario');
Route::match(['get', 'post'], 'comercial/producto_tarifario/edit_particular/{id}','comercial\ProdTarifarioController@edit_particular')->name('prodtarifario.edit_particular');
Route::match(['get', 'post'],'comercial/producto_tarifario/update_particular','comercial\ProdTarifarioController@update_particular')->name('prodtarifario.update_particular');
Route::match(['get', 'post'], 'comercial/proforma/index_proforma', 'comercial\ProformaController@index_proforma')->name('comercial.proforma.index_proforma');

//CAMBIAR ESTA RUTA DE AQUI NUEVO RECIBO DE COBRO 22226
Route::get('nuevo_recibo_de_cobro/{id_agenda}', 'contable\NuevoReciboCobroController@crear')->name('nuevorecibocobro.crear');
Route::get('nuevo_recibo_de_cobro/editar/{id}', 'contable\NuevoReciboCobroController@editar')->name('nuevorecibocobro.editar');
Route::get('nuevo_recibo_de_cobro/detalles/{id}', 'contable\NuevoReciboCobroController@detalles')->name('nuevorecibocobro.detalles');
Route::post('nuevo_recibo_de_cobro/guardar_producto', 'contable\NuevoReciboCobroController@guardar_producto')->name('nuevorecibocobro.guardar_producto');
Route::post('nuevo_recibo_de_cobro/actualizar/producto', 'contable\NuevoReciboCobroController@actualizar_producto')->name('nuevorecibocobro.actualizar_producto');
Route::post('nuevo_recibo_de_cobro/actualizar/producto/descripcion', 'contable\NuevoReciboCobroController@actualizar_descripcion')->name('nuevorecibocobro.actualizar_descripcion');
Route::post('nuevo_recibo_de_cobro/eliminar/producto', 'contable\NuevoReciboCobroController@eliminar_producto')->name('nuevorecibocobro.eliminar_producto');
Route::post('nuevo_rc/cabecera', 'contable\NuevoReciboCobroController@actualizar_cabecera')->name('nuevorecibocobro.actualizar_cabecera');
Route::get('nuevo_rc/formas_pago/{id}', 'contable\NuevoReciboCobroController@formas_pago')->name('nuevorecibocobro.formas_pago');
Route::post('nuevo_rc/guardar_formapago', 'contable\NuevoReciboCobroController@guardar_formapago')->name('nuevorecibocobro.guardar_formapago');
Route::post('nuevo_rc/eliminar/forma_pago', 'contable\NuevoReciboCobroController@eliminar_pago')->name('nuevorecibocobro.eliminar_pago');

Route::match(['get', 'post'],'proforma_comercial_pdf/{id_orden}', 'comercial\ProformaController@pdf_proforma')->name('comercial.proforma.pdf_proforma');
Route::get('nuevo_rc/validar/valores/{id_orden}', 'contable\NuevoReciboCobroController@validar_valores')->name('nuevorecibocobro.validar_valores');
Route::get('nuevo_rc/emitir/cierre/caja/{id_orden}', 'contable\NuevoReciboCobroController@emitir_recibo')->name('nuevorecibocobro.emitir_recibo');
Route::get('nuevo_rc/deducible/crear/item/xseguro/{id_orden}', 'contable\NuevoReciboCobroController@crear_deducible')->name('nuevorecibocobro.crear_deducible');

Route::get('comercial/deducible/crear/item/xseguro/{id_orden}', 'comercial\ProformaController@crear_deducible')->name('comercial.proforma.crear_deducible');

Route::get('nrc_descuentos/aprobacion', 'contable\NuevoReciboCobroController@lista_aprobacion')->name('nuevorecibocobro.lista_aprobacion');
Route::get('nrc_descuentos/aprobar/{id}', 'contable\NuevoReciboCobroController@aprobar')->name('nuevorecibocobro.aprobar');

Route::match(['get', 'post'], 'agrupador_proforma', 'comercial\ProformaController@mostrar_agrupador_proforma')->name('proforma.mostrar_agrupador_proforma');
Route::post('agrupador_proforma/guardar', 'comercial\ProformaController@guardar_agrupador')->name('proforma.guardar_agrupador');

//PLANTILLA
Route::match(['get', 'post'], 'proforma_plantilla_agrupador', 'comercial\ProformaController@index_plantilla')->name('proforma.index_plantilla');
Route::get('proforma_plantilla_agrupador/detalle/{id}', 'comercial\ProformaController@index_plantilla_detalle')->name('proforma.index_plantilla_detalle');
Route::post('proforma_plantilla_agrupador/guardar', 'comercial\ProformaController@guadar_producto_plantilla')->name('proforma.guardar_producto_detalle');
Route::get('proforma_plantilla_agrupador/crear_plantilla','comercial\ProformaController@crear_plantilla')->name('proforma.crear_plantilla');
Route::post('proforma_plantilla_agrupador/store_plantilla', 'comercial\ProformaController@store_plantilla')->name('proforma.store_plantilla');
Route::get('proforma_plantilla_agrupador/editar_plantilla', 'comercial\ProformaController@editar_plantilla')->name('proforma.editar_plantilla');
Route::post('proforma_plantilla_agrupador/update_planilla', 'comercial\ProformaController@update_plantilla')->name('proforma.update_plantilla');
Route::get('proforma_plantilla_agrupador/delete/{id}', 'comercial\ProformaController@eliminar_producto_plantilla')->name('proforma.eliminar_producto_detalle');
//MANTENIMIENTO TIPO TUBOS
Route::get('laboratorio/mantenimientos_tubos/index', 'laboratorio\Tipo_TuboController@index')->name('tipo_tubo.index');
Route::get('laboratorio/mantenimientos_tubos/crear', 'laboratorio\Tipo_TuboController@crear')->name('tipo_tubo.crear');
Route::post('laboratorio/mantenimientos_tubos/store', 'laboratorio\Tipo_TuboController@store')->name('tipo_tubo.store');
Route::get('laboratorio/mantenimientos_tubos/editar{id}', 'laboratorio\Tipo_TuboController@editar')->name('tipo_tubo.editar');
Route::post('laboratorio/mantenimientos_tubos/update', 'laboratorio\Tipo_TuboController@update')->name('tipo_tubo.update');
Route::match(['get', 'post'],'laboratorio/mantenimientos_tubos/delete', 'laboratorio\Tipo_TuboController@delete')->name('tipo_tubo.delete');




Route::get('labs_modal/examenes/query/{id}', 'laboratorio\OrdenController@query_examenes')->name('orden.query.tubo');
Route::get('labs_modal/examenes/query/vista', 'laboratorio\OrdenController@query_vista')->name('orden.query.vista');

//GEORGE
Route::match(['get', 'post'], 'comercial/buscarProductoCoincidencia', 'comercial\ProdTarifarioController@buscar_productos_contenido')->name('prodtarifario.buscar_productos_contenido');
Route::match(['get', 'post'], 'comercial/buscar/productos/todos', 'comercial\ProdTarifarioController@buscar_todos_productos')->name('prodtarifario.buscar_todos_productos');

Route::get('webservices_labs', 'ImportarController@valores_guayaquil')->name('laborario.guayaquil'); 

Route::get('comercial/producto_tarifario/excel', 'comercial\ProdTarifarioController@excel')->name('prodtarifario.excel');



