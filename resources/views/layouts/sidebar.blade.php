<?php

use Sis_medico\De_Empresa;
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


      @if(in_array($rolUsuario, array(1, 4, 5, 20)) == true)
      <li><a href="{{ route('disponibilidad.disponibilidad_menu') }}"><i class="fa fa-calendar"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Disponibilidad</span></a></li>
      @endif

      @if(in_array($rolUsuario, array(1, 4, 12, 22)) == true)
      <li><a href="{{ route('produccion.produccion_estad') }}"><i class="fa fa-user-md"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Producción de Médicos</span></a></li>
      @endif

      <li><a href="{{ route('paciente.historial_orden_lab_paciente') }}"><i class="fa fa-user-md"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>@if(in_array($rolUsuario, array(2)) == false) Tu @endif Historial de Examenes </span></a></li>

      @if(in_array($rolUsuario, array(2)) == false)
      <li><a href="{{ route('historial.rol') }}"><i class="fa fa-file-text"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Tus Roles de Pago</span></a></li>
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
          <li><a href="{{ route('comercial.proforma.index_proforma') }}">Proformas</a></li>
        </ul>
        <ul class="treeview-menu">
          <li><a href="{{ route('prodtarifario.index') }}">Producto Tarifario</a></li>
        </ul>
        <ul class="treeview-menu">
          <li><a href="{{ route('proforma.index_plantilla') }}">Plantilla de Productos</a></li>
        </ul>
      </li>
      @endif



      @if(in_array($rolUsuario, array(2)) == true)
      <li><a href="{{ route('paciente.historial_examenes') }}"><i class="fa fa-book"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Historial de Ordenes </span></a></li>
      @endif

      @if(in_array($rolUsuario, array(2)) == true)
      <li><a href="{{ route('recetas_usuario') }}"><i class="fa fa-file-text-o" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Historial de Receta </span></a></li>
      @endif

      @if(in_array($rolUsuario, array(9, 1, 7)) == true)
      <li><a href="{{ route('enfermeria.index_insumos') }}"><i class="glyphicon glyphicon-plus-sign"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Anestesiologos </span></a></li>
      @endif
      @if(in_array($rolUsuario, array(1,20, 22,14)) == true)
      <li><a href="{{ route('venta.estadisticos') }}"><i class="fa fa-pie-chart"> </i>&nbsp;<span> E. Factura de Venta </span></a></li>
      @endif

      @if(in_array($rolUsuario, array(1,22,26)) == true)
      <li class="treeview @if(strrpos($sidebar, 'financiero')) active @endif">
        <a href="#"><i class="glyphicon glyphicon-book"></i> <span>Financiero</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">

          <li><a href="{{ route('balance_general.index') }}"></i>Balance General</a></li>
          <li class="@if(strrpos($sidebar, 'estado/situacion/consolidado')) active @endif"><a href="{{ route('financiero.estadosituacion') }}"></i>Estado de Situacion Consolidado</a></li>
          <li><a href="{{ route('estadoresultados.index') }}"></i>Estado de Resultado Integral</a></li>
          <li class="@if(strrpos($sidebar, 'estado/resultados/consolidado')) active @endif"><a href="{{ route('financiero.estadoresultados') }}"></i>Estado de Resultado Consolidado</a></$ <li class="@if(strrpos($sidebar, 'indicador/consolidado')) active @endif"><a href="{{ route('financiero.indicadorconsolidado') }}"></i>Inidicador Financiero Consolidado</a></li>
          <li class="@if(strrpos($sidebar, 'indice/financiero')) active @endif"><a href="{{ route('financiero.indicefinanciero_index') }}"></i>Indice Financiero</a></li>
          <li class="@if(strrpos($sidebar, 'proyeccion/financiera')) active @endif"><a href="{{ route('financiero.proyeccionfinanciera') }}"></i>Proyección Financiera</a></li>
          <li><a href="{{ route('financiero.proyeccionfinanciera2_index') }}"></i>Proyección Financiera II</a></li>
          <li><a href="{{ route('financiero.proyeccionfinanciera3') }}"></i>Proyección Financiera III</a></li>
        </ul>
      </li>
      @endif




      @if(in_array($rolUsuario, array(26)) == true)
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
          <li class="treeview @if(strrpos($sidebar, 'contabilidad/flujo/efectivo/comparativo_1')) active @endif"><a href="{{ route('flujoefectivocomparativo.index') }}"></i> Flujo Efectivo Comparativo </a></li>
          <li class="treeview @if(strrpos($sidebar, 'contabilidad/flujo/efectivo/comparativo/dos')) active @endif"><a href="{{ route('flujoefectivocomparativo.index2') }}"></i> Flujo Efectivo Comparativo II</a></li>
          <li class="treeview @if(strrpos($sidebar, 'contabilidad/ats')) active @endif"><a href="{{ route('ats.index') }}"></i>SRI / ATS</a></li>
          <li class="treeview @if(strrpos($sidebar, 'contabilidad/descuadrados')) active @endif"><a href="{{ route('librodiario.descuadrados') }}"></i>Descuadrados</a></li>
        </ul>
      </li>
    </ul>
    @endif
    @if(in_array($rolUsuario, array(1,20, 22)) == true)
    <li class="treeview @if(strrpos($sidebar, 'contable')) active @endif">

      <a href="#"><i class="glyphicon glyphicon-book"></i> <span>Contable</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview @if(strrpos($sidebar, 'contable/empresa')) active @endif">
          <a href="#"> <i class="fa fa-building"> </i>Empresa
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-rigth"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="treeview @if(strrpos($sidebar, 'empresa/seleccion')) active @endif">
              <a href="{{ route('plan_cuentas.seleccion_empresa') }}"></i>Seleccion Empresa</a>
            </li>
          </ul>
        </li>

        <li class="treeview @if(strrpos($sidebar, 'contable/contabilidad')) active @endif">
          <a href="#"> <i class="fa fa-university"> </i>Contabilidad
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-rigth"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="treeview @if(strrpos($sidebar, 'plan_cuentas')) active @endif">
              <a href="{{ route('plan_cuentas.index') }}"></i>Plan de Cuentas</a>
            </li>
            <li class="treeview @if(strrpos($sidebar, 'libro_mayor')) active @endif">
              <a href="{{ route('libro_mayor.index') }}"></i>Libro Mayor</a>
            </li>
            <li class="treeview @if(strrpos($sidebar, 'contabilidad/balance_general')) active @endif"><a href="{{ route('balance_general.index') }}"></i>Balance General</a></li>
            <li class="treeview @if(strrpos($sidebar, 'contabilidad/estado/resultados')) active @endif"><a href="{{ route('estadoresultados.index') }}"></i>Estado de Resultado Integral</a></li>
            <li class="treeview @if(strrpos($sidebar, 'contabilidad/balance_comprobacion')) active @endif"><a href="{{ route('balance_comprobacion.index') }}"></i>Balance de Comprobacion</a></li>
            <li class="treeview @if(strrpos($sidebar, 'contabilidad/libro')) active @endif"><a href="{{ route('librodiario.index') }}"></i>Visualizar Asientos Diario</a></li>
            {{-- <li><a href="{{ route('compras.informe') }}"></i>Informe Compras</a>
        </li> --}}
        <li class="treeview @if(strrpos($sidebar, 'contabilidad/flujo/efectivo_1')) active @endif"><a href="{{ route('flujoefectivo.index') }}"></i>Flujo de efectivo</a></li>
        <li class="treeview @if(strrpos($sidebar, 'contabilidad/flujo/efectivo/comparativo_1')) active @endif"><a href="{{ route('flujoefectivocomparativo.index') }}"></i>Flujo de efectivo comparativo</a></li>
        <li class="treeview @if(strrpos($sidebar, 'contabilidad/flujo/efectivo/comparativo/dos')) active @endif"><a href="{{ route('flujoefectivocomparativo.index2') }}"></i>Flujo de efectivo comparativo II</a></li>
        <li class="treeview @if(strrpos($sidebar, 'contabilidad/ats')) active @endif"><a href="{{ route('ats.index') }}"></i>SRI / ATS</a></li>
        <li class="treeview @if(strrpos($sidebar, 'contabilidad/descuadrados')) active @endif"><a href="{{ route('librodiario.descuadrados') }}"></i>Descuadrados</a></li>
      </ul>
    </li>

    <li class="treeview @if(strrpos($sidebar, 'acreedores')) active @endif">
      <a href="#"> <i class="fa  fa-users"> </i>Acreedores
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview @if(strrpos($sidebar, 'acreedores/mantenimiento')) active @endif">
          <a href="#"> <i class="fa fa-bars"> </i>1 Mantenimiento
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-rigth"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="treeview @if(strrpos($sidebar, 'acreedores/mantenimiento/rubrosa')) active @endif"><a href="{{ route('rubrosa.index') }}"><i class="fa fa-gear"></i>Rubros Acreedores</a></li>
            <li class="treeview @if(strrpos($sidebar, 'acreedores/mantenimiento/acreedor')) active @endif"><a href="{{ route('acreedores_index') }}"><i class="fa fa-gear"></i>Acreedores</a></li>
            <li class="treeview @if(strrpos($sidebar, 'acreedores/mantenimiento/acreedor')) active @endif"><a href="{{ route('saldosinicialesp.index2') }}"> Saldos Iniciales</a></li>

          </ul>

        </li>
      </ul>
      <ul class="treeview-menu">
        <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos')) active @endif">
          <a href="#"> <i class="fa  fa-file-text"> </i>2 Documentos
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-rigth"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="treeview">
            <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos/retenciones')) active @endif"><a href="{{ route('retenciones_index') }}"><i class="fa fa-calculator"></i>Retenciones</a></li>
            <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos/comprobante/egreso')) active @endif"><a href="{{ route('acreedores_cegreso') }}"><i class="fa  fa-file-text"></i>Comprobante Egreso</a></li>

            <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos/comprobante/index')) active @endif"><a href="{{ route('comp_egreso_masivo.index') }}"><i class="fa  fa-file-text"></i>Comprobante Egreso M</a></li>
            <li class="treeview @if(strrpos($sidebar, 'acreedores/documentos/cuentas/egreso/varios')) active @endif"><a href="{{ route('egresosv.index') }}"><i class="fa  fa-file-text"></i> Comprobante Egresos V</a></li>
            <li><a href="{{ route('cruce.index') }}"><i class="fa fa-exchange"></i>Cruce de Valores</a></li>
            <li class="treeview @if(strrpos($sidebar, 'contable/cruce_cuentas/valores')) active @endif"><a href="{{ route('pr.cruce_cuentas') }}"><i class="fa  fa-file-text"></i> Cruce Cuentas</a></li>
            <li><a href="{{ route('creditoacreedores.index') }}"><i class="fa fa-clone"></i>Nota de Crédito</a></li>
            <li><a href="{{ route('debitoacreedores.index') }}"><i class="fa fa-file-o"></i>Nota de Débito</a></li>

        </li>
      </ul>

    </li>
    </ul>
    <ul class="treeview-menu">
      <li class="treeview @if(strrpos($sidebar, 'acreedores/informes')) active @endif">
        <a href="#"> <i class="fa fa-pie-chart"> </i>3 Informes
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="treeview @if(strrpos($sidebar, 'acreedores/informes/cartera/pagar')) active @endif">
            <a href="{{ route('acreedores.informenc') }}"><i class="fa fa-file-excel-o"> </i>Informe Notas de Credito</a>
          </li>
          <li class="treeview @if(strrpos($sidebar, 'acreedores/credito/informe')) active @endif">
            <a href="{{ route('carterap.index') }}"><i class="fa fa-file-excel-o"> </i>Carteras por Pagar</a>
          </li>
          <li class="treeview @if(strrpos($sidebar, 'acreedores/informes/informe/saldos')) active @endif">
            <a href="{{ route('saldos_acreedor.index') }}"><i class="fa fa-file-excel-o"></i>Informe de Saldos</a>
          </li>
          <li><a href="{{ route('contable.reporte_tiempo') }}"><i class="fa fa-file-excel-o"></i>Informe Tiempo</a></li>
          <li><a href="{{ route('chequesa.index') }}"><i class="fa fa-file-excel-o"></i>Informe de Cheques Girados</a></li>
          <li><a href="{{ route('informe_retenciones.index') }}"><i class="fa fa-file-excel-o"></i>Informe Retenciones</a></li>
          <li><a href="{{ route('contable.anticipo_proveedores') }}"><i class="fa fa-file-excel-o"></i>Informe Anticipos</a></li>
          <li><a href="{{ route('deudasvspagos.index') }}"><i class="fa fa-file-excel-o"></i>Deudas vs Pagos</a></li>
          <li><a href="{{ route('deudas_pendientes.index') }}"><i class="fa fa-file-excel-o"></i>Deudas Pendientes</a></li>

        </ul>

      </li>
    </ul>
    </li>

    <li class="treeview">
      <a href="#"> <i class="fa  fa-users"> </i>Clientes
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
          <a href="#"> <i class="fa fa-bars"> </i>1 Mantenimiento
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-rigth"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="treeview">
            <li><a href="{{ route('clientes.index') }}"></i>Crear Cliente</a></li>
            <li><a href="{{ route('rubros_cliente.index') }}"></i>Rubros Cliente</a></li>
            <li><a href="{{ route('saldosinicialesclientes.index2') }}"></i>Saldos Iniciales</a></li>
        </li>
      </ul>
    </li>
    </ul>
    <ul class="treeview-menu">
      <li class="treeview">
        <a href="#"> <i class="fa  fa-file-text"> </i>2 Documentos
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>

        <ul class="treeview-menu">
          <li class="treeview">
          <li><a href="{{route('retencion.cliente')}}"><i class="fa fa-calculator"></i>Retenciones</a></li>
          <li><a href="{{route('chequespost.index')}}"><i class="fa fa-files-o"></i>Cheques PostFechados</a></li>
          <li><a href="{{route('cr.cruce_cuentas')}}"><i class="fa fa-files-o"></i>Cruce Cuentas</a></li>
          <li><a href="{{route('comprobante_ingreso.index')}}"><i class="fa  fa-file-text"></i>Comprobante Ingreso</a></li>
          <li><a href="{{route('comprobante_ingreso_v.index')}}"><i class="fa  fa-file-text"></i> Comprobante Ingreso V</a></li>
          <li><a href="{{route('cruce_clientes.index')}}"><i class="fa fa-exchange"></i>Cruce de Valores</a></li>
          <li><a href="{{route('nota_credito_cliente.index')}}"><i class="fa fa-clone"></i>Nota de Crédito</a></li>
          <li><a href="{{route('nota_cliente_debito.index')}}"><i class="fa fa-file-o"></i>Nota de Débito</a></li>

      </li>
    </ul>

    </li>
    </ul>
    <ul class="treeview-menu">
      <li class="treeview">
        <a href="#"> <i class="fa fa-pie-chart"> </i>3 Informes
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="treeview">
          <li><a href="{{route('clientes.deudas.pendientes')}}"><i class="fa fa-file-excel-o"></i>Deudas Pendientes</a></li>
          <li><a href="{{route('clientes.saldo.cxc')}}"><i class="fa fa-file-excel-o"></i>Saldo de Cuentas por Cobrar</a></li>
          <li><a href="{{route('cliente.informe.retenciones')}}"><i class="fa fa-file-excel-o"></i>Informe Retenciones</a></li>
          {{-- <li><a href="#"><i class="fa fa-file-excel-o"> </i>Carteras por Pagar</a></li>
                        <li><a href="#"><i class="fa fa-file-excel-o"></i>Informe de Saldos</a></li>
                        <li><a href="#"><i class="fa fa-file-excel-o"></i>Informe de Cheques Girados</a></li>
                        <li><a href="#"><i class="fa fa-file-excel-o"></i>Informe Retenciones</a></li> --}}
          <li><a href="{{route('deudasvspagos.cliente')}}"><i class="fa fa-file-excel-o"></i>Deudas vs Pagos</a></li>
          <li><a href="{{ route('nota_credito_cliente.informe_notacreditoclientes') }}"><i class="fa fa-file-excel-o"></i>Informe Nota Credito Clientes</a></li>
      </li>
    </ul>

    </li>
    </ul>
    </li>

    <li class="treeview @if(strrpos($sidebar, 'Banco')) active @endif">
      {{-- <a href="#"> <i class="fa fa-cubes"> </i>Caja y Bancos --}}
      <a href="#"> <i class="fa fa-money"> </i>Caja y Bancos
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview "><a href="{{ route('caja_banco.index') }}"></i>Crear Caja Banco</a></li>
        <li><a href="{{ route('banco_clientes.index') }}"></i>Listado de Bancos</a></li>
        <li class="treeview @if(strrpos($sidebar, 'Banco/notadebito')) active @endif"><a href="{{ route('notadebito.index') }}"></<i>Nota de Debito</a></li>
        <li class="treeview"><a href="{{ route('notacredito.index') }}"></i>Nota de Credito</a></li>
        <li class="treeview"><a href="{{ route('debitobancario.index') }}"></i>Nota de Debito Bancaria</a></li>
        <li class="treeview"><a href="{{ route('depositobancario.index') }}"></i>Déposito Bancario</a></li>
        <li class="treeview"><a href="{{ route('transferenciabancaria.index') }}"></i>Transferencia Bancaria</a></li>
        <li class="treeview"><a href="{{ route('conciliacionbancaria.index') }}"></i>Conciliaci&oacute;n Bancaria </a></li>
        <li class="treeview"><a href="{{ route('estadocuentabancos.index') }}"></i>Estado de Cuenta </a></li>
      </ul>
    </li>

    <li class="treeview">
      <a href="#">
        <i class="fa fa-share"></i> <span>Activos Fijos</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
          <a href="#"><i class="fa fa-circle-o"></i> Mantenimientos
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ route('afGrupo.index') }}"> Grupos</a></li>
            <li><a href="{{ route('afTipo.index') }}"> Tipos</a></li>
            <li><a href="{{ route('afActivo.index') }}"> Activos</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#"><i class="fa fa-circle-o"></i> Documentos
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ route('afDocumentoFactura.index') }}"> Facturas</a></li>
            <li><a href="{{ route('afDepreciacion.index') }}"> Depreciaciones</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#"><i class="fa fa-circle-o"></i> Informes
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{ route('afInformes.index') }}"> Saldos</a></li>
            <li><a href="{{route('activofjo.index_listado')}}">Listado Activos</a></li>
            {{-- <li><a href="#"> Retenci&oacute;n</a></li> --}}
            {{-- <li><a href="#"> Facturas</a></li> --}}
          </ul>
        </li>
      </ul>
    </li>

    <li class="treeview">
      <a href="#"> <i class="fa fa-truck"> </i>Compras
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li><a href="{{ route('compras_index') }}"></i>Compras</a></li>
        <li><a href="{{ route('saldosinicialesp.index2') }}"></i>Saldos Iniciales</a></li>
        <li><a href="{{ route('compras.pedidos') }}"></i>Pedidos</a></li>
        <li><a href="{{ route('fact_contable_index') }}"></i>Factura Contable</a></li>
        <li><a href="{{ route('compras.informe') }}"><i class="fa fa-file-excel-o"></i>Informe Compras</a></li>
        <!-- <li><a href="{{ route('kardex.index') }}"></i>Kardex</a></li>-->
        <li><a href="{{ route('contable.compras.kardex.index') }}"></i>Kardex Compra</a></li>
        <li><a href="{{ route('insumos.inventario.index') }}">Existencias</a></li>
    </li>
    </ul>
    </li>
    <li class="treeview">
      <a href="#"> <i class="fa fa-truck"> </i>Compras - Interno
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <!--<li class="treeview"><a href="{{ route('contable.bodega.index') }}"></i>Bodegas</a></li>-->
        <li class="treeview"><a href="{{ route('contable.compraspedidos.index') }}"></i>Pedidos</a></li>
        <!--<li class="treeview"><a href="{{ route('saldosinicialesp.index2') }}"></i>Autorizaciones</a></li>-->
        <!--<li class="treeview"><a href="{{ route('saldosinicialesp.index2') }}"></i>Inventario</a></li>-->
        <li class="treeview"><a href="{{ route('kardex_inventario.index') }}"></i>Kardex</a></li>
        <!--<li class="treeview"><a href="{{ route('fact_contable_index') }}"></i>Factura</a></li>-->
        <li class="treeview"><a href="{{ route('pedidos_inventario.index') }}"></i>Informe Productos</a></li>
        <li><a href="{{ route('contable.compraspedido.indexInicial')}}">Mantenimiento Iniciales</a></li>
        <li><a href="{{ route('compraspedidos.index_proceso')}}">Mantenimiento Usuarios</a></li>
      </ul>
    </li>

    <li class="treeview">
      <a href="#"> <i class="fa fa-cubes"> </i>Inventario
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li><a href="{{ route('kardex.index') }}"></i>Kardex</a></li>
        <li><a href="{{ route('u.getUses') }}"></i>Inventario Insumos</a></li>
        <li><a href="{{ route('productos.saldos_iniciales') }}"></i>Saldos iniciales</a></li>
        <li><a href="{{ route('notainventario.index') }}"></i>Nota de Ingreso Inventario</a></li>
        <li><a href="{{ route('productos_servicios_index') }}"></i>Productos</a></li>

    </li>
    </ul>
    </li>

    <li class="treeview">
      <a href="#"> <i class="ion-person-stalker"> </i>Nomina
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li><a href="{{ route('config_valor.index') }}"></i>Configuracion</a></li>
        <li><a href="{{ route('nomina.index') }}"></i>Crear Empleado / Rol</a></li>
        <li><a href="{{ route('nuevo_rol.masivo_search') }}"></i><span style="color: red;">Roles de Pago por Empresa</span></a></li>
        <li><a href="{{ route('buscador_rol.index') }}"></i>Roles de Pago Individual</a></li>
        <li><a href="{{ route('prestamos_empleado.index') }}"></i>Prestamos Empleados</a></li>
        <li><a href="{{ route('prestamos_empleados.index_saldos') }}"></i>Saldos Iniciales Empleados</a></li>
        <!--<li><a href="{{ route('anticipos_empleado.index') }}"></i>Anticipo 1Ra Quinc Empl</a></li>-->
        {{--<li><a href="{{ route('nomina_anticipos_1eraquincena.index') }}"></i>Anticipo 1Ra Quinc Empl</a>
    </li>--}}
    <li><a href="{{ route('nominaquincena.index_quincena') }}"></i>Nuevo Anticipo Quinc Empl</a></li>
    <li><a href="{{ route('otros_anticipos_empleado.index') }}"></i>Otros Anticipos Empleados</a></li>
    <!-- <li><a href="{{ route('plantillas_nomina.plantillas_prestamos') }}"></i>Plantilla Prestamos</a></li>
        <li><a href="{{ route('plantillas_nomina.horas_extras') }}"></i>Plantilla Horas Extras</a></li> -->
    <!--li><a href="{{ route('prestamos_empleados.index_cruce') }}"></i>Prestamos vs Utilidades</a></li-->
    </li>
    </ul>
    <ul class="treeview-menu">
      <li class="treeview">
        <a href="#"> <i class="fa fa-file-excel-o"> </i>Informes
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-rigth"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="treeview">
            <!--<li><a href="{{ route('nomina_anticipos.index') }}"><i class="fa fa-file-excel-o"> </i>Anticipo Quincena</a></li>-->
          <li><a href="{{ route('reportes_empl.index') }}"><i class="fa fa-file-excel-o"> </i>Datos Empleados</a></li>
          <li><a href="{{ route('reportes_rol.index') }}"><i class="fa fa-file-excel-o"> </i>Rol Pagos (RRHH)</a></li>
          <li><a href="{{ route('reportes_rolcontable.index') }}"><i class="fa fa-file-excel-o"> </i>Rol Pagos (Contabilidad)</a></li>
          <li><a href="{{ route('reportes_banco.index') }}"><i class="fa fa-file-excel-o"> </i>Archivo Banco</a></li>
          <li><a href="{{ route('prestamos_empleados.prestamos_saldos') }}"><i class="fa fa-file-excel-o"> </i>Saldo Prestamos</a></li>
      </li>
    </ul>
    </li>
    </ul>
    </li>

    <li class="treeview">
      <a href="#"> <i class="fa fa-line-chart"> </i>Flujos de Caja
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li><a href="{{ route('flujoefectivo.index') }}"></i>Flujo Efectivo</a></li>
        <li><a href="{{ route('flujoefectivocomparativo.index') }}"></i>Flujo Efectivo Comparativo</a></li>
        <li><a href="{{ route('estructuraflujoefectivo.index') }}"></i>Estructura Flujo Efectivo</a></li>
    </li>
    </ul>
    </li>

    <li class="treeview">
      <a href="#"> <i class="fa  fa-shopping-cart"> </i>Ventas
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>

      <ul class="treeview-menu">
        <li class="treeview">
        <li><a href="{{ route('venta_index') }}"></i>Factura de Venta</a></li>
    </li>
    </ul>
    <ul class="treeview-menu">
      <li class="treeview">
      <li><a href="{{ route('ventas.index_cierre') }}"></i>Factura de Caja</a></li>
      </li>
    </ul>

    <ul class="treeview-menu">
      <li class="treeview">
      <li><a href="{{ route('orden_venta') }}"></i>Ordenes de Venta</a></li>
      </li>
    </ul>
    <ul class="treeview-menu">
      <li class="treeview">
      <li><a href="{{ route('venta.index_recibo') }}"></i>Factura Recibo de Cobro</a></li>
      </li>
    </ul>
    <ul class="treeview-menu">
      <li class="treeview">
      <li><a href="{{ route('factura_convenios.index') }}"></i>Factura Convenios</a></li>
      </li>
    </ul>
    <ul class="treeview-menu">
      <li class="treeview">
      <li><a href="{{ route('venta_index2') }}"></i>Facturas Conglomerada</a></li>
      </li>
    </ul>
    <ul class="treeview-menu">
      <li class="treeview">
      <li><a href="{{ route('ventas.omni') }}"></i>Facturación Omni</a></li>
      </li>
    </ul>
    <ul class="treeview-menu">
      <li class="treeview">
      <li><a href="{{ route('venta.informe_ventas') }}"><i class="fa fa-file-excel-o"></i>Informe Ventas</a></li>
      @if(!($id_empresa == '0992704152001'))
      <li><a href="{{ route('venta.informe_nca') }}"><i class="fa fa-file-excel-o"></i>Informe de Ventas Netas</a></li>
      @endif
      <li><a href="{{ route('venta.informe_liquidaciones_comisiones') }}"><i class="fa fa-file-excel-o"></i>Informe de liquidaciones comisiones</a></li>
      </li>
    </ul>


    <li class="treeview">
      <a href="#"> <i class="fa  fa-shopping-cart"> </i>Importaciones
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li><a href="{{ route('importaciones.index') }}"></i>Importaciones</a></li>
        <li><a href="{{ route('gastosimportacion.index') }}">Mantenimiento Gastos</a></li>
    </li>
    </ul>
    </li>
    @if(in_array($rolUsuario, array(1,20,21)) == true)
    <?php
    $deEmpresa = De_Empresa::where('id_empresa', $id_empresa)->first();
    if ($deEmpresa != '') {
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
      <a href="#"> <i class="fa fa-cogs"> </i>Mantenimiento
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li class="treeview">
        <li><a href="{{ route('establecimiento.index') }}"></i>Sucursales</a></li>
        <li><a href="{{ route('punto_emision.index') }}"></i>Punto de Emision</a></li>
        <li><a href="{{ route('empleados.index') }}"></i>Asignar Recaudador P-E</a></li>
        <li><a href="{{ route('divisas.index')}}"></i>Divisas</a></li>
        <li><a href="{{ route('tipo_pago.index') }}"></i>Tipo de Pago</a></li>
        <li><a href="{{ route('tipo_tarjeta.index') }}"></i>Tipo de Tarjeta</a></li>
        <li><a href="{{ route('bodegas.index') }}"></i>Bodegas</a></li>
        <!--<li><a href="#"></i>Bancos</a></li>
                 <li><a href="#"></i>Ciudad</a></li>-->
        <!--<li><a href="{{ route('caja_banco.index') }}"></i>Cajas y Bancos</a></li>-->
        <li><a href="{{ route('tipo_emision.index') }}"></i>Tipo de Emision</a></li>
        <li><a href="{{ route('tipo_comprobante.index') }}"></i>Tipo de Comprobante</a></li>
        <li><a href="{{ route('tipo_ambiente.index') }}"></i>Tipo de Ambiente</a></li>
        <li><a href="{{ route('porcentaje_imp_renta.index') }}"></i>% Retención Impuesto a la Renta</a></li>
        <li><a href="{{ route('Porcentaje.index') }}"></i>% Pago Impuesto a la Renta</a></li>
        <li><a href="{{ route('retenciones.index') }}"></i>Retenciones</a></li>
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
    <li><a href="{{ route('user-management.index') }}"><i class="fa fa-user-md"></i> <span>Administración de Usuarios</span></a></li>
    @endif

    @if(in_array($rolUsuario, array(1, 20, 22)) == true)
    <li><a href="{{ route('facturalabs.reporte_anual') }}"><i class="fa fa-calendar"></i> <span>Reporte Anual Laboratorio</span></a></li>
    @endif

    @if(in_array($rolUsuario, array(1, 4, 5, 20, 22)) == true)
    <li><a href="{{ url('agenda') }}"><i class="fa fa-calendar"></i> <span>Agenda</span></a></li>
    @endif
    @if(in_array($rolUsuario, array(1, 4, 5, 11, 20)) == true)
    <li><a href="{{ url('paciente') }}"><i class="fa fa-fw fa-users"></i> <span>Pacientes</span></a></li>
    @endif
    @if(in_array($rolUsuario, array(1, 4, 5, 20)) == true)
    <li><a href="{{ route('biopsias_paciente.index') }}"><i class="fa fa-file-text"></i> <span>Ingreso Masivo de Biopsias</span></a></li>
    @endif
    @if(in_array($rolUsuario, array(4, 5, 20)) == true )
    <li><a href="{{ route('orden.index') }}"><i class="ionicons ion-ios-flask"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Laboratorio - Ordenes </span></a></li>
    @endif
    @if(in_array($rolUsuario, array(1, 20)) == true )
    <li><a href="{{ route('factura_agrupada.index_factura_agrupada') }}"><i class="ionicons ion-ios-flask"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Factura Agrupada Labs</span></a></li>
    @endif
    @if(in_array($rolUsuario, array(1, 4, 5, 20)) == true )
    <li>
      <a href="{{ route('observacion.index') }}"><i class="glyphicon glyphicon-copy"></i>
        <span>Observaciones </span>
        <span class="pull-right-container">
          <small class="label pull-right bg-red" id="o_cantidad"></small>
        </span>
      </a>
    </li>
    @endif
    @if(in_array($rolUsuario, array(10)) == true)
    <li><a href="{{ route('orden.index_control') }}"><i class="ionicons ion-ios-flask"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Laboratorio - Control </span></a></li>
    <li><a href="{{ route('agendalabs.agenda') }}"><i class="fa fa-calendar"></i>&nbsp;&nbsp;<span>Agenda </span></a></li>
    <li><a href="{{ route('tipo_tubo.index') }}"><i class="fa fa-wrench"> </i> Mantenimiento Tubos</a></li>
    <li><a href="{{ route('mantenimientoexcel.index') }}"><i class="fa fa-plus-square"> </i> Administración de Tubos</a></li>
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
    <li><a href="{{ route('horario.index_admin') }}"><i class="fa fa-clock-o"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Horario Doctores </span></a></li>
    @endif
    @if(in_array($rolUsuario, array(1, 3, 4, 5, 10, 11, 12, 7, 15, 20, 22)) == true)
    <li><a href="{{ url('consultam ') }}"><i class="fa fa-calendar-minus-o"></i> <span>Consultas/Procedimientos</span></a></li>
    @endif
    @if(in_array($rolUsuario, array(7, 11)) == true)
    <li><a href="{{ url('pentaxtv_dr') }}"><i class="fa fa-television  "></i><span>Pentax TV</span></a></li>
    @endif

    @if(in_array($rolUsuario, array(1,11,13,5, 7, 20, 9)) == true)
    <li><a href="{{ route('historia_clinica.fullcontrol') }}"><i class="fa fa-history"></i><span>Pacientes del Dia</span></a></li>
    @endif

    @if(in_array($rolUsuario, array(1, 4, 5, 20, 11)) == true)
    <li class="treeview">
      <a href="#"><i class="treeview-menu"></i> <span>Control Documental</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li><a href="{{ url('adelantado ') }}">Adelantado Integral</a></li>
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
    <li><a href="{{ route('ap_estadisticos.honorarios') }}"><i class="fa fa-list-ol"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Consolidado de Honorarios</span></a></li>
    @endif
    @if(in_array($rolUsuario, array(12, 22)) == true )
    <li><a href="{{ route('examen_costo.index') }}"><i class="glyphicon glyphicon-usd"></i> Exámenes Costos</a></li>
    @endif

    @if(in_array($rolUsuario, array(1, 4, 5, 20)) == true)
    <li class="treeview">
      <a href="#"><i class="fa fa-television"></i> <span>Pentax</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li><a href="{{ url('pentax') }}"">Control Pentax</a></li>
            <li><a href=" {{ url('pentaxtv') }}">Pentax Sala Espera</a></li>
        <li><a href="{{ url('pentaxtv_dr') }}">Pentax TV</a></li>
        <li><a href="{{ url('consulta_tv') }}">Consultas TV</a></li>
        <li><a href="{{ url('nrc_descuentos/aprobacion') }}">Recibos Pendientes Aprobación</a></li>
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
            <li><a href=" {{ url('procedimientostv_dr') }}">Procedimientos TV</a></li>
      </ul>
    </li>
    @endif
    @if(in_array($rolUsuario, array(1,4,5,11, 20)) == true)
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
    @if(in_array($rolUsuario, array(1, 7, 20)) == true)
    <li class="treeview">
      <a href="#"><i class="glyphicon glyphicon-list-alt"></i> <span>Insumos</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li><a href="{{ route('proveedor.index') }}">Proveedores</a></li>
        <li><a href="{{ route('productos.comparar.index') }}">Comparativo</a></li>
        <li><a href="{{ url('bodega') }}">Bodegas</a></li>
        <li><a href="{{ route('producto.index') }}">Productos</a></li>
        <li><a href="{{ route('marca.index') }}">Marcas</a></li>
        <li><a href="{{ route('tipo.index') }}">Tipos de Productos</a></li>
        <li><a href="{{ route('transito.index_transito') }}">Productos en Transito</a></li>
        <li><a href="{{ route('codigo.barra') }}">Pedidos Realizados</a></li>
        <li><a href="{{ route('inventario.ingresos.egresos.varios') }}">Ingresos / Egresos Varios</a></li>
        <li><a href="{{ route('equipo.index') }}">Equipos Medicos</a></li>
        <li><a href="{{ route('plantilla.index') }}">Plantillas Procedimientos Enfermeria</a></li>
        <li><a href="{{ route('plantilla_procedimiento.index') }}">Plantillas Procedimientos Control</a></li>
      </ul>
    </li>
    @endif
    @if(in_array($rolUsuario, array(1, 7, 20)) == true)
    <li class="treeview">
      <a href="#"><i class="fa fa-file"></i> <span>Insumos Reportes</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li><a href="{{ route('reporte.buscador_master') }}">Buscador Master</a></li>
        <li><a href="{{ route('reporte.reporte_bodega') }}">Productos en Bodega</a></li>
        <li><a href="{{ route('reporte.reporte_caducado') }}">Productos Caducados</a></li>
        <li><a href="{{ route('reporte.buscador_usos') }}">Uso de Productos</a></li>
        <li><a href="{{ route('reporte.buscador_usos_equipo') }}">Uso de Equipos</a></li>
        <li><a href="{{ route('insumos.kardex.index') }}">Kardex</a></li>
        <li><a href="{{ route('insumos.inventario.index') }}">Existencias</a></li>
        <li><a href="{{ route('insumos.inventario_serie.index') }}">Existencias Serie</a></li>
        <li><a href="{{ route('insumos.inventario.busqueda') }}">Busqueda de Item</a></li>
        <li><a href="{{ route('insumos.inventario.egresoprocedimiento') }}">Materiales Utilizados</a></li>
      </ul>
    </li>
    @endif


    @if(in_array($rolUsuario, array(1, 12, 5, 15,19)) == true)

    <li><a href="{{ route('dashboard.apps') }}"><i class="fa fa-dashboard"></i> <span>Dashboard - Apps</span></a></li>
    @endif




    @if(in_array($rolUsuario, array(1, 4, 5, 21, 20, 22)) == true || $id_auth == '0922053467')
    <li><a href="{{ route('reporte.index_cierre') }}"><i class="fa fa-money"></i> <span>Cierre de Caja</span></a></li>
    <li><a href="{{ route('estaditicos_plano.orden') }}"><i class="fa fa-pie-chart"></i> <span>Estadísticos Recibo Cobro</span></a></li>
    @endif

    @if(in_array($rolUsuario, array(1)) == true)
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
        <li><a href="{{ route('factura_agrupada.index_factura_agrupada') }}">Factura Agrupada</a></li>
        <li><a href="{{ route('e.labs_estadisticos') }}"><i class="fa fa-pie-chart"> </i> Estadisticos</a></li>
        <li><a href="{{ route('documento.excel_index') }}"><i class="fa fa-file-archive-o"> </i> Mantenimiento de Examenes</a></li>
        <li><a href="{{ route('tipo_tubo.index') }}"><i class="fa fa-wrench"> </i> Mantenimiento Tubos</a></li>
        <li><a href="{{ route('mantenimientoexcel.index') }}"><i class="fa fa-plus-square"> </i> Administración de Tubos</a></li>
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
      <a href="#"><i class="fa fa-commenting-o"></i> <span>Encuestas</span>
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
      <a href="#"><i class="fa fa-commenting-o"></i> <span>Encuestas LABS</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li><a href="{{ route('laboratorio.resultados_labs') }}">Listado de Encuestas Labs</a></li>
        <li><a href="{{ route('laboratorio.estadisticalabs') }}">Estadisticas Labs</a></li>

      </ul>
    </li>
    @endif

    @if(in_array($rolUsuario, array(1, 4, 5, 14, 20, 22)) == true)
    <li><a href="{{ url('manual') }}"><i class="fa fa-file-pdf-o"></i> <span>Tarifarios</span></a></li>
    @endif
    @if(in_array($rolUsuario, array(1, 6, 7, 11)) == true)
    <li><a href="{{ route('enfermeria.index') }}"><i class="fa fa-history"></i> <span>Pacientes del Dia @if(in_array($rolUsuario, array(1, 6, 7, 11)) == true) Enfermeros @endif</span></a></li>
    @endif
    @if(in_array($rolUsuario, array(1, 4)) == true)
    <li class="treeview">
      <a href="#"><i class="fa fa-link"></i> <span>Administración Sistema</span>
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
    <!-- NUEVO OPCIONES POR TIPO DE USUARIO-->
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

    @if(in_array($rolUsuario, array(11)) == true)
    <li><a href="{{ url('procedimientostv_dr') }}"><i class="fa fa-television"></i> <span>Procedimientos TV</span></a></li>
    @endif

    @if(in_array($rolUsuario, array(1, 11, 22)))
    <li class="treeview">
      <a href="#"><i class="fa fa-check-square-o"></i> <span>Orden Formato 012</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li><a href="{{ route('orden_ingresada_formato012') }}">Ordenes Ingresadas</a></li>
      </ul>
    </li>
    <li class="treeview">
      <a href="#"><i class="fa fa-cubes"></i> <span>Liquidación</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li><a href="{{ route('planilla.genera_planillas') }}">Ingresar Planillas</a></li>
        <li><a href="{{ route('estaditicos_plano.index') }}"><i class="fa fa-pie-chart"> </i> Estadisticos</a></li>
        <li><a href="{{ route('formatosProductos.index') }}">Formato Producto</a></li>
        <li><a href="{{ route('planilla.planillas_generadas') }}">Consulta General de Planilla</a></li>
        <li><a href="{{ route('planilla.reportes') }}">Generación de Reportes</a></li>
        <li><a href="{{ route('planilla.genera_ap') }}">Generar Archivo Plano IESS</a></li>
        <li><a href="{{ route('genera_ap_msp.planilla') }}">Generar Archivo Plano MSP</a></li>
        <li><a href="{{ url('administracion/procedimientos') }}">Mantenimiento Items</a></li>
        <li><a href="{{ url('administracion/plantillas') }}">Mantenimiento Plantillas</a></li>
      </ul>
    </li>
    @endif
    @if(in_array($rolUsuario, array(1,5)))
    <li class="treeview">
      <a href="#"><i class="fa fa-file"> </i>
        <span>Turnero</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li><a href="{{route('turnero_sala_administracion')}}"><i class="fa fa-hospital-o" aria-hidden="true"></i>Administracíon de turnos</a></li>
      </ul>
    </li>

    @endif

    @if(in_array($rolUsuario, array(1, 3, 4, 5, 10, 11, 12, 7, 15,6)) == true)
    <li class="treeview">
      <a href="#"> <i class="fa fa-file"> </i><span>Enfermeria</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li><a href="{{route('limpieza_equipo.index')}}"><i class="fa fa-hospital-o" aria-hidden="true"></i> Registro de Limpieza de Equipos</a></li>
      </ul>
    </li>

    @endif

    @if(in_array($rolUsuario, array(1, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 15,21,20,19,16,14)) == true)
    <li class="treeview">
      <a href="{{route('ticket_soporte_tecnico.index')}}"> <i class="fa fa-ticket" aria-hidden="true"></i><span>Ticket Soporte Tecnico </span></a>
    </li>
    @endif
    @if(in_array($rolUsuario, array(1, 6, 11,4)) == true)
    <li><a href="{{ route('camilla.index') }}"><i class="fa fa-hospital-o" aria-hidden="true"></i><span>Riesgo de Caida</span></a></li>
    @endif

    @php
    $permisos = \Sis_medico\Agenda_Permiso::where('id_usuario', $id_auth)->where('estado', 3)->first();
    @endphp
    @if(in_array($rolUsuario, array(1,4,6,11,24,4)) == true || $id_auth=='0954346441' || !is_null($permisos))
    <li class="treeview">
      <a href="#"> <i class="fa fa-file"> </i><span>Servicios Generales</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-rigth"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li><a href="{{route('limpieza_banos.index')}}"><i class="fa fa-hospital-o" aria-hidden="true"></i>Registro de Limpieza y Desinfección</a></li>
        <li><a href="{{route('mantenimientos_generales.index')}}"><i class="fa fa-wrench" aria-hidden="true"></i> Mantenimientos</a></li>
        <li><a href="{{route('index_pentax_limpieza')}}"><i class="fa fa-leanpub" aria-hidden="true"></i> Registro Limpieza de Pentax</a></li>
      </ul>
    </li>
    @endif

    @if($rolUsuario == 1)
    <li class="treeview">
      <a href="#"> <i class="fa fa-file"> </i>Permisos Laborales
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
    <li><a href="{{route('ticketpermisos.index_usuario')}}"><i class="fa fa-file"></i><span>Solicitud de Permiso</span></a></li>
    @endif
    @if($id_auth == '0912197217' || $id_auth == '0954400404')
    <li><a href="{{route('ticketpermisos.index')}}"><i class="fa fa-file"></i><span>Administración de Permisos</span></a></li>
    @endif

    @if(in_array($rolUsuario, array(1, 6, 11,4)) == true)
    <li><a href="{{ route('limpieza.salas') }}"><i class="fa fa-file"></i><span>Limpieza y Desinfección</span></a></li>
    @endif

    @if(in_array($rolUsuario, array(1)) == true)
    <li><a href="{{ route('consultas.index_rfellows') }}"><i class="fa fa-book"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span>Reporte Fellows</span></a></li>
    @endif
    @if($rolUsuario != 2)
    <li><a href="{{route('trabajo_campo_index')}}"><i class="fa fa-file"></i><span>Trabajo de campo</span></a></li>
    @endif
    @php
    $control = UsuariosControl::where('estado',1)->get();
    @endphp

    @if(in_array($rolUsuario, array(2)) == true)
    <li><a href="{{route('index_control')}}"><i class="fa fa-user-md"></i><span>Control de sintomas</span></a></li>
    @endif

    @if(in_array($rolUsuario, array(1)) == true)
    <?php
    $deEmpresa = De_Empresa::where('id_empresa', $id_empresa)->first();
    if ($deEmpresa != '') {
    ?>
      <li>

        <!--Facturacion Electronica-->
      <li class="treeview">
        <a href="#"><i class="fa fa-link"></i> <span>Facturación Electrónica</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="{{ route('sri_electronico.enviar_sri') }}"><i class="fa fa-fw fa-file-o"></i> <span>Generar XML</span></a></li>
        </ul>
        <ul class="treeview-menu">
          <li><a href="{{ route('sri_electronico.enviar_sri') }}"><i class="fa fa-fw fa-file-o"></i> <span>Firmar XML</span></a></li>
        </ul>
        <ul class="treeview-menu">
          <li><a href="{{ route('sri_electronico.enviar_sri') }}"><i class="fa fa-fw fa-file-o"></i> <span>Validar XSD</span></a></li>
        </ul>
        <ul class="treeview-menu">
          <li><a href="{{ route('sri_electronico.enviar_sri') }}"><i class="fa fa-fw fa-file-o"></i> <span>Recepción SRI</span></a></li>
        </ul>
        <ul class="treeview-menu">
          <li><a href="{{ route('sri_electronico.enviar_sri') }}"><i class="fa fa-fw fa-file-o"></i> <span>Autorización SRI</span></a></li>
        </ul>
        <ul class="treeview-menu">
          <li><a href="{{ route('sri_electronico.enviar_sri') }}"><i class="fa fa-fw fa-file-o"></i>No Autorizado SRI</a></li>
        </ul>
        <ul class="treeview-menu">
          <li><a href="{{ route('sri_electronico.enviar_sri') }}"><i class="fa fa-fw fa-file-o"></i>Notificación SRI</a></li>
        </ul>
        <ul class="treeview-menu">
          <li><a href="{{ route('demaestrodoc.index') }}"><i class="fa fa-fw fa-file-o"></i>Maestro Documentos</a></li>
        </ul>
        @if(in_array($rolUsuario, array(1)) == true)
        @endif
      </li>
    <?php
    }
    ?>
    <li>
      <a href="{{ route('muestrabiopsias.index') }}">
        <i class="fa fa-calendar-minus-o"></i><span> Revision Ordenes Biopsia </span>
      </a>
    </li>


    @endif


    </ul>
    <!-- /.sidebar-menu -->
  </section>
  <!-- /.sidebar -->
</aside>

<script type="text/javascript">
  @if(in_array($rolUsuario, array(1, 4, 5)) == true)
  $(document).ready(function() {
    cantidad_observaciones();
  });
  var cantidad_observaciones = function() {

    $.ajax({
      type: 'get',
      url: `{{route('observacion.cantidad')}}`,
      success: function(data) {
        //alert(data);
        //console.log(data);
        if (data > 0) {
          $('#o_cantidad').empty().html(data);
        }
      }
    })

  }

  /*vartiempo = setInterval(function(){ cantidad_observaciones(); }, 5000);*/
  @endif
</script>