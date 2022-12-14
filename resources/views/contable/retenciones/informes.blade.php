@extends('contable.balance_comprobacion.base')
@section('action-content')
<style>
  p.s1 {
    font-size:    12px;
    font-weight:  bold;
  } 
  p.s2 {
    margin-left:  20px;
    font-size:    12px;
    font-weight:  bold;
  } 
  p.s3 {
    margin-left:  30px;
    font-size:    10px;
    font-weight:  bold;
  } 
  p.s4 {
    margin-left:  40px;
    font-size:    10px;
  } 
  p.t1 { 
    font-size:    14px;
    font-weight:  bold;
  } 
  p.t2 { 
    font-size:    12px;
    font-weight:  bold;
  } 
  p.t3 { 
    font-size:    10px;
  }
  .table-striped>thead>tr>th>td, .table-striped>tbody>tr>th>td, .table-striped>tfoot>tr>th>td, .table-striped>thead>tr>td, .table-striped>tbody>tr>td, .table-striped>tfoot>tr>td {
    padding: 0.5px;
    text-align: center;
    
  }
  .hidden-paginator {

    display: none;

    }
    .right_text{
      text-align: right;
    }
  .table{
    margin-bottom: -5px;
  }
  .ui-autocomplete
  {
      overflow-x: hidden;
        max-height: 200px;
        width:1px;
      position: absolute;
      top: 100%;
      left: 0;
      z-index: 1000;
      float: left;
      display: none;
      min-width: 160px;
      _width: 160px;
      padding: 4px 0;
      margin: 2px 0 0 0;
      list-style: none;
      background-color: #fff;
      border-color: #ccc;
      border-color: rgba(0, 0, 0, 0.2);
      border-style: solid;
      border-width: 1px;
      -webkit-border-radius: 5px;
      -moz-border-radius: 5px;
      border-radius: 5px;
      -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
      -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
      box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
      -webkit-background-clip: padding-box;
      -moz-background-clip: padding;
      background-clip: padding-box;
      *border-right-width: 2px;
      *border-bottom-width: 2px;
  }
  
  </style>
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
  <!-- Main content -->
  <section class="content">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
        <li class="breadcrumb-item"><a href="#">{{trans('contableM.Contabilidad')}}</a></li>
        <li class="breadcrumb-item"><a href="#">{{trans('contableM.acreedor')}}</a></li> 
      </ol>
    </nav>

    <div class="box" style=" background-color: white;">
        <!-- <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
            <div class="col-md-6">
              <h3 class="box-title">Criterios de b??squeda</h3>
            </div>
        </div> -->
        <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" for="title">{{trans('contableM.Buscador')}}</label>
          </div>
        </div>
      <!-- /.box-header -->
      <div class="box-body dobra">
        <form method="POST" id="reporte_master" action="{{ route('informe_retenciones.index') }}" >
        {{ csrf_field() }}

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">{{trans('contableM.FechaDesde')}}</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_desde').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha_hasta" class="texto col-md-3 control-label">{{trans('contableM.Fechahasta')}}</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>
            <div class="form-group col-md-2 col-xs-2">
              <label class="texto" for="nombre_proveedor">{{trans('contableM.proveedor')}}: </label>
            </div>
            <div class="form-group col-md-4 col-xs-4 container-4">
              <select class="form-control select2_cuentas" name="id_proveedor" id="id_proveedor" style="width: 100%;">
                    <option value="">Seleccione...</option>
                @foreach($proveedores as $value)
                    <option @if(!is_null($id_proveedor)) @if(($id_proveedor)==$value->id) selected='selected' @endif @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                @endforeach
              </select>     
              
            </div>
            <div class="form-group col-md-2 col-xs-2">
              <label class="texto" for="nombre_proveedor">{{trans('contableM.secuencia')}}: </label>
            </div>
            <div class="form-group col-md-4 col-xs-4 container-4">
              <input class="form-control" type="text" id="secuencia" name="secuencia"  placeholder="Ingrese Secuencia..." />
          
              
            </div>

        <!-- <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
            <label for="mostrar_detalles" class="texto col-md-5 control-label" >{{trans('contableM.Mostrarresumen')}}</label>
            <input type="checkbox" id="mostrar_detalles" class="flat-green" name="mostrar_detalles" value="1"  @if(old('mostrar_detalles')=="1") checked @endif>
        </div> -->

        <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
          <!-- <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;Buscar&nbsp;</span>
          </button>
          <button type="button" class="btn btn-primary btn-sm" id="btn_imprimir" name="btn_imprimir">
            <span class="glyphicon glyphicon-print" aria-hidden="true" style="font-size: 16px">&nbsp;{{trans('contableM.Imprimir')}}&nbsp;</span>
          </button> -->
          <button type="submit" class="btn btn-primary" id="boton_buscar">
                <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
          </button>
          <button type="button" class="btn btn-primary" onclick="excel();" id="btn_exportar">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> {{trans('contableM.Exportar')}}
          </button> 
        </div>

        <div class="form-group col-md-6 col-xs-9" style="text-align: right;">
          
        </div>
      </form> 
      </div>
      <!-- /.box-body -->
      <form method="POST" id="print_reporte_master" action="{{ route('retenciones.excel') }}" target="_blank">
          {{ csrf_field() }}
        <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{$fecha_desde}}">
        <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{$fecha_hasta}}">
        <input type="hidden" name="id_proveedor2" id="id_proveedor2" value="{{$id_proveedor}}">
      </form>

      @if(count($retenciones)>0)
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="col-md-12">
            <div class="box box-solid">
              <div class="box-header with-border">
              </div>
              <!-- /.box-header -->
              {{-- <div class="box-body">
                <div class="col-md-4">
                  <dl> 
                    <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>
                    <dd>&nbsp; {{$empresa->nombrecomercial}}</dd>
                  </dl>
                </div>
                <div class="col-md-4">
                  <h4 style="text-align: center;">{{trans('contableM.InformedeRetenciones')}}</h4>
                  <h4 style="text-align: center;">{{trans('contableM.periodo')}} {{date("d-m-Y", strtotime($fecha_desde))}} - {{date("d-m-Y", strtotime($fecha_hasta))}}</h4>
                </div>
                <div class="col-md-4">
                  <dl>   
                    <dd style="text-align:right">{{$empresa->direccion}} &nbsp; <i class="fa fa-building"></i></dd> 
                    <dd style="text-align:right">{{trans('contableM.telefono')}}: {{$empresa->telefono1}} - {{$empresa->telefono2}}&nbsp;<i class="fa fa-phone"></i> </dd>
                    <dd style="text-align:right"> {{$empresa->email}} &nbsp;<i class="fa fa-envelope-o"></i></dd>   
                  </dl>
                </div>
              </div> --}}

              <div class="box-body">
                <div class="col-md-1">
                  <dl> 
                    <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>
                    {{-- <dd>&nbsp; {{$empresa->nombrecomercial}}</dd> --}}
                  </dl>
                </div>
                <div class="col-md-3">
                  <dl> 
                    <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
                    <dd>&nbsp; {{$empresa->id}}</dd> 
                  </dl>
                </div>
                <div class="col-md-4"> 
                  <h4 style="text-align: center;">{{trans('contableM.InformedeRetenciones')}}</h4>
                  <h5 style="text-align: center;"> @if(($fecha_desde)!=null)  {{date("d-m-Y", strtotime($fecha_desde))}} - {{date("d-m-Y", strtotime($fecha_hasta))}} @else Al- {{date("d-m-Y",strtotime($fecha_hasta))}} @endif</h5>
                </div>
                <div class="col-md-4"> 
                </div>  
                <div class="col-md-12">

                </div>
               
                  <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item active">
                      <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Informe 1</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Informe 2</a>
                    </li>
                    
                  </ul>
                
              <!-- PILAS MMV TIENES QUE PONER UN ELSE EN LA RETENCION PORQUE NO SALE SI TIENE UNA SOLA U OTRA -->
              <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade active in" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <table id="example2" class="display compact" style="font-size: 12px!important">
                      <thead>
                        <tr >
                          <th style="text-align:center;" rowspan="1" >{{trans('contableM.fecha')}}</th>
                          <th style="text-align:center;" rowspan="1">{{trans('contableM.numero')}}</th>
                          <th style="text-align:center;" colspan="1">{{trans('contableM.Preimpresa')}}</th>
                          <th style="text-align:center;" colspan="1">{{trans('contableM.acreedor')}}</th>
                          <th  style="text-align:center;" colspan="1">{{trans('contableM.ruc')}}</th>
                          <th  style="text-align:center;" colspan="1">{{trans('contableM.detalle')}}</th>
                          <th class="right_text" colspan="1">{{trans('contableM.totalrfir')}}.</th>
                          <th class="right_text" rowspan="1" >%</th>
                          <th class="right_text"  colspan="1">{{trans('contableM.totalrfiva')}}</th>
                          <th class="right_text"  colspan="1">%</th>
                          <th style="text-align:center;" colspan="1">{{trans('contableM.estado')}}</th>
                          <th style="text-align:center;" colspan="1">{{trans('contableM.creadopor')}}</th>
                          <th style="text-align:center;"  colspan="1">{{trans('contableM.anuladopor')}}</th>
                        </tr>
                      </thead>
                      <tbody>
                        @php $totales_ref = 0; @endphp
                            @foreach($retenciones as $value)
                            @php 
                              if(!is_null($value->valor_fuente)&& $value->estado == 1){
                                $totales_ref +=$value->valor_fuente;
                              }
                            @endphp
                                <tr>
                                    <td>{{date("d-m-Y", strtotime($value->fecha))}}</td>
                                    <td style="text-align: left;">@if(($value->compras)!=null) @if(($value->compras->tipo)==1) COM-FA @else COM-FACT @endif :{{$value->compras->numero}} @endif</td>
                                    <td>@if(($value->nro_comprobante)!=null) {{$value->nro_comprobante}} @endif</td>
                                    <td style="text-align:left;">@if(($value->proveedor)!=null) {{$value->proveedor->nombrecomercial}} @endif</td>
                                    <td style="text-align:left;">@if(($value->proveedor)!=null) {{$value->proveedor->id}} @endif</td>
                                    <td style="text-align:left;">@if(($value->descripcion)!=null){{$value->descripcion}}  @endif</td>
                                    <td class="right_text">@if(($value->valor_fuente)!=null) {{number_format($value->valor_fuente,2,'.',',')}}  @endif </td>
                                    <td class="right_text">@if(($value->detalle)!=null) @foreach($value->detalle as $val) @if(($val->porcentajer->tipo)==2) {{$val->porcentajer->valor}}% <br>  @endif @endforeach  @endif</td>
                                    <td class="right_text">@if(($value->valor_iva)!=null) {{number_format($value->valor_iva,2,'.',',')}}  @endif @if(($value->detalle)!=null)  @endif</td>
                                    <td style="text-align: right;" >@if(($value->detalle)!=null) @foreach($value->detalle as $val) @if(($val->porcentajer->tipo)==1) {{$val->porcentajer->valor}}%@endif @endforeach  @endif</td>
                                    <td>@if(($value->estado)==1) {{trans('contableM.activo')}} @else ANULADA  @endif @if($value->anulado==1) R-ANULADO @endif</td>
                                    <td>@if(($value->usuario)!=null) {{$value->usuario->nombre1}} {{$value->usuario->apellido1}} @endif</td>
                                    <td>@if(($value->estado)==0) {{$value->usuario->nombre1}} {{$value->usuario->apellido1}} @else  @endif</td>
                                </tr>
                            @endforeach
                           
                        </tbody>
                        <tfoot>
                                 <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td style="text-align: center; font-weight: bold;">{{trans('contableM.totales')}}:</td>
                                    <td class="right_text"> <input type="hidden" value="{{$totales_ref}}"> <b>{{number_format($total1,2,'.',',')}}</b> </td>
                                    <td style="text-align: center;">&nbsp;</td>
                                    <td class="right_text"> <b>{{number_format($total2,2,'.',',')}}</b> </td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                        </tfoot>
                    </table>

                </div>
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                  <div class="col-md-12">
                    <div class="table table-responsive">
                    <table id="example3" class="display compact"  style="font-size: 12px!important">
                        <thead>
                          <tr >
                         
                            <th style="text-align:center;"    rowspan="1" ># Retencion</th>
                            <th style="text-align:center;"   rowspan="1" >{{trans('contableM.fecha')}}</th>
                            <th style="text-align:center;"    colspan="1">{{trans('contableM.proveedor')}}</th>
                            <th style="text-align:center;"    colspan="1">{{trans('contableM.ruc')}}</th>
                            <th  style="text-align:center;"   colspan="1">{{trans('contableM.factura')}}</th>
                            <th class="right_text"   colspan="1">Tarifa 12 % </th>
                            <th class="right_text"   colspan="1">Tarifa 0 % </th>
                            <th class="right_text"   colspan="1">{{trans('contableM.subtotal')}}</th>
                            <th class="right_text"   colspan="1">{{trans('contableM.iva')}}</th>
                            <th class="right_text"   colspan="1">{{trans('contableM.total')}}</th>
                            <th class="right_text"   colspan="1">{{trans('contableM.TotalRetenido')}}</th>
                            <th class="right_text"   colspan="1">{{trans('contableM.codigo')}}</th>
                            <th class="right_text"   rowspan="1" >%</th>
                            <th class="right_text"   colspan="1">{{trans('contableM.TotalRetenido')}}</th>
                            <th class="right_text"   colspan="1">{{trans('contableM.codigo')}}</th>
                            <th class="right_text"   colspan="1">%</th>
                            
                            <th style="text-align:center;"    colspan="1">{{trans('contableM.autorizacion')}}</th>
                           <!--  <th style="text-align:center;"   colspan="1">{{trans('contableM.anuladopor')}}</th> -->
                          </tr>
                        </thead>
                        <tbody>
                             @php 
                               $totalretenido1= 0;
                               $totalretenido2=0;
                             @endphp
                              @foreach($retenciones as $s)
                                  @php
                                  $fuente= Sis_medico\Ct_Detalle_Retenciones::where('id_retenciones',$s->id)->where('tipo','RENTA')->get();
                                  $iva= Sis_medico\Ct_Detalle_Retenciones::where('id_retenciones',$s->id)->where('tipo','IVA')->get();
                                  @endphp
                                 
                                  @if(count($iva)>1)
                                    @foreach($iva as $i)
                                      <tr>
                                          <td>@if(($s->nro_comprobante)!=null) {{$s->nro_comprobante}} @endif</td>
                                          <td>{{date("d-m-Y", strtotime($s->fecha))}}</td>
                                          <!-- <td style="text-align: left;">@if(($s->compras)!=null) @if(($s->compras->tipo)==1) COM-FA @else COM-FACT @endif :{{$s->compras->numero}} @endif</td> -->
                                      
                                          <td style="text-align:left;">@if(($s->proveedor)!=null) {{$s->proveedor->razonsocial}} @endif</td>
                                          <td style="text-align:left;">@if(($s->proveedor)!=null) {{$s->proveedor->id}} @endif</td>
                                          <td style="text-align:left;">@if(($s->compras)!=null){{$s->compras->numero}}  @endif</td>
                                          <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->subtotal_12}}  @endif</td>
                                          <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->subtotal_0}}  @endif</td>
                                          <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->subtotal}}  @endif</td>
                                          <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->iva_total}}  @endif</td>
                                          <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->total_final}}  @endif</td>
                                          <td class="right_text"></td>
                                          <td class="right_text"></td>
                                          <td class="right_text"></td>
                                          <td class="right_text">@if(($i->totales)!=null) {{number_format($i->totales,2,'.',',')}}  @endif </td>
                                          <td class="right_text">{{$i->porcentajer->codigo_interno}}</td>
                                          <td style="text-align: right;">{{$i->porcentajer->valor}} %</td>
                                        <!--  <td>@if(($s->usuario)!=null) {{$s->usuario->nombre1}} {{$s->usuario->apellido1}} @endif</td>
                                          <td>@if(($s->estado)==0) {{$s->usuario->nombre1}} {{$s->usuario->apellido1}} @else  @endif</td> -->
                                          <td>{{$s->autorizacion}}</td>
                                      </tr>
                                    @endforeach
                                  @else 
                                  @foreach($iva as $i)
                                      <tr>
                                          <td>@if(($s->nro_comprobante)!=null) {{$s->nro_comprobante}} @endif</td>
                                          <td>{{date("d-m-Y", strtotime($s->fecha))}}</td>
                                          <!-- <td style="text-align: left;">@if(($s->compras)!=null) @if(($s->compras->tipo)==1) COM-FA @else COM-FACT @endif :{{$s->compras->numero}} @endif</td> -->
                                      
                                          <td style="text-align:left;">@if(($s->proveedor)!=null) {{$s->proveedor->razonsocial}} @endif</td>
                                          <td style="text-align:left;">@if(($s->proveedor)!=null) {{$s->proveedor->id}} @endif</td>
                                          <td style="text-align:left;">@if(($s->compras)!=null){{$s->compras->numero}}  @endif</td>
                                          <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->subtotal_12}}  @endif</td>
                                          <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->subtotal_0}}  @endif</td>
                                          <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->subtotal}}  @endif</td>
                                          <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->iva_total}}  @endif</td>
                                          <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->total_final}}  @endif</td>
                                          <td class="right_text"></td>
                                          <td class="right_text"></td>
                                          <td class="right_text"></td>
                                          <td class="right_text">@if(($i->totales)!=null) {{number_format($i->totales,2,'.',',')}}  @endif </td>
                                          <td class="right_text">{{$i->porcentajer->codigo_interno}}</td>
                                          <td style="text-align: right;">{{$i->porcentajer->valor}} %</td>
                                        <!--  <td>@if(($s->usuario)!=null) {{$s->usuario->nombre1}} {{$s->usuario->apellido1}} @endif</td>
                                          <td>@if(($s->estado)==0) {{$s->usuario->nombre1}} {{$s->usuario->apellido1}} @else  @endif</td> -->
                                          <td>{{$s->autorizacion}}</td>
                                      </tr>
                                    @endforeach
                                  @endif
                                  
                                  @if(count($fuente)>1)
                                  @foreach($fuente as $f)
                                    <tr>
                                        <td>@if(($s->nro_comprobante)!=null) {{$s->nro_comprobante}} @endif</td>
                                        <td>{{date("d-m-Y", strtotime($s->fecha))}}</td>
                                        <!-- <td style="text-align: left;">@if(($s->compras)!=null) @if(($s->compras->tipo)==1) COM-FA @else COM-FACT @endif :{{$s->compras->numero}} @endif</td> -->
                                    
                                        <td style="text-align:left;">@if(($s->proveedor)!=null) {{$s->proveedor->razonsocial}} @endif</td>
                                        <td style="text-align:left;">@if(($s->proveedor)!=null) {{$s->proveedor->id}} @endif</td>
                                        <td style="text-align:left;">@if(($s->compras)!=null){{$s->compras->numero}}  @endif</td>
                                        <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->subtotal_12}}  @endif</td>
                                        <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->subtotal_0}}  @endif</td>
                                        <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->subtotal}}  @endif</td>
                                        <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->iva_total}}  @endif</td>
                                        <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->total_final}}  @endif</td>
                                        <td class="right_text">@if(($f->totales)!=null) {{number_format($f->totales,2,'.',',')}}  @endif </td>
                                        <td class="right_text">{{$f->porcentajer->codigo_interno}}</td>
                                        <td class="right_text">{{$f->porcentajer->valor}} %</td>
                                        <td class="right_text"></td>
                                        <td class="right_text"></td>
                                        <td style="text-align: right;"></td>
                                        
                                      
                                      <!--  <td>@if(($s->usuario)!=null) {{$s->usuario->nombre1}} {{$s->usuario->apellido1}} @endif</td>
                                        <td>@if(($s->estado)==0) {{$s->usuario->nombre1}} {{$s->usuario->apellido1}} @else  @endif</td> -->
                                        <td>{{$s->autorizacion}}</td>
                                    </tr>
                                  @endforeach
                                  @else 
                                  @foreach($fuente as $f)
                                    <tr>
                                        <td>@if(($s->nro_comprobante)!=null) {{$s->nro_comprobante}} @endif</td>
                                        <td>{{date("d-m-Y", strtotime($s->fecha))}}</td>
                                        <!-- <td style="text-align: left;">@if(($s->compras)!=null) @if(($s->compras->tipo)==1) COM-FA @else COM-FACT @endif :{{$s->compras->numero}} @endif</td> -->
                                    
                                        <td style="text-align:left;">@if(($s->proveedor)!=null) {{$s->proveedor->razonsocial}} @endif</td>
                                        <td style="text-align:left;">@if(($s->proveedor)!=null) {{$s->proveedor->id}} @endif</td>
                                        <td style="text-align:left;">@if(($s->compras)!=null){{$s->compras->numero}}  @endif</td>
                                        <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->subtotal_12}}  @endif</td>
                                        <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->subtotal_0}}  @endif</td>
                                        <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->subtotal}}  @endif</td>
                                        <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->iva_total}}  @endif</td>
                                        <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->total_final}}  @endif</td>
                                        <td class="right_text">@if(($f->totales)!=null) {{number_format($f->totales,2,'.',',')}}  @endif </td>
                                        <td class="right_text">{{$f->porcentajer->codigo_interno}}</td>
                                        <td class="right_text">{{$f->porcentajer->valor}} %</td>
                                        <td class="right_text"></td>
                                        <td class="right_text"></td>
                                        <td style="text-align: right;"></td>
                                        
                                      
                                      <!--  <td>@if(($s->usuario)!=null) {{$s->usuario->nombre1}} {{$s->usuario->apellido1}} @endif</td>
                                        <td>@if(($s->estado)==0) {{$s->usuario->nombre1}} {{$s->usuario->apellido1}} @else  @endif</td> -->
                                        <td>{{$s->autorizacion}}</td>
                                    </tr>
                                  @endforeach
                                  @endif
                                  
                                  @if(count($iva)==1 && count($fuente)==1)
                                  
                                      <tr>
                                        <td>@if(($s->nro_comprobante)!=null) {{$s->nro_comprobante}} @endif</td>
                                        <td>{{date("d-m-Y", strtotime($s->fecha))}}</td>
                                        <td style="text-align:left;">@if(($s->proveedor)!=null) {{$s->proveedor->razonsocial}} @endif</td>
                                        <td style="text-align:left;">@if(($s->proveedor)!=null) {{$s->proveedor->id}} @endif</td>
                                        <td style="text-align:left;">@if(($s->compras)!=null){{$s->compras->numero}}  @endif</td>
                                        <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->subtotal_12}}  @endif</td>
                                        <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->subtotal_0}}  @endif</td>
                                        <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->subtotal}}  @endif</td>
                                        <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->iva_total}}  @endif</td>
                                        <td style="text-align:center;">@if(($s->compras)!=null){{$s->compras->total_final}}  @endif</td>
                                        <td class="right_text">@if(($s)!=null) {{number_format($s->valor_fuente,2,'.',',')}}  @endif </td>
                                        <td class="right_text">@foreach($fuente as $fs){{$fs->porcentajer->codigo_interno}} @endforeach</td>
                                        <td class="right_text">@foreach($fuente as $fs){{$fs->porcentajer->valor}} % @endforeach</td>
                                        <td class="right_text">@if(($s)!=null) {{number_format($s->valor_iva,2,'.',',')}}  @endif</td>
                                        <td class="right_text">@foreach($iva as $ivas){{$ivas->porcentajer->codigo_interno}} @endforeach</td>
                                        <td style="text-align: right;">@foreach($iva as $ivas){{$ivas->porcentajer->valor}} % @endforeach</td>
                                                                        
                                      <!--  <td>@if(($s->usuario)!=null) {{$s->usuario->nombre1}} {{$s->usuario->apellido1}} @endif</td>
                                        <td>@if(($s->estado)==0) {{$s->usuario->nombre1}} {{$s->usuario->apellido1}} @else  @endif</td> -->
                                        <td>{{$s->autorizacion}}</td>
                                    </tr>
                                  @endif
                               
                                 
                                  
                              @endforeach
                            
                          </tbody>
                          <tfoot>
                                  <tr>
                                    <!--   <td>&nbsp;</td>
                                      <td>&nbsp;</td>
                                      <td>&nbsp;</td>
                                      <td>&nbsp;</td>
                                      <td>&nbsp;</td>
                                      <td style="text-align: center; font-weight: bold;">{{trans('contableM.totales')}}:</td>
                                      <td class="right_text"> <b>{{number_format($total1,2,'.',',')}}</b> </td>
                                      <td style="text-align: center;">&nbsp;</td>
                                      <td class="right_text"> <b>{{number_format($total2,2,'.',',')}}</b> </td>
                                      <td>&nbsp;</td>
                                      <td>&nbsp;</td>
                                      <td>&nbsp;</td>
                                      <td>&nbsp;</td> -->
                                  </tr>
                          </tfoot>
                    </table>
                    </div>
                    
                  </div>
                 
                    
                  </div>
                
              </div>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->
          </div>
        </div> 
      </div>
      @endif

    </div>
  </div>
  <!-- /.content -->
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script type="text/javascript">

    $(document).ready(function(){
            $('.select2_cuentas').select2({
                tags: false
              });
    });
    $('#example2').DataTable({
        'paging': false,
         dom: 'Bfrtip',
        'lengthChange': false,
        'searching': true,
        'ordering': false,
        responsive: true,
        'info': false,
        'autoWidth': true,
        buttons: [{
          extend: 'copyHtml5',
          footer: true
        },
        {
          extend: 'excelHtml5',
          footer: true,
          title: 'REPORTE RETENCIONES {{$empresa->nombrecomercial}}'
        },
        {
          extend: 'csvHtml5',
          footer: true
        },
        {
          extend: 'pdfHtml5',
          orientation: 'landscape',
          pageSize: 'LEGAL',
          footer: true,
          title: 'REPORTE RETENCIONES {{$empresa->nombrecomercial}}',
          customize: function(doc) {
            doc.styles.title = {
              color: 'black',
              fontSize: '17',
              alignment: 'center'
            }
          }
        }
      ],
    })

    $('#example3').DataTable({
        'paging': false,
         dom: 'Bfrtip',
        'lengthChange': false,
        'searching': true,
        'ordering': false,
        responsive: true,
        'info': false,
        'autoWidth': true,
        buttons: [{
          extend: 'copyHtml5',
          footer: true
        },
        {
          extend: 'excelHtml5',
          footer: true,
          title: 'REPORTE RETENCIONES {{$empresa->nombrecomercial}}'
        },
        {
          extend: 'csvHtml5',
          footer: true
        },
        {
          extend: 'pdfHtml5',
          orientation: 'landscape',
          pageSize: 'LEGAL',
          footer: true,
          title: 'REPORTE RETENCIONES {{$empresa->nombrecomercial}}',
          customize: function(doc) {
            doc.styles.title = {
              color: 'black',
              fontSize: '17',
              alignment: 'center'
            }
          }
        }
      ],
    })

    $('#seguimiento').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });


    $('#cuenta').on('select2:select', function (e) {
        var cuenta = $('#cuenta').val();
        $('#nombre').val(cuenta);
        $('#nombre').select2().trigger('change');
      });


    $('#nombre').on('select2:select', function (e) {
        var nombre = $('#nombre').val();
        $('#cuenta').val(nombre);
        $('#cuenta').select2().trigger('change');
      });

    $( "#btn_imprimir" ).click(function() {  
      $( "#print_reporte_master" ).submit();
      // document.getElementById("print_reporte_master").submit(); 
    });
    $(function() {
        $('.infinite-scroll').jscroll({
            autoTrigger: true,
            loadingHtml: '<img class="center-block" src="{{asset("/loading.gif")}}" width="50px" alt="Loading..." />',
            padding: 0,
            nextSelector: '.pagination li.active + li a',
            contentSelector: 'div.infinite-scroll',
            callback: function() {
                $('div.paginationLinks').remove();
               
            }
        });
    });

    $(document).ready(function(){

      $('input[type="checkbox"].flat-green').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass   : 'iradio_flat-green'
      }); 

      $('input[type="checkbox"].flat-red').iCheck({
        checkboxClass: 'icheckbox_flat-red',
        radioClass   : 'iradio_flat-red'
      });

    });
    function excel(){
    $("#print_reporte_master" ).submit();
    }
    $("#nombre_proveedor").autocomplete({
    source: function( request, response ) {
        $.ajax( {
        url: "{{route('compra_buscar_nombreproveedor')}}",
        dataType: "json",
        data: {
            term: request.term
        },
        success: function( data ) {
            response(data);
        }
        } );
    },
    minLength: 2,
    } );
    function cambiar_nombre_proveedor(){
        $.ajax({
            type: 'post',
            url:"{{route('compra_buscar_proveedornombre')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'nombre':$("#nombre_proveedor").val()},
            success: function(data){
                if(data.value != "no"){
                    $('#id_proveedor').val(data.value);
                    $('#id_proveedor2').val(data.value);
                    $('#direccion_id_proveedor').val(data.direccion);
                }else{
                    $('#id_proveedor').val("");
                    $('#id_proveedor2').val("");
                    $('#direccion_proveedor').val("");
                }

            },
            error: function(data){
                console.log(data);
            }
        });
    }

    $(function () {
        $('#fecha_desde').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fecha_desde}}',
            });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fecha_hasta}}',

            });
        $("#fecha_desde").on("dp.change", function (e) {
            verifica_fechas();
        });

         $("#fecha_hasta").on("dp.change", function (e) {
            verifica_fechas();
        });
 
  });
  function buscar()
  {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }  
  function verifica_fechas(){
    if(Date.parse($("#fecha_desde").val()) > Date.parse($("#fecha_hasta").val())){
      Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Verifique el rango de fechas y vuelva consultar'
      });
    } 
  }

</script>
@endsection
