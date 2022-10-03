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
          <div id="contenedor">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="tablaVentas" class="table table-hover dataTable" role="grid" aria-describedby="example2_info" width="100%">
                    <thead>
                      <tr class="well-dark">
                        <th>#</th>
                        <th># {{trans('wfact_venta.Comprobante')}}</th>
                        <th>{{trans('wfact_venta.Tipo')}}</th>
                        <th>{{trans('wfact_venta.Fecha')}}</th>
                        <th>{{trans('wfact_venta.Cliente')}}</th>
                        <!--<th >RUC/CID</th>-->
                        <th>{{trans('wfact_venta.Paciente')}}</th>
                        <th>{{trans('wfact_venta.seguro')}}</th>
                        <th>{{trans('wfact_venta.Procedimiento')}}</th>
                        <th style="font-size: 14px;">{{trans('wfact_venta.fecha_procedimiento')}}</th>
                        <th>{{trans('wfact_venta.creado_por')}}</th>
                        <th>{{trans('wfact_venta.anulado_por')}}</th>
                        <th style="font-size: 14px;">{{trans('wfact_venta.electronica')}}</th>
                        <th>{{trans('wfact_venta.informacion')}}</th>
                        <th style="width:20%;">{{trans('wfact_venta.accion')}}</th>
                      </tr>
                    </thead>
                    <tbody>

                      @foreach($ventas as $value)
                      <tr class="well">
                        <td>@if(!is_null($value->id)){{$value->id}}@endif</td>
                        <td>@if(!is_null($value->nro_comprobante)){{$value->nro_comprobante}}@endif</td>
                        <td>@if(!is_null($value->tipo)){{$value->tipo}}@endif</td>
                        <td>@if(!is_null($value->fecha)){{substr($value->fecha,0,11)}}@endif</td>
                        @php
                        $cliente = \Sis_medico\Ct_Clientes::where('identificacion',$value->id_cliente)->first();
                        $seguro = Sis_medico\Seguro::find($value->seguro_paciente);
                        @endphp
                        <td>@if(!is_null($cliente->nombre)){{$cliente->nombre}}@endif</td>
                        <!-- <td>@if(!is_null($cliente->cedula_representante)){{$cliente->cedula_representante}}@endif</td>-->
                        <td>@if(!is_null($value->nombres_paciente)){{$value->nombres_paciente}}@endif</td>
                        <td>@if(!is_null($seguro)){{$seguro->nombre}}@endif</td>
                        <td>@if(!is_null($value->procedimientos)){{$value->procedimientos}}@endif</td>
                        <td>@if(!is_null($value->fecha_procedimiento)){{$value->fecha_procedimiento}}@endif</td>
                        <!--<td>@if($value->estado == '1') {{trans('contableM.activo')}} @endif</td>-->
                        <td>@if(isset($value->usuario)) {{$value->usuario->nombre1}} {{$value->usuario->nombre2}} @endif</td>
                        <td>@if($value->estado == 0) @if(isset($value->usuariomod)) {{$value->usuariomod->nombre1}} {{$value->usuariomod->nombre2}} @endif @endif</td>
                        @if($value->electronica == 0)
                        <td>{{trans('wfact_venta.no')}}</td>
                        @else
                        @if($value->estado_electronica == 3)
                        <td>{{trans('wfact_venta.autorizado')}}</td>
                        @else
                        @php
                        $fecha1 = new DateTime($value->created_at);//fecha inicial
                        $fecha2 = new DateTime(date('Y-m-d H:i:s'));//fecha de cierre
                        $intervalo = $fecha1->diff($fecha2);
                        $minutos = $intervalo->format('%i');
                        $horas = $intervalo->format('%h');
                        $tiempo = $minutos +($horas*60);
                        @endphp
                        @if($tiempo > 5)
                        @php
                        $data['empresa'] = $empresa->id;
                        $data['comprobante'] = $value->nro_comprobante;
                        $data['tipo'] = "comprobante";

                        //$envio = \Sis_medico\Http\Controllers\ApiFacturacionController::estado_comprobante($data);
                        $envio=[];
                        $estado = 0;
                        $documento = \Sis_medico\Ct_ventas::find($value->id);
                        if(isset($envio->details)){
                        if($envio->details->log[0]->proceso == "Notificación" || $envio->details->log[0]->mensaje == "Autorizado"){
                        $estado = 3;
                        $documento->nro_autorizacion = $envio->details->autorizacion;
                        echo "<td>{{trans('wfact_venta.autorizado')}}</td>";
                        }elseif($envio->details->log[0]->mensaje == "Documento recibido"){
                        $estado = 2;
                        echo "<td>{{trans('wfact_venta.enviado_sri')}}</td>";
                        }elseif($envio->details->log[0]->proceso == "Firma"){
                        $estado = 1;
                        echo "<td>{{trans('wfact_venta.firmado')}}</td>";
                        }else{
                        echo "<td>{{trans('wfact_venta.enviado')}}</td>";
                        }
                        }else{
                        echo "<td>{{trans('wfact_venta.pendiente_envio')}}</td>";
                        }
                        $documento->estado_electronica = $estado;
                        $documento->save();
                        @endphp
                        @else
                        <td>{{trans('wfact_venta.pendiente_envio')}}</td>
                        @endif
                        @endif
                        @endif
                        <td>@if(isset($value->omni)) @if($value->omni==1) OMNI @else @endif @endif</td>
                        <td style="padding-left: 1px;padding-right: 1px;">
                          @php /*
                          @if($value->estado == 1)
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a href="javascript:anular({{$value->id}});" class="btn btn-success col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">&nbsp;&nbsp;{{trans('wfact_venta.anular')}}</a>
                          @elseif($value->estado == 0)
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a class="btn btn-danger col-md-3 col-xs-3 btn-margin" disabled style="font-size: 10px;padding-left: 2px;padding-right: 20px;">&nbsp;&nbsp;{{trans('wfact_venta.anular')}}</a>
                          @endif
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a href="{{ route('ventas_editar', ['id' => $value->id]) }}" class="btn btn-warning col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">{{trans('wfact_venta.visualizar')}}</a>
                          @php
                          $permitidos= Sis_medico\ParametersConglomerada::pdf_permitidos($empresa->id);
                          //dd($permitidos);
                          @endphp
                          @if($permitidos)

                          <a href="{{route('venta.visualizador_pdf_html',['id'=>$value->id])}}" class="btn btn-warning btn-gray btn-margin col-xs-3" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">{{trans('wfact_venta.IECED')}}</a>
                          @endif
                          @if(!empty($value->rutapdf))
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a target="_blank" href="{{ route('pdf.visualizar', ['id' => $value->id]) }}" class="btn btn-warning col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">{{trans('wfact_venta.archivo')}}</a>
                          @endif

                          <!--<a class="btn btn-warning col-md-3 col-xs-3 btn-margin btn-gray" data-remote="{{route('detalle_paquete.facturacion',['id' => $value->id])}}" data-toggle="modal" data-target="#detalle_paquete" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Detall Ord</a>-->
                          @if($value->ct_orden_venta!=null)
                          <a target="_blank" href="{{ route('pdf_comprobante_detalle.paquete', ['id' => $value->id]) }}" class="btn btn-success col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">{{trans('wfact_venta.detalle_orden')}}</a>
                          {{-- <a target="_blank" href="{{ route('venta.planilla.detalle.pdf', ['id' => $value->id, 'tipo'=>'vta']) }}" class="btn btn-success col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">{{trans('wfact_venta.planilla')}}</a> --}}
                          {{-- <a data-toggle="modal" data-target="#md_planilla"  href="{{ route('ventas.comprobante_publico', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'comprobante']) }}" class="btn btn-info col-md-6 col-xs-6 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">{{trans('wfact_venta.planilla')}}</a> --}}
                          {{-- <a class="btn btn-warning col-md-3 col-xs-3 btn-margin btn-gray" data-remote="{{route('venta.planilla.detalle',['id' => $value->id])}}" data-toggle="modal" data-target="#md_planilla" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">{{trans('wfact_venta.planilla')}}</a> --}}
                          @endif

                          @if($value->electronica == 0)
                          @if($value->ip_creacion=="OMNI")
                          <a target="_blank" href="{{ route('ventas.pdf_omni', ['id' => $value->id]) }}" class="btn btn-success col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">{{trans('wfact_venta.pdf_fact')}}</a>
                          @else
                          <a target="_blank" href="{{ route('pdf_comprobante_no.tributario', ['id' => $value->id]) }}" class="btn btn-success col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">{{trans('wfact_venta.pdf_fact')}}</a>
                          @endif
                          @else
                          <a target="_blank" href="{{ route('ventas.comprobante_publico', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'pdf']) }}" class="btn btn-success col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">{{trans('wfact_venta.ride_fact')}}</a>

                          <a target="_blank" href="{{ route('ventas.comprobante_publico', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'xml']) }}" class="btn btn-success col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">{{trans('wfact_venta.xml_fact')}}</a>
                          <a data-toggle="modal" data-target="#log_factura" href="{{ route('ventas.comprobante_publico', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'comprobante']) }}" class="btn btn-info col-md-6 col-xs-6 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">{{trans('wfact_venta.log_fact')}}</a>
                          @endif
                          @if(Auth::user()->id == "0957258056")
                          <button type="button" onclick="enviarSri({{$value->id}})" class="btn btn-success">Enviar SRI</button>
                          @endif
                          */@endphp
                          <!--///Lista de Opciones-->

                          <div class="btn-group" style="width: 100%;">
                            <button style="width:50%;" type="button" class="btn btn-success btn-xs"><span style="font-size: 12px;">{{trans('contableM.acciones')}}</span></button>
                            <button style="width:10%;" type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false" style="padding-left: 2px;padding-right: 2px">
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu cot" role="menu" style="background-color: #00a65a;padding: 2px;min-width: 69%;">
                              @if($value->estado == 1)
                              <input type="hidden" name="_token" value="{{ csrf_token() }}">
                              <li><a href="javascript:anular({{$value->id}});" class="btn btn-danger" style="">&nbsp;&nbsp;{{trans('wfact_venta.anular')}}</a> </li>
                              @elseif($value->estado == 0)
                              <input type="hidden" name="_token" value="{{ csrf_token() }}">
                              <li><a class="btn btn-danger" disable">&nbsp;&nbsp;{{trans('wfact_venta.anular')}}</a> </li>
                              @endif
                              <input type="hidden" name="_token" value="{{ csrf_token() }}">
                              <li><a href="{{ route('ventas_editar', ['id' => $value->id]) }}" class="btn btn-warning">{{trans('wfact_venta.visualizar')}}</a> </li>
                              @php
                              $permitidos= Sis_medico\ParametersConglomerada::pdf_permitidos($empresa->id);
                              //dd($permitidos);
                              @endphp
                              @if($permitidos)

                              <li><a href="{{route('venta.visualizador_pdf_html',['id'=>$value->id])}}" class="btn btn-warning">{{trans('wfact_venta.IECED')}}</a> </li>
                              @endif
                              @if(!empty($value->rutapdf))
                              <input type="hidden" name="_token" value="{{ csrf_token() }}">
                              <li><a target="_blank" href="{{ route('pdf.visualizar', ['id' => $value->id]) }}" class="btn btn-warning col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">{{trans('wfact_venta.archivo')}}</a> </li>
                              @endif

                              <!--<a class="btn btn-warning col-md-3 col-xs-3 btn-margin btn-gray" data-remote="{{route('detalle_paquete.facturacion',['id' => $value->id])}}" data-toggle="modal" data-target="#detalle_paquete" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Detall Ord</a>-->
                              @if($value->ct_orden_venta!=null)
                              <li><a target="_blank" href="{{ route('pdf_comprobante_detalle.paquete', ['id' => $value->id]) }}" class="btn btn-success" style="">{{trans('wfact_venta.detalle_orden')}}</a> </li>
                              @endif

                              @if($value->electronica == 0)
                              @if($value->ip_creacion=="OMNI")
                              <li><a target="_blank" href="{{ route('ventas.pdf_omni', ['id' => $value->id]) }}" class="btn btn-success" style="">{{trans('wfact_venta.pdf_fact')}}</a> </li>
                              @else
                              <li><a target="_blank" href="{{ route('pdf_comprobante_no.tributario', ['id' => $value->id]) }}" class="btn btn-success" style="">{{trans('wfact_venta.pdf_fact')}}</a> </li>
                              @endif
                              @else
                              <li><a target="_blank" href="{{ route('ventas.comprobante_publico', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'pdf']) }}" class="btn btn-success" style="">{{trans('wfact_venta.ride_fact')}}</a> </li>

                              <li><a target="_blank" href="{{ route('ventas.comprobante_publico', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'xml']) }}" class="btn btn-success" style="">{{trans('wfact_venta.xml_fact')}}</a> </li>
                              <li><a data-toggle="modal" data-target="#log_factura" href="{{ route('ventas.comprobante_publico', ['comprobante' => $value->nro_comprobante, 'id_empresa' => $empresa->id, 'tipo' => 'comprobante']) }}" class="btn btn-info" style="">{{trans('wfact_venta.log_fact')}}</a> </li>
                              @endif
                              @if(Auth::user()->id == "0957258056")
                              <li><button style="width: 79%;padding: 3px 12px;" type="button" onclick="enviarSri({{$value->id}})" class="btn btn-success">Enviar SRI</button></li>
                              @endif
                            </ul>
                          </div>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                    <tfoot>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-5">
                  <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('wfact_venta.mostrando')}} {{1 + (($ventas->currentPage() - 1) * $ventas->perPage())}} / {{count($ventas) + (($ventas->currentPage() - 1) * $ventas->perPage())}} {{trans('wfact_venta.de')}} {{$ventas->total()}} {{trans('wfact_venta.registros')}}</div>
                </div>
                <div class="col-sm-7">
                  <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                    {{ $ventas->appends(Request::only(['id', 'numero','nombre_cliente','nombres_paciente','fecha','id_asiento','omni']))->links() }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection