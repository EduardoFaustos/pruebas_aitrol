<?php 
  $imagen= Auth::user()->imagen_url;
  if($imagen==' '){
    $imagen='avatar.jpg';
  }
  $rolUsuario = Auth::user()->id_tipo_usuario;
  $id_auth = Auth::user()->id;      
?>

<style type="text/css">
.sidebar, aside.main-sidebar {
    /*background: url({{asset('/imagenes')}}/index-top-bg.png) repeat-x scroll 0 0 transparent !important;*/
  }

</style>  

  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar" >

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
      <ul class="sidebar-menu" >
        <!-- Optionally, you can add icons to the links -->
        <!--<li class="active"><a href="/"><i class="fa fa-link"></i> <span>Dashboard</span></a></li>-->
        @if(in_array($rolUsuario, array(9)) == true)   
        <li><a href="{{ route('orden.index_control') }}" ><i class="ionicons ion-ios-heart"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Cardiologia </span></a></li>
        @endif 
        @if(in_array($rolUsuario, array(1)) == true)
        <li><a href="{{ route('user-management.index') }}"><i class="fa fa-user-md"></i> <span>Administración de Usuarios</span></a></li>
        @endif
        @if(in_array($rolUsuario, array(1, 4, 5)) == true)
        <li><a href="{{ url('agenda') }}" ><i class="fa fa-calendar"></i> <span>Agenda</span></a></li>
        @endif
        @if(in_array($rolUsuario, array(1, 4, 5, 11)) == true)   
        <li><a href="{{ url('paciente') }}" ><i class="fa fa-fw fa-users"></i> <span>Pacientes</span></a></li>
        @endif 
        @if(in_array($rolUsuario, array(1, 4, 5)) == true)   
        <li><a href="{{ route('biopsias_paciente.index') }}" ><i class="fa fa-file-text"></i> <span>Ingreso Masivo de Biopsias</span></a></li>
        @endif 
        @if(in_array($rolUsuario, array(4, 5)) == true )   
        <li><a href="{{ route('orden.index') }}" ><i class="ionicons ion-ios-flask"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Laboratorio - Ordenes </span></a></li>
        @endif
        @if(in_array($rolUsuario, array(1, 4, 5)) == true )
        <li>
          <a href="{{ route('observacion.index') }}" ><i class="glyphicon glyphicon-copy"></i>
            <span>Observaciones </span>
            <span class="pull-right-container">
              <small class="label pull-right bg-red" id="o_cantidad"></small>
            </span>
          </a>
        </li>
        @endif
        @if(in_array($rolUsuario, array(10)) == true)   
        <li><a href="{{ route('orden.index_control') }}" ><i class="ionicons ion-ios-flask"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Laboratorio - Control </span></a></li>
        <li><a href="{{ route('agendalabs.agenda') }}" ><i class="fa fa-calendar"></i>&nbsp;&nbsp;<span>Agenda </span></a></li>
        @endif
        
        
        
        @if(in_array($rolUsuario, array(3)) == true)
        <li><a href="{{ route('agenda.agenda2') }}" ><i class="fa fa-fw fa fa-calendar"></i> <span>Agenda</span></a></li>
        @if($id_auth == '1307189140')
        <li><a href="{{ route('horario.index') }}" ><i class="fa fa-fw fa fa-calendar"></i> <span>Horario Laborable</span></a></li>
        <li><a href="{{ route('cortesia.index') }}" ><i class="fa fa-fw fa fa-user"></i> <span>Cortesia</span></a></li>
        @endif 
        <!--li><a href="{{ url('tecnicas') }}"><i class="glyphicon glyphicon-tasks"></i>Procedimientos</a></li-->
        @endif
        @if(in_array($rolUsuario, array(1,5)) == true || $id_auth == '0916593445') 
        <li><a href="{{ route('horario.index_admin') }}"><i class="fa fa-clock-o"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Horario Doctores </span></a></li>
         @endif 
         @if(in_array($rolUsuario, array(1, 3, 4, 5, 11, 7)) == true)
        <li><a href="{{ url('consultam ') }}" ><i class="fa fa-calendar-minus-o"></i> <span>Consultas/Procedimientos</span></a></li>
        @endif
        @if(in_array($rolUsuario, array(7)) == true)
        <li><a href="{{ url('pentaxtv_dr') }}"><i class="fa fa-television  "></i><span>Pentax TV</span></a></li>
        @endif
        @if(in_array($rolUsuario, array(1,3,6,11,13,5, 7)) == true)
        <li><a href="{{ route('historia_clinica.fullcontrol') }}"><i class="fa fa-history"></i><span>Pacientes del Dia</span></a></li>
        @endif
        @if(in_array($rolUsuario, array(12,11)) == true )   <!--Supervision-->
        <li><a href="{{ route('orden.index_supervision') }}" ><i class="ionicons ion-ios-flask"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Laboratorio </span></a></li>
        @endif  
        @if(in_array($rolUsuario, array(3)) == true )   <!--CAMBIO PARA LABS CERTIFICACION DE EXÁMENES-->
        <li><a href="{{ route('orden.index_doctor_menu') }}" ><i class="ionicons ion-ios-flask"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Laboratorio </span></a></li>
        @endif 
        @if(in_array($rolUsuario, array(12)) == true )  
        <li><a href="{{ route('examen_costo.index') }}"><i class="glyphicon glyphicon-usd"></i> Exámenes Costos</a></li>
        @endif
        @if(in_array($rolUsuario, array(1, 4, 5)) == true)
        <li class="treeview"> 
          <a href="#"><i class="fa fa-television"></i> <span>Pentax</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ url('pentax') }}"">Control Pentax</a></li>
            <li><a href="{{ url('pentaxtv') }}">Pentax Sala Espera</a></li>
            <li><a href="{{ url('pentaxtv_dr') }}">Pentax TV</a></li>
          </ul>
        </li>
        <li class="treeview"> 
          <a href="#"><i class="fa fa-television"></i> <span>Procedimientos</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ url('procedimientos_dr') }}"">Control</a></li>
            <li><a href="{{ url('procedimientostv_dr') }}">Procedimientos TV</a></li>
          </ul>
        </li>
        @endif
        @if(in_array($rolUsuario, array(1,4,5,11)) == true)
        <li class="treeview"> 
          <a href="#"><i class="fa fa-table"></i> <span>Reportes</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          
          <ul class="treeview-menu">
            <li><a href="{{ route('agenda.reportediario') }}">Agendamiento Diario</a></li>
            <!--reporte agenda--> 
            <li><a href="{{ route('pentax.reporteagenda') }}">Procedimientos Pentax</a></li>
            <!--reporte drH  CAMBIOS 08052018--> 
            <li><a href="{{ route('consultam.reporteagenda') }}">Procedimientos Otras Salas</a></li>
            <li><a href="{{ route('consultam.reporteagenda2') }}">Procedimientos por Doctor</a></li>
            <!--reporte Hospitalizados--> 
            <li><a href="{{ route('hospitalizados.reporte') }}">Hospitalizados</a></li>
          </ul>
        </li>
        @endif
        @if(in_array($rolUsuario, array(1, 7)) == true)
        <li class="treeview"> 
          <a href="#"><i class="glyphicon glyphicon-list-alt"></i> <span>Insumos</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ route('proveedor.index') }}">Proveedores</a></li>
            <li><a href="{{ url('bodega') }}">Bodegas</a></li>
            <li><a href="{{ route('producto.index') }}">Productos</a></li>
          </ul>
        </li>
        @endif
        @if(in_array($rolUsuario, array(1)) == true )
        <li class="treeview"> 
          <a href="#"><i class="glyphicon glyphicon-list-alt"></i> <span>Convenios Privados</span>
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
              <li><a href="{{route('privados.index')}}" ><i class="ionicons ion-ios-flask"></i> <span> {{$opcion_u->opcion->nombre}}</span></a></li>
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
              <li><a href="{{route('solicitud.agenda')}}" ><i class="fa fa-calendar"></i> <span>{{$opcion_u->opcion->nombre}}</span></a></li>
            @endif
                
          </ul>
        </li>
        @endif
        
        @if(in_array($rolUsuario, array(1)) == true )
        <li class="treeview"> 
          <a href="#"><i class="ionicons ion-ios-flask"></i> <span>Administración Laboratorio</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ route('orden.index') }}">Recepción</a></li>
            <li><a href="{{ route('orden.index_control') }}">Laboratorio</a></li>
            <li><a href="{{ route('orden.index_supervision') }}">Supervisión</a></li>
            <li><a href="{{ route('examen.index') }}">Exámenes</a></li>
            <li><a href="{{ route('examen_costo.index') }}">Exámenes Costos</a></li>
            <li><a href="{{ route('protocolo.index') }}">Protocolos</a></li>
            <li><a href="{{ url('exa_agrupadores') }}">Agrupadores</a></li>
            <li><a href="{{ url('agendalabs/agenda') }}">Agenda</a></li>
            
          </ul>
        </li>
        @endif
        @if(in_array($rolUsuario, array(1)))
        <li class="treeview"> 
          <a href="#"><i class="fa fa-commenting-o"></i> <span>Encuestas  y Sugerencias</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ route('area.index') }}">Areas</a></li>
            <li><a href="{{ route('tipo_sugerencia.index') }}">Tipos de Sugerencia</a></li>
            <li><a href="{{ route('sugerencia.resultados') }}">Resultados de Sugerencia</a></li>
            <li><a href="{{ route('preguntas.index') }}">Preguntas de Encuesta</a></li>
            
          </ul>
        </li>
        @endif
        @if(in_array($rolUsuario, array(1, 4, 5, 14)) == true)
        <li><a href="{{ url('manual') }}" ><i class="fa fa-file-pdf-o"></i> <span>Tarifarios</span></a></li>
        @endif
        @if(in_array($rolUsuario, array(1, 4)) == true)
        <li class="treeview"> 
          <a href="#"><i class="fa fa-link"></i> <span>Administración Sistema</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>  
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ url('especialidad ') }}" ><i class="fa fa-fw fa-briefcase"></i> <span>Especialidades</span></a></li>
          </ul>
          <ul class="treeview-menu">
            <li><a href="{{ route('hospital-management.index') }}"><i class="fa fa-building"></i> <span>Ubicaciones</span></a></li>
          </ul>
          <ul class="treeview-menu">
            <li><a href="{{ route('procedimiento.index') }}"><i class="fa fa-book"></i> <span>Procedimientos</span></a></li>
          </ul>
          <ul class="treeview-menu">
            <li><a href="{{ url('form_enviar_seguro') }}" ><i class="fa fa-fw fa-medkit"></i> <span>Seguros</span></a></li>
          </ul>
          <ul class="treeview-menu">
            <li><a href="{{ url('empresa') }}">Empresas</a></li>
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
        </li>
        
        @endif
        <!-- NUEVO OPCIONES POR TIPO DE USUARIO-->
        @if(in_array($rolUsuario, array(1)) == false )<!-- OPCIONES DE CONVENIOS PRIVADOS -->
          @php 
            $opcion = '1'; // LABS PRIVADOS
            $opcion_u = Sis_medico\Opcion_Usuario::where('id_tipo_usuario',$rolUsuario)->where('id_opcion', $opcion)->first();
          @endphp
          @if(!is_null($opcion_u))
            <li><a href="{{route('privados.index')}}" ><i class="ionicons ion-ios-flask"></i> <span>{{$opcion_u->opcion->nombre}}</span></a></li>
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
            <li><a href="{{route('solicitud.agenda')}}" ><i class="fa fa-calendar"></i> <span>{{$opcion_u->opcion->nombre}}</span></a></li>
          @endif
        @endif 

        @if(in_array($rolUsuario, array(11)) == true)
        <li><a href="{{ url('pentaxtv_dr') }}"><i class="fa fa-television"></i><span>Procedimientos TV</span></a></li>
        @endif


        

      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>

  <script type="text/javascript">
    
    @if(in_array($rolUsuario, array(1, 4, 5)) == true )
    var cantidad_observaciones = function ()
      {
        
        $.ajax({
            type: 'get',
            url:'{{ route(\'observacion.cantidad\')}}',
            success: function(data){
                //alert(data);
                //console.log(data);
                if(data>0){
                  $('#o_cantidad').empty().html(data);  
                }
            }
        })
        
      }

    vartiempo = setInterval(function(){ cantidad_observaciones(); }, 5000);
    @endif
    
  </script>