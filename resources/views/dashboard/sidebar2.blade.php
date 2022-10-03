<!-- split buttons box -->
<style>
    .btn{
        font-size: 15px;
        font-weight: bold;
    }
</style>
<div class="content">
    <!-- <div class="box-header">
        <h3 class="box-title">Menu</h3>
    </div> -->
    <div class="box-body">
        <!-- Split button -->
        <div class="margin">
            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                    <a href="{{ route('user-management.index') }}" class="btn btn-primary" style="width: 100%; height: 50px; line-height: 35px;"><i class="fa fa-fw fa-users"></i> ADMINISTRACI&Oacute;N DE USUARIOS</a>
                </div>
            </div>
            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 col-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                    <a href="{{ url('agenda') }}" class="btn btn-primary"  style="width: 100%; height: 50px; line-height: 35px;"><i class="fa fa-calendar"></i> AGENDA DE DOCTORES</a>
                </div>
            </div>
            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                    <a href="{{ url('paciente') }}" class="btn btn-primary" style="width: 100%; height: 50px; line-height: 35px;"><i class="fa fa-fw fa-users"></i> ADMINISTRACI&Oacute;N DE PACIENTES</a>
                </div>
            </div>
            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                    <button type="button" class="btn btn-primary"  style="width: 90%; height: 50px;"><i class="glyphicon glyphicon-book"></i> SISTEMA CONTABLE</button>
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" style="width: 10%; height: 50px;">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" style="width: 100%;">

                        <li class="dropdown">
                            <a href="#"> <i class="fa fa-book"> </i> Contabilidad
                                <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-rigth"></i>
                                </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="dropdown">
                                    <li><a href="{{ route('balancegeneral.index') }}">Balance General</a></li>
                                    <li><a href="{{ route('estadoresultados.index') }}">Estado de Resultados</a></li>
                                    <li><a href="{{ route('librodiario.index') }}">Asientos de Diario</a></li>
                                    <li><a href="{{ route('libro_mayor.index') }}">Libro Mayor</a></li>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown"><a href="{{ route('banco.index') }}"> <i class="fa fa-money"> </i>Banco y Caja</a></li>
                        <li class="dropdown"><a href="{{ route('porcentaje_retencion.index') }}"> <i class="fa fa-money"> </i>Retenciones_Mantenimiento</a></li>
                        <li class="dropdown">
                            <a href="#"> <i class="glyphicon glyphicon-list-alt"> </i>Transacciones
                                <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-rigth"></i>
                                </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="dropdown">
                                    <a href="#"><i class="glyphicon glyphicon-th-list"></i> Contables
                                        <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-rigth"></i>
                                        </span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li class="dropdown">
                                            <li><a href="{{ route('librodiario.index') }}">Asientos de Libro Diario</a></li>
                                            <li><a href="{{ route('retenciones_index') }}">Retenciones</a></li>
                                            <li><a href="{{ route('egresoa_index') }}">Egreso Acreedores</a></li>
                                            <li><a >Guías de Remisión</a></li>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="treeview">
                            <a href="#"> <i class="fa fa-cog"> </i>Mantenimiento
                                <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-rigth"></i>
                                </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="treeview">
                                    <li><a href="{{ route('plan_cuentas.index') }}">Plan de Cuentas</a></li>

                                    <li><a href="{{ route('rubros.index') }}">Rubros</a></li>
                                    <li><a href="{{ route('productos_servicios_index') }}">Agregar Productos o Servicios</a></li>
                                    <li><a href="#">Nota de Ingreso de Productos</a></li>
                                    <li><a href="#">Nota de Egreso de Productos</a></li>
                                    <li><a href="{{ route('configuraciones.index') }}">Configuraciones</a></li>
                                </li>
                            </ul>
                        </li>
                        <li class="treeview">
                            <a href="#"> <i class="glyphicon glyphicon-stats"> </i> Informes
                                <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-rigth"></i>
                                </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                <a><i class="glyphicon glyphicon-th-list"> </i> Contabilidad
                                    <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-rigth"></i>
                                    </span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="treeview">
                                    <li><a>Balance General</a></li>
                                    <li><a>Pérdidas y Ganancias</a></li>
                                    <li><a>Asientos de Diario</a></li>
                                    <li><a>Plan de Cuentas</a></li>
                                    </li>
                                </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="treeview">
                            <a href="#"> <i class="fa fa-shopping-cart"> </i>Ventas
                                <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-rigth"></i>
                                </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a>Orden de Venta</a></li>
                                <li class="treeview">
                                <li><a href="{{ route('venta_index') }}">Registro Factura de Ventas</a></li>
                                </li>
                            </ul>
                        </li>
                        <li class="treeview">
                            <a href="#"> <i class="glyphicon glyphicon-tag"> </i>Factura de Gastos
                                <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-rigth"></i>
                                </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="treeview">
                                <li><a href="{{ route('fact_contable_index') }}">Factura Contable</a></li>
                                <li><a href="{{ route('empresa.index') }}">Facturación</a></li>
                                </li>
                            </ul>
                        </li>
                        <li class="treeview">
                            <a href="#"> <i class="glyphicon glyphicon-user"> </i>Clientes
                                <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-rigth"></i>
                                </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="treeview">
                                <li><a href="{{ route('clientes.index') }}">Agregar Clientes</a></li>
                                </li>
                            </ul>
                        </li>
                        <li class="treeview">
                            <a href="#"> <i class="fa fa-shopping-basket"> </i>Compras
                                <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-rigth"></i>
                                </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="treeview">
                                <li><a href="{{ route('compras_index') }}">Registro Factura de Compras</a></li>
                                </li>
                            </ul>
                        </li>
                        <li class="treeview">
                            <a href="#"> <i class="fa fa-users"> </i>Empleados
                                <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-rigth"></i>
                                </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="treeview">
                                <li><a href="{{ route('empleados.index') }}">Agregra Vendedor/Recaudador</a></li>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="margin">
            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                    <a href="{{ route('observacion.index') }}" class="btn btn-primary" style="width: 100%; height: 50px; line-height: 35px;"><i class="glyphicon glyphicon-copy"></i> OBSERVACIONES GENERALES</a>
                </div>
            </div>
            <!-- <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                    <a href="{{ route('horario.index_admin') }}" class="btn btn-primary"  style="width: 100%; height: 50px; line-height: 35px;"><i class="fa fa-clock-o"></i> HORARIO DOCTORES</a>
                </div>
            </div> -->
            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                    <a href="{{ url('consultam ') }}" class="col-md-12 btn btn-primary" style="width: 100%; height: 50px; line-height: 35px;"><i class="fa fa-calendar-minus-o"></i> CONSULTAS & PROCEDIMIENTOS</a>
                </div>
            </div>
            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                    <a href="{{ route('historia_clinica.fullcontrol') }}" class="btn btn-primary" style="width: 100%; height: 50px; line-height: 35px;"><i class="fa fa-history"></i> PACIENTES DEL D&Iacute;A</a>
                </div>
            </div>
        </div>
        
        <div class="margin">
            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                    <button type="button" class="btn btn-primary" style="width: 90%; height: 50px;"><i class="fa fa-history"></i> RESULTADOS DE BIOPSIAS</button>
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" style="width: 10%; height: 50px;">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" style="width: 100%;" role="menu">
                        <li><a href="{{ route('biopsias.index') }}">Biopsias Portoviejo</a></li>
                        <li><a href="{{ route('biopsias.index') }}">Biopsias Guayaquil</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                    <button type="button" class="btn btn-primary"  style="width: 90%; height: 50px;"><i class="fa fa-television"></i> CONTROL DE PROCEDIMIENTOS <br> ENDOSCOPICOS</button>
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" style="width: 10%; height: 50px;">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" style="width: 100%;" role="menu">
                        <li><a href="{{ url('pentax') }}">Control Pentax</a></li>
                        <li><a href="{{ url('pentaxtv') }}">Pentax Sala Espera</a></li>
                        <li><a href="{{ url('pentaxtv_dr') }}">Pentax TV</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                    <button type="button" class="col-md-12 btn btn-primary" style="width: 90%; height: 50px;"><i class="fa fa-television"></i> CONTROL DE PROCEDIMIENTOS <br> FUNCIONALES E IMAGENES</button>
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" style="width: 10%; height: 50px;">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" style="width: 100%;" role="menu">
                        <li><a href="{{ url('procedimientos_dr') }}">Control</a></li>
                        <li><a href="{{ url('procedimientostv_dr') }}">Procedimientos TV</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                    <button type="button" class="btn btn-primary" style="width: 90%; height: 50px;"><i class="fa fa-table"></i> REPORTES</button>
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" style="width: 10%; height: 50px;">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" style="width: 100%;" role="menu">
                        <li><a href="{{ route('agenda.reportediario') }}">Agendamiento Diario</a></li>
                        <!--reporte agenda-->
                        <li><a href="{{ route('pentax.reporteagenda') }}">Procedimientos Pentax</a></li>
                        <!--reporte drH  CAMBIOS 08052018-->
                        <li><a href="{{ route('consultam.reporteagenda') }}">Procedimientos Otras Salas</a></li>
                        <li><a href="{{ route('consultam.reporteagenda2') }}">Procedimientos por Doctor</a></li>
                        <!--reporte Hospitalizados-->
                        <li><a href="{{ route('hospitalizados.reporte') }}">Hospitalizados</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="margin">
            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                    <button type="button" class="btn btn-primary" style="width: 90%; height: 50px;"><i class="glyphicon glyphicon-list-alt"></i> ADMINISTRACI&Oacute;N DE INSUMOS</button>
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" style="width: 10%; height: 50px;">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" style="width: 100%;" role="menu">
                        <li><a href="{{ route('proveedor.index') }}">Proveedores</a></li>
                        <li><a href="{{ url('bodega') }}">Bodegas</a></li>
                        <li><a href="{{ route('producto.index') }}">Productos</a></li>
                        <li><a href="{{ route('marca.index') }}">Marcas</a></li>
                        <li><a href="{{ route('tipo.index') }}">Tipos de Productos</a></li>
                        <li><a href="{{ route('transito.index') }}">Productos en Transito</a></li>
                        <li><a href="{{ route('codigo.barra') }}">Pedidos Realizados</a></li>
                        <li><a href="{{ route('equipo.index') }}">Equipos Medicos</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                    <button type="button" class="btn btn-primary"  style="width: 90%; height: 50px;"><i class="fa fa-file"></i>  INSUMOS REPORTES</button>
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" style="width: 10%; height: 50px;">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" style="width: 100%;" role="menu">
                        <li><a href="{{ route('reporte.buscador_master') }}">Buscador Master</a></li>
                        <li><a href="{{ route('reporte.reporte_bodega') }}">Productos en Bodega</a></li>
                        <li><a href="{{ route('reporte.reporte_caducado') }}">Productos Caducados</a></li>
                        <li><a href="{{ route('reporte.buscador_usos') }}">Uso de Productos</a></li>
                        <li><a href="{{ route('reporte.buscador_usos_equipo') }}">Uso de Equipos</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                    <a href="{{ route('biopsias_paciente.index') }}" class="btn btn-primary" style="width: 100%; height: 50px; line-height: 35px;"><i class="fa fa-file-text"></i> INGRESO MASIVO BIOPSIAS</a>
                </div>
            </div>
            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                    <button type="button" class="col-md-12 btn btn-primary" style="width: 90%; height: 50px;"><i class="glyphicon glyphicon-list-alt"></i> CONVENIOS PRIVADOS</button>
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" style="width: 10%; height: 50px;">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" style="width: 100%;" role="menu">
                        <li><a href="{{route('privados.index')}}">LABS PRIVADOS</a></li>
                        <li><a href="{{route('pacientes.consulta')}}">CONSULTA PACIENTES</a></li>
                        <li><a href="{{route('solicitud.agenda')}}">AGENDA</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="margin">
            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                    <a href="{{ url('manual') }}" class="btn btn-primary" style="width: 100%; height: 50px; line-height: 35px;"><i class="fa fa-file-pdf-o"></i> ADMINISTRACI&Oacute;N DE TARIFARIOS</a>
                </div>
            </div>
            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                    <button type="button" class="btn btn-primary"  style="width: 90%; height: 50px;"><i class="ionicons ion-ios-flask"></i>  ADMINISTRACI&Oacute;N LABORATORIO</button>
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" style="width: 10%; height: 50px;">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" style="width: 100%;" role="menu">
                        <li><a href="{{ route('orden.index') }}">Recepción</a></li>
                        <li><a href="{{ route('orden.index_control') }}">Laboratorio</a></li>
                        <li><a href="{{ route('orden.index_supervision') }}">Supervisión</a></li>
                        <li><a href="{{ route('examen.index') }}">Exámenes</a></li>
                        <li><a href="{{ route('examen_costo.index') }}">Exámenes Costos</a></li>
                        <li><a href="{{ route('protocolo.index') }}">Protocolos</a></li>
                        <li><a href="{{ url('exa_agrupadores') }}">Agrupadores</a></li>
                        <li><a href="{{ url('agendalabs/agenda') }}">Agenda</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                    <button type="button" class="col-md-12 btn btn-primary" style="width: 90%; height: 50px;"><i class="fa fa-commenting-o"></i> ENCUESTAS Y SUGERENCIAS</button>
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" style="width: 10%; height: 50px;">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu" style="width: 100%;" role="menu">
                        <li><a href="{{ route('area.index') }}">Areas</a></li>
                        <li><a href="{{ route('tipo_sugerencia.index') }}">Tipos de Sugerencia</a></li>
                        <li><a href="{{ route('sugerencia.resultados') }}">Resultados de Sugerencia</a></li>
                        <li><a href="{{ route('preguntas.index') }}">Preguntas de Encuesta</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                    <a href="{{ route('enfermeria.index') }}" class="btn btn-primary" style="width: 100%; height: 50px; line-height: 35px;"><i class="fa fa-history"></i> PACIENTES DEL D&Iacute;A ENFERMEROS</a>
                </div>
            </div>
        </div>
        <div class="margin">
            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group dropup" style="padding-left: 0px; padding-right: 0px;">
                    <button type="button" class="btn btn-primary" style="width: 90%; height: 50px;"><i class="fa fa-link"></i>
                    ADMINISTRACI&Oacute;N DEL SISTEMA
                    </button>
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 10%; height: 50px;">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu" style="width: 100%;">
                        <li><a href="{{ url('especialidad ') }}"><i class="fa fa-fw fa-briefcase"></i> Especialidades</a></li>
                        <li><a href="{{ route('hospital-management.index') }}"><i class="fa fa-building"></i> Ubicaciones</a></li>
                        <li><a href="{{ route('procedimiento.index') }}"><i class="fa fa-book"></i> Procedimientos</a></li>
                        <li><a href="{{ url('form_enviar_seguro') }}"><i class="fa fa-fw fa-medkit"></i> Seguros</a></li>
                        <li><a href="{{ url('empresa') }}">Empresas</a></li>
                        <li><a href="{{ url('tecnicas') }}">Procedimientos Completos</a></li>
                        <li><a href="{{ route('tipo_usuario-management.index') }}">Tipos de Usuario</a></li>
                        <li><a href="{{ url('cie_10_3') }}">Cie 10 3</a></li>
                        <li><a href="{{ url('cie_10_4') }}">Cie 10 4</a></li>
                        <li><a href="{{ route('plan_cuentas.index') }}">Plan de Cuentas</a></li>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.box-body -->
</div>
<!-- end split buttons box -->