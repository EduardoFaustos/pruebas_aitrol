<?php

use Sis_medico\UsuariosControl;

$imagen = Auth::user()->imagen_url;
if ($imagen == ' ') {
  $imagen = 'avatar.jpg';
}
$rolUsuario = Auth::user()->id_tipo_usuario;
$id_auth    = Auth::user()->id;
$empresa = \Sis_medico\Empresa::where('prioridad', '1')->first();
$id_empresa = $empresa->id;
//dd($id_empresa);
?>
@if(session()->has('id_empresa'))
@php
$id_empresa = Session::get('id_empresa');
@endphp
@endif

<style type="text/css">
  .sidebar,
  aside.main-sidebar {
    /*background: url({{asset('/imagenes')}}/index-top-bg.png) repeat-x scroll 0 0 transparent !important;*/
  }
</style>
@php
$sidebar = $_SERVER["REQUEST_URI"];
$id_auth = Auth::user()->id;
@endphp
</style>

<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">

    <!-- Sidebar user panel (optional) -->
    <div class="user-panel">
      <div class="pull-left image">
        <img src="{{asset('/avatars').'/'.$imagen}}" class="img-circle" alt="User Image">
      </div>
      <div class="pull-left info">
        <p>{{ Auth::user()->name}}</p>
        <!-- Status -->
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>

    <!-- search form (Optional) -->
    <!--form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form-->
    <!-- /.search form -->

    <!-- Sidebar Menu -->
    <ul class="sidebar-menu">
      <!-- Optionally, you can add icons to the links -->
      <!--<li class="active"><a href="/"><i class="fa fa-link"></i> <span>Dashboard</span></a></li>-->
      <li class="treeview  @if(strrpos($sidebar, 'atencion')) active @endif ">
        <a href="#"><i class="fa fa-medkit"></i><span>{{trans('tsidebar.AtencionAlPaciente')}}</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          @if(in_array($rolUsuario, array(1,5)))
          <li><a href="{{route('turnero_sala_administracion')}}"><i class="fa fa-hospital-o" aria-hidden="true"></i>Administracíon de turnos</a></li>
          @endif
        </ul>
        <ul class="treeview-menu">
          @if(in_array($rolUsuario, array(1, 4, 5, 20)) == true)
          <li><a href="{{ route('biopsias_paciente.index') }}"><i class="fa fa-file-text"></i> <span>{{trans('tsidebar.ingreso_masivo_biopsias')}}</span></a></li>
          @endif
        </ul>
        <ul class="treeview-menu">
          @if(in_array($rolUsuario, array(1, 3, 4, 5, 10, 11, 12, 7, 15, 20, 22)) == true)
          <li><a href="{{ url('consultam ') }}"><i class="fa fa-calendar-minus-o"></i> <span>{{trans('tsidebar.consultas_procedimientos')}}</span></a></li>
          @endif
        </ul>
        <ul class="treeview-menu">
          @if(in_array($rolUsuario, array(1, 11, 22)))
          <li class="treeview">
            <a href="#"><i class="fa fa-check-square-o"></i> <span>{{trans('tsidebar.Orden_Formato_012')}}</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="{{ route('orden_ingresada_formato012') }}">{{trans('tsidebar.Ordenes_Ingresadas')}}</a></li>
            </ul>
          </li>
          @endif
        </ul>
        <ul class="treeview-menu">
          @if(in_array($rolUsuario, array(1, 4, 5, 11, 20)) == true)
          <li><a href="{{ url('paciente') }}"><i class="fa fa-fw fa-users"></i> <span>{{trans('tsidebar.pacientes')}}</span></a></li>
          @endif
        </ul>
        <ul class="treeview-menu">
          @if(in_array($rolUsuario, array(1, 6, 7, 11)) == true)
          <li><a href="{{ route('enfermeria.index') }}"><i class="fa fa-history"></i> <span>Pacientes del Dia @if(in_array($rolUsuario, array(1, 6, 7, 11)) == true) {{trans('tsidebar.enfermeros')}} @endif</span></a></li>
          @endif
        </ul>
        <ul class="treeview-menu">
          @if(in_array($rolUsuario, array(1,11,13,5, 7, 20, 9)) == true)
          <li><a href="{{ route('historia_clinica.fullcontrol') }}"><i class="fa fa-history"></i><span>{{trans('tsidebar.pacientes_dia')}}</span></a></li>
          @endif
        </ul>
        <ul class="treeview-menu">
          @if(in_array($rolUsuario, array(1, 6, 11,4)) == true)
          <li><a href="{{ route('camilla.index') }}"><i class="fa fa-hospital-o" aria-hidden="true"></i><span>{{trans('tsidebar.Riesgo_de_Caida')}}</span></a></li>
          @endif
        </ul>
        <ul class="treeview-menu">
          <li><a href="{{ route('paciente.historial_orden_lab_paciente') }}"><i class="fa fa-user-md"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>@if(in_array($rolUsuario, array(2)) == false) Tu @endif Historial de Examenes </span></a></li>
        </ul>
        <ul class="treeview-menu">
          @if(in_array($rolUsuario, array(2)) == true)
          <li><a href="{{ route('paciente.historial_examenes') }}"><i class="fa fa-book"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>{{trans('tsidebar.historial_ordenes')}} </span></a></li>
          @endif
        </ul>
        <ul class="treeview-menu">
          @if(in_array($rolUsuario, array(2)) == true)
          <li><a href="{{ route('recetas_usuario') }}"><i class="fa fa-file-text-o" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>{{trans('tsidebar.historial_receta')}} </span></a></li>
          @endif
        </ul>

        <ul class="treeview-menu">
          @if(in_array($rolUsuario, array(1, 4, 5, 20)) == true)
          <li><a href="{{ route('disponibilidad.disponibilidad_menu') }}"><i class="fa fa-calendar"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>{{trans('tsidebar.disponibilidad')}}</span></a></li>
          @endif
        </ul>
        <ul class="treeview-menu">
          @if(in_array($rolUsuario, array(1, 4, 5, 20, 11)) == true)
          <li class="treeview">
            <a href="#"><i class="treeview-menu"></i> <span>{{trans('tsidebar.control_documental')}}</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="{{ url('adelantado ') }}">{{trans('tsidebar.adelantados')}}</a></li>
            </ul>
          </li>

          @endif
        </ul>

      </li>

      <li class="treeview  @if(strrpos($sidebar, 'persobal')) active @endif ">
        <a href="#"><i class="fa fa-user-md"></i><span>{{trans('tsidebar.PersonalMedico')}}</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          @if(in_array($rolUsuario, array(1, 4, 5, 20, 22)) == true)
          <li><a href="{{ url('agenda') }}"><i class="fa fa-calendar"></i> <span>{{trans('tsidebar.agenda')}}</span></a></li>
          @endif
        </ul>

        <ul class="treeview-menu">
          @if(in_array($rolUsuario, array(33)) == true)
          <li><a href="{{ route('agenda.agenda2') }}"><i class="fa fa-fw fa fa-calendar"></i> <span>Agenda</span></a></li>
          @if($id_auth == '1307189140')
          <li><a href="{{ route('horario.index') }}"><i class="fa fa-fw fa fa-calendar"></i> <span>Horario Laborable</span></a></li>
          < li><a href="{{ route('cortesia.index') }}"><i class="fa fa-fw fa fa-user"></i> <span>Cortesia</span></a>
      </li>
      @endif
      <!--li><a href="{{ url('tecnicas') }}"><i class="glyphicon glyphicon-tasks"></i>Procedimientos</a></li-->
      @endif
    </ul>

    <ul class="treeview-menu">
      @if(in_array($rolUsuario, array(9, 1, 7)) == true)
      <li><a href="{{ route('enfermeria.index_insumos') }}"><i class="glyphicon glyphicon-plus-sign"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>{{trans('tsidebar.anestesiologos')}} </span></a></li>
      @endif
    </ul>
    <ul class="treeview-menu">
      @if(in_array($rolUsuario, array(1,5)) == true || $id_auth == '0916593445')
      <li><a href="{{ route('horario.index_admin') }}"><i class="fa fa-clock-o"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>{{trans('tsidebar.horario_doctores')}} </span></a></li>
      @endif
    </ul>
    </li>

    <li class="treeview  @if(strrpos($sidebar, 'administracion')) active @endif ">
      <a href="#"><i class="fa fa-briefcase"></i><span>{{trans('tsidebar.Administracion')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1)) == true )
        <li class="treeview">
          <a href="#"><i class="ionicons ion-ios-flask"></i> <span>{{trans('tsidebar.administracion_laboratorio')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ route('orden.index') }}">{{trans('tsidebar.recepcion')}}</a></li>
            <li><a href="{{ route('orden.index_control') }}">{{trans('tsidebar.laboratorio')}}</a></li>
            <li><a href="{{ route('orden.index_supervision') }}">{{trans('tsidebar.supervision')}}</a></li>
            <li><a href="{{ route('examen.index') }}">{{trans('tsidebar.examenes')}}</a></li>
            <li><a href="{{ route('examen_costo.index') }}">{{trans('tsidebar.costos')}}</a></li>
            <li><a href="{{ route('protocolo.index') }}">{{trans('tsidebar.protocolos')}}</a></li>
            <li><a href="{{ url('exa_agrupadores') }}">{{trans('tsidebar.agrupadores')}}</a></li>
            <li><a href="{{ url('agendalabs/agenda') }}">{{trans('tsidebar.agenda')}}</a></li>
            <li><a href="{{ route('factura_agrupada.index_factura_agrupada') }}">{{trans('tsidebar.factura_agrupada')}}</a></li>
            <li><a href="{{ route('e.labs_estadisticos') }}"><i class="fa fa-pie-chart"> </i> {{trans('tsidebar.estadisticos')}}</a></li>
          </ul>
        </li>
        @endif
      </ul>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(4, 5, 20)) == true )
        <li><a href="{{ route('orden.index') }}"><i class="ionicons ion-ios-flask"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Laboratorio - Ordenes </span></a></li>
        @endif
      </ul>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(10)) == true)
        <li><a href="{{ route('orden.index_control') }}"><i class="ionicons ion-ios-flask"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Laboratorio - Control </span></a></li>
        <li><a href="{{ route('agendalabs.agenda') }}"><i class="fa fa-calendar"></i>&nbsp;&nbsp;<span>Agenda </span></a></li>
        @endif
      </ul>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(12,11, 22)) == true )
        <!--Supervision-->
        <li><a href="{{ route('orden.index_supervision') }}"><i class="ionicons ion-ios-flask"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Laboratorio </span></a></li>
        <li><a href="{{ route('examen.index') }}"><i class="ionicons ion-ios-flask"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Exámenes </span></a></li>

        @endif
        @if(in_array($rolUsuario, array(33)) == true )
        <!--CAMBIO PARA LABS CERTIFICACION DE EXÁMENES-->
        <li><a href="{{ route('orden.index_doctor_menu') }}"><i class="ionicons ion-ios-flask"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Laboratorio </span></a></li>
        @endif

        @if(in_array($rolUsuario, array(12, 22)) == true )
        <li><a href="{{ route('examen_costo.index') }}"><i class="glyphicon glyphicon-usd"></i> Exámenes Costos</a></li>
        @endif

        @if(in_array($rolUsuario, array(1)) == true)
        @endif

        @if(in_array($rolUsuario, array(12)) == true)
        <li><a href="{{ route('e.labs_estadisticos') }}"><i class="fa fa-pie-chart"> </i> Estadisticos</a></li>
        @endif
      </ul>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1, 20)) == true )
        <li><a href="{{ route('factura_agrupada.index_factura_agrupada') }}"><i class="ionicons ion-ios-flask"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>{{trans('tsidebar.factura_agrupada_labs')}}</span></a></li>
        @endif
      </ul>

      <ul class="treeview-menu">
        @php
        $agenda_permiso = Sis_medico\Agenda_Permiso::where('id_usuario', $id_auth)->where('ip_creacion','like','%COMERCIAL%')->first();
        @endphp

        @if(!is_null($agenda_permiso) || in_array($rolUsuario, array(1)) == true)
        <li class="treeview  @if(strrpos($sidebar, 'insumos')) active @endif ">
          <a href="#"><i class="glyphicon glyphicon-list-alt"></i> <span>Comercial</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ route('comercial.proforma.index_proforma') }}">{{trans('tsidebar.proformas')}}</a></li>
          </ul>
          <ul class="treeview-menu">
            <li><a href="{{ route('prodtarifario.index') }}">{{trans('tsidebar.producto_tarifario')}}</a></li>
          </ul>
        </li>
        @endif
      </ul>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1)) == true )
        <li class="treeview">
          <a href="#"><i class="glyphicon glyphicon-list-alt"></i> <span>{{trans('tsidebar.convenios_privados')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">

            @php
            $opcion = '1'; // LABS PRIVADOS
            $opcion_u = Sis_medico\Opcion_Usuario::where('id_tipo_usuario',$rolUsuario)->where('id_opcion', $opcion)->first();
            @endphp
            @if(!is_null($opcion_u))
            <li><a href="{{route('privados.index')}}"><i class="ionicons ion-ios-flask"></i> <span> {{$opcion_u->opcion->nombre}}</span></a></li>
            @endif

            @php
            $opcion = '3'; // CONSULTA PACIENTES
            $opcion_u = Sis_medico\Opcion_Usuario::where('id_tipo_usuario',$rolUsuario)->where('id_opcion', $opcion)->first();
            @endphp
            @if(!is_null($opcion_u))
            <li><a href="{{route('pacientes.consulta')}}" style="padding-left: 10px;"><i class="fa fa-fw fa-users"></i> <span>{{$opcion_u->opcion->nombre}}</span></a></li>
            @endif

            @php
            $opcion = '4'; // AGENDA
            $opcion_u = Sis_medico\Opcion_Usuario::where('id_tipo_usuario',$rolUsuario)->where('id_opcion', $opcion)->first();
            @endphp
            @if(!is_null($opcion_u))
            <li><a href="{{route('solicitud.agenda')}}"><i class="fa fa-calendar"></i> <span>{{$opcion_u->opcion->nombre}}</span></a></li>
            @endif

          </ul>
        </li>
        @endif
      </ul>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1)) == false )
        <!-- OPCIONES DE CONVENIOS PRIVADOS -->
        @php
        $opcion = '1'; // LABS PRIVADOS
        $opcion_u = Sis_medico\Opcion_Usuario::where('id_tipo_usuario',$rolUsuario)->where('id_opcion', $opcion)->first();
        @endphp
        @if(!is_null($opcion_u))
        <li><a href="{{route('privados.index')}}"><i class="ionicons ion-ios-flask"></i> <span>{{$opcion_u->opcion->nombre}}</span></a></li>
        @endif

        @php
        $opcion = '3'; // CONSULTA PACIENTES
        $opcion_u = Sis_medico\Opcion_Usuario::where('id_tipo_usuario',$rolUsuario)->where('id_opcion', $opcion)->first();
        @endphp
        @if(!is_null($opcion_u))
        <li><a href="{{route('pacientes.consulta')}}" style="padding-left: 10px;"><i class="fa fa-fw fa-users"></i> <span>{{$opcion_u->opcion->nombre}}</span></a></li>
        @endif

        @php
        $opcion = '4'; // AGENDA
        $opcion_u = Sis_medico\Opcion_Usuario::where('id_tipo_usuario',$rolUsuario)->where('id_opcion', $opcion)->first();
        @endphp
        @if(!is_null($opcion_u))
        <li><a href="{{route('solicitud.agenda')}}"><i class="fa fa-calendar"></i> <span>{{$opcion_u->opcion->nombre}}</span></a></li>
        @endif
        @endif
      </ul>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1, 12, 5, 15,19)) == true)
        <li><a href="{{ route('dashboard.apps') }}"><i class="fa fa-dashboard"></i> <span>{{trans('tsidebar.menu_apps')}}</span></a></li>
        @endif
      </ul>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1, 11)))
        <li class="treeview">
          <a href="#"><i class="fa fa-commenting-o"></i> <span>{{trans('tsidebar.encuestas')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <!--li><a href="{{ route('area.index') }}">Areas</a></li>
                  <li><a href="{{ route('tipo_sugerencia.index') }}">Tipos de Sugerencia</a></li>
                  <li><a href="{{ route('sugerencia.resultados') }}">Resultados de Sugerencia</a></li>
                  <li><a href="{{ route('preguntas.index') }}">Preguntas de Encuesta</a></li-->
            <li><a href="{{ route('rrhh.resultados_ok') }}">Listado de Encuestas</a></li>
            <!--li><a href="{{ route('rrhh.estadisticas') }}">Estadisticas</a></li-->
            <li><a href="{{ route('rrhh.encuesta_estadistica') }}">Estadisticas</a></li>

          </ul>
        </li>
        @endif
      </ul>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1, 12)) == true )
        <li class="treeview">
          <a href="#"><i class="fa fa-commenting-o"></i> <span>{{trans('tsidebar.encuestas_labs')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ route('laboratorio.resultados_labs') }}">{{trans('tsidebar.listado_encuestas_labs')}}</a></li>
            <li><a href="{{ route('laboratorio.estadisticalabs') }}">{{trans('tsidebar.estadisticas_labs')}}</a></li>

          </ul>
        </li>
        @endif
      </ul>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1, 3, 4, 5, 10, 11, 12, 7, 15,6)) == true)
        <li class="treeview">
          <a href="#"> <i class="fa fa-file"> </i><span>Enfermeria</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-rigth"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{route('limpieza_equipo.index')}}"><i class="fa fa-hospital-o" aria-hidden="true"></i> {{trans('tsidebar.Registro_de_Limpieza_de_Equipos')}}</a></li>
          </ul>
        </li>

        @endif
      </ul>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1, 6, 11,4)) == true)
        <li><a href="{{ route('limpieza.salas') }}"><i class="fa fa-file"></i><span>{{trans('tsidebar.Limpieza_y_Desinfeccion')}}</span></a></li>
        @endif
      </ul>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1,4,6,11,24,4)) == true || $id_auth=='0954346441')
        <li class="treeview">
          <a href="#"> <i class="fa fa-file"> </i><span>{{trans('tsidebar.Servicios_Generales')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-rigth"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{route('limpieza_banos.index')}}"><i class="fa fa-hospital-o" aria-hidden="true"></i>{{trans('tsidebar.Registro_de_Limpieza_de_Baños')}}</a></li>
            <li><a href="{{route('mantenimientos_generales.index')}}"><i class="fa fa-wrench" aria-hidden="true"></i> Mantenimientos</a></li>
            <li><a href="{{route('mantenimientohorario.index')}}"><i class="fa fa-leanpub" aria-hidden="true"></i> Registro de Limpieza y Desinfección de Salas</a></li>
          </ul>
        </li>
        @endif
      </ul>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 15,21,20,19,16,14)) == true)
        <li class="treeview">
          <a href="{{route('ticket_soporte_tecnico.index')}}"> <i class="fa fa-ticket" aria-hidden="true"></i><span>{{trans('tsidebar.Ticket_Soporte_Tecnico')}} </span></a>
        </li>
        @endif
      </ul>
      <ul class="treeview-menu">
        @if($rolUsuario != 2)
        <li><a href="{{route('trabajo_campo_index')}}"><i class="fa fa-file"></i><span>{{trans('tsidebar.trabajo_campo')}}</span></a></li>
        @endif
      </ul>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1, 4, 5, 20)) == true )
        <li>
          <a href="{{ route('observacion.index') }}"><i class="glyphicon glyphicon-copy"></i>
            <span>{{trans('tsidebar.observaciones')}} </span>
            <span class="pull-right-container">
              <small class="label pull-right bg-red" id="o_cantidad"></small>
            </span>
          </a>
        </li>
        @endif
      </ul>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1, 4, 5, 20)) == true)
        <li class="treeview">
          <a href="#"><i class="fa fa-television"></i> <span>{{trans('tsidebar.pentax')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ url('pentax') }}"">{{trans('tsidebar.control')}}</a></li>
                <li><a href=" {{ url('pentaxtv') }}">{{trans('tsidebar.sala_espera')}}</a></li>
            <li><a href="{{ url('pentaxtv_dr') }}">{{trans('tsidebar.ver')}}</a></li>
            <li><a href="{{ url('consulta_tv') }}">{{trans('tsidebar.control_consultas')}}</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#"><i class="fa fa-television"></i> <span>{{trans('tsidebar.procedimientos')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ url('procedimientos_dr') }}"">{{trans('tsidebar.control')}}</a></li>
                <li><a href=" {{ url('procedimientostv_dr') }}">{{trans('tsidebar.ver')}}</a></li>
          </ul>
        </li>
        @endif
      </ul>

      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(11)) == true)
        <li><a href="{{ url('procedimientostv_dr') }}"><i class="fa fa-television"></i> <span>Procedimientos TV</span></a></li>
        @endif
      </ul>

    </li>

    <li class="treeview  @if(strrpos($sidebar, 'contable')) active @endif ">
      <a href="#"><i class="fa fa-usd"></i><span>{{trans('tsidebar.CuentasyFinanzas')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1,20, 22)) == true)
        <li class="treeview @if(strrpos($sidebar, 'contable')) active @endif">

          <a href="#"><i class="glyphicon glyphicon-book"></i> <span>{{trans('tsidebar.contable')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="treeview @if(strrpos($sidebar, 'contable/empresa')) active @endif">
              <a href="#"> <i class="fa fa-building"> </i>{{trans('tsidebar.empresa')}}
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-rigth"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li class="treeview @if(strrpos($sidebar, 'empresa/seleccion')) active @endif">
                  <a href="{{ route('plan_cuentas.seleccion_empresa') }}"></i>{{trans('tsidebar.seleccion_empresa')}}</a>
                </li>
              </ul>
            </li>

            <li class="treeview @if(strrpos($sidebar, 'contable/contabilidad')) active @endif">
              <a href="#"> <i class="fa fa-university"> </i>{{trans('tsidebar.contabilidad')}}
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-rigth"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li class="treeview @if(strrpos($sidebar, 'plan_cuentas')) active @endif">
                  <a href="{{ route('plan_cuentas.index') }}"></i>{{trans('tsidebar.plan_cuentas')}}</a>
                </li>
                <li class="treeview @if(strrpos($sidebar, 'libro_mayor')) active @endif">
                  <a href="{{ route('libro_mayor.index') }}"></i>{{trans('tsidebar.libro_mayor')}}</a>
                </li>
                <li class="treeview @if(strrpos($sidebar, 'contabilidad/balance_general')) active @endif"><a href="{{ route('balance_general.index') }}"></i>{{trans('tsidebar.balance_general')}}</a></li>
                <li class="treeview @if(strrpos($sidebar, 'contabilidad/estado/resultados')) active @endif"><a href="{{ route('estadoresultados.index') }}"></i>{{trans('tsidebar.estado_resultado_integral')}}</a></li>
                <li class="treeview @if(strrpos($sidebar, 'contabilidad/balance_comprobacion')) active @endif"><a href="{{ route('balance_comprobacion.index') }}"></i>{{trans('tsidebar.balance_comprobacion')}}</a></li>
                <li class="treeview @if(strrpos($sidebar, 'contabilidad/libro')) active @endif"><a href="{{ route('librodiario.index') }}"></i>{{trans('tsidebar.visualizar_asientos_diarios')}}</a></li>
                {{-- <li><a href="{{ route('compras.informe') }}"></i>{{trans('tsidebar.informe_compras')}}</a>
            </li> --}}
            <li class="treeview @if(strrpos($sidebar, 'contabilidad/flujo/efectivo_1')) active @endif"><a href="{{ route('flujoefectivo.index') }}"></i>{{trans('tsidebar.flujo_efectivo')}}</a></li>
            <li class="treeview @if(strrpos($sidebar, 'contabilidad/flujo/efectivo/comparativo_1')) active @endif"><a href="{{ route('flujoefectivocomparativo.index') }}"></i>{{trans('tsidebar.flujo_efectivo_comparativo')}}</a></li>
            <li class="treeview @if(strrpos($sidebar, 'contabilidad/flujo/efectivo/comparativo/dos')) active @endif"><a href="{{ route('flujoefectivocomparativo.index2') }}"></i>{{trans('tsidebar.flujo_efectivo_comparativo')}} II</a></li>
            <li class="treeview @if(strrpos($sidebar, 'contabilidad/ats')) active @endif"><a href="{{ route('ats.index') }}"></i>SRI / ATS</a></li>
            <li class="treeview @if(strrpos($sidebar, 'contabilidad/descuadrados')) active @endif"><a href="{{ route('librodiario.descuadrados') }}"></i>{{trans('tsidebar.descuadrados')}}</a></li>
          </ul>
        </li>

        <li class="treeview @if(strrpos($sidebar, 'acreedores')) active @endif">
          <a href="#"> <i class="fa  fa-users"> </i>{{trans('tsidebar.acreedores')}}
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-rigth"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="treeview @if(strrpos($sidebar, 'acreedores/mantenimiento')) active @endif">
              <a href="#"> <i class="fa fa-bars"> </i>1 {{trans('tsidebar.mantenimiento')}}
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-rigth"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li class="treeview @if(strrpos($sidebar, 'acreedores/mantenimiento/rubrosa')) active @endif"><a href="{{ route('rubrosa.index') }}"><i class="fa fa-gear"></i>{{trans('tsidebar.rubros_acreedores')}}</a></li>
                <li class="treeview @if(strrpos($sidebar, 'acreedores/mantenimiento/acreedor')) active @endif"><a href="{{ route('acreedores_index') }}"><i class="fa fa-gear"></i>{{trans('tsidebar.acreedores')}}</a></li>
                <li class="treeview @if(strrpos($sidebar, 'acreedores/mantenimiento/acreedor')) active @endif"><a href="{{ route('saldosinicialesp.index2') }}"> {{trans('tsidebar.saldos_iniciales')}}</a></li>

              </ul>

            </li>
          </ul>
          <ul class="treeview-menu">
            <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos')) active @endif">
              <a href="#"> <i class="fa  fa-file-text"> </i>2 {{trans('tsidebar.documentos')}}
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-rigth"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li class="treeview">
                <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos/retenciones')) active @endif"><a href="{{ route('retenciones_index') }}"><i class="fa fa-calculator"></i>{{trans('tsidebar.retenciones')}}</a></li>
                <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos/comprobante/egreso')) active @endif"><a href="{{ route('acreedores_cegreso') }}"><i class="fa  fa-file-text"></i>{{trans('tsidebar.comprobante_egreso')}}</a></li>

                <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos/comprobante/index')) active @endif"><a href="{{ route('comp_egreso_masivo.index') }}"><i class="fa  fa-file-text"></i>{{trans('tsidebar.comprobante_egreso_masivo')}}</a></li>
                <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos/cuentas/egreso/varios')) active @endif"><a href="{{ route('egresosv.index') }}"><i class="fa  fa-file-text"></i> {{trans('tsidebar.comprobante_egreso_vario')}}</a></li>
                <li><a href="{{ route('cruce.index') }}"><i class="fa fa-exchange"></i>{{trans('tsidebar.cruce_valores_favor')}}</a></li>
                <li class="treeview @if(strrpos($sidebar, 'contable/cruce_cuentas/valores')) active @endif"><a href="{{ route('pr.cruce_cuentas') }}"><i class="fa  fa-file-text"></i> {{trans('tsidebar.cruce_cuentas')}}</a></li>
                <li><a href="{{ route('creditoacreedores.index') }}"><i class="fa fa-clone"></i>{{trans('tsidebar.nota_credito')}}</a></li>
                <li><a href="{{ route('debitoacreedores.index') }}"><i class="fa fa-file-o"></i>{{trans('tsidebar.nota_debito')}}</a></li>

            </li>
          </ul>

        </li>
      </ul>
      <ul class="treeview-menu">
        <li class="treeview @if(strrpos($sidebar, 'acreedores/informes')) active @endif">
          <a href="#"> <i class="fa fa-pie-chart"> </i>3 {{trans('tsidebar.informes')}}
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-rigth"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="treeview @if(strrpos($sidebar, 'acreedores/informes/cartera/pagar')) active @endif">
              <a href="{{ route('acreedores.informenc') }}"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.informes_nota_creadito')}}</a>
            </li>
            <li class="treeview @if(strrpos($sidebar, 'acreedores/credito/informe')) active @endif">
              <a href="{{ route('carterap.index') }}"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.carteras_pagar')}}</a>
            </li>
            <li class="treeview @if(strrpos($sidebar, 'acreedores/informes/informe/saldos')) active @endif">
              <a href="{{ route('saldos_acreedor.index') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_saldos')}}</a>
            </li>
            <li><a href="{{ route('contable.reporte_tiempo') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_tiempo')}}</a></li>
            <li><a href="{{ route('chequesa.index') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_cheques_girados')}}</a></li>
            <li><a href="{{ route('informe_retenciones.index') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_retenciones')}}</a></li>
            <li><a href="{{ route('contable.anticipo_proveedores') }}"><i class="fa fa-file-excel-o"></i>I{{trans('tsidebar.informe_anticipos')}}</a></li>
            <li><a href="{{ route('deudasvspagos.index') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.deudas_vs_pagos')}}</a></li>
            <li><a href="{{ route('deudas_pendientes.index') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.deudas_pendientes')}}</a></li>

          </ul>

        </li>
      </ul>
    </li>

    <li class="treeview">
      <a href="#"> <i class="fa  fa-users"> </i>{{trans('tsidebar.clientes')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
          <a href="#"> <i class="fa fa-bars"> </i>1 {{trans('tsidebar.mantenimiento')}}
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-rigth"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="treeview">
            <li><a href="{{ route('clientes.index') }}"></i>{{trans('tsidebar.crear_cleinte')}}</a></li>
            <li><a href="{{ route('rubros_cliente.index') }}"></i>{{trans('tsidebar.rubros_clientes')}}</a></li>
            <li><a href="{{ route('saldosinicialesclientes.index2') }}"></i>{{trans('tsidebar.saldos_iniciales')}}</a></li>
        </li>
      </ul>
    </li>
    </ul>
    <ul class="treeview-menu">
      <li class="treeview">
        <a href="#"> <i class="fa  fa-file-text"> </i>2 {{trans('tsidebar.documentos')}}
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>

        <ul class="treeview-menu">
          <li class="treeview">
          <li><a href="{{route('retencion.cliente')}}"><i class="fa fa-calculator"></i>{{trans('tsidebar.retenciones')}}</a></li>
          <li><a href="{{route('chequespost.index')}}"><i class="fa fa-files-o"></i>{{trans('tsidebar.cheques_postfechados')}}</a></li>
          <li><a href="{{route('cr.cruce_cuentas')}}"><i class="fa fa-files-o"></i>{{trans('tsidebar.cruce_cuentas')}}</a></li>
          <li><a href="{{route('comprobante_ingreso.index')}}"><i class="fa  fa-file-text"></i>{{trans('tsidebar.comprobante_ingreso')}}</a></li>
          <li><a href="{{route('comprobante_ingreso_v.index')}}"><i class="fa  fa-file-text"></i>{{trans('tsidebar.comprobante_ingresos_varios')}}</a></li>
          <li><a href="{{route('cruce_clientes.index')}}"><i class="fa fa-exchange"></i>{{trans('tsidebar.cruce_valores')}}</a></li>
          <li><a href="{{route('nota_credito_cliente.index')}}"><i class="fa fa-clone"></i>{{trans('tsidebar.nota_credito')}}</a></li>
          <li><a href="{{route('nota_cliente_debito.index')}}"><i class="fa fa-file-o"></i>{{trans('tsidebar.nota_debito')}}</a></li>

      </li>
    </ul>

    </li>
    </ul>
    <ul class="treeview-menu">
      <li class="treeview">
        <a href="#"> <i class="fa fa-pie-chart"> </i>3 {{trans('tsidebar.informes')}}
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="treeview">
          <li><a href="{{route('clientes.deudas.pendientes')}}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.deudas_pendientes')}}</a></li>
          <li><a href="{{route('clientes.saldo.cxc')}}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.saldo_cuentas_por_cobrar')}}</a></li>
          <li><a href="{{route('cliente.informe.retenciones')}}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_retenciones')}}</a></li>
          {{-- <li><a href="#"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.carteras_pagar')}}</a>
      </li>
      <li><a href="#"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_saldos')}}</a></li>
      <li><a href="#"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_cheques_girados')}}</a></li>
      <li><a href="#"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_retenciones')}}</a></li> --}}
      <li><a href="{{route('deudasvspagos.cliente')}}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.deudas_vs_pagos')}}</a></li>
      <li><a href="{{ route('nota_credito_cliente.informe_notacreditoclientes') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_notas_credito_clientes')}}</a></li>
      </li>
    </ul>

    </li>
    </ul>
    </li>

    <li class="treeview @if(strrpos($sidebar, 'Banco')) active @endif">
      {{-- <a href="#"> <i class="fa fa-cubes"> </i>Caja y Bancos --}}
      <a href="#"> <i class="fa fa-money"> </i>{{trans('tsidebar.caja_bancos')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview "><a href="{{ route('caja_banco.index') }}"></i>{{trans('tsidebar.crear_cajabanco')}}</a></li>
        <li><a href="{{ route('banco_clientes.index') }}"></i>{{trans('tsidebar.listado_bancos')}}</a></li>
        <li class="treeview @if(strrpos($sidebar, 'Banco/notadebito')) active @endif"><a href="{{ route('notadebito.index') }}"></<i>{{trans('tsidebar.nota_debito')}}</a></li>
        <li class="treeview"><a href="{{ route('notacredito.index') }}"></i>{{trans('tsidebar.nota_credito')}}</a></li>
        <li class="treeview"><a href="{{ route('debitobancario.index') }}"></i>{{trans('tsidebar.nota_debito_bancaria')}}</a></li>
        <li class="treeview"><a href="{{ route('depositobancario.index') }}"></i>{{trans('tsidebar.deposito_bancario')}}</a></li>
        <li class="treeview"><a href="{{ route('transferenciabancaria.index') }}"></i>{{trans('tsidebar.transferencia_bancaria')}}</a></li>
        <li class="treeview"><a href="{{ route('conciliacionbancaria.index') }}"></i>{{trans('tsidebar.conciliacion_bancaria')}}</a></li>
        <li class="treeview"><a href="{{ route('estadocuentabancos.index') }}"></i>{{trans('tsidebar.estado_cuenta')}}</a></li>
      </ul>
    </li>

    <li class="treeview">
      <a href="#">
        <i class="fa fa-share"></i> <span>{{trans('tsidebar.activos_fijos')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
          <a href="#"><i class="fa fa-circle-o"></i> {{trans('tsidebar.mantenimientos')}}
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ route('afGrupo.index') }}"> {{trans('tsidebar.grupos')}}</a></li>
            <li><a href="{{ route('afTipo.index') }}"> {{trans('tsidebar.tipos')}}</a></li>
            <li><a href="{{ route('afActivo.index') }}"> {{trans('tsidebar.activos')}}</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#"><i class="fa fa-circle-o"></i> {{trans('tsidebar.documentos')}}
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ route('afDocumentoFactura.index') }}"> {{trans('tsidebar.facturas')}}</a></li>
            <li><a href="{{ route('afDepreciacion.index') }}"> {{trans('tsidebar.depreciaciones')}}</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#"><i class="fa fa-circle-o"></i> {{trans('tsidebar.informes')}}
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ route('afInformes.index') }}"> {{trans('tsidebar.saldos')}}</a></li>
            <li><a href="{{route('activofjo.index_listado')}}">{{trans('tsidebar.listado_activos')}}</a></li>
            {{-- <li><a href="#"> Retenci&oacute;n</a></li> --}}
            {{-- <li><a href="#"> Facturas</a></li> --}}
          </ul>
        </li>
      </ul>
    </li>

    <li class="treeview">
      <a href="#"> <i class="fa fa-truck"> </i>{{trans('tsidebar.compras')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li><a href="{{ route('compras_index') }}"></i>{{trans('tsidebar.compras')}}</a></li>
        <li><a href="{{ route('saldosinicialesp.index2') }}"></i>{{trans('tsidebar.saldos_iniciales')}}</a></li>
        <li><a href="{{ route('compras.pedidos') }}"></i>{{trans('tsidebar.pedidos')}}</a></li>
        <li><a href="{{ route('fact_contable_index') }}"></i>{{trans('tsidebar.factura_contable')}}</a></li>
        <li><a href="{{ route('compras.informe') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informa_compras')}}</a></li>
        <!-- <li><a href="{{ route('kardex.index') }}"></i>Kardex</a></li>-->
        <li><a href="{{ route('contable.compras.kardex.index') }}"></i>{{trans('tsidebar.kardex_compra')}}</a></li>
    </li>
    </ul>
    </li>
    <li class="treeview">
      <a href="#"> <i class="fa fa-truck"> </i>{{trans('tsidebar.compras_interno')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <!--<li class="treeview"><a href="{{ route('contable.bodega.index') }}"></i>Bodegas</a></li>-->
        <li class="treeview"><a href="{{ route('contable.compraspedidos.index') }}"></i>{{trans('tsidebar.pedidos')}}</a></li>
        <!--<li class="treeview"><a href="{{ route('saldosinicialesp.index2') }}"></i>Autorizaciones</a></li>-->
        <!--<li class="treeview"><a href="{{ route('saldosinicialesp.index2') }}"></i>Inventario</a></li>-->
        <li class="treeview"><a href="{{ route('kardex_inventario.index') }}"></i>{{trans('tsidebar.kardex')}}</a></li>
        <!--<li class="treeview"><a href="{{ route('fact_contable_index') }}"></i>Factura</a></li>-->
        <li class="treeview"><a href="{{ route('pedidos_inventario.index') }}"></i>{{trans('tsidebar.informe_productos')}}</a></li>
        <li><a href="{{ route('contable.compraspedido.indexInicial')}}">{{trans('tsidebar.mantenimiento_iniciales')}}</a></li>
        <li><a href="{{ route('compraspedidos.index_proceso')}}">{{trans('tsidebar.mantenimiento_usuarios')}}</a></li>
      </ul>
    </li>

    <li class="treeview">
      <a href="#"> <i class="fa fa-cubes"> </i>{{trans('tsidebar.inventario')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li><a href="{{ route('kardex.index') }}"></i>{{trans('tsidebar.kardex')}}</a></li>
        <li><a href="{{ route('u.getUses') }}"></i>{{trans('tsidebar.inventario_insumos')}}</a></li>
        <li><a href="{{ route('productos.saldos_iniciales') }}"></i>{{trans('tsidebar.saldos_iniciales')}}</a></li>
        <li><a href="{{ route('notainventario.index') }}"></i>{{trans('tsidebar.nota_ingreso_inventario')}}</a></li>
        <li><a href="{{ route('productos_servicios_index') }}"></i>{{trans('tsidebar.productos')}}</a></li>

        <li><a href="{{ route('importaciones.PrecioProductoAprobado.index') }}"></i>{{trans('contableM.precio_producto_aprobado')}}</a></li>

    </li>
    </ul>
    </li>

    <li class="treeview">
      <a href="#"> <i class="ion-person-stalker"> </i>{{trans('tsidebar.nomina')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li><a href="{{ route('config_valor.index') }}"></i>{{trans('tsidebar.configuracion')}}</a></li>
        <li><a href="{{ route('nomina.index') }}"></i>{{trans('tsidebar.crear_empleado_rol')}}</a></li>
        <li><a href="{{ route('nuevo_rol.masivo_search') }}"></i><span style="color: red;">{{trans('tsidebar.roles_pago_empresas')}}</span></a></li>
        <li><a href="{{ route('buscador_rol.index') }}"></i>{{trans('tsidebar.rol_pago individual')}}</a></li>
        <li><a href="{{ route('prestamos_empleado.index') }}"></i>{{trans('tsidebar.prestamos_empleados')}}s</a></li>
        <li><a href="{{ route('prestamos_empleados.index_saldos') }}"></i>{{trans('tsidebar.saldo_inicial_empleados')}}</a></li>
        <!--<li><a href="{{ route('anticipos_empleado.index') }}"></i>Anticipo 1Ra Quinc Empl</a></li>-->
        <!-- <li><a href="{{ route('nomina_anticipos_1eraquincena.index') }}"></i>Anticipo 1Ra Quinc Empl</a></li> -->
        <li><a href="{{ route('nominaquincena.index_quincena')}}"></i>{{trans('tsidebar.anticipo_primera_quincena')}}</a></li>
        <li><a href="{{ route('otros_anticipos_empleado.index') }}"></i>{{trans('tsidebar.otros_anticipos_empleados')}}</a></li>
        <li><a href="{{ route('plantillas_nomina.plantillas_prestamos') }}"></i>{{trans('tsidebar.plantilla_prestamos')}}</a></li>
        <li><a href="{{ route('plantillas_nomina.horas_extras') }}"></i>{{trans('tsidebar.plantilla_horas_extras')}}</a></li>
        <!--li><a href="{{ route('prestamos_empleados.index_cruce') }}"></i>Prestamos vs Utilidades</a></li-->
    </li>
    </ul>
    <ul class="treeview-menu">
      <li class="treeview">
        <a href="#"> <i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.informes')}}
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="treeview">
            <!--<li><a href="{{ route('nomina_anticipos.index') }}"><i class="fa fa-file-excel-o"> </i>Anticipo Quincena</a></li>-->
          <li><a href="{{ route('reportes_empl.index') }}"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.datos_empleados')}}</a></li>
          <li><a href="{{ route('reportes_rol.index') }}"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.rol_pagos')}} (TTHH)</a></li>
          <li><a href="{{ route('reportes_rolcontable.index') }}"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.rol_pagos')}} ({trans('tsidebar.contabilidad')}})</a></li>
          <li><a href="{{ route('reportes_banco.index') }}"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.archivo_banco')}}</a></li>
          <li><a href="{{ route('prestamos_empleados.prestamos_saldos') }}"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.saldo_prestamos')}}</a></li>
      </li>
    </ul>
    </li>
    </ul>
    <ul class="treeview-menu">
      <li class="treeview">
        <a href="#"> <i class="fa fa-wrench"> </i>{{trans('tsidebar.mantenimiento')}}
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="treeview">
          <li><a href="{{ route('area_rh.index') }}"><i class="fa fa-wrench"> </i> {{trans('tsidebar.area')}} </a></li>
          <li><a href="{{ route('estado_civil.index') }}"><i class="fa fa-wrench"> </i> {{trans('tsidebar.estado_civil')}} </a></li>
          <li><a href="{{ route('mantenimiento.horario.index') }}"><i class="fa fa-wrench"> </i> {{trans('tsidebar.Horario')}} </a></li>
          <li><a href="{{ route('nivel_academico.index') }}"><i class="fa fa-wrench"> </i>{{trans('tsidebar.nivel_academico')}}</a></li>
          <li><a href="{{ route('pagobeneficio.index') }}"><i class="fa fa-wrench"> </i>{{trans('tsidebar.pago_benefico')}}</a></li>
          <li><a href="{{ route('tipo_aporte.index') }}"><i class="fa fa-wrench"> </i> {{trans('tsidebar.tipo_aporte')}} </a></li>
          <li><a href="{{ route('tipo_rol.index') }}"><i class="fa fa-wrench"> </i> {{trans('tsidebar.tipo_rol')}} </a></li>

      </li>
    </ul>
    </li>
    </ul>

    </li>

    <li class="treeview">
      <a href="#"> <i class="fa fa-line-chart"> </i>{{trans('tsidebar.flujos_caja')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li><a href="{{ route('flujoefectivo.index') }}"></i>{{trans('tsidebar.flujo_efectivo')}}</a></li>
        <li><a href="{{ route('flujoefectivocomparativo.index') }}"></i>{{trans('tsidebar.flujo_efectivo_comparativo')}}</a></li>
        <li><a href="{{ route('estructuraflujoefectivo.index') }}"></i>{{trans('tsidebar.estructura_flujo_efectivo')}}</a></li>
    </li>
    </ul>
    </li>

    <li class="treeview">
      <a href="#"> <i class="fa  fa-shopping-cart"> </i>{{trans('tsidebar.ventas')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>

      <ul class="treeview-menu">
        <li class="treeview">
          <a href="#"> <i class="fa fa-bars"> </i>1 {{trans('tsidebar.factura_venta')}}
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-rigth"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="treeview">
            <li><a href="{{ route('venta_index') }}"></i>{{trans('tsidebar.factura_venta_manual')}}</a></li>
            <li><a href="{{ route('ventas.index_cierre') }}"></i>{{trans('tsidebar.factura_caja')}}</a></li>
            <li><a href="{{ route('venta.index_recibo') }}"></i>{{trans('tsidebar.factura_recibo_cobro')}}</a></li>
            <li><a href="{{ route('factura_convenios.index') }}"></i>{{trans('tsidebar.factura_convenios')}}</a></li>
            <li><a href="{{ route('venta_index2') }}"></i>{{trans('tsidebar.factura_conglomerada')}}</a></li>
            <li><a href="{{ route('ventas.omni') }}"></i>{{trans('tsidebar.factura_hospitalizados')}}</a></li>
        </li>
      </ul>
    </li>
    </ul>

    <ul class="treeview-menu">
      <li class="treeview">
        <a href="#"> <i class="fa fa-file-text"> </i>2 {{trans('tsidebar.documentos')}}
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="treeview">
          <li><a href=""></i>{{trans('tsidebar.liquidacion_comision')}}</a></li>
          <li><a href=""></i>{{trans('tsidebar.liquidacion_honorarios_medicos')}}</a></li>
          <li><a href="{{ route('orden_venta') }}"></i>{{trans('tsidebar.ordenes_venta')}}</a></li>
      </li>
    </ul>
    </li>
    </ul>

    <ul class="treeview-menu">
      <li class="treeview">
        <a href="#"> <i class="fa fa-pie-chart"> </i>3 {{trans('tsidebar.informes')}}
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="treeview">
          <li><a href="{{ route('venta.informe_ventas') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_ventas')}}</a></li>
          <li><a href="{{ route('venta.informe_nca') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_ventas_netas')}}</a></li>
          <li><a href="{{ route('venta.informe_ordenes_pendientes') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_ordenes_pendiente')}}</a></li>
          <li><a href="{{ route('venta.informe_liquidaciones_comisiones') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_liquidacion_comisiones')}}</a></li>
          <li><a href=""><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_liquidacion_honorarios')}}</a></li>
          <li><a href=""><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_factura')}}</a></li>
      </li>
    </ul>
    </li>
    </ul>

    @if(in_array($rolUsuario, array(1)) == true)
    <li class="treeview">
      <a href="#"> <i class="fa  fa-shopping-cart"> </i>{{trans('tsidebar.importaciones')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li><a href="{{ route('contable.importaciones.pre_orden') }}">Pre Orden</a></li>
        <li><a href="{{ route('importaciones.index') }}">{{trans('tsidebar.importaciones')}}</a></li>
        <li><a href="{{ route('gastosimportacion.index') }}">{{trans('tsidebar.mantenimiento_gastos')}}</a></li>
    </li>
    </ul>
    </li>
    @endif



    </li>
    <li class="treeview">
      <a href="#"> <i class="fa fa-cogs"> </i>{{trans('tsidebar.mantenimiento')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li><a href="{{ route('establecimiento.index') }}"></i>{{trans('tsidebar.sucursales')}}</a></li>
        <li><a href="{{ route('punto_emision.index') }}"></i>{{trans('tsidebar.punto_emision')}}</a></li>
        <li><a href="{{ route('empleados.index') }}"></i>{{trans('tsidebar.asignar_recaudación')}} P-E</a></li>
        <li><a href="{{ route('divisas.index')}}"></i>{{trans('tsidebar.divisas')}}</a></li>
        <li><a href="{{ route('tipo_pago.index') }}"></i>{{trans('tsidebar.tipo_pago')}}</a></li>
        <li><a href="{{ route('tipo_tarjeta.index') }}"></i>{{trans('tsidebar.tipo_tarjeta')}}</a></li>
        <li><a href="{{ route('bodegas.index') }}"></i>{{trans('tsidebar.bodegas')}}</a></li>
        <!--<li><a href="#"></i>Bancos</a></li>
                    <li><a href="#"></i>Ciudad</a></li>-->
        <!--<li><a href="{{ route('caja_banco.index') }}"></i>Cajas y Bancos</a></li>-->
        <li><a href="{{ route('tipo_emision.index') }}"></i>{{trans('tsidebar.tipo_emision')}}</a></li>
        <li><a href="{{ route('tipo_comprobante.index') }}"></i>{{trans('tsidebar.tipo_comprobante')}}</a></li>
        <li><a href="{{ route('tipo_ambiente.index') }}"></i>{{trans('tsidebar.tipo_ambiente')}}</a></li>
        <li><a href="{{ route('porcentaje_imp_renta.index') }}"></i>% {{trans('tsidebar.retencion_impuesto_renta')}}</a></li>
        <li><a href="{{ route('Porcentaje.index') }}"></i>% {{trans('tsidebar.pago_impuesto_renta')}}</a></li>
        <li><a href="{{ route('retenciones.index') }}"></i>{{trans('tsidebar.retenciones')}}</a></li>
    </li>
    </ul>
    </li>

    <li class="treeview">
      <a href="#"> <i class="fa fa-cog"> </i>Configuraciones
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li><a href="{{ route('configuraciones.index') }}"></i>Plan de Cuenta</a></li>
        <li><a href="{{ route('productos.productos_tarifario') }}"></i>Producto Tarifario</a></li>
        <li><a href="{{route('ctglobales.index')}}">Configuraciones Globales</a></li>
        <li><a href="{{ route('configuraciones_pdf_index') }}"></i>Configuraciones Pdf</a></li>
    </li>
    </ul>
    </li>
    </ul>
    </li>
    @endif
    </ul>
    <ul class="treeview-menu">
      @if(in_array($rolUsuario, array(1,22)) == true)
      <li class="treeview @if(strrpos($sidebar, 'financiero')) active @endif">
        <a href="#"><i class="glyphicon glyphicon-book"></i> <span>{{trans('tsidebar.financiero')}}</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">

          <li><a href="{{ route('balance_general.index') }}"></i>{{trans('tsidebar.balance_general')}}</a></li>
          <li class="@if(strrpos($sidebar, 'estado/situacion/consolidado')) active @endif"><a href="{{ route('financiero.estadosituacion') }}"></i>{{trans('tsidebar.estado_situacion_consolidado')}}</a></li>
          <li><a href="{{ route('estadoresultados.index') }}"></i>{{trans('tsidebar.estado_resultado_integral')}}</a></li>
          <li class="@if(strrpos($sidebar, 'estado/resultados/consolidado')) active @endif"><a href="{{ route('financiero.estadoresultados') }}"></i>{{trans('tsidebar.estado_resultado_consolidado')}}</a></$ <li class="@if(strrpos($sidebar, 'indicador/consolidado')) active @endif"><a href="{{ route('financiero.indicadorconsolidado') }}"></i>{{trans('tsidebar.indicador_financiero_consolidado')}}</a></li>
          <li class="@if(strrpos($sidebar, 'indice/financiero')) active @endif"><a href="{{ route('financiero.indicefinanciero_index') }}"></i>{{trans('tsidebar.indice_financiero')}}</a></li>
          <li class="@if(strrpos($sidebar, 'proyeccion/financiera')) active @endif"><a href="{{ route('financiero.proyeccionfinanciera') }}"></i>{{trans('tsidebar.proyeccion_financiera')}}</a></li>
          <li><a href="{{ route('financiero.proyeccionfinanciera2_index') }}"></i>{{trans('tsidebar.proyeccion_financiera')}} II</a></li>
          <li><a href="{{ route('financiero.proyeccionfinanciera3') }}"></i>{{trans('tsidebar.proyeccion_financiera')}} III</a></li>
        </ul>
      </li>
      @endif
    </ul>
    <ul class="treeview-menu">
      @if(in_array($rolUsuario, array(1, 11, 22)))
      <li class="treeview">
        <a href="#"><i class="fa fa-cubes"></i> <span>{{trans('tsidebar.Liquidacion')}}</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="{{ route('planilla.genera_planillas') }}">{{trans('tsidebar.Ingresar_Planillas')}}</a></li>
          <li><a href="{{ route('estaditicos_plano.index') }}"><i class="fa fa-pie-chart"> </i> {{trans('tsidebar.Estadisticos')}}</a></li>
          <li><a href="{{ route('formatosProductos.index') }}">{{trans('tsidebar.Formato_Producto')}}</a></li>
          <li><a href="{{ route('planilla.planillas_generadas') }}">{{trans('tsidebar.Consulta_General_de_Planilla')}}</a></li>
          <li><a href="{{ route('planilla.reportes') }}">{{trans('tsidebar.Generacion_de_Reportes')}}</a></li>
          <li><a href="{{ route('planilla.genera_ap') }}">{{trans('tsidebar.Generar_Archivo_Plano_IESS')}}</a></li>
          <li><a href="{{ route('genera_ap_msp.planilla') }}">{{trans('tsidebar.Generar_Archivo_Plano_MSP')}}</a></li>
          <li><a href="{{ url('administracion/procedimientos') }}">{{trans('tsidebar.Mantenimiento_Items')}}</a></li>
          <li><a href="{{ url('administracion/plantillas') }}">{{trans('tsidebar.Mantenimiento_Plantillas')}}</a></li>
        </ul>
      </li>
      @endif
    </ul>
    <ul class="treeview-menu">
      @if(in_array($rolUsuario, array(1, 4, 5, 21, 20, 22)) == true || $id_auth == '0922053467')
      <li><a href="{{ route('reporte.index_cierre') }}"><i class="fa fa-money"></i> <span>{{trans('tsidebar.cierre_caja')}}</span></a></li>
      @endif
    </ul>
    <ul class="treeview-menu">
      @if(in_array($rolUsuario, array(1,20)) == true)
      @if($id_empresa == '0993075000001')
      <li><a href="{{ route('c_caja.index') }}"><i class="fa fa-money"></i> <span>Cierre de Caja Laboratorio</span></a></li>
      @endif
      @endif
    </ul>

    </li>


    <li class="treeview  @if(strrpos($sidebar, 'recursos')) active @endif ">
      <a href="#"><i class="fa fa-users"></i><span>{{trans('tsidebar.RecursosHumanos')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        @if($rolUsuario == 1)
        <li class="treeview">
          <a href="#"> <i class="fa fa-file"> </i>{{trans('tsidebar.Permisos_Laborales')}}
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-rigth"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{route('ticketpermisos.index')}}"><i class="fa fa-ticket" aria-hidden="true"></i>Solicitud de permisos</a></li>
            <li><a href="{{route('ticketpermisos.index_usuario')}}"><i class="fa fa-ticket" aria-hidden="true"></i>Permisos por usuarios</a></li>
          </ul>
        </li>
        @endif
        @if($rolUsuario != 2)
        <li><a href="{{route('ticketpermisos.index_usuario')}}"><i class="fa fa-file"></i><span>{{trans('tsidebar.Solicitud_de_Permiso')}}</span></a></li>
        @endif
        @if($id_auth == '0912197217' || $id_auth == '0954400404')
        <li><a href="{{route('ticketpermisos.index')}}"><i class="fa fa-file"></i><span>Administración de Permisos</span></a></li>
        @endif
      </ul>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(2)) == false)
        <li><a href="{{ route('historial.rol') }}"><i class="fa fa-file-text"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>{{trans('tsidebar.tus_roles_pago')}}</span></a></li>
        @endif
      </ul>

    </li>

    <li class="treeview  @if(strrpos($sidebar, 'reportes')) active @endif ">
      <a href="#"><i class="fa fa-pie-chart"></i><span>{{trans('tsidebar.Reportes')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1,4,5,11, 20)) == true)
        <li class="treeview">
          <a href="#"><i class="fa fa-table"></i> <span>{{trans('tsidebar.reportes')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>

          <ul class="treeview-menu">
            <li><a href="{{ route('agenda.reportediario') }}">{{trans('tsidebar.Agendamiento_Diario')}}</a></li>
            <!--reporte agenda-->
            <li><a href="{{ route('pentax.reporteagenda') }}">{{trans('tsidebar.procedimientos_endoscopicos')}}</a></li>
            <!--reporte drH  CAMBIOS 08052018-->
            <li><a href="{{ route('consultam.reporteagenda') }}">{{trans('tsidebar.otros_procedimientos')}}</a></li>
            <li><a href="{{ route('consultam.reporteagenda2') }}">{{trans('tsidebar.procedimientos_doctor')}}</a></li>
            <!--reporte Hospitalizados-->
            <li><a href="{{ route('hospitalizados.reporte') }}">{{trans('tsidebar.hospitalizados')}}</a></li>
          </ul>
        </li>
        @endif
      </ul>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1,20, 22,14)) == true)
        <li><a href="{{ route('venta.estadisticos') }}"><i class="fa fa-pie-chart"> </i>&nbsp;<span> {{trans('tsidebar.estadisticos_facturas_venta')}} </span></a></li>
        @endif
      </ul>

      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1, 22)) == true )
        <li><a href="{{ route('ap_estadisticos.honorarios') }}"><i class="fa fa-list-ol"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>{{trans('tsidebar.consolidado_honorarios')}}</span></a></li>
        @endif
      </ul>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1, 20, 22)) == true)
        <li><a href="{{ route('facturalabs.reporte_anual') }}"><i class="fa fa-calendar"></i> <span>{{trans('tsidebar.reporte_anual_laboratorio')}}</span></a></li>
        @endif
      </ul>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1, 4, 5, 21, 20, 22)) == true || $id_auth == '0922053467')
        <li><a href="{{ route('estaditicos_plano.orden') }}"><i class="fa fa-pie-chart"></i> <span>{{trans('tsidebar.estadisticos_recibo_cobro')}}</span></a></li>
        @endif
      </ul>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1)) == true)
        <li><a href="{{ route('consultas.index_rfellows') }}"><i class="fa fa-book"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>{{trans('tsidebar.Reporte_Fellows')}}</span></a></li>
        @endif
      </ul>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1, 7, 20)) == true)
        <li class="treeview">
          <a href="#"><i class="fa fa-file"></i> <span>{{trans('tsidebar.insumos_reportes')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ route('reporte.buscador_master') }}">{{trans('tsidebar.master')}}</a></li>
            <li><a href="{{ route('reporte.reporte_bodega') }}">{{trans('tsidebar.Productos_en_Bodega')}}</a></li>
            <li><a href="{{ route('reporte.reporte_caducado') }}">{{trans('tsidebar.Productos_Caducados')}}</a></li>
            <li><a href="{{ route('reporte.buscador_usos') }}">{{trans('tsidebar.Uso_de_Productos')}}</a></li>
            <li><a href="{{ route('reporte.buscador_usos_equipo') }}">{{trans('tsidebar.Uso_de_Equipos')}}</a></li>
            <li><a href="{{ route('insumos.kardex.index') }}">{{trans('tsidebar.Kardex')}}</a></li>
            <li><a href="{{ route('insumos.inventario.index') }}">{{trans('tsidebar.Existencias')}}</a></li>
            <li><a href="{{ route('insumos.inventario_serie.index') }}">{{trans('tsidebar.Existencias_Serie')}}</a></li>
            <li><a href="{{ route('insumos.inventario.busqueda') }}">{{trans('tsidebar.Busqueda_de_Item')}}</a></li>
            <li><a href="{{ route('insumos.inventario.egresoprocedimiento') }}">{{trans('tsidebar.Materiales_Utilizados')}}</a></li>
          </ul>
        </li>
        @endif
      </ul>

      <ul class="treeview-menu">
        @php
        $control = UsuariosControl::where('estado',1)->get();
        @endphp
        @foreach($control as $val)
        @if($val->user == $id_auth)
        <li><a href="{{route('index_control')}}"><i class="fa fa-user-md"></i><span>Control de sintomas</span></a></li>
        @endif
        @endforeach
      </ul>

    </li>

    <li class="treeview  @if(strrpos($sidebar, 'insumos')) active @endif ">
      <a href="#"><i class="fa fa-list-alt"></i><span>{{trans('tsidebar.Suministro')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1, 7, 20)) == true)
        <li class="treeview">
          <a href="#"><i class="glyphicon glyphicon-list-alt"></i> <span>{{trans('tsidebar.insumos')}}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ route('proveedor.index') }}">{{trans('tsidebar.Proveedores')}}</a></li>
            <li><a href="{{ route('productos.comparar.index') }}">{{trans('tsidebar.Comparativo')}}</a></li>
            <li><a href="{{ url('bodega') }}">{{trans('tsidebar.Bodegas')}}</a></li>
            <li><a href="{{ route('producto.index') }}">{{trans('tsidebar.Productos')}}</a></li>
            <li><a href="{{ route('marca.index') }}">{{trans('tsidebar.Marcas')}}</a></li>
            <li><a href="{{ route('tipo.index') }}">{{trans('tsidebar.Tipos_de_Productos')}}</a></li>
            <li><a href="{{ route('transito.index_transito') }}">{{trans('tsidebar.Productos_en_Transito')}}</a></li>
            <li><a href="{{ route('codigo.barra') }}">{{trans('tsidebar.Pedidos_Realizados')}}</a></li>
            <li><a href="{{ route('inventario.ingresos.egresos.varios') }}">{{trans('tsidebar.Ingresos_Egresos_Varios')}}</a></li>
            <li><a href="{{ route('equipo.index') }}">{{trans('tsidebar.Equipos_Medicos')}}</a></li>
            <li><a href="{{ route('plantilla.index') }}">{{trans('tsidebar.Plantillas_Procedimientos_Enfermeria')}}</a></li>
            <li><a href="{{ route('plantilla_procedimiento.index') }}">{{trans('tsidebar.Plantillas_Procedimientos_Control')}}</a></li>
          </ul>
        </li>
        @endif
      </ul>
    </li>

    <li class="treeview  @if(strrpos($sidebar, 'configuraciones')) active @endif ">
      <a href="#"><i class="fa fa-cog"></i><span>{{trans('tsidebar.Configuraciones')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        @if(in_array($rolUsuario, array(1)) == true)
        li><a href="{{ route('user-management.index') }}"><i class="fa fa-user-md"></i> <span>{{trans('tsidebar.administracion_usuarios')}}</span></a>
    </li>
    @endif
    </ul>
    <ul class="treeview-menu">
      @if(in_array($rolUsuario, array(1, 4)) == true)
      <li class="treeview">
        <a href="#"><i class="fa fa-link"></i> <span>{{trans('tsidebar.administracion_sistemas')}}</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="{{ url('especialidad ') }}"><i class="fa fa-fw fa-briefcase"></i> <span>Especialidades</span></a></li>
        </ul>
        <ul class="treeview-menu">
          <li><a href="{{ route('hospital-management.index') }}"><i class="fa fa-building"></i> <span>Ubicaciones</span></a></li>
        </ul>
        <ul class="treeview-menu">
          <li><a href="{{ route('procedimiento.index') }}"><i class="fa fa-book"></i> <span>Procedimientos</span></a></li>
        </ul>
        <ul class="treeview-menu">
          <li><a href="{{ url('form_enviar_seguro') }}"><i class="fa fa-fw fa-medkit"></i> <span>Seguros</span></a></li>
        </ul>
        <ul class="treeview-menu">
          <li><a href="{{ url('empresa') }}">Empresas</a></li>
        </ul>
        <ul class="treeview-menu">
          <li><a href="{{ route('tituloprofesional.index') }}">Mantenimiento Titulo Profesional</a></li>
        </ul>
        <ul class="treeview-menu">
          <li><a href="{{ url('tecnicas') }}">Procedimientos Completos</a></li>
        </ul>
        <ul class="treeview-menu">
          <li><a href="{{ route('tipo_usuario-management.index') }}"><span>Tipos de Usuario</span></a></li>
        </ul>
        <ul class="treeview-menu">
          <li><a href="{{ url('cie_10_3') }}">Cie 10 3</a></li>
        </ul>
        <ul class="treeview-menu">
          <li><a href="{{ url('cie_10_4') }}">Cie 10 4</a></li>
        </ul>
        @if(in_array($rolUsuario, array(1)) == true)
        @endif
      </li>
      @endif
    </ul>
    <ul class="treeview-menu">
      @if(in_array($rolUsuario, array(1, 4, 5, 14, 20, 22)) == true)
      <li><a href="{{ url('manual') }}"><i class="fa fa-file-pdf-o"></i> <span>{{trans('tsidebar.tarifarios')}}</span></a></li>
      @endif
    </ul>

    </li>


    @if(in_array($rolUsuario, array(1, 4, 12, 22)) == true)
    <!--li><a href="{{ route('produccion.produccion_estad') }}" ><i class="fa fa-user-md"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Producción de Médicos</span></a></li-->
    @endif

    <!-- @if(in_array($rolUsuario, array(2)) == false)
        <li><a href="{{ route('prestamos_empleados.prestamos_visualizar') }}" ><i class="ion-person-stalker"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Prestamos</span></a></li>
        @endif -->

    @php
    $agenda_permiso = Sis_medico\Agenda_Permiso::where('id_usuario', $id_auth)->where('ip_creacion','like','%COMERCIAL%')->first();
    @endphp

    @if(!is_null($agenda_permiso) || in_array($rolUsuario, array(1)) == true)
    <li class="treeview  @if(strrpos($sidebar, 'insumos')) active @endif ">
      <a href="#"><i class="glyphicon glyphicon-list-alt"></i> <span>Comercial</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li><a href="{{ route('comercial.proforma.index_proforma') }}">{{trans('tsidebar.proformas')}}</a></li>
      </ul>
      <ul class="treeview-menu">
        <li><a href="{{ route('prodtarifario.index') }}">{{trans('tsidebar.producto_tarifario')}}</a></li>
      </ul>
    </li>
    @endif



    @if(in_array($rolUsuario, array(2)) == true)
    <li><a href="{{ route('paciente.historial_examenes') }}"><i class="fa fa-book"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>{{trans('tsidebar.historial_ordenes')}} </span></a></li>
    @endif

    @if(in_array($rolUsuario, array(2)) == true)
    <li><a href="{{ route('recetas_usuario') }}"><i class="fa fa-file-text-o" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>{{trans('tsidebar.historial_receta')}} </span></a></li>
    @endif

    @if(in_array($rolUsuario, array(9, 1, 7)) == true)
    <li><a href="{{ route('enfermeria.index_insumos') }}"><i class="glyphicon glyphicon-plus-sign"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>{{trans('tsidebar.anestesiologos')}} </span></a></li>
    @endif
    @if(in_array($rolUsuario, array(1,20, 22,14)) == true)
    <li><a href="{{ route('venta.estadisticos') }}"><i class="fa fa-pie-chart"> </i>&nbsp;<span> {{trans('tsidebar.estadisticos_facturas_venta')}} </span></a></li>
    @endif

    @if(in_array($rolUsuario, array(1,22)) == true)
    <li class="treeview @if(strrpos($sidebar, 'financiero')) active @endif">
      <a href="#"><i class="glyphicon glyphicon-book"></i> <span>{{trans('tsidebar.financiero')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">

        <li><a href="{{ route('balance_general.index') }}"></i>{{trans('tsidebar.balance_general')}}</a></li>
        <li class="@if(strrpos($sidebar, 'estado/situacion/consolidado')) active @endif"><a href="{{ route('financiero.estadosituacion') }}"></i>{{trans('tsidebar.estado_situacion_consolidado')}}</a></li>
        <li><a href="{{ route('estadoresultados.index') }}"></i>{{trans('tsidebar.estado_resultado_integral')}}</a></li>
        <li class="@if(strrpos($sidebar, 'estado/resultados/consolidado')) active @endif"><a href="{{ route('financiero.estadoresultados') }}"></i>{{trans('tsidebar.estado_resultado_consolidado')}}</a></$ <li class="@if(strrpos($sidebar, 'indicador/consolidado')) active @endif"><a href="{{ route('financiero.indicadorconsolidado') }}"></i>{{trans('tsidebar.indicador_financiero_consolidado')}}</a></li>
        <li class="@if(strrpos($sidebar, 'indice/financiero')) active @endif"><a href="{{ route('financiero.indicefinanciero_index') }}"></i>{{trans('tsidebar.indice_financiero')}}</a></li>
        <li class="@if(strrpos($sidebar, 'proyeccion/financiera')) active @endif"><a href="{{ route('financiero.proyeccionfinanciera') }}"></i>{{trans('tsidebar.proyeccion_financiera')}}</a></li>
        <li><a href="{{ route('financiero.proyeccionfinanciera2_index') }}"></i>{{trans('tsidebar.proyeccion_financiera')}} II</a></li>
        <li><a href="{{ route('financiero.proyeccionfinanciera3') }}"></i>{{trans('tsidebar.proyeccion_financiera')}} III</a></li>
      </ul>
    </li>
    @endif


    @if(in_array($rolUsuario, array(1,20, 22)) == true)
    <li class="treeview @if(strrpos($sidebar, 'contable')) active @endif">

      <a href="#"><i class="glyphicon glyphicon-book"></i> <span>{{trans('tsidebar.contable')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview @if(strrpos($sidebar, 'contable/empresa')) active @endif">
          <a href="#"> <i class="fa fa-building"> </i>{{trans('tsidebar.empresa')}}
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-rigth"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="treeview @if(strrpos($sidebar, 'empresa/seleccion')) active @endif">
              <a href="{{ route('plan_cuentas.seleccion_empresa') }}"></i>{{trans('tsidebar.seleccion_empresa')}}</a>
            </li>
          </ul>
        </li>

        <li class="treeview @if(strrpos($sidebar, 'contable/contabilidad')) active @endif">
          <a href="#"> <i class="fa fa-university"> </i>{{trans('tsidebar.contabilidad')}}
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-rigth"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="treeview @if(strrpos($sidebar, 'plan_cuentas')) active @endif">
              <a href="{{ route('plan_cuentas.index') }}"></i>{{trans('tsidebar.plan_cuentas')}}</a>
            </li>
            <li class="treeview @if(strrpos($sidebar, 'libro_mayor')) active @endif">
              <a href="{{ route('libro_mayor.index') }}"></i>{{trans('tsidebar.libro_mayor')}}</a>
            </li>
            <li class="treeview @if(strrpos($sidebar, 'contabilidad/balance_general')) active @endif"><a href="{{ route('balance_general.index') }}"></i>{{trans('tsidebar.balance_general')}}</a></li>
            <li class="treeview @if(strrpos($sidebar, 'contabilidad/estado/resultados')) active @endif"><a href="{{ route('estadoresultados.index') }}"></i>{{trans('tsidebar.estado_resultado_integral')}}</a></li>
            <li class="treeview @if(strrpos($sidebar, 'contabilidad/balance_comprobacion')) active @endif"><a href="{{ route('balance_comprobacion.index') }}"></i>{{trans('tsidebar.balance_comprobacion')}}</a></li>
            <li class="treeview @if(strrpos($sidebar, 'contabilidad/libro')) active @endif"><a href="{{ route('librodiario.index') }}"></i>{{trans('tsidebar.visualizar_asientos_diarios')}}</a></li>
            {{-- <li><a href="{{ route('compras.informe') }}"></i>{{trans('tsidebar.informe_compras')}}</a>
        </li> --}}
        <li class="treeview @if(strrpos($sidebar, 'contabilidad/flujo/efectivo_1')) active @endif"><a href="{{ route('flujoefectivo.index') }}"></i>{{trans('tsidebar.flujo_efectivo')}}</a></li>
        <li class="treeview @if(strrpos($sidebar, 'contabilidad/flujo/efectivo/comparativo_1')) active @endif"><a href="{{ route('flujoefectivocomparativo.index') }}"></i>{{trans('tsidebar.flujo_efectivo_comparativo')}}</a></li>
        <li class="treeview @if(strrpos($sidebar, 'contabilidad/flujo/efectivo/comparativo/dos')) active @endif"><a href="{{ route('flujoefectivocomparativo.index2') }}"></i>{{trans('tsidebar.flujo_efectivo_comparativo')}} II</a></li>
        <li class="treeview @if(strrpos($sidebar, 'contabilidad/ats')) active @endif"><a href="{{ route('ats.index') }}"></i>SRI / ATS</a></li>
        <li class="treeview @if(strrpos($sidebar, 'contabilidad/descuadrados')) active @endif"><a href="{{ route('librodiario.descuadrados') }}"></i>{{trans('tsidebar.descuadrados')}}</a></li>
      </ul>
    </li>

    <li class="treeview @if(strrpos($sidebar, 'acreedores')) active @endif">
      <a href="#"> <i class="fa  fa-users"> </i>{{trans('tsidebar.acreedores')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview @if(strrpos($sidebar, 'acreedores/mantenimiento')) active @endif">
          <a href="#"> <i class="fa fa-bars"> </i>1 {{trans('tsidebar.mantenimiento')}}
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-rigth"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="treeview @if(strrpos($sidebar, 'acreedores/mantenimiento/rubrosa')) active @endif"><a href="{{ route('rubrosa.index') }}"><i class="fa fa-gear"></i>{{trans('tsidebar.rubros_acreedores')}}</a></li>
            <li class="treeview @if(strrpos($sidebar, 'acreedores/mantenimiento/acreedor')) active @endif"><a href="{{ route('acreedores_index') }}"><i class="fa fa-gear"></i>{{trans('tsidebar.acreedores')}}</a></li>
            <li class="treeview @if(strrpos($sidebar, 'acreedores/mantenimiento/acreedor')) active @endif"><a href="{{ route('saldosinicialesp.index2') }}"> {{trans('tsidebar.saldos_iniciales')}}</a></li>

          </ul>

        </li>
      </ul>
      <ul class="treeview-menu">
        <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos')) active @endif">
          <a href="#"> <i class="fa  fa-file-text"> </i>2 {{trans('tsidebar.documentos')}}
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-rigth"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="treeview">
            <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos/retenciones')) active @endif"><a href="{{ route('retenciones_index') }}"><i class="fa fa-calculator"></i>{{trans('tsidebar.retenciones')}}</a></li>
            <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos/comprobante/egreso')) active @endif"><a href="{{ route('acreedores_cegreso') }}"><i class="fa  fa-file-text"></i>{{trans('tsidebar.comprobante_egreso')}}</a></li>

            <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos/comprobante/index')) active @endif"><a href="{{ route('comp_egreso_masivo.index') }}"><i class="fa  fa-file-text"></i>{{trans('tsidebar.comprobante_egreso_masivo')}}</a></li>
            <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos/cuentas/egreso/varios')) active @endif"><a href="{{ route('egresosv.index') }}"><i class="fa  fa-file-text"></i> {{trans('tsidebar.comprobante_egreso_vario')}}</a></li>
            <li><a href="{{ route('cruce.index') }}"><i class="fa fa-exchange"></i>{{trans('tsidebar.cruce_valores_favor')}}</a></li>
            <li class="treeview @if(strrpos($sidebar, 'contable/cruce_cuentas/valores')) active @endif"><a href="{{ route('pr.cruce_cuentas') }}"><i class="fa  fa-file-text"></i> {{trans('tsidebar.cruce_cuentas')}}</a></li>
            <li><a href="{{ route('creditoacreedores.index') }}"><i class="fa fa-clone"></i>{{trans('tsidebar.nota_credito')}}</a></li>
            <li><a href="{{ route('debitoacreedores.index') }}"><i class="fa fa-file-o"></i>{{trans('tsidebar.nota_debito')}}</a></li>

        </li>
      </ul>

    </li>
    </ul>
    <ul class="treeview-menu">
      <li class="treeview @if(strrpos($sidebar, 'acreedores/informes')) active @endif">
        <a href="#"> <i class="fa fa-pie-chart"> </i>3 {{trans('tsidebar.informes')}}
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="treeview @if(strrpos($sidebar, 'acreedores/informes/cartera/pagar')) active @endif">
            <a href="{{ route('acreedores.informenc') }}"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.informes_nota_creadito')}}</a>
          </li>
          <li class="treeview @if(strrpos($sidebar, 'acreedores/credito/informe')) active @endif">
            <a href="{{ route('carterap.index') }}"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.carteras_pagar')}}</a>
          </li>
          <li class="treeview @if(strrpos($sidebar, 'acreedores/informes/informe/saldos')) active @endif">
            <a href="{{ route('saldos_acreedor.index') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_saldos')}}</a>
          </li>
          <li><a href="{{ route('contable.reporte_tiempo') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_tiempo')}}</a></li>
          <li><a href="{{ route('chequesa.index') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_cheques_girados')}}</a></li>
          <li><a href="{{ route('informe_retenciones.index') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_retenciones')}}</a></li>
          <li><a href="{{ route('contable.anticipo_proveedores') }}"><i class="fa fa-file-excel-o"></i>I{{trans('tsidebar.informe_anticipos')}}</a></li>
          <li><a href="{{ route('deudasvspagos.index') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.deudas_vs_pagos')}}</a></li>
          <li><a href="{{ route('deudas_pendientes.index') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.deudas_pendientes')}}</a></li>

        </ul>

      </li>
    </ul>
    </li>

    <li class="treeview">
      <a href="#"> <i class="fa  fa-users"> </i>{{trans('tsidebar.clientes')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
          <a href="#"> <i class="fa fa-bars"> </i>1 {{trans('tsidebar.mantenimiento')}}
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-rigth"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="treeview">
            <li><a href="{{ route('clientes.index') }}"></i>{{trans('tsidebar.crear_cleinte')}}</a></li>
            <li><a href="{{ route('rubros_cliente.index') }}"></i>{{trans('tsidebar.rubros_clientes')}}</a></li>
            <li><a href="{{ route('saldosinicialesclientes.index2') }}"></i>{{trans('tsidebar.saldos_iniciales')}}</a></li>
        </li>
      </ul>
    </li>
    </ul>
    <ul class="treeview-menu">
      <li class="treeview">
        <a href="#"> <i class="fa  fa-file-text"> </i>2 {{trans('tsidebar.documentos')}}
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>

        <ul class="treeview-menu">
          <li class="treeview">
          <li><a href="{{route('retencion.cliente')}}"><i class="fa fa-calculator"></i>{{trans('tsidebar.retenciones')}}</a></li>
          <li><a href="{{route('chequespost.index')}}"><i class="fa fa-files-o"></i>{{trans('tsidebar.cheques_postfechados')}}</a></li>
          <li><a href="{{route('cr.cruce_cuentas')}}"><i class="fa fa-files-o"></i>{{trans('tsidebar.cruce_cuentas')}}</a></li>
          <li><a href="{{route('comprobante_ingreso.index')}}"><i class="fa  fa-file-text"></i>{{trans('tsidebar.comprobante_ingreso')}}</a></li>
          <li><a href="{{route('comprobante_ingreso_v.index')}}"><i class="fa  fa-file-text"></i>{{trans('tsidebar.comprobante_ingresos_varios')}}</a></li>
          <li><a href="{{route('cruce_clientes.index')}}"><i class="fa fa-exchange"></i>{{trans('tsidebar.cruce_valores')}}</a></li>
          <li><a href="{{route('nota_credito_cliente.index')}}"><i class="fa fa-clone"></i>{{trans('tsidebar.nota_credito')}}</a></li>
          <li><a href="{{route('nota_cliente_debito.index')}}"><i class="fa fa-file-o"></i>{{trans('tsidebar.nota_debito')}}</a></li>

      </li>
    </ul>

    </li>
    </ul>
    <ul class="treeview-menu">
      <li class="treeview">
        <a href="#"> <i class="fa fa-pie-chart"> </i>3 {{trans('tsidebar.informes')}}
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="treeview">
          <li><a href="{{route('clientes.deudas.pendientes')}}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.deudas_pendientes')}}</a></li>
          <li><a href="{{route('clientes.saldo.cxc')}}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.saldo_cuentas_por_cobrar')}}</a></li>
          <li><a href="{{route('cliente.informe.retenciones')}}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_retenciones')}}</a></li>
          {{-- <li><a href="#"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.carteras_pagar')}}</a>
      </li>
      <li><a href="#"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_saldos')}}</a></li>
      <li><a href="#"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_cheques_girados')}}</a></li>
      <li><a href="#"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_retenciones')}}</a></li> --}}
      <li><a href="{{route('deudasvspagos.cliente')}}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.deudas_vs_pagos')}}</a></li>
      <li><a href="{{ route('nota_credito_cliente.informe_notacreditoclientes') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_notas_credito_clientes')}}</a></li>
      </li>
    </ul>

    </li>
    </ul>
    </li>

    <li class="treeview @if(strrpos($sidebar, 'Banco')) active @endif">
      {{-- <a href="#"> <i class="fa fa-cubes"> </i>Caja y Bancos --}}
      <a href="#"> <i class="fa fa-money"> </i>{{trans('tsidebar.caja_bancos')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview "><a href="{{ route('caja_banco.index') }}"></i>{{trans('tsidebar.crear_cajabanco')}}</a></li>
        <li><a href="{{ route('banco_clientes.index') }}"></i>{{trans('tsidebar.listado_bancos')}}</a></li>
        <li class="treeview @if(strrpos($sidebar, 'Banco/notadebito')) active @endif"><a href="{{ route('notadebito.index') }}"></<i>{{trans('tsidebar.nota_debito')}}</a></li>
        <li class="treeview"><a href="{{ route('notacredito.index') }}"></i>{{trans('tsidebar.nota_credito')}}</a></li>
        <li class="treeview"><a href="{{ route('debitobancario.index') }}"></i>{{trans('tsidebar.nota_debito_bancaria')}}</a></li>
        <li class="treeview"><a href="{{ route('depositobancario.index') }}"></i>{{trans('tsidebar.deposito_bancario')}}</a></li>
        <li class="treeview"><a href="{{ route('transferenciabancaria.index') }}"></i>{{trans('tsidebar.transferencia_bancaria')}}</a></li>
        <li class="treeview"><a href="{{ route('conciliacionbancaria.index') }}"></i>{{trans('tsidebar.conciliacion_bancaria')}}</a></li>
        <li class="treeview"><a href="{{ route('estadocuentabancos.index') }}"></i>{{trans('tsidebar.estado_cuenta')}}</a></li>
      </ul>
    </li>

    <li class="treeview">
      <a href="#">
        <i class="fa fa-share"></i> <span>{{trans('tsidebar.activos_fijos')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
          <a href="#"><i class="fa fa-circle-o"></i> {{trans('tsidebar.mantenimientos')}}
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ route('afGrupo.index') }}"> {{trans('tsidebar.grupos')}}</a></li>
            <li><a href="{{ route('afTipo.index') }}"> {{trans('tsidebar.tipos')}}</a></li>
            <li><a href="{{ route('afActivo.index') }}"> {{trans('tsidebar.activos')}}</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#"><i class="fa fa-circle-o"></i> {{trans('tsidebar.documentos')}}
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ route('afDocumentoFactura.index') }}"> {{trans('tsidebar.facturas')}}</a></li>
            <li><a href="{{ route('afDepreciacion.index') }}"> {{trans('tsidebar.depreciaciones')}}</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#"><i class="fa fa-circle-o"></i> {{trans('tsidebar.informes')}}
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ route('afInformes.index') }}"> {{trans('tsidebar.saldos')}}</a></li>
            <li><a href="{{route('activofjo.index_listado')}}">{{trans('tsidebar.listado_activos')}}</a></li>
            {{-- <li><a href="#"> Retenci&oacute;n</a></li> --}}
            {{-- <li><a href="#"> Facturas</a></li> --}}
          </ul>
        </li>
      </ul>
    </li>

    <li class="treeview">
      <a href="#"> <i class="fa fa-truck"> </i>{{trans('tsidebar.compras')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li><a href="{{ route('compras_index') }}"></i>{{trans('tsidebar.compras')}}</a></li>
        <li><a href="{{ route('saldosinicialesp.index2') }}"></i>{{trans('tsidebar.saldos_iniciales')}}</a></li>
        <li><a href="{{ route('compras.pedidos') }}"></i>{{trans('tsidebar.pedidos')}}</a></li>
        <li><a href="{{ route('fact_contable_index') }}"></i>{{trans('tsidebar.factura_contable')}}</a></li>
        <li><a href="{{ route('compras.informe') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informa_compras')}}</a></li>
        <!-- <li><a href="{{ route('kardex.index') }}"></i>Kardex</a></li>-->
        <li><a href="{{ route('contable.compras.kardex.index') }}"></i>{{trans('tsidebar.kardex_compra')}}</a></li>
    </li>
    </ul>
    </li>
    <li class="treeview">
      <a href="#"> <i class="fa fa-truck"> </i>{{trans('tsidebar.compras_interno')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <!--<li class="treeview"><a href="{{ route('contable.bodega.index') }}"></i>Bodegas</a></li>-->
        <li class="treeview"><a href="{{ route('contable.compraspedidos.index') }}"></i>{{trans('tsidebar.pedidos')}}</a></li>
        <!--<li class="treeview"><a href="{{ route('saldosinicialesp.index2') }}"></i>Autorizaciones</a></li>-->
        <!--<li class="treeview"><a href="{{ route('saldosinicialesp.index2') }}"></i>Inventario</a></li>-->
        <li class="treeview"><a href="{{ route('kardex_inventario.index') }}"></i>{{trans('tsidebar.kardex')}}</a></li>
        <!--<li class="treeview"><a href="{{ route('fact_contable_index') }}"></i>Factura</a></li>-->
        <li class="treeview"><a href="{{ route('pedidos_inventario.index') }}"></i>{{trans('tsidebar.informe_productos')}}</a></li>
        <li><a href="{{ route('contable.compraspedido.indexInicial')}}">{{trans('tsidebar.mantenimiento_iniciales')}}</a></li>
        <li><a href="{{ route('compraspedidos.index_proceso')}}">{{trans('tsidebar.mantenimiento_usuarios')}}</a></li>
      </ul>
    </li>

    <li class="treeview">
      <a href="#"> <i class="fa fa-cubes"> </i>{{trans('tsidebar.inventario')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li><a href="{{ route('kardex.index') }}"></i>{{trans('tsidebar.kardex')}}</a></li>
        <li><a href="{{ route('u.getUses') }}"></i>{{trans('tsidebar.inventario_insumos')}}</a></li>
        <li><a href="{{ route('productos.saldos_iniciales') }}"></i>{{trans('tsidebar.saldos_iniciales')}}</a></li>
        <li><a href="{{ route('notainventario.index') }}"></i>{{trans('tsidebar.nota_ingreso_inventario')}}</a></li>
        <li><a href="{{ route('productos_servicios_index') }}"></i>{{trans('tsidebar.productos')}}</a></li>

    </li>
    </ul>
    </li>

    <li class="treeview">
      <a href="#"> <i class="ion-person-stalker"> </i>{{trans('tsidebar.nomina')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li><a href="{{ route('config_valor.index') }}"></i>{{trans('tsidebar.configuracion')}}</a></li>
        <li><a href="{{ route('nomina.index') }}"></i>{{trans('tsidebar.crear_empleado_rol')}}</a></li>
        <li><a href="{{ route('nuevo_rol.masivo_search') }}"></i><span style="color: red;">{{trans('tsidebar.roles_pago_empresas')}}</span></a></li>
        <li><a href="{{ route('buscador_rol.index') }}"></i>{{trans('tsidebar.rol_pago individual')}}</a></li>
        <li><a href="{{ route('prestamos_empleado.index') }}"></i>{{trans('tsidebar.prestamos_empleados')}}s</a></li>
        <li><a href="{{ route('prestamos_empleados.index_saldos') }}"></i>{{trans('tsidebar.saldo_inicial_empleados')}}</a></li>
        <!--<li><a href="{{ route('anticipos_empleado.index') }}"></i>Anticipo 1Ra Quinc Empl</a></li>-->
        <!-- <li><a href="{{ route('nomina_anticipos_1eraquincena.index') }}"></i>Anticipo 1Ra Quinc Empl</a></li> -->
        <li><a href="{{ route('nominaquincena.index_quincena')}}"></i>{{trans('tsidebar.anticipo_primera_quincena')}}</a></li>
        <li><a href="{{ route('otros_anticipos_empleado.index') }}"></i>{{trans('tsidebar.otros_anticipos_empleados')}}</a></li>
        <li><a href="{{ route('plantillas_nomina.plantillas_prestamos') }}"></i>{{trans('tsidebar.plantilla_prestamos')}}</a></li>
        <li><a href="{{ route('plantillas_nomina.horas_extras') }}"></i>{{trans('tsidebar.plantilla_horas_extras')}}</a></li>
        <!--li><a href="{{ route('prestamos_empleados.index_cruce') }}"></i>Prestamos vs Utilidades</a></li-->
    </li>
    </ul>
    <ul class="treeview-menu">
      <li class="treeview">
        <a href="#"> <i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.informes')}}
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="treeview">
            <!--<li><a href="{{ route('nomina_anticipos.index') }}"><i class="fa fa-file-excel-o"> </i>Anticipo Quincena</a></li>-->
          <li><a href="{{ route('reportes_empl.index') }}"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.datos_empleados')}}</a></li>
          <li><a href="{{ route('reportes_rol.index') }}"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.rol_pagos')}} (TTHH)</a></li>
          <li><a href="{{ route('reportes_rolcontable.index') }}"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.rol_pagos')}} ({trans('tsidebar.contabilidad')}})</a></li>
          <li><a href="{{ route('reportes_banco.index') }}"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.archivo_banco')}}</a></li>
          <li><a href="{{ route('prestamos_empleados.prestamos_saldos') }}"><i class="fa fa-file-excel-o"> </i>{{trans('tsidebar.saldo_prestamos')}}</a></li>
      </li>
    </ul>
    </li>
    </ul>
    <ul class="treeview-menu">
      <li class="treeview">
        <a href="#"> <i class="fa fa-wrench"> </i>{{trans('tsidebar.mantenimiento')}}
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="treeview">
          <li><a href="{{ route('area_rh.index') }}"><i class="fa fa-wrench"> </i> {{trans('tsidebar.area')}} </a></li>
          <li><a href="{{ route('estado_civil.index') }}"><i class="fa fa-wrench"> </i> {{trans('tsidebar.estado_civil')}} </a></li>
          <li><a href="{{ route('mantenimiento.horario.index') }}"><i class="fa fa-wrench"> </i> {{trans('tsidebar.Horario')}} </a></li>
          <li><a href="{{ route('nivel_academico.index') }}"><i class="fa fa-wrench"> </i>{{trans('tsidebar.nivel_academico')}}</a></li>
          <li><a href="{{ route('pagobeneficio.index') }}"><i class="fa fa-wrench"> </i>{{trans('tsidebar.pago_benefico')}}</a></li>
          <li><a href="{{ route('tipo_aporte.index') }}"><i class="fa fa-wrench"> </i> {{trans('tsidebar.tipo_aporte')}} </a></li>
          <li><a href="{{ route('tipo_rol.index') }}"><i class="fa fa-wrench"> </i> {{trans('tsidebar.tipo_rol')}} </a></li>

      </li>
    </ul>
    </li>
    </ul>

    </li>

    <li class="treeview">
      <a href="#"> <i class="fa fa-line-chart"> </i>{{trans('tsidebar.flujos_caja')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li><a href="{{ route('flujoefectivo.index') }}"></i>{{trans('tsidebar.flujo_efectivo')}}</a></li>
        <li><a href="{{ route('flujoefectivocomparativo.index') }}"></i>{{trans('tsidebar.flujo_efectivo_comparativo')}}</a></li>
        <li><a href="{{ route('estructuraflujoefectivo.index') }}"></i>{{trans('tsidebar.estructura_flujo_efectivo')}}</a></li>
    </li>
    </ul>
    </li>

    <li class="treeview">
      <a href="#"> <i class="fa  fa-shopping-cart"> </i>{{trans('tsidebar.ventas')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>

      <ul class="treeview-menu">
        <li class="treeview">
          <a href="#"> <i class="fa fa-bars"> </i>1 {{trans('tsidebar.factura_venta')}}
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-rigth"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="treeview">
            <li><a href="{{ route('venta_index') }}"></i>{{trans('tsidebar.factura_venta_manual')}}</a></li>
            <li><a href="{{ route('ventas.index_cierre') }}"></i>{{trans('tsidebar.factura_caja')}}</a></li>
            <li><a href="{{ route('venta.index_recibo') }}"></i>{{trans('tsidebar.factura_recibo_cobro')}}</a></li>
            <li><a href="{{ route('factura_convenios.index') }}"></i>{{trans('tsidebar.factura_convenios')}}</a></li>
            <li><a href="{{ route('venta_index2') }}"></i>{{trans('tsidebar.factura_conglomerada')}}</a></li>
            <li><a href="{{ route('ventas.omni') }}"></i>{{trans('tsidebar.factura_hospitalizados')}}</a></li>
        </li>
      </ul>
    </li>
    </ul>

    <ul class="treeview-menu">
      <li class="treeview">
        <a href="#"> <i class="fa fa-file-text"> </i>2 {{trans('tsidebar.documentos')}}
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="treeview">
          <li><a href=""></i>{{trans('tsidebar.liquidacion_comision')}}</a></li>
          <li><a href=""></i>{{trans('tsidebar.liquidacion_honorarios_medicos')}}</a></li>
          <li><a href="{{ route('orden_venta') }}"></i>{{trans('tsidebar.ordenes_venta')}}</a></li>
      </li>
    </ul>
    </li>
    </ul>

    <ul class="treeview-menu">
      <li class="treeview">
        <a href="#"> <i class="fa fa-pie-chart"> </i>3 {{trans('tsidebar.informes')}}
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="treeview">
          <li><a href="{{ route('venta.informe_ventas') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_ventas')}}</a></li>
          <li><a href="{{ route('venta.informe_nca') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_ventas_netas')}}</a></li>
          <li><a href="{{ route('venta.informe_ordenes_pendientes') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_ordenes_pendiente')}}</a></li>
          <li><a href="{{ route('venta.informe_liquidaciones_comisiones') }}"><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_liquidacion_comisiones')}}</a></li>
          <li><a href=""><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_liquidacion_honorarios')}}</a></li>
          <li><a href=""><i class="fa fa-file-excel-o"></i>{{trans('tsidebar.informe_factura')}}</a></li>
      </li>
    </ul>
    </li>
    </ul>

    @if(in_array($rolUsuario, array(1)) == true)
    <li class="treeview">
      <a href="#"> <i class="fa  fa-shopping-cart"> </i>{{trans('tsidebar.importaciones')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li><a href="{{ route('importaciones.index') }}">{{trans('tsidebar.importaciones')}}</a></li>
        <li><a href="{{ route('gastosimportacion.index') }}">{{trans('tsidebar.mantenimiento_gastos')}}</a></li>
    </li>
    </ul>
    </li>
    @endif

    @if(in_array($rolUsuario, array(1)) == true)
    <?php
    if ($id_auth == '0921605895') {
    ?>
      <li class="treeview">
        <a href="#"> <i class="fa fa-truck" aria-hidden="true"></i>Guia de Remisión
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="treeview">
          <li><a href="{{ route('guia_remision_index') }}">Guia de remisión</a></li>
          <li><a href="{{ route('transportistas.index') }}">Transportistas</a></li>
      </li>
      </ul>
      </li>
    <?php
    }
    ?>
    @endif



    </li>
    <li class="treeview">
      <a href="#"> <i class="fa fa-cogs"> </i>{{trans('tsidebar.mantenimiento')}}
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li><a href="{{ route('establecimiento.index') }}"></i>{{trans('tsidebar.sucursales')}}</a></li>
        <li><a href="{{ route('punto_emision.index') }}"></i>{{trans('tsidebar.punto_emision')}}</a></li>
        <li><a href="{{ route('empleados.index') }}"></i>{{trans('tsidebar.asignar_recaudación')}} P-E</a></li>
        <li><a href="{{ route('divisas.index')}}"></i>{{trans('tsidebar.divisas')}}</a></li>
        <li><a href="{{ route('tipo_pago.index') }}"></i>{{trans('tsidebar.tipo_pago')}}</a></li>
        <li><a href="{{ route('tipo_tarjeta.index') }}"></i>{{trans('tsidebar.tipo_tarjeta')}}</a></li>
        <li><a href="{{ route('bodegas.index') }}"></i>{{trans('tsidebar.bodegas')}}</a></li>
        <!--<li><a href="#"></i>Bancos</a></li>
                 <li><a href="#"></i>Ciudad</a></li>-->
        <!--<li><a href="{{ route('caja_banco.index') }}"></i>Cajas y Bancos</a></li>-->
        <li><a href="{{ route('tipo_emision.index') }}"></i>{{trans('tsidebar.tipo_emision')}}</a></li>
        <li><a href="{{ route('tipo_comprobante.index') }}"></i>{{trans('tsidebar.tipo_comprobante')}}</a></li>
        <li><a href="{{ route('tipo_ambiente.index') }}"></i>{{trans('tsidebar.tipo_ambiente')}}</a></li>
        <li><a href="{{ route('porcentaje_imp_renta.index') }}"></i>% {{trans('tsidebar.retencion_impuesto_renta')}}</a></li>
        <li><a href="{{ route('Porcentaje.index') }}"></i>% {{trans('tsidebar.pago_impuesto_renta')}}</a></li>
        <li><a href="{{ route('retenciones.index') }}"></i>{{trans('tsidebar.retenciones')}}</a></li>
    </li>
    </ul>
    </li>

    <li class="treeview">
      <a href="#"> <i class="fa fa-cog"> </i>Configuraciones
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li><a href="{{ route('configuraciones.index') }}"></i>Plan de Cuenta</a></li>
        <li><a href="{{ route('productos.productos_tarifario') }}"></i>Producto Tarifario</a></li>
        <li><a href="{{route('ctglobales.index')}}">Configuraciones Globales</a></li>
        <li><a href="{{ route('configuraciones_pdf_index') }}"></i>Configuraciones Pdf</a></li>
    </li>
    </ul>
    </li>
    </ul>
    </li>
    @endif
    @if(in_array($rolUsuario, array(1)) == true)
    <li><a href="{{ route('user-management.index') }}"><i class="fa fa-user-md"></i> <span>{{trans('tsidebar.administracion_usuarios')}}</span></a></li>
    @endif

    @if(in_array($rolUsuario, array(1, 20, 22)) == true)
    <li><a href="{{ route('facturalabs.reporte_anual') }}"><i class="fa fa-calendar"></i> <span>{{trans('tsidebar.reporte_anual_laboratorio')}}</span></a></li>
    @endif

    @if(in_array($rolUsuario, array(1, 4, 5, 20, 22)) == true)
    <li><a href="{{ url('agenda') }}"><i class="fa fa-calendar"></i> <span>{{trans('tsidebar.agenda')}}</span></a></li>
    @endif
    @if(in_array($rolUsuario, array(1, 4, 5, 11, 20)) == true)
    <li><a href="{{ url('paciente') }}"><i class="fa fa-fw fa-users"></i> <span>{{trans('tsidebar.pacientes')}}</span></a></li>
    @endif
    @if(in_array($rolUsuario, array(1, 4, 5, 20)) == true)
    <li><a href="{{ route('biopsias_paciente.index') }}"><i class="fa fa-file-text"></i> <span>{{trans('tsidebar.ingreso_masivo_biopsias')}}</span></a></li>
    @endif
    @if(in_array($rolUsuario, array(4, 5, 20)) == true )
    <li><a href="{{ route('orden.index') }}"><i class="ionicons ion-ios-flask"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Laboratorio - Ordenes </span></a></li>
    @endif
    @if(in_array($rolUsuario, array(1, 20)) == true )
    <li><a href="{{ route('factura_agrupada.index_factura_agrupada') }}"><i class="ionicons ion-ios-flask"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>{{trans('tsidebar.factura_agrupada_labs')}}</span></a></li>
    @endif
    @if(in_array($rolUsuario, array(1, 4, 5, 20)) == true )
    <li>
      <a href="{{ route('observacion.index') }}"><i class="glyphicon glyphicon-copy"></i>
        <span>{{trans('tsidebar.observaciones')}} </span>
        <span class="pull-right-container">
          <small class="label pull-right bg-red" id="o_cantidad"></small>
        </span>
      </a>
    </li>
    @endif
    @if(in_array($rolUsuario, array(10)) == true)
    <li><a href="{{ route('orden.index_control') }}"><i class="ionicons ion-ios-flask"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Laboratorio - Control </span></a></li>
    <li><a href="{{ route('agendalabs.agenda') }}"><i class="fa fa-calendar"></i>&nbsp;&nbsp;<span>Agenda </span></a></li>
    @endif



    @if(in_array($rolUsuario, array(33)) == true)
    <li><a href="{{ route('agenda.agenda2') }}"><i class="fa fa-fw fa fa-calendar"></i> <span>Agenda</span></a></li>
    @if($id_auth == '1307189140')
    <li><a href="{{ route('horario.index') }}"><i class="fa fa-fw fa fa-calendar"></i> <span>Horario Laborable</span></a></li>
    <li><a href="{{ route('cortesia.index') }}"><i class="fa fa-fw fa fa-user"></i> <span>Cortesia</span></a></li>
    @endif
    <!--li><a href="{{ url('tecnicas') }}"><i class="glyphicon glyphicon-tasks"></i>Procedimientos</a></li-->
    @endif
    @if(in_array($rolUsuario, array(1,5)) == true || $id_auth == '0916593445')
    <li><a href="{{ route('horario.index_admin') }}"><i class="fa fa-clock-o"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>{{trans('tsidebar.horario_doctores')}} </span></a></li>
    @endif
    @if(in_array($rolUsuario, array(1, 3, 4, 5, 10, 11, 12, 7, 15, 20, 22)) == true)
    <li><a href="{{ url('consultam ') }}"><i class="fa fa-calendar-minus-o"></i> <span>{{trans('tsidebar.consultas_procedimientos')}}</span></a></li>
    @endif
    @if(in_array($rolUsuario, array(7, 11)) == true)
    <li><a href="{{ url('pentaxtv_dr') }}"><i class="fa fa-television  "></i><span>Pentax TV</span></a></li>
    @endif

    @if(in_array($rolUsuario, array(1,11,13,5, 7, 20, 9)) == true)
    <li><a href="{{ route('historia_clinica.fullcontrol') }}"><i class="fa fa-history"></i><span>{{trans('tsidebar.pacientes_dia')}}</span></a></li>
    @endif

    @if(in_array($rolUsuario, array(1, 4, 5, 20, 11)) == true)
    <li class="treeview">
      <a href="#"><i class="treeview-menu"></i> <span>{{trans('tsidebar.control_documental')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li><a href="{{ url('adelantado ') }}">{{trans('tsidebar.adelantados')}}</a></li>
      </ul>
    </li>

    @endif

    @if(in_array($rolUsuario, array(12,11, 22)) == true )
    <!--Supervision-->
    <li><a href="{{ route('orden.index_supervision') }}"><i class="ionicons ion-ios-flask"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Laboratorio </span></a></li>
    <li><a href="{{ route('examen.index') }}"><i class="ionicons ion-ios-flask"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Exámenes </span></a></li>

    @endif
    @if(in_array($rolUsuario, array(33)) == true )
    <!--CAMBIO PARA LABS CERTIFICACION DE EXÁMENES-->
    <li><a href="{{ route('orden.index_doctor_menu') }}"><i class="ionicons ion-ios-flask"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Laboratorio </span></a></li>
    @endif
    @if(in_array($rolUsuario, array(1, 22)) == true )
    <li><a href="{{ route('ap_estadisticos.honorarios') }}"><i class="fa fa-list-ol"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>{{trans('tsidebar.consolidado_honorarios')}}</span></a></li>
    @endif
    @if(in_array($rolUsuario, array(12, 22)) == true )
    <li><a href="{{ route('examen_costo.index') }}"><i class="glyphicon glyphicon-usd"></i> Exámenes Costos</a></li>
    @endif

    @if(in_array($rolUsuario, array(1, 4, 5, 20)) == true)
    <li class="treeview">
      <a href="#"><i class="fa fa-television"></i> <span>{{trans('tsidebar.pentax')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li><a href="{{ url('pentax') }}"">{{trans('tsidebar.control')}}</a></li>
            <li><a href=" {{ url('pentaxtv') }}">{{trans('tsidebar.sala_espera')}}</a></li>
        <li><a href="{{ url('pentaxtv_dr') }}">{{trans('tsidebar.ver')}}</a></li>
        <li><a href="{{ url('consulta_tv') }}">{{trans('tsidebar.control_consultas')}}</a></li>
      </ul>
    </li>
    <li class="treeview">
      <a href="#"><i class="fa fa-television"></i> <span>{{trans('tsidebar.procedimientos')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li><a href="{{ url('procedimientos_dr') }}"">{{trans('tsidebar.control')}}</a></li>
            <li><a href=" {{ url('procedimientostv_dr') }}">{{trans('tsidebar.ver')}}</a></li>
      </ul>
    </li>
    @endif
    @if(in_array($rolUsuario, array(1,4,5,11, 20)) == true)
    <li class="treeview">
      <a href="#"><i class="fa fa-table"></i> <span>{{trans('tsidebar.reportes')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>

      <ul class="treeview-menu">
        <li><a href="{{ route('agenda.reportediario') }}">{{trans('tsidebar.Agendamiento_Diario')}}</a></li>
        <!--reporte agenda-->
        <li><a href="{{ route('pentax.reporteagenda') }}">{{trans('tsidebar.procedimientos_endoscopicos')}}</a></li>
        <!--reporte drH  CAMBIOS 08052018-->
        <li><a href="{{ route('consultam.reporteagenda') }}">{{trans('tsidebar.otros_procedimientos')}}</a></li>
        <li><a href="{{ route('consultam.reporteagenda2') }}">{{trans('tsidebar.procedimientos_doctor')}}</a></li>
        <!--reporte Hospitalizados-->
        <li><a href="{{ route('hospitalizados.reporte') }}">{{trans('tsidebar.hospitalizados')}}</a></li>
      </ul>
    </li>
    @endif
    @if(in_array($rolUsuario, array(1, 7, 20)) == true)
    <li class="treeview">
      <a href="#"><i class="glyphicon glyphicon-list-alt"></i> <span>{{trans('tsidebar.insumos')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li><a href="{{ route('proveedor.index') }}">{{trans('tsidebar.Proveedores')}}</a></li>
        <li><a href="{{ route('productos.comparar.index') }}">{{trans('tsidebar.Comparativo')}}</a></li>
        <li><a href="{{ url('bodega') }}">{{trans('tsidebar.Bodegas')}}</a></li>
        <li><a href="{{ route('producto.index') }}">{{trans('tsidebar.Productos')}}</a></li>
        <li><a href="{{ route('marca.index') }}">{{trans('tsidebar.Marcas')}}</a></li>
        <li><a href="{{ route('tipo.index') }}">{{trans('tsidebar.Tipos_de_Productos')}}</a></li>
        <li><a href="{{ route('transito.index_transito') }}">{{trans('tsidebar.Productos_en_Transito')}}</a></li>
        <li><a href="{{ route('codigo.barra') }}">{{trans('tsidebar.Pedidos_Realizados')}}</a></li>
        <li><a href="{{ route('inventario.ingresos.egresos.varios') }}">{{trans('tsidebar.Ingresos_Egresos_Varios')}}</a></li>
        <li><a href="{{ route('equipo.index') }}">{{trans('tsidebar.Equipos_Medicos')}}</a></li>
        <li><a href="{{ route('plantilla.index') }}">{{trans('tsidebar.Plantillas_Procedimientos_Enfermeria')}}</a></li>
        <li><a href="{{ route('plantilla_procedimiento.index') }}">{{trans('tsidebar.Plantillas_Procedimientos_Control')}}</a></li>
      </ul>
    </li>
    @endif
    @if(in_array($rolUsuario, array(1, 7, 20)) == true)
    <li class="treeview">
      <a href="#"><i class="fa fa-file"></i> <span>{{trans('tsidebar.insumos_reportes')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li><a href="{{ route('reporte.buscador_master') }}">{{trans('tsidebar.master')}}</a></li>
        <li><a href="{{ route('reporte.reporte_bodega') }}">{{trans('tsidebar.Productos_en_Bodega')}}</a></li>
        <li><a href="{{ route('reporte.reporte_caducado') }}">{{trans('tsidebar.Productos_Caducados')}}</a></li>
        <li><a href="{{ route('reporte.buscador_usos') }}">{{trans('tsidebar.Uso_de_Productos')}}</a></li>
        <li><a href="{{ route('reporte.buscador_usos_equipo') }}">{{trans('tsidebar.Uso_de_Equipos')}}</a></li>
        <li><a href="{{ route('insumos.kardex.index') }}">{{trans('tsidebar.Kardex')}}</a></li>
        <li><a href="{{ route('insumos.inventario.index') }}">{{trans('tsidebar.Existencias')}}</a></li>
        <li><a href="{{ route('insumos.inventario_serie.index') }}">{{trans('tsidebar.Existencias_Serie')}}</a></li>
        <li><a href="{{ route('insumos.inventario.busqueda') }}">{{trans('tsidebar.Busqueda_de_Item')}}</a></li>
        <li><a href="{{ route('insumos.inventario.egresoprocedimiento') }}">{{trans('tsidebar.Materiales_Utilizados')}}</a></li>
      </ul>
    </li>
    @endif


    @if(in_array($rolUsuario, array(1, 12, 5, 15,19)) == true)

    <li><a href="{{ route('dashboard.apps') }}"><i class="fa fa-dashboard"></i> <span>{{trans('tsidebar.menu_apps')}}</span></a></li>
    @endif




    @if(in_array($rolUsuario, array(1, 4, 5, 21, 20, 22)) == true || $id_auth == '0922053467')
    <li><a href="{{ route('reporte.index_cierre') }}"><i class="fa fa-money"></i> <span>{{trans('tsidebar.cierre_caja')}}</span></a></li>
    <li><a href="{{ route('estaditicos_plano.orden') }}"><i class="fa fa-pie-chart"></i> <span>{{trans('tsidebar.estadisticos_recibo_cobro')}}</span></a></li>
    @endif

    @if(in_array($rolUsuario, array(1)) == true)
    @endif

    @if(in_array($rolUsuario, array(1)) == true )
    <li class="treeview">
      <a href="#"><i class="glyphicon glyphicon-list-alt"></i> <span>{{trans('tsidebar.convenios_privados')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">

        @php
        $opcion = '1'; // LABS PRIVADOS
        $opcion_u = Sis_medico\Opcion_Usuario::where('id_tipo_usuario',$rolUsuario)->where('id_opcion', $opcion)->first();
        @endphp
        @if(!is_null($opcion_u))
        <li><a href="{{route('privados.index')}}"><i class="ionicons ion-ios-flask"></i> <span> {{$opcion_u->opcion->nombre}}</span></a></li>
        @endif

        @php
        $opcion = '3'; // CONSULTA PACIENTES
        $opcion_u = Sis_medico\Opcion_Usuario::where('id_tipo_usuario',$rolUsuario)->where('id_opcion', $opcion)->first();
        @endphp
        @if(!is_null($opcion_u))
        <li><a href="{{route('pacientes.consulta')}}" style="padding-left: 10px;"><i class="fa fa-fw fa-users"></i> <span>{{$opcion_u->opcion->nombre}}</span></a></li>
        @endif

        @php
        $opcion = '4'; // AGENDA
        $opcion_u = Sis_medico\Opcion_Usuario::where('id_tipo_usuario',$rolUsuario)->where('id_opcion', $opcion)->first();
        @endphp
        @if(!is_null($opcion_u))
        <li><a href="{{route('solicitud.agenda')}}"><i class="fa fa-calendar"></i> <span>{{$opcion_u->opcion->nombre}}</span></a></li>
        @endif

      </ul>
    </li>
    @endif
    @if(in_array($rolUsuario, array(12)) == true)
    <li><a href="{{ route('e.labs_estadisticos') }}"><i class="fa fa-pie-chart"> </i> Estadisticos</a></li>
    @endif

    @if(in_array($rolUsuario, array(1)) == true )
    <li class="treeview">
      <a href="#"><i class="ionicons ion-ios-flask"></i> <span>{{trans('tsidebar.administracion_laboratorio')}} </span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li><a href="{{ route('orden.index') }}">{{trans('tsidebar.recepcion')}}</a></li>
        <li><a href="{{ route('orden.index_control') }}">{{trans('tsidebar.laboratorio')}}</a></li>
        <li><a href="{{ route('orden.index_supervision') }}">{{trans('tsidebar.supervision')}}</a></li>
        <li><a href="{{ route('examen.index') }}">{{trans('tsidebar.examenes')}}</a></li>
        <li><a href="{{ route('examen_costo.index') }}">{{trans('tsidebar.costos')}}</a></li>
        <li><a href="{{ route('protocolo.index') }}">{{trans('tsidebar.protocolos')}}</a></li>
        <li><a href="{{ url('exa_agrupadores') }}">{{trans('tsidebar.agrupadores')}}</a></li>
        <li><a href="{{ url('agendalabs/agenda') }}">{{trans('tsidebar.agenda')}}</a></li>
        <li><a href="{{ route('factura_agrupada.index_factura_agrupada') }}">{{trans('tsidebar.factura_agrupada')}}</a></li>
        <li><a href="{{ route('e.labs_estadisticos') }}"><i class="fa fa-pie-chart"> </i> {{trans('tsidebar.estadisticos')}}</a></li>
        <li><a href="{{ route('tipo_tubo.index') }}"><i class="fa fa-wrench"> </i> Mantenimiento Tubos</a></li>
      </ul>
    </li>
    @endif

    @if(in_array($rolUsuario, array(1,20)) == true)
    @if($id_empresa == '0993075000001')
    <li><a href="{{ route('c_caja.index') }}"><i class="fa fa-money"></i> <span>Cierre de Caja Laboratorio</span></a></li>
    @endif
    @endif

    @if(in_array($rolUsuario, array(1, 11)))
    <li class="treeview">
      <a href="#"><i class="fa fa-commenting-o"></i> <span>{{trans('tsidebar.encuestas')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <!--li><a href="{{ route('area.index') }}">Areas</a></li>
            <li><a href="{{ route('tipo_sugerencia.index') }}">Tipos de Sugerencia</a></li>
            <li><a href="{{ route('sugerencia.resultados') }}">Resultados de Sugerencia</a></li>
            <li><a href="{{ route('preguntas.index') }}">Preguntas de Encuesta</a></li-->
        <li><a href="{{ route('rrhh.resultados_ok') }}">Listado de Encuestas</a></li>
        <!--li><a href="{{ route('rrhh.estadisticas') }}">Estadisticas</a></li-->
        <li><a href="{{ route('rrhh.encuesta_estadistica') }}">Estadisticas</a></li>

      </ul>
    </li>
    @endif

    @if(in_array($rolUsuario, array(1, 12)) == true )
    <li class="treeview">
      <a href="#"><i class="fa fa-commenting-o"></i> <span>{{trans('tsidebar.encuestas_labs')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li><a href="{{ route('laboratorio.resultados_labs') }}">{{trans('tsidebar.listado_encuestas_labs')}}</a></li>
        <li><a href="{{ route('laboratorio.estadisticalabs') }}">{{trans('tsidebar.estadisticas_labs')}}</a></li>

      </ul>
    </li>
    @endif

    @if(in_array($rolUsuario, array(1, 4, 5, 14, 20, 22)) == true)
    <li><a href="{{ url('manual') }}"><i class="fa fa-file-pdf-o"></i> <span>{{trans('tsidebar.tarifarios')}}</span></a></li>
    @endif
    @if(in_array($rolUsuario, array(1, 6, 7, 11)) == true)
    <li><a href="{{ route('enfermeria.index') }}"><i class="fa fa-history"></i> <span>Pacientes del Dia @if(in_array($rolUsuario, array(1, 6, 7, 11)) == true) {{trans('tsidebar.enfermeros')}} @endif</span></a></li>
    @endif
    @if(in_array($rolUsuario, array(1, 4)) == true)
    <li class="treeview">
      <a href="#"><i class="fa fa-link"></i> <span>{{trans('tsidebar.administracion_sistemas')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li><a href="{{ url('especialidad ') }}"><i class="fa fa-fw fa-briefcase"></i> <span>Especialidades</span></a></li>
      </ul>
      <ul class="treeview-menu">
        <li><a href="{{ route('hospital-management.index') }}"><i class="fa fa-building"></i> <span>Ubicaciones</span></a></li>
      </ul>
      <ul class="treeview-menu">
        <li><a href="{{ route('procedimiento.index') }}"><i class="fa fa-book"></i> <span>Procedimientos</span></a></li>
      </ul>
      <ul class="treeview-menu">
        <li><a href="{{ url('form_enviar_seguro') }}"><i class="fa fa-fw fa-medkit"></i> <span>Seguros</span></a></li>
      </ul>
      <ul class="treeview-menu">
        <li><a href="{{ url('empresa') }}">Empresas</a></li>
      </ul>
      <ul class="treeview-menu">
        <li><a href="{{ route('tituloprofesional.index') }}">Mantenimiento Titulo Profesional</a></li>
      </ul>
      <ul class="treeview-menu">
        <li><a href="{{ url('tecnicas') }}">Procedimientos Completos</a></li>
      </ul>
      <ul class="treeview-menu">
        <li><a href="{{ route('tipo_usuario-management.index') }}"><span>Tipos de Usuario</span></a></li>
      </ul>


      <ul class="treeview-menu">
        <li><a href="{{ url('cie_10_3') }}">Cie 10 3</a></li>
      </ul>
      <ul class="treeview-menu">
        <li><a href="{{ url('cie_10_4') }}">Cie 10 4</a></li>
      </ul>
      @if(in_array($rolUsuario, array(1)) == true)
      @endif
    </li>
    <!--Facturacion Electronica-->
    <li class="treeview">
      <a href="#"><i class="fa fa-link"></i> <span>{{trans('tsidebar.Facturacion_electronica')}}</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li><a href="{{ route('sri_electronico.enviar_sri') }}"><i class="fa fa-fw fa-file-o"></i> <span>{{trans('tsidebar.Generar_xml')}}</span></a></li>
      </ul>
      <ul class="treeview-menu">
        <li><a href="{{ route('sri_electronico.enviar_sri') }}"><i class="fa fa-fw fa-file-o"></i> <span>{{trans('tsidebar.Firmar_xml')}}</span></a></li>
      </ul>
      <ul class="treeview-menu">
        <li><a href="{{ route('sri_electronico.enviar_sri') }}"><i class="fa fa-fw fa-file-o"></i> <span>{{trans('tsidebar.Validacion_xsd')}}</span></a></li>
      </ul>
      <ul class="treeview-menu">
        <li><a href="{{ route('sri_electronico.enviar_sri') }}"><i class="fa fa-fw fa-file-o"></i> <span>{{trans('tsidebar.Recepcion_sri')}}</span></a></li>
      </ul>
      <ul class="treeview-menu">
        <li><a href="{{ route('sri_electronico.enviar_sri') }}"><i class="fa fa-fw fa-file-o"></i> <span>{{trans('tsidebar.Autorizacion_sri')}}</span></a></li>
      </ul>
      <ul class="treeview-menu">
        <li><a href="{{ route('sri_electronico.enviar_sri') }}"><i class="fa fa-fw fa-file-o"></i>{{trans('tsidebar.No_autorizados_sri')}}</a></li>
      </ul>
      <ul class="treeview-menu">
        <li><a href="{{ route('sri_electronico.enviar_sri') }}"><i class="fa fa-fw fa-file-o"></i>{{trans('tsidebar.Notificacion_sri')}}</a></li>
      </ul>
      <ul class="treeview-menu">
        <li><a href="{{ route('demaestrodoc.index') }}"><i class="fa fa-fw fa-file-o"></i>{{trans('tsidebar.maestroDocumentos')}}</a></li>
      </ul>
      @if(in_array($rolUsuario, array(1)) == true)
      @endif
    </li>
    @endif

    <!-- NUEVO OPCIONES POR TIPO DE USUARIO-->

    @if(in_array($rolUsuario, array(1, 5, 20)) == true)
    <!--li>
          <a href="{{ route('muestrabiopsias.index') }}">
            <i class="fa fa-calendar-minus-o"></i><span> Revision Ordenes Biopsia </span>
          </a>
        </li-->
    @endif


    </ul>
    <!-- /.sidebar-menu -->
  </section>
  <!-- /.sidebar -->
</aside>