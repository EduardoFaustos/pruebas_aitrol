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

/*Nomina*/
Route::get('contable/rol/pago/buscar_cuentas', 'contable\RolPagoController@buscar_cuentas')->name('rol_pago.buscar_cuentas');
Route::match(['get', 'post'], 'contable/nomina', 'contable\NominaController@index')->name('nomina.index');
Route::get('contable/nomina/crear/', 'contable\NominaController@crear')->name('nomina.crear');
Route::post('contable/nomina/buscarIdentificacion/', 'contable\NominaController@identificacion')->name('nomina.identificacion');
Route::get('contable/nomina/anular/{id}', 'contable\NominaController@anular')->name('nomina.anular');
//Route::post('contable/nomina/guardar', 'contable\NominaController@store')->name('nomina.store');
Route::match(['get', 'post'], 'contable/nomina/guardar', 'contable\NominaController@store')->name('nomina.store');
Route::get('contable/nomina/revisar/{id}', 'contable\NominaController@revisar')->name('nomina.revisar');
Route::match(['get', 'post'], 'contable/nomina/actualizar/', 'contable\NominaController@update')->name('nomina.actualizar');
Route::match(['get', 'post'], 'contable/nomina/buscar', 'contable\NominaController@buscar')->name('nomina.buscar');
Route::get('contable/nomina/egresos/empleado/{id}', 'contable\NominaController@crear_egresos')->name('nomina.crear_egresos');
Route::post('contable/nomina/egresos/empleado/guardar', 'contable\NominaController@store_egresos')->name('nomina.store_egresos');

/*Rol de Pago*/
Route::get('contable/rol/pago/create/{id}', 'contable\RolPagoController@crear_rol_pago')->name('rol_pago.create');
Route::post('contable/rol/pago/store', 'contable\RolPagoController@store_rol_pago')->name('rol_pago.store');
Route::post('contable/rol/pago/update', 'contable\RolPagoController@update_rol_pago')->name('rol_pago.update');
Route::get('comprobante/rol/pago/{id_rolpag}', 'contable\RolPagoController@imprimir_rol_pago')->name('rol_pago.imprimir');
Route::get('contable/rol/pago/index/{id}/{id_empresa}', 'contable\RolPagoController@index')->name('rol_pago.index');
Route::get('contable/rol/pago/anular/{id}', 'contable\RolPagoController@anular')->name('rol_pago.anular');
Route::match(['get', 'post'], 'contable/rol/pago/buscar', 'contable\RolPagoController@buscar')->name('rol_pago.buscar');
Route::post('contable/rol/pago/existe_anticipo', 'contable\RolPagoController@existe_valor')->name('rol_pago.existe_anticipo');
Route::post('contable/rol/pago/existe_prestamos', 'contable\RolPagoController@verifica_existe_prestamos')->name('rol_pago.existe_prestamos');
Route::post('contable/rol/pago/empleado/buscar_anticipo', 'contable\RolPagoController@verifica_existe_anticipo')->name('existe_anticipo.empleado');
Route::get('contable/rol/pago/editar/{id}', 'contable\RolPagoController@editar_rol')->name('rol_pago.editar');

/*Rol de Provisiones Sociales*/
Route::match(['get', 'post'], 'contable/rol/provisiones/sociales', 'contable\ProvisionesSocialesController@index')->name('rol_provisiones.index');
Route::match(['get', 'post'], 'contable/rol/provisiones/sociales/buscar', 'contable\ProvisionesSocialesController@buscar')->name('rol_provisiones.buscar');

/*Configuraciones Valores*/
Route::match(['get', 'post'], 'contable/configuracion/valores', 'contable\NominaConfiguracionController@index')->name('config_valor.index');

Route::get('contable/configuracion/valores/create', 'contable\NominaConfiguracionController@crear_configuracion_valores')->name('configuracion_valores.create');

Route::post('contable/configuracion/valores/guardar', 'contable\NominaConfiguracionController@store')->name('config_valor.store');

Route::get('contable/configuracion/valores/editar/{id}', 'contable\NominaConfiguracionController@edit')->name('configuracion_valores.editar');

Route::post('contable/configuracion/valores/actualizar', 'contable\NominaConfiguracionController@update')->name('configuracion_valores.update');

Route::get('contable/configuracion/valores/anular/{id}', 'contable\NominaConfiguracionController@anular')->name('configuracion_valores.anular');

Route::match(['get', 'post'], 'contable/configuracion/valores/buscar', 'contable\NominaConfiguracionController@buscar')->name('config_valor.buscar');

/*Lista Roles de Pago*/
Route::match(['get', 'post'], 'contable/buscador/rol/pago', 'contable\RolPagoController@buscador_index')->name('buscador_rol.index');
Route::post('contable/buscador/rol/pago/search', 'contable\RolPagoController@buscador_roles')->name('buscador_roles.pago');

/*Contable*/
Route::match(['get', 'post'], 'contable/buscador/rol/_contable', 'contable\NominaReporteRolContableController@buscadorcont_index')->name('buscador_rol_contable.index');

//Route::match(['get', 'post'], 'contable/buscador/rol/pago/search', 'contable\RolPagoController@buscador_search')->name('buscador_rol.search');
//Exportar Excel
Route::match(['get', 'post'], 'contable/buscador/rol/pago/exportar_excel', 'contable\RolPagoController@exportar_excel')->name('exportar_reporte');

/*Modal Editar Tipo de Pago*/
Route::get('contable/modal/edit/pago/{id}', 'contable\RolPagoController@obtener_edit_pago')->name('editar_pago.modal');

/*Prestamos Recibidos Empleados*/
Route::match(['get', 'post'], 'contable/nomina/prestamos/empleados', 'contable\PrestamosEmpleadosController@index')->name('prestamos_empleado.index');
Route::get('contable/nomina/prestamos/empleados/crear/{id_nomina}/{cedula}', 'contable\PrestamosEmpleadosController@crear')->name('prestamos_empleado.crear');
Route::post('contable/nomina/prestamos/store', 'contable\PrestamosEmpleadosController@store_prestamos_empl')->name('prestamos_empleado.store');
Route::match(['get', 'post'], 'contable/nomina/prestamo/empleado/buscar', 'contable\PrestamosEmpleadosController@search_prestamo')->name('prestamo_empleado.search');

/*Anticipo Recibido Empleado*/
Route::match(['get', 'post'], 'contable/nomina/anticipos/empleados', 'contable\NominaAnticiposController@index_anticipos')->name('anticipos_empleado.index');
Route::get('contable/nomina/anticipos/empleados/crear/{id_nomina}/{nombre1}/{nombre2}/{apellido1}/{apellido2}/{cedula}', 'contable\NominaAnticiposController@crear_anticipo_empleado')->name('anticipo_empleado.crear');
Route::post('contable/nomina/anticipos/empleados/store', 'contable\NominaAnticiposController@store_anticipo_empleado')->name('anticipo_empleado.store');
Route::match(['get', 'post'], 'contable/nomina/anticipo/empleado/buscar', 'contable\NominaAnticiposController@search_anticipo')->name('anticipo_empleado.search');

/*Otros Anticipos Empleados 25-10-2020*/
Route::get('contable/nomina/otros_anticipos/empleados/crear/{id_nomina}/{cedula}', 'contable\OtrosAnticiposEmpleadosController@crear_otros_anticipo_empleado')->name('otros_anticipo_empleado.crear');
Route::post('contable/nomina/otros_anticipos/empleados/store', 'contable\OtrosAnticiposEmpleadosController@store_otros_anticipo_empleado')->name('otros_anticipo_empleado.store');
Route::post('contable/rol/pago/empleado/buscar_otro_anticipo', 'contable\RolPagoController@verifica_existe_otros_anticipo')->name('existe_otros_anticipo.empleado');
Route::match(['get', 'post'], 'contable/modal_anticipo/modal_ver_anticipos/{id_nomina}/{cedula}', 'contable\OtrosAnticiposEmpleadosController@modal_ver_anticipos')->name('modal_anticipo.modal_ver_anticipos');
Route::match(['get', 'post'], 'contable/modal_anticipo/modal_editar_anticipos/{id_nomina}/{cedula}', 'contable\OtrosAnticiposEmpleadosController@modal_editar_anticipos')->name('modal_anticipo.modal_editar_anticipos');
/*Calculo Anticipo Quincena*/
Route::match(['get', 'post'], 'contable/nomina/anticipo/quincena', 'contable\NominaAnticiposController@index')->name('nomina_anticipos.index');
Route::post('contable/nomina/anticipo/search', 'contable\NominaAnticiposController@buscar_anticipos_quincena')->name('anticipos_quincena.buscar');
Route::post('contable/nomina/anticipo/calculo', 'contable\NominaOtrosAnticiposController@obtener_anticipo_quincena')->name('anticipos_quincena.valor');
Route::get('contable/nomina/anticipo/reporte', 'contable\NominaAnticiposController@obtener_reporte_quincena')->name('anticipos_quincena.reporte');
Route::get('contable/nomina/anticipo/editar/{id}/{idempleado}/{idempresa}', 'contable\NominaAnticiposController@edit_anticipo')->name('anticipos_quincena.editar');
Route::post('contable/nomina/anticipo/actualizar', 'contable\NominaAnticiposController@update')->name('anticipos_quincena.update');

/*Reportes Generales*/
Route::match(['get', 'post'], 'contable/nomina/reportes/empleados/crear', 'contable\NominaReporteEmpleadoController@index')->name('reportes_empl.index');
Route::match(['get', 'post'], 'contable/nomina/reportes/datos/empleados', 'contable\NominaReporteEmpleadoController@reporte_datos_empleados')->name('reporte_datos_empl');
Route::match(['get', 'post'], 'contable/nomina/reportes/roles/pago', 'contable\NominaReporteRolController@index')->name('reportes_rol.index');
Route::match(['get', 'post'], 'contable/nomina/reportes/datos/rol', 'contable\NominaReporteRolController@reporte_datos_rol_pago')->name('reporte_datos.rol');
Route::match(['get', 'post'], 'contable/nomina/reportes/banco', 'contable\NominaReporteBancoController@index')->name('reportes_banco.index');
Route::match(['get', 'post'], 'contable/nomina/reportes/datos/banco', 'contable\NominaReporteBancoController@reporte_datos_banco')->name('reporte_datos.banco');
Route::match(['get', 'post'], 'contable/nomina/reportes/roles/pago_contable', 'contable\NominaReporteRolContableController@index')->name('reportes_rolcontable.index');

/*Rol contable*/
Route::match(['get', 'post'], 'contable/nomina/reportes/datos/rol_contable', 'contable\NominaReporteRolContableController@reporte_datos_rol_pago_contable')->name('reporte_datos_contable.rol');

//Visualizar Pdf Curriculum
Route::get('contable/nomina/mostrar_foto_curriculum/{id}', 'contable\NominaController@obtener_imagen_curriculum')->name('nomina_imagen_curr.modal');

//Descargar Archivo Pdf Curriculum
Route::get('descarga_archivo_curriculum/{id}', 'contable\NominaController@descarga_archivo_curriculum');

//Visualizar Pdf Ficha Tecnica
Route::get('contable/nomina/mostrar_foto_ficha/{id}', 'contable\NominaController@obtener_imagen_ficha')->name('nomina_imagen_ficha.modal');

//Descargar Archivo Pdf Ficha Tecnica
Route::get('descarga_archivo_ficha/{id}', 'contable\NominaController@descarga_archivo_ficha');

//Visualizar Pdf Ficha Ocupacional
Route::get('contable/nomina/mostrar_foto_ocupacional/{id}', 'contable\NominaController@obtener_imagen_ocupacional')->name('nomina_imagen_ocupacional.modal');

//Descargar Archivo Pdf Ficha Ocupacional
Route::get('descarga_archivo_ocupacional/{id}', 'contable\NominaController@descarga_arch_ocupacional');

//Registro de Asientos de Diario-Rol Pago Empleados por Mes y Año
Route::post('contable/rol/pago/asientos_diario/store', 'contable\RolPagoController@store_asientos_diario')->name('asientos_rolpago.store');

//Vista de Anticipos Empleados por Empresa Index
Route::match(['get', 'post'], 'contable/nomina/anticipo/empleado/empresa', 'contable\NominaAnticiposEmpresaController@index')->name('nomina_anticipos_empleados.empresa');

//Busqueda de Empleados por Empresa Efectuar Anticipos
Route::post('contable/nomina/empleados/anticipos', 'contable\NominaAnticiposEmpresaController@buscar_empleado_anticipo')->name('busqueda_empleado.anticipo');

//Carga Tabla Quirografario
Route::get('contable/rol_pago/carga_listado/quirografario/{id}', 'contable\RolPagoController@listado_cuota_quirografario')->name('carga_listado.quirografario');

//Carga Tabla Hipotecario
Route::get('contable/rol_pago/carga_listado/hipotecario/{id}', 'contable\RolPagoController@listado_cuota_hipotecario')->name('carga_listado.hipotecario');

/*Calculo Anticipos Funcionaro*/
Route::match(['get', 'post'], 'contable/nomina/anticipo/funcionario', 'contable\NominaAnticiposFuncionarioController@index')->name('nomina_anticipos_funcionario.index');

/*Calculo Anticipo 1era Quincena*/
Route::get('contable/nomina/anticipos/1era_quincena', 'contable\NominaOtrosAnticiposController@index')->name('nomina_anticipos_1eraquincena.index');

Route::match(['get', 'post'], 'contable/nomina/anticipos/1era_quincena/buscar/empresa', 'contable\NominaOtrosAnticiposController@comprobar_empresa')->name('nominaotrosanticipos.comprobar_empresa');

/*Anticipo*/
//Route::get('contable/nomina/anticipo/individual/{id_nomina}', 'contable\NominaOtrosAnticiposController@obtener_anticipo_individual')->name('anticipo_individual.valor');
Route::match(['get', 'post'], 'contable/nomina/anticipo/individual/{id_nomina}', 'contable\NominaOtrosAnticiposController@obtener_anticipo_individual')->name('anticipo_individual.valor');

//Prestamo Reporte
Route::match(['get', 'post'], 'contable/prestamo/reporte', 'contable\PrestamosEmpleadosController@reporte_prestamo')->name('reporte_prestamo.index');
Route::match(['get', 'post'], 'contable/prestamo/buscar', 'contable\PrestamosEmpleadosController@buscar')->name('reportes_prestamos.buscar');

/*Pdf Prestamos*/
Route::get('contable/nomina/prestamos/empleados/pdf_prestamos/{id}', 'contable\PrestamosEmpleadosController@pdfprestamos')->name('pdf_prestamos_egreso');

/*Pdf Anticipos 1ERA Quincena*/
Route::get('contable/nomina/anticipo_quincena/empleados/pdf_anticipo/{id}', 'contable\NominaAnticiposController@pdf_anticipo_quincena')->name('pdf_anticipo_egreso');

/*Pdf Otros Anticipos*/
Route::get('contable/nomina/otros_anticipos/empleados/pdf_otros_anticipo/{id}', 'contable\OtrosAnticiposEmpleadosController@pdf_otros_anticipo')->name('pdf_otros_anticipo_egreso');

//Route::match(['get', 'post'], 'hc4/cie10/agregar/consulta', 'hc4\ConsultaController@agregar_cie10')->name('hc4/epicrisis.agregar_cie10');

Route::match(['get', 'post'], 'contable/nomina/anticipo_1era_quincena/valor', 'contable\NominaOtrosAnticiposController@registrar_anticipo_quincena')->name('anticipo_primera.quincena');

//Saldos Iniciales De Años Anteriores
Route::get('contable/nomina/saldos_iniciales/empleados/crear/{id_nomina}/{cedula}', 'contable\SaldoInicialEmpleadosController@crear_saldo_inicial')->name('saldoinicial_empleado.crear');

Route::post('contable/nomina/saldos_iniciales/empleados/store', 'contable\SaldoInicialEmpleadosController@store_saldo_inicial')->name('saldo_inicial_empleado.store');

Route::post('contable/rol/pago/empleado/buscar_saldo_inicial', 'contable\RolPagoController@verifica_existe_saldo_inicial')->name('existe_saldo_inicial.prestamo');

Route::match(['get', 'post'], 'contable/nomina/asiento_anticipo_1era_quincena/sum_anticipos', 'contable\NominaOtrosAnticiposController@registrar_asiento_anticipo_quincena')->name('asiento_anticipo_primera.quincena');

//Buscador Rol de Pago por Empresa Opcion Contabilidad
Route::post('contable/buscador/rol/pago/search_contable', 'contable\NominaReporteRolContableController@buscador_roles_contable')->name('buscador_roles_contable.pago');

//new Imprimir rol pago
//Route::get('comprobante/rol/pago/download/{id}', 'contable\RolPagoController@imprimir_new_rol_pago')->name('rol_pago.imprimir2');
Route::get('comprobante/rol/pago/download/rol_por_anio/{mes}/{anio}', 'contable\RolPagoController@imprimir_new_rol_pago')->name('rol_pago.imprimir2');
Route::get('comprobante/rol/pago/download/rol_por_anio/')->name('rol_pago.imprimir_2_2');
Route::post('comprobante/rol/pago/download/rol_por_anio/comprobar', 'contable\RolPagoController@existe_new_rol_pago')->name('rol_pago.comprobar');

//visualiza otros anticipos de empleados
Route::match(['get', 'post'], 'contable/nomina/otros_anticipos/empleados', 'contable\OtrosAnticiposEmpleadosController@index')->name('otros_anticipos_empleado.index');
Route::match(['get', 'post'], 'contable/nomina/otros_anticipos/buscar', 'contable\OtrosAnticiposEmpleadosController@search_otros_anticipos')->name('otros_anticipo_empleado.search');

//Modal Otros Anticipos
Route::match(['get', 'post'], 'contable/nomina/update_valor_anticipo_quinc/{id_nomina}', 'contable\NominaOtrosAnticiposController@actualiza_valor_anticipos')->name('update_anticipo_1era_quince');

//Guarda Valor Anticipos 1era Quincena
Route::match(['get', 'post'], 'contable/nomina/store_valor_anticipo', 'contable\NominaOtrosAnticiposController@store_valor_anticipo')->name('store_anticipo_valor.quincena');

//Imprimir Anticipos 1era Quincena
Route::post('contable/nomina/download/imprime_1era_quincena', 'contable\NominaOtrosAnticiposController@imprime_anticipos_quincena')->name('pdf_anticipos_quincena.descargar');

//Pdf Anticipo 1era Quincena
Route::get('contable/nomina/pdf_anticipo_1eraquincena/{mes}/{anio}', 'contable\NominaOtrosAnticiposController@obtener_pdf_anticipo_quincena')->name('descarga_pdf_anticipo.quincena');
Route::get('contable/nomina/pdf_anticipo_quincena2/{mes}/{anio}', 'contable\NominaOtrosAnticiposController@obtener_pdf_anticipo_quincena')->name('descarga_pdf_anticipo.quincena2');

Route::get('contable/nomina/pdf_anticipo_1eraquincena/')->name('descarga_pdf_anticipo.quincena');

//planillas prestamos y horas extras
Route::match(['get', 'post'], 'contable/nomina/plantillas/prestamos', 'contable\PlantillasNominaController@plantillas_prestamos')->name('plantillas_nomina.plantillas_prestamos');

Route::post('contable/nomina/prestamos/subir_archivo', 'contable\PlantillasNominaController@subir_prestamos')->name('plantillas_nomina.subir_prestamos');

Route::match(['get', 'post'], 'contable/nomina/plantillas/horas_extras', 'contable\PlantillasNominaController@plantillas_horas_extras')->name('plantillas_nomina.horas_extras');

Route::post('contable/nomina/prestamos/subir_horas_extras', 'contable\PlantillasNominaController@subir_horas_extras')->name('plantillas_nomina.subir_horas_extras');

Route::get('contable/nomina/rol/excel_plantilla_prestamo', 'contable\PlantillasNominaController@excel_plantilla_prestamo')->name('plantillas_nomina.excel_plantilla_prestamo');

Route::get('contable/nomina/rol/excel_plantilla_horas', 'contable\PlantillasNominaController@excel_plantilla_horas')->name('plantillas_nomina.excel_plantilla_horas');

//prestamos

Route::get('contable/nomina/prestamos/visualizar', 'contable\PrestamosEmpleadosController@prestamos_visualizar')->name('prestamos_empleados.prestamos_visualizar');

Route::get('contable/nomina/modal_prestamos/{id_prestamo}', 'contable\PrestamosEmpleadosController@modal_prestamos')->name('prestamos_empleados.modal_prestamos');

Route::match(['get', 'post'], 'contable/nomina/reportes/prestamos_saldos', 'contable\PrestamosEmpleadosController@prestamos_saldos')->name('prestamos_empleados.prestamos_saldos');

Route::match(['get', 'post'], 'contable/nomina/reportes/excel_reporte_prestamo', 'contable\PrestamosEmpleadosController@excel_reporte_prestamo')->name('prestamos_empleados.excel_reporte_prestamo');

Route::get('contable/nomina/utilidades/prestamos', 'contable\PrestamosEmpleadosController@index_cruce')->name('prestamos_empleados.index_cruce');
Route::get('contable/nomina/modal_utilidades/{id}', 'contable\PrestamosEmpleadosController@modal_utilidades')->name('prestamos_empleados.modal_utilidades');
Route::get('contable/nomina/modal_utilidades_saldos/{id}', 'contable\PrestamosEmpleadosController@modal_utilidades_saldos')->name('prestamos_empleados.modal_utilidades_saldos');
Route::post('contable/nomina/asientos/guardar', 'contable\PrestamosEmpleadosController@asientos_guardar')->name('prestamos_empleados.asientos_guardar');
Route::post('contable/nomina/guardar_mod', 'contable\PrestamosEmpleadosController@guardar_mod')->name('prestamos_empleados.guardar_mod');
Route::post('contable/nomina/guardar_mod_saldos', 'contable\PrestamosEmpleadosController@guardar_mod_saldos')->name('prestamos_empleados.guardar_mod_saldos');
//SALDOS INICALES
Route::get('contable/nomina/saldos_iniciales/index_saldos', 'contable\PrestamosEmpleadosController@index_saldos')->name('prestamos_empleados.index_saldos');
Route::get('contable/nomina/modal_saldos/{id_saldo}', 'contable\PrestamosEmpleadosController@modal_saldos')->name('prestamos_empleados.modal_saldos');
Route::match(['get', 'post'], 'contable/nomina/saldos_iniciales/buscar', 'contable\PrestamosEmpleadosController@buscar_saldos')->name('prestamo_empleado.buscar_saldos');
Route::match(['get', 'post'], 'contable/nomina/cruce/pdf/{mes}/{anio}', 'contable\PrestamosEmpleadosController@pdf_cruce')->name('prestamos_empleados.pdf_cruce');

Route::match(['get', 'post'], 'contable/nomina/actualizar_saldo_prestamo', 'contable\PrestamosEmpleadosController@actualizar_saldo_prestamo')->name('prestamo_empleado.actualizar_saldo_prestamo');

Route::match(['get', 'post'], 'contable/nomina/busca_quincena', 'contable\NominaOtrosAnticiposController@busca_quincena')->name('prestamo_empleado.busca_quincena');
Route::match(['get', 'post'],'contable/eliminar/log', 'contable\RolPagoController@log_eliminar')->name('rol_log_eliminar');

//VH CREAR ROL DE PAGO
Route::match(['get', 'post'],'nuevo_rol/contable/roles/index/{id_nomina}', 'contable\Nuevo_RolController@index')->name('nuevo_rol.index');
Route::get('nuevo_rol/contable/roles/create/{id_nomina}', 'contable\Nuevo_RolController@crear')->name('nuevo_rol.crear');
Route::post('nuevo_rol/contable/roles/editar/actualizar', 'contable\Nuevo_RolController@update')->name('nuevo_rol.update');
Route::post('nuevo_rol_o/contable/roles/editar/actualizar_observaciones', 'contable\Nuevo_RolController@update_observaciones')->name('nuevo_rol.update_observaciones');
Route::get('detalle_prestamos_rol/contable/{id}', 'contable\Nuevo_RolController@eliminar_prestammo_rol')->name('nuevo_rol.eliminar_prestammo_rol');
Route::get('detalle_prestamos_rol/contable/recargar/{id_nomin}/{id_rol}', 'contable\Nuevo_RolController@recargar_prestammo_rol')->name('nuevo_rol.recargar_prestammo_rol');
Route::get('detalle_saldos_rol/contable/{id}', 'contable\Nuevo_RolController@eliminar_saldo_rol')->name('nuevo_rol.eliminar_saldo_rol');
Route::get('detalle_saldos_rol/contable/recargar/{id_nomin}/{id_rol}', 'contable\Nuevo_RolController@recargar_saldo_rol')->name('nuevo_rol.recargar_saldo_rol');
Route::get('anticipos_rol/contable/{id}', 'contable\Nuevo_RolController@eliminar_anticipo_rol')->name('nuevo_rol.eliminar_anticipo_rol');
Route::get('anticipos_rol/contable/recargar/{id_nomin}/{id_rol}', 'contable\Nuevo_RolController@recargar_anticipo_rol')->name('nuevo_rol.recargar_anticipo_rol');

Route::match(['get', 'post'], 'cuotas_rol/quirografario/contable/{id_rol}', 'contable\Nuevo_RolController@cargar_cuota_quirografario')->name('nuevo_rol.cargar_cuota_quirografario');
Route::get('cuota_rol/quirografario/eliminar/contable/{id}', 'contable\Nuevo_RolController@eliminar_cuota_quiro')->name('nuevo_rol.eliminar_cuota_quiro');

Route::match(['get', 'post'], 'cuotas_rol/hipotecario/contable/{id_rol}', 'contable\Nuevo_RolController@cargar_cuota_hipotecario')->name('nuevo_rol.cargar_cuota_hipotecario');
Route::get('cuota_rol/hipotecario/eliminar/contable/{id}', 'contable\Nuevo_RolController@eliminar_cuota_hipo')->name('nuevo_rol.eliminar_cuota_hipo');

Route::get('nuevo_rol_detalle/prestamos/saldos/masivo','contable\Nuevo_RolController@masivo_prestamo_saldo')->name('rol_pago.masivo_prestamo_saldo');
Route::post('nuevo_rol_forma_pago/guardar', 'contable\Nuevo_RolController@forma_pago_store')->name('nuevo_rol.forma_pago_store');
Route::get('nuevo_rol/editar/guardar/{id}', 'contable\Nuevo_RolController@editar_nuevo_rol')->name('nuevo_rol.editar_nuevo_rol');

Route::get('nuevo_rol_e/pago/eliminar/{id}', 'contable\Nuevo_RolController@eliminar_rol')->name('nuevo_rol.eliminar_rol');

Route::get('nuevo_rol_detalle/prestamos/saldos/masivo','contable\Nuevo_RolController@masivo_prestamo_saldo')->name('rol_pago.masivo_prestamo_saldo');

Route::get('nrol_masivo_anticipos','contable\Nuevo_RolController@masivo_anticipos')->name('nuevo_rol.masivo_anticipos');

Route::get('nuevo_rol/contable/prestamos_saldos/index/{id_user}','contable\Nuevo_RolController@index_prestamos_saldos')->name('nuevo_rol.index_prestamos_saldos');

Route::get('nuevo_rol/contable/reporte/prestamos_saldos/{id_user}','contable\Nuevo_RolController@excel_prestamos_saldos')->name('nuevo_rol.excel_prestamos_saldos');

//28-9-2021 PROCESOS MASIVOS
Route::match(['get', 'post'], 'masivos_nuevo_rol/search', 'contable\Nuevo_RolController@masivo_search')->name('nuevo_rol.masivo_search');
Route::post('masivos_nuevo_rol/genera_roles', 'contable\Nuevo_RolController@masivo_genera_roles')->name('nuevo_rol.masivo_genera_roles');
Route::get('masivos_nuevo_rol/certificar/{id}/{certificar}', 'contable\Nuevo_RolController@masivo_certificar')->name('nuevo_rol.masivo_certificar');
Route::post('masivos_nuevo_rol/certifica/masivo', 'contable\Nuevo_RolController@masivo_certificar_mes')->name('nuevo_rol.masivo_certificar_mes');
Route::post('masivos_horario_extra', 'contable\Nuevo_RolController@masivos_horario_extra')->name('nuevo_rol.masivos_horario_extra');
Route::get('mnr_recalcular_db/{id}', 'contable\Nuevo_RolController@recalcular_db')->name('nuevo_rol.recalcular_db');
Route::get('procesos_detalle_rol/{id}', 'contable\Nuevo_RolController@detalle_he_rol')->name('nuevo_rol.detalle_he_rol');
Route::get('procesos_detalle_rol_iess/{id}', 'contable\Nuevo_RolController@detalle_iess_rol')->name('nuevo_rol.detalle_iess_rol');

Route::post('masivos_prestamos', 'contable\Nuevo_RolController@masivos_prestamos')->name('nuevo_rol.masivos_prestamos');

Route::post('masivos_horario_extra/valida_ejecutado', 'contable\Nuevo_RolController@he_valida_ejecutado')->name('nuevo_rol.he_valida_ejecutado');
Route::post('masivos_prestamos/valida_ejecutado', 'contable\Nuevo_RolController@p_valida_ejecutado')->name('nuevo_rol.p_valida_ejecutado');


//MANTENIEMTO PRESTAMOS
Route::get('mantemiento_prestamos/index', 'contable\MantenimientoPrestamosController@manteniemto_index')->name('mantenimientoprestamos.index');

// anticipo quincena nuevo
Route::match(['get', 'post'], 'contable/nomina/anticipo_quincena/index_quincena', 'contable\NominaQuincenaController@index_quincena')->name('nominaquincena.index_quincena');
Route::match(['get', 'post'], 'contable/nomina/anticipo_quincena/busca_quincena', 'contable\NominaQuincenaController@busca_quincena')->name('nominaquincena.busca_quincena');
Route::match(['get', 'post'], 'contable/nomina/crea_anticipo', 'contable\NominaQuincenaController@crea_anticipo')->name('nominaquincena.crea_anticipo');
Route::match(['get', 'post'], 'contable/nomina/edit_anticipo/{id_valida}','contable\NominaQuincenaController@edit_anticipo')->name('nominaquincena.edit_anticipo');
Route::match(['get', 'post'], 'contable/nomina/tabla_detalle/{id_valida}','contable\NominaQuincenaController@tabla_detalle')->name('nominaquincena.tabla_detalle');
Route::match(['get', 'post'], 'contable/nomina/actualiza_anticipo/{id_anticipo}','contable\NominaQuincenaController@actualiza_anticipo')->name('nominaquincena.actualiza_anticipo');
Route::match(['get', 'post'], 'contable/nomina/guarda_asiento_anticipo','contable\NominaQuincenaController@guarda_asiento_anticipo')->name('nomina.guarda_asiento_anticipo');

Route::get('nuevos_asientos_roles/rol/{anio}/{mes}', 'contable\Nuevo_RolController@asientos_por_generar')->name('nuevo_rol.asientos_por_generar');
Route::get('nuevos_asientos_roles/rol/{id}', 'contable\Nuevo_RolController@detalle_asientos_rol')->name('nuevo_rol.detalle_asientos_rol');
Route::get('nuevos_asientos_roles/{id}', 'contable\Nuevo_RolController@generar_asientos')->name('nuevo_rol.generar_asientos');
Route::post('pagos_de_roles/nuevos_asientos', 'contable\Nuevo_RolController@pago_de_roles')->name('nuevo_rol.pago_de_roles');
Route::post('roles_aportes_patronales/nuevos_asientos', 'contable\Nuevo_RolController@aportes_patronales')->name('nuevo_rol.aportes_patronales');
Route::post('roles_aportes_patronales/pago_aportes', 'contable\Nuevo_RolController@pago_aportes_patronales')->name('nuevo_rol.pago_aportes_patronales');

Route::post('nomina_beneficios_iess', 'contable\Nuevo_RolController@asientos_beneficios_sociales')->name('nuevo_rol.asientos_beneficios_sociales');


//MASIVO DE SUCURSALES EN NOMINA
Route::get('proceso_masivo/sucursales_nomina', 'contable\NominaController@masivo_sucursales_nomina')->name('nomina.masivo_sucursales_nomina');



