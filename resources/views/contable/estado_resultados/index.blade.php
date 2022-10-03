@extends('contable.estado_resultados.base')
@section('action-content')
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
  <!-- Main content -->
<style>
  p.s1 {
    margin-left:  10px;
    font-size:    14px;
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
    margin-left:  60px;
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
  .table-condensed>thead>tr>th>td, .table-condensed>tbody>tr>th>td, .table-condensed>tfoot>tr>th>td, .table-condensed>thead>tr>td, .table-condensed>tbody>tr>td, .table-condensed>tfoot>tr>td {
    padding: 0.5px;
    line-height: 1;
  }
  .clicks p:hover{
    color:green;
    cursor: pointer;
    font-weight: bold;
  }
</style>
  <section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.Contabilidad')}}</a></li>
      <li class="breadcrumb-item"><a href="../">{{trans('etodos.Estadoderesultadointegral')}}</a></li>
    </ol>
  </nav>
  <div class="box">
      <div class="row head-title">
        <div class="col-md-12 cabecera">
            <label class="color_texto" for="title">{{trans('etodos.BUSCADOR')}}</label>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body dobra">
        <form method="POST" id="reporte_master" action="{{ route('estadoresultados.show') }}" >
        {{ csrf_field() }}

          <div class="form-group col-md-6 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha" class="texto col-md-3 control-label">{{trans('etodos.Fechadesde')}}</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" value="@if($tipo_dato == 0){{$fecha_desde}}@endif" autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_desde').value = ''; buscar();"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group col-md-6 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha_hasta" class="texto col-md-3 control-label">{{trans('etodos.Fechahasta')}}</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" value="{{$fecha_hasta}}"  autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="lbl_cuentas_detalle" class="texto col-md-5 control-label" >{{trans('contableM.Mostrarcuentasdedetalle')}}</label>
              <input type="checkbox" id="cuentas_detalle" class="flat-green" name="cuentas_detalle" value=""  @if(@$cuentas_detalle=="1") checked @endif>
          </div>

          <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="mostrar_cierre" class="texto col-md-5 control-label" >{{trans('etodos.Mostrarsincierre')}}</label>
              <input type="checkbox" id="mostrar_cierre" class="flat-green" name="mostrar_cierre" value="1"  @if(@$mostrar_cierre=="1") checked @endif>
          </div>

          <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="mostrar_acumulado" class="texto col-md-5 control-label" >{{trans('etodos.MostrarAcumulado')}}</label>
              <input type="checkbox" id="mostrar_acumulado" class="flat-green" name="mostrar_acumulado" value="1"  @if(@$mostrar_acumulado=="1") checked @endif>
          </div>

          <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
            <button type="submit" class="btn btn-primary" id="boton_buscar">
                  <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
            </button>
            <button type="button" class="btn btn-primary" id="btn_imprimir" name="btn_imprimir">
                  <span class="glyphicon glyphicon-print" aria-hidden="true"></span> Imprimir
            </button>
            <button type="button" class="btn btn-primary" id="btn_exportar">
              <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
            </button>
          </div>
        </form>
      </div>

      @if(count($ingresos)>0 || count($costos)>0 || count($gastos)>0)
      <div id="example2_wrapper" class="form-inline dt-bootstrap">
        <div class="row">
          <div class="col-md-12">
            <div class="box box-solid">
              <!-- /.box-header -->
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
                  <h4 style="text-align: center;">ESTADO DE RESULTADOS INTEGRAL</h4>
                  @if($tipo_dato == 0)
                  <h4 style="text-align: center;">DEL {{$fecha_desde}} AL {{$fecha_hasta}}</h4>
                  @else
                  <h4 style="text-align: center;">AL {{$fecha_hasta}}</h4>
                  @endif
                </div>
                <div class="col-md-4">
                  {{-- <dl>
                    <dd style="text-align:right">{{$empresa->direccion}} &nbsp; <i class="fa fa-building"></i></dd>
                    <dd style="text-align:right">{{trans('contableM.telefono')}}: {{$empresa->telefono1}} - {{$empresa->telefono2}}&nbsp;<i class="fa fa-phone"></i> </dd>
                    <dd style="text-align:right"> {{$empresa->email}} &nbsp;<i class="fa fa-envelope-o"></i></dd>
                  </dl> --}}
                </div>


              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-2">&nbsp;</div>
          <div class="table-responsive col-md-8">
            <div class="content">

              <table id="example2" class="table table-striped table-condensed" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                <thead>
                  <tr class='well-dark'>
                    <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.Cuenta')}}</th>
                    <th width="40%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.detalle')}}</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('contableM.saldo')}}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td colspan="3"><strong>{{trans('etodos.INGRESOS')}}</strong></td>
                  </tr>
                  @php
                    $cont = 0;  $esp = ""; $contador=0;
                    $idHiddens =0;
                  @endphp

                  @foreach($ingresos as $value)
                  @php
                    $cont = 0;  $esp = "";
                    $cont = substr_count($value['cuenta'],".");
                    if($cont > 3){$cont = 3;}
                    if($cont<>0){   $esp = str_repeat("&nbsp;",($cont*2));  }
                    if($value['cuenta']=="4") {$toting = $value['saldo'];}

                    $cont = substr_count($value['cuenta'],".");
                    if($cont==0){   $sangria = "class=s1"; $t = "class=t1";  }
                    if($cont==1){   $sangria = "class=s2"; $t = "class=t2";  }
                    if($cont==2){   $sangria = "class=s3"; $t = "class=t3";  }
                    if($cont>=3){   $sangria = "class=s4"; $t = "class=t3";  }
                  @endphp
                  @if($value['saldo']!=0)
                    @if($cont>=4)
                      @php
                        $busqueda = DB::table('plan_cuentas_empresa as pce')
                        ->where('pce.plan', $value['cuenta'])
                        ->where('pce.id_empresa', $id_empresa)
                        ->first();
                      @endphp
                      @if(!is_null($busqueda))
                        
                        @php $idHiddens++; @endphp
                        @if($busqueda->estado == 2)
                        <tr class="clicks">
                          <td><p {{ $sangria }} onClick="busc(cuenta_d<?php echo $idHiddens; ?>)">{{$value['cuenta']}}</p></td>
                            <input type="hidden" id="cuenta_d<?php echo $idHiddens; ?>" value="{{$value['cuenta']}}">
                          <td style="font-size: 10px;@if($cont < 3) font-weight: bold; @endif"><p onClick="busc(cuenta_d<?php echo $idHiddens; ?>)"  {{ $sangria }}>{{ $value['nombre']}}</p></td>
                          <td style="font-size: 10px;text-align: right; @if($value['saldo'] < 0) color:red; @endif" >{{number_format($value['saldo'],2)}}</td>
                        </tr>
                        @else
                        <tr>
                          <td><p {{ $sangria }} >{{$value['cuenta']}}</p></td>
                          <td style="font-size: 10px;@if($cont < 3) font-weight: bold; @endif"><p {{ $sangria }}>{{ $value['nombre']}}</p></td>
                          <td style="font-size: 10px;text-align: right; @if($value['saldo'] < 0) color:red; @endif" >{{number_format($value['saldo'],2)}}</td>
                        </tr> 
                        @endif
                      @else
                        <tr>
                          <td><p {{ $sangria }} >{{$value['cuenta']}}</p></td>
                          <td style="font-size: 10px;@if($cont < 3) font-weight: bold; @endif"><p {{ $sangria }}>{{ $value['nombre']}}</p></td>
                          <td style="font-size: 10px;text-align: right; @if($value['saldo'] < 0) color:red; @endif" >{{number_format($value['saldo'],2)}}</td>
                        </tr>
                      @endif
                      
                    @else
                      <tr>
                        <td><p {{ $sangria }} >{{$value['cuenta']}}</p></td>
                        <td style="font-size: 10px;@if($cont < 3) font-weight: bold; @endif"><p {{ $sangria }}>{{ $value['nombre']}}</p></td>
                        <td style="font-size: 10px;text-align: right; @if($value['saldo'] < 0) color:red; @endif" >{{number_format($value['saldo'],2)}}</td>
                      </tr>
                    @endif
                  @endif
                  @endforeach
                  <tr>
                    <td colspan="3">&nbsp;</td>
                  </tr>
                  {{-- COSTOS --}}
                  <tr>
                    <td colspan="3"><strong>COSTOS Y GASTOS</strong></td>
                  </tr>
                  @php
                  $cont = 0;  $esp = "";
                  $idHidden=0;
                  @endphp

                  @foreach($costos as $value)
                    @php

                      $cont = 0;  $esp = "";
                      $cont = substr_count($value['cuenta'],".");
                      if($cont > 3){$cont = 3;}
                      if($cont<>0){   $esp = str_repeat("&nbsp;",($cont*2));  }
                      //if($value['cuenta']=="5") {$totgas = $value['saldo'];}
                      $cont = substr_count($value['cuenta'],".");
                      if($cont==0){   $sangria = "class=s1"; $t = "class=t1";  }
                      if($cont==1){   $sangria = "class=s2"; $t = "class=t2";  }
                      if($cont==2){   $sangria = "class=s3"; $t = "class=t3";  }
                      if($cont>=3){   $sangria = "class=s4"; $t = "class=t3";  }
                    @endphp
                    @if($value['saldo']!=0)
                      @if($cont>=4)
                      @php
                        $busqueda = DB::table('plan_cuentas_empresa as pce')
                        ->where('pce.plan', $value['cuenta'])
                        ->where('pce.id_empresa', $id_empresa)
                        ->first();
                      @endphp
                      @if(!is_null($busqueda))
                        @php  $idHidden++; @endphp
                        @if($busqueda->estado == 2)
                        <tr class="clicks">
                          <td><p {{ $sangria }} onClick="busc(cuenta<?php echo $idHidden; ?>)">{{$value['cuenta']}}</p></td>
                            <input type="hidden" id="cuenta<?php echo $idHidden; ?>" value="{{$value['cuenta']}}">
                          <td style="font-size: 10px;@if($cont < 3) font-weight: bold; @endif"><p onClick="busc(cuenta<?php echo $idHidden; ?>)"  {{ $sangria }}>{{ $value['nombre']}}</p></td>
                          <td style="font-size: 10px;text-align: right; @if($value['saldo'] < 0) color:red; @endif" >{{number_format($value['saldo'],2)}}</td>
                        </tr>
                        @else
                        <tr>
                          <td><p {{ $sangria }} >{{$value['cuenta']}}</p></td>
                          <td style="font-size: 10px;@if($cont < 3) font-weight: bold; @endif"><p {{ $sangria }}>{{ $value['nombre']}}</p></td>
                          <td style="font-size: 10px;text-align: right; @if($value['saldo'] < 0) color:red; @endif" >{{number_format($value['saldo'],2)}}</td>
                        </tr> 
                        @endif
                      @else
                        <tr>
                          <td><p {{ $sangria }} >{{$value['cuenta']}}</p></td>
                          <td style="font-size: 10px;@if($cont < 3) font-weight: bold; @endif"><p {{ $sangria }}>{{ $value['nombre']}}</p></td>
                          <td style="font-size: 10px;text-align: right; @if($value['saldo'] < 0) color:red; @endif" >{{number_format($value['saldo'],2)}}</td>
                        </tr>
                      @endif
                      
                    @else
                      <tr>
                        <td><p {{ $sangria }} >{{$value['cuenta']}}</p></td>
                        <td style="font-size: 10px;@if($cont < 3) font-weight: bold; @endif"><p {{ $sangria }}>{{ $value['nombre']}}</p></td>
                        <td style="font-size: 10px;text-align: right; @if($value['saldo'] < 0) color:red; @endif" >{{number_format($value['saldo'],2)}}</td>
                      </tr>
                    @endif
                    @endif
                  @endforeach
                  <tr>
                    <td style="font-size: 12px;" colspan="2"><strong>UTILIDAD / PERDIDA DEL PERÍODO:</strong></td>
                    <td style="font-size: 12px;text-align: right; @if(($totpyg) < 0) color:red; @endif"  >{{number_format(($totpyg),2)}}</td>
                  </tr>
                  <tr>
                    <td style="font-size: 12px;" colspan="2"><strong>15% PARTICIPACION A TRABAJADORES:</strong></td>
                    <td style="font-size: 12px;text-align: right; @if(($trabajadores) < 0) color:red; @endif"  >{{number_format(($trabajadores),2)}}</td>
                  </tr>
                  <tr>
                    <td style="font-size: 12px;" colspan="2"><strong>UTILIDAD CONTABLE:</strong></td>
                    <td style="font-size: 12px;text-align: right; @if(($total) < 0) color:red; @endif"  >{{number_format(($total),2)}}</td>
                  </tr>
                  <tr>
                    <td style="font-size: 12px;" colspan="2"><strong>UTILIDAD GRAVABLE:</strong></td>
                    <td style="font-size: 12px;text-align: right; @if(($total_gravable) < 0) color:red; @endif"  >{{number_format(($total_gravable),2)}}</td>
                  </tr>
                  <tr>
                    <td style="font-size: 12px;" colspan="2"><strong>IMPUESTO GENERADO:</strong></td>
                    <td style="font-size: 12px;text-align: right; @if(($renta_acumulada) < 0) color:red; @endif"  >{{number_format(($renta_acumulada),2)}}</td>
                  </tr>
                  <tr>
                    <td style="font-size: 12px;" colspan="2"><strong>@if(($total) < 0) PERDIDA @else UTILIDAD @endif NETA:</strong></td>
                    <td style="font-size: 12px;text-align: right; @if(($total) < 0) color:red; @endif"  >{{number_format(($total -$renta_acumulada),2)}}</td>
                  </tr>
                  <tr>
                    <td style="font-size: 12px;" colspan="2"><strong>@if(($total) < 0) PERDIDA @else UTILIDAD @endif NETA:</strong></td>
                    <td style="font-size: 12px;text-align: right; @if(($total) < 0) color:red; @endif"  >{{number_format(($total -$renta_acumulada),2)}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="col-md-2">&nbsp;</div>
        </div>
      @endif
        
      <div class="content">
          
              <div class="col-md-12">
                <div class="row">
                  <div class=" col-md-6">
                    @if(!is_null($empresa->id_representante))
                      @if($empresa->persona_nat_jur == '2')
                      <div class="row">
                        <label class="col-md-12">{{trans('contableM.RepresentanteLegal')}}</label>
                        @if(is_null($empresa->tipo_representante))
                        <label class="col-md-12">{{$empresa->pref_repre->titulo_prefijo}} {{$empresa->usuario_representante->nombre1}} {{$empresa->usuario_representante->nombre2}} {{$empresa->usuario_representante->apellido1}} {{$empresa->usuario_representante->apellido2}}
                        </label>
                        <label class="col-md-12">C.I. {{$empresa->id_representante}}</label>
                        @else
                        <label class="col-md-12">{{$empresa->pref_repre->titulo_prefijo}} {{$empresa->usuario_representante->nombre1}} {{$empresa->usuario_representante->nombre2}} {{$empresa->usuario_representante->apellido1}} {{$empresa->usuario_representante->apellido2}} <br> EN REPRESENTACION DE {{$empresa->empresa_representante}}
                        </label>
                        @endif

                      </div>
                      @endif
                    @endif
                  </div>

                  <div class="col-md-6">
                    <div class="row" style="float:right">
                      @if(!is_null($empresa->id_contador))
                        <label class="col-md-12">{{trans('contableM.ContadoraGeneral')}}</label> <br>

                        <label class="col-md-12">{{$empresa->pref_cont->titulo_prefijo}} {{$empresa->usuario_contador->nombre1}} {{$empresa->usuario_contador->nombre2}} {{$empresa->usuario_contador->apellido1}} {{$empresa->usuario_contador->apellido2}}</label> <br>


                        <label class="col-md-12">C.I. {{$empresa->id_contador}}</label> <br>

                        <label class="col-md-12">Registro # {{$empresa->num_registro_contador}}</label> <br>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
        </div>



  </div>
  </section>
  <form method="POST" id="print_reporte_master" action="{{ route('estadoresultados.show') }}" target="_blank">
    {{ csrf_field() }}
    <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{$fecha_desde}}">
    <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{$fecha_hasta}}">
    <input type="hidden" name="filcuentas_detalle" id="filcuentas_detalle" value="{{@$cuentas_detalle}}">
    <input type="hidden" name="filmostrar_detalles" id="filcuentas_detalle" value="{{@$cuentas_detalle}}">
    <input type="hidden" name="mostrar_cierre" id="filmostrar_cierre" value="{{@$mostrar_detalles}}">
    <input type="hidden" name="mostrar_acumulado" id="filmostrar_acumulado" value="{{@$mostrar_acumulado}}">
    <input type="hidden" name="exportar" id="exportar" value="0">
    <input type="hidden" name="imprimir" id="imprimir" value="">
  </form>
  <form action="{{ route('libro_mayor.index') }}" style="display:none" target="_blank">
    <input type="text"  name="fecha" id="fechan">
    <input type="text" name="fecha_hasta" id="fecha_hastan">
    <input type="text" name="cuenta" id="cuentan">
    <input type="text" id="cuenta_hasta" name="cuenta_hasta">
    <input type="submit" id="abrir_nuevo">
  </form>

  <!-- /.content -->
  <script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
    $('#seguimiento').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });

    $(document).ready(function(){

      // $('#example2').DataTable({
      //   'paging'      : false,
      //   'lengthChange': false,
      //   'searching'   : false,
      //   'ordering'    : true,
      //   'info'        : false,
      //   'autoWidth'   : false
      // });

      tinymce.init({
        selector: '#hc'
      });

      $('input[type="checkbox"].flat-green').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass   : 'iradio_flat-green'
      });

      $('input[type="checkbox"].flat-red').iCheck({
        checkboxClass: 'icheckbox_flat-red',
        radioClass   : 'iradio_flat-red'
      });

    });

    $(function () {
        $('#fecha_desde').datetimepicker({
            format: 'DD/MM/YYYY',
            //defaultDate: '{{$fecha_desde}}',
            });
        $('#fecha_hasta').datetimepicker({
            format: 'DD/MM/YYYY',
            //defaultDate: '{{$fecha_hasta}}',

            });
        $("#fecha_desde").on("dp.change", function (e) {
            //buscar();
        });

         $("#fecha_hasta").on("dp.change", function (e) {
            //buscar();
        });
        $( "#btn_imprimir").click(function() {
          $("#filfecha_desde").val($("#fecha_desde").val());
          $("#filfecha_hasta").val($("#fecha_hasta").val());
          // $("#filcuentas_detalle").val($("#cuentas_detalle").val()); alert($("#cuentas_detalle").val());
          if($("#cuentas_detalle").prop("checked")){
            $("#filcuentas_detalle").val(1);
          }else{
            $("#filcuentas_detalle").val("");
          }
          if($("#mostrar_cierre").prop("checked")){
            $("#filmostrar_cierre").val(1);
          }else{
            $("#filmostrar_cierre").val("");
          }
          if($("#mostrar_acumulado").prop("checked")){
            $("#filmostrar_acumulado").val(1);
          }else{
            $("#filmostrar_acumulado").val("");
          }
          // $("#filmostrar_detalles").val($("#mostrar_detalles").val());
          $("#exportar").val(0);
          $( "#print_reporte_master" ).submit();
        });
  });
  function buscar()
  {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }
  function verifica_fechas(){
    // var fecha_desde = Date($("#fecha_desde").val() + '01-01');
    // var fecha_hasta = Date($("#fecha_hasta").val() + '-30');
    var fecha_desde = Date($("#fecha_desde").val());
    var fecha_hasta = Date($("#fecha_hasta").val());
    if(Date.parse(fecha_desde) > Date.parse(fecha_hasta)){
      Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Verifique el rango de fechas y vuelva consultar'
      });
    }
  }
  $( "#btn_exportar").click(function() {
        $("#filfecha_desde").val($("#fecha_desde").val());
        $("#filfecha_hasta").val($("#fecha_hasta").val());
        if($("#cuentas_detalle").prop("checked")){
          $("#filcuentas_detalle").val(1);
        }else{
          $("#filcuentas_detalle").val("");
        }
        if($("#mostrar_cierre").prop("checked")){
          $("#filmostrar_cierre").val(1);
        }else{
          $("#filmostrar_cierre").val("");
        }
        if($("#mostrar_acumulado").prop("checked")){
          $("#filmostrar_acumulado").val(1);
        }else{
          $("#filmostrar_acumulado").val("");
        }
         //alert($("#cuentas_detalle").prop("checked"));
        $("#filmostrar_detalles").val($("#mostrar_detalles").val());
        $("#exportar").val(1);
        $("#print_reporte_master" ).submit();
    });
    function clicks(ids){
      $("#"+ids).click();
    }

    function busc(id){
      let fecha_desde = document.getElementById('fecha_desde').value;
      let fecha_hasta = document.getElementById('fecha_hasta').value;
      let cuenta = id.value;
      console.log(cuenta);

      //console.log(cuenta);
      document.getElementById('fechan').value= fecha_desde;
      document.getElementById('fecha_hastan').value=  fecha_hasta;
      document.getElementById('cuentan').value= cuenta;
      document.getElementById('cuenta_hasta').value= cuenta;
      $("#abrir_nuevo").click();

    }
</script>
@endsection
