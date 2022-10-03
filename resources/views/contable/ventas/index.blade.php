@extends('contable.ventas.base')
@section('action-content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
<style>
  .dropdown-menu>li>a {
    color: white !important;
    font-weight: 600;
  }

  <?php

  use Illuminate\Support\Facades\Auth;

  if (Auth::user()->id == "0957258056") {
  ?>* {
    font-family: 'Poppins',
      sans-serif;
  }

  .sidebar-menu>li>a {
    font-size: 13px;
  }

  .well>td {
    font-size: 12px;
    font-weight: 600;
  }

  th {
    font-size: 12px !important;
  }

  <?php
  }
  ?>
</style>
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css') }}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<div class="modal fade" id="detalle_paquete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="width: 95%;">
    </div>
  </div>
</div>
<div class="modal fade" id="edit_orde_det_paq" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="width: 95%;">
    </div>
  </div>
</div>
<div class="modal fade" id="log_factura" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<div class="modal fade" id="md_planilla" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<!-- Main content -->
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('wfact_venta.Contable')}}</a></li>
      <li class="breadcrumb-item"><a href="#">{{trans('wfact_venta.Ventas')}}</a></li>
      <li class="breadcrumb-item active">{{trans('wfact_venta.Registro de Factura de Ventas')}}</li>

    </ol>
  </nav>
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-9">
        <h3 class="box-title">{{trans('wfact_venta.Facturas de Venta')}} </h3>
      </div>
      <div class="col-md-1 text-right">
        <button onclick="location.href='<?= route('ventas_crear'); ?>'" class="btn btn-success btn-gray">
          <i aria-hidden="true"></i>{{trans('wfact_venta.Nueva Factura')}}
        </button>
      </div>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">{{trans('wfact_venta.BUSCADOR FACTURA DE VENTA')}}</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('ventas_search') }}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="numero">{{trans('wfact_venta.Id')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="id" name="id" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" placeholder="{{trans('wfact_venta.Ingrese_id')}}" />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="numero">{{trans('wfact_venta.Numero')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="numero" name="numero" value="@if(isset($searchingVals)){{$searchingVals['nro_comprobante']}}@endif" placeholder="{{trans('wfact_venta.Ingrese_numero')}}" />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="asiento">{{trans('wfact_venta.Asiento')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="id_asiento" name="id_asiento" value="@if(isset($searchingVals)){{$searchingVals['id_asiento']}}@endif" placeholder="{{trans('wfact_venta.Ingrese_asiento')}}" />
        </div>

        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="fecha">{{trans('wfact_venta.Fecha')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <div class="input-group date">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="date" name="fecha" class="form-control fecha" id="fecha" value="@if(isset($searchingVals)){{$searchingVals['fecha']}}@endif">
          </div>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="cliente">{{trans('wfact_venta.Cliente')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="nombre_cliente" name="nombre_cliente" value="@if(isset($searchingVals)){{$searchingVals['nombre_cliente']}}@endif" placeholder="{{trans('wfact_venta.Ingrese_cliente')}}" />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="paciente">{{trans('wfact_venta.Paciente')}}</label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="nombres_paciente" name="nombres_paciente" value="@if(isset($searchingVals)){{$searchingVals['nombres_paciente']}}@endif" placeholder="{{trans('wfact_venta.Ingrese_paciente')}}" />
        </div>

        <div class="col-md-offset-9 col-xs-2">
          <button type="submit" id="buscarAsiento" class="btn btn-primary btn-gray">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span>{{trans('wfact_venta.Buscar')}}
          </button>
        </div>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">{{trans('wfact_venta.Ventas')}}</label>
      </div>
    </div>
    <div class="box-body dobra">
      <div class="form-group col-md-12">
        <div class="form-row">
          <div id="resultados">
          </div>
          <div id="example2_wrapper">
            <div class="row">
              <div class="table-responsive col-md-12">
                <table id="tablaVentas" class="table table-hover dataTable" role="grid" aria-describedby="example2_info" width="100%">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>{{trans('wfact_venta.accion')}}</th>
                      <th>{{trans('wfact_venta.estadosri')}}</th>
                      <th>{{trans('wfact_venta.Comprobante')}}</th>
                      <th>{{trans('wfact_venta.Tipo')}}</th>
                      <th>{{trans('wfact_venta.Fecha')}}</th>
                      <th>{{trans('wfact_venta.Cliente')}}</th>
                      <th>{{trans('wfact_venta.Paciente')}}</th>
                      <th>{{trans('wfact_venta.seguro')}}</th>
                      <th>{{trans('wfact_venta.Procedimiento')}}</th>
                      <th>{{trans('wfact_venta.fecha_procedimiento')}}</th>
                      <th>{{trans('wfact_venta.creado_por')}}</th>
                      <th>{{trans('wfact_venta.anulado_por')}}</th>
                      <!--<th>{{trans('wfact_venta.informacion')}}</th>-->
                    </tr>
                  </thead>
                  <tbody></tbody>
                  <tfoot>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection